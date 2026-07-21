#!/usr/bin/env bash
set -euo pipefail

COMMAND="${1:-}"

if [[ -z "$COMMAND" ]]; then
  echo "Uso: pr.sh create|edit|verify ..." >&2
  exit 1
fi

shift
PR_NUMBER=""
TITLE=""
BODY_FILE=""
BASE="master"
HEAD=""
REPOSITORY=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --pr)
      PR_NUMBER="${2:-}"
      shift 2
      ;;
    --title)
      TITLE="${2:-}"
      shift 2
      ;;
    --body-file)
      BODY_FILE="${2:-}"
      shift 2
      ;;
    --base)
      BASE="${2:-master}"
      shift 2
      ;;
    --head)
      HEAD="${2:-}"
      shift 2
      ;;
    *)
      echo "Parametro desconhecido: $1" >&2
      exit 1
      ;;
  esac
done

require_file() {
  if [[ -z "$1" || ! -f "$1" ]]; then
    echo "Arquivo de corpo nao encontrado: ${1:-vazio}" >&2
    exit 1
  fi
}

normalize_body() {
  local source="$1"
  local target="$2"

  php -r '
    $source = $argv[1];
    $target = $argv[2];
    $value = file_get_contents($source);
    if ($value === false) {
        fwrite(STDERR, "Nao foi possivel ler o corpo do PR.\n");
        exit(1);
    }
    if (preg_match("//u", $value) !== 1) {
        $converted = iconv("Windows-1252", "UTF-8//IGNORE", $value);
        if ($converted === false) {
            fwrite(STDERR, "Nao foi possivel converter o corpo para UTF-8.\n");
            exit(1);
        }
        $value = $converted;
    }
    if (preg_match("/\\\\n/", $value) === 1) {
        fwrite(STDERR, "O corpo contem \\\\n literal; use quebras de linha reais.\n");
        exit(1);
    }
    if (preg_match("/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/", $value) === 1) {
        fwrite(STDERR, "O corpo contem caracteres de controle invalidos.\n");
        exit(1);
    }
    if (file_put_contents($target, $value) === false) {
        fwrite(STDERR, "Nao foi possivel gravar o corpo normalizado.\n");
        exit(1);
    }
  ' "$source" "$target"
}

repository_name() {
  if [[ -z "$REPOSITORY" ]]; then
    REPOSITORY="$(gh repo view --json nameWithOwner --jq .nameWithOwner)"
  fi
  echo "$REPOSITORY"
}

json_payload() {
  local body_file="$1"
  local target="$2"

  php -r '
    $body = file_get_contents($argv[1]);
    if ($body === false) {
        fwrite(STDERR, "Nao foi possivel ler o corpo normalizado.\n");
        exit(1);
    }
    $payload = json_encode(
        ["body" => $body],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    if (file_put_contents($argv[2], $payload) === false) {
        fwrite(STDERR, "Nao foi possivel gravar o payload JSON.\n");
        exit(1);
    }
  ' "$body_file" "$target"
}

prepare_body() {
  require_file "$BODY_FILE"
  local normalized
  normalized="$(mktemp "${TMPDIR:-/tmp}/contex-pr-body.XXXXXX")"
  normalize_body "$BODY_FILE" "$normalized"
  echo "$normalized"
}

case "$COMMAND" in
  create)
    [[ -n "$TITLE" ]] || { echo "--title e obrigatorio." >&2; exit 1; }
    [[ -n "$HEAD" ]] || HEAD="$(git branch --show-current)"
    body_file="$(prepare_body)"
    trap 'rm -f "$body_file"' EXIT
    gh pr create --base "$BASE" --head "$HEAD" --title "$TITLE" --body-file "$body_file"
    ;;
  edit)
    [[ -n "$PR_NUMBER" ]] || { echo "--pr e obrigatorio." >&2; exit 1; }
    body_file="$(prepare_body)"
    payload_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-payload.XXXXXX")"
    trap 'rm -f "$body_file" "$payload_file"' EXIT
    json_payload "$body_file" "$payload_file"
    gh api "repos/$(repository_name)/pulls/${PR_NUMBER}" --method PATCH --input "$payload_file" --jq .html_url
    ;;
  verify)
    [[ -n "$PR_NUMBER" ]] || { echo "--pr e obrigatorio." >&2; exit 1; }
    gh pr view "$PR_NUMBER" --json body --jq .body | php -r '
      $body = stream_get_contents(STDIN);
      if (preg_match("//u", $body) !== 1 || preg_match("/\\\\n/", $body) === 1 || preg_match("/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/", $body) === 1) {
          fwrite(STDERR, "O corpo publicado do PR nao passou na validacao UTF-8.\n");
          exit(1);
      }
      echo "Corpo do PR validado em UTF-8, sem escapes literais ou caracteres de controle.\n";
    '
    ;;
  *)
    echo "Comando desconhecido: $COMMAND" >&2
    exit 1
    ;;
esac
