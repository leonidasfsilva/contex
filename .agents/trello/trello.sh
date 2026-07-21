#!/usr/bin/env bash
set -euo pipefail

COMMAND="${1:-}"

if [[ -z "$COMMAND" ]]; then
  echo "Informe um comando: test, boards, lists, cards, card, create-card, comment, move-card, attach-url, update-name ou update-description." >&2
  exit 1
fi

shift || true

BOARD_ID=""
LIST_ID=""
CARD_ID=""
CARD_NUMBER=""
NAME=""
DESC=""
DESC_FILE=""
TEXT=""
URL_VALUE=""
POSITION="top"

while [[ $# -gt 0 ]]; do
  case "$1" in
    -BoardId|--board-id)
      BOARD_ID="${2:-}"
      shift 2
      ;;
    -ListId|--list-id)
      LIST_ID="${2:-}"
      shift 2
      ;;
    -CardId|--card-id)
      CARD_ID="${2:-}"
      shift 2
      ;;
    -CardNumber|--card-number)
      CARD_NUMBER="${2:-}"
      shift 2
      ;;
    -Name|--name)
      NAME="${2:-}"
      shift 2
      ;;
    -Desc|--desc)
      DESC="${2:-}"
      shift 2
      ;;
    -DescFile|--desc-file)
      DESC_FILE="${2:-}"
      shift 2
      ;;
    -Text|--text)
      TEXT="${2:-}"
      shift 2
      ;;
    -Url|--url)
      URL_VALUE="${2:-}"
      shift 2
      ;;
    -Position|--position)
      POSITION="${2:-top}"
      shift 2
      ;;
    *)
      echo "Parametro desconhecido: $1" >&2
      exit 1
      ;;
  esac
done

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_PATH="$SCRIPT_DIR/config.local.json"

if [[ -z "${TRELLO_CURL_BIN:-}" ]]; then
  if [[ -x "/c/laragon/bin/laragon/utils/curl.exe" ]]; then
    TRELLO_CURL_BIN="/c/laragon/bin/laragon/utils/curl.exe"
  else
    TRELLO_CURL_BIN="$(command -v curl)"
  fi
fi

if [[ -z "$TRELLO_CURL_BIN" || ! -x "$TRELLO_CURL_BIN" ]]; then
  echo "cURL nao encontrado. Defina TRELLO_CURL_BIN com o caminho do executavel." >&2
  exit 1
fi

json_config_value() {
  local key="$1"

  if [[ ! -f "$CONFIG_PATH" ]]; then
    return 0
  fi

  php -r '
    $path = $argv[1];
    $key = $argv[2];
    $config = json_decode(file_get_contents($path), true);
    if (is_array($config) && array_key_exists($key, $config) && $config[$key] !== null) {
        echo $config[$key];
    }
  ' "$CONFIG_PATH" "$key"
}

urlencode() {
  php -r 'echo rawurlencode($argv[1]);' "$1"
}

normalize_utf8() {
  php -r '
    $value = $argv[1];

    if (preg_match("//u", $value) === 1) {
        echo $value;
        exit(0);
    }

    $converted = iconv("Windows-1252", "UTF-8//IGNORE", $value);
    echo $converted === false ? $value : $converted;
  ' "$1"
}

TRELLO_API_KEY="${TRELLO_KEY:-$(json_config_value key)}"
TRELLO_API_TOKEN="${TRELLO_TOKEN:-$(json_config_value token)}"
CONFIG_BOARD_ID="$(json_config_value boardId)"
BOARD_ID="${BOARD_ID:-${TRELLO_BOARD_ID:-$CONFIG_BOARD_ID}}"

if [[ -z "$TRELLO_API_KEY" ]]; then
  echo "TRELLO_KEY nao configurada. Preencha .agents/trello/config.local.json ou a variavel de ambiente TRELLO_KEY." >&2
  exit 1
fi

if [[ -z "$TRELLO_API_TOKEN" ]]; then
  echo "TRELLO_TOKEN nao configurado. Preencha .agents/trello/config.local.json ou a variavel de ambiente TRELLO_TOKEN." >&2
  exit 1
fi

trello_request() {
  local method="$1"
  local path="$2"
  shift 2

  local separator="?"
  if [[ "$path" == *"?"* ]]; then
    separator="&"
  fi

  local auth="key=$(urlencode "$TRELLO_API_KEY")&token=$(urlencode "$TRELLO_API_TOKEN")"
  local request_url="https://api.trello.com/1/${path}${separator}${auth}"

  if [[ $# -gt 0 ]]; then
    "$TRELLO_CURL_BIN" -sS -X "$method" "$request_url" "$@"
  else
    "$TRELLO_CURL_BIN" -sS -X "$method" "$request_url"
  fi
}

require_value() {
  local value="$1"
  local message="$2"

  if [[ -z "$value" ]]; then
    echo "$message" >&2
    exit 1
  fi
}

NAME="$(normalize_utf8 "$NAME")"
DESC="$(normalize_utf8 "$DESC")"
TEXT="$(normalize_utf8 "$TEXT")"

case "$COMMAND" in
  test)
    trello_request GET "members/me?fields=id,username,fullName"
    ;;

  boards)
    trello_request GET "members/me/boards?fields=id,name,closed,url"
    ;;

  lists)
    require_value "$BOARD_ID" "BoardId nao informado. Use -BoardId, config.local.json ou TRELLO_BOARD_ID."
    trello_request GET "boards/${BOARD_ID}/lists?fields=id,name,closed,pos"
    ;;

  cards)
    require_value "$LIST_ID" "ListId e obrigatorio para listar cards."
    trello_request GET "lists/${LIST_ID}/cards?fields=id,idShort,name,desc,closed,url,idList,pos"
    ;;

  card)
    if [[ -z "$CARD_ID" && -z "$CARD_NUMBER" ]]; then
      echo "Informe CardId ou CardNumber para consultar um card." >&2
      exit 1
    fi

    if [[ -n "$CARD_NUMBER" ]]; then
      require_value "$BOARD_ID" "BoardId e obrigatorio para consultar card por numero. Use -BoardId, config.local.json ou TRELLO_BOARD_ID."
      trello_request GET "boards/${BOARD_ID}/cards?fields=id,idShort,name,desc,closed,url,idList,pos" |
        php -r '
          $number = (int) $argv[1];
          $cards = json_decode(stream_get_contents(STDIN), true);
          foreach ($cards ?: [] as $card) {
              if ((int) ($card["idShort"] ?? 0) === $number) {
                  echo json_encode($card, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                  exit(0);
              }
          }
          fwrite(STDERR, "Card numero {$number} nao encontrado no board configurado.\n");
          exit(1);
        ' "$CARD_NUMBER"
    else
      trello_request GET "cards/${CARD_ID}?fields=id,idShort,name,desc,closed,url,idList,pos"
    fi
    ;;

  create-card)
    require_value "$LIST_ID" "ListId e obrigatorio para criar card."
    require_value "$NAME" "Name e obrigatorio para criar card."

    body=(
      --data-raw "idList=$(urlencode "$LIST_ID")"
      --data-raw "name=$(urlencode "$NAME")"
      --data-raw "pos=$(urlencode "$POSITION")"
    )

    if [[ -n "$DESC" ]]; then
      body+=(--data-raw "desc=$(urlencode "$DESC")")
    fi

    trello_request POST "cards" "${body[@]}"
    ;;

  comment)
    require_value "$CARD_ID" "CardId e obrigatorio para comentar."
    require_value "$TEXT" "Text e obrigatorio para comentar."
    trello_request POST "cards/${CARD_ID}/actions/comments" --data-raw "text=$(urlencode "$TEXT")"
    ;;

  move-card)
    require_value "$CARD_ID" "CardId e obrigatorio para mover card."
    require_value "$LIST_ID" "ListId e obrigatorio para mover card."

    trello_request PUT "cards/${CARD_ID}/idList?value=$(urlencode "$LIST_ID")" >/dev/null
    trello_request PUT "cards/${CARD_ID}/pos?value=$(urlencode "$POSITION")"
    ;;

  attach-url)
    require_value "$CARD_ID" "CardId e obrigatorio para anexar URL."
    require_value "$URL_VALUE" "Url e obrigatoria para anexar URL."

    body=(--data-raw "url=$(urlencode "$URL_VALUE")")

    if [[ -n "$NAME" ]]; then
      body+=(--data-raw "name=$(urlencode "$NAME")")
    fi

    trello_request POST "cards/${CARD_ID}/attachments" "${body[@]}"
    ;;

  update-name)
    require_value "$CARD_ID" "CardId e obrigatorio para atualizar o nome."
    require_value "$NAME" "Name e obrigatorio para atualizar o nome."

    trello_request PUT "cards/${CARD_ID}" --data-raw "name=$(urlencode "$NAME")"
    ;;

  update-description)
    require_value "$CARD_ID" "CardId e obrigatorio para atualizar descricao."

    if [[ -n "$DESC_FILE" ]]; then
      require_value "$DESC_FILE" "DescFile e obrigatorio para atualizar descricao por arquivo."

      if [[ ! -f "$DESC_FILE" ]]; then
        echo "Arquivo de descricao nao encontrado: $DESC_FILE" >&2
        exit 1
      fi

      DESC="$(normalize_utf8 "$(cat "$DESC_FILE")")"
    fi

    require_value "$DESC" "Desc ou DescFile e obrigatorio para atualizar descricao."

    trello_request PUT "cards/${CARD_ID}?desc=$(urlencode "$DESC")"
    ;;

  *)
    echo "Comando desconhecido: $COMMAND" >&2
    exit 1
    ;;
esac
