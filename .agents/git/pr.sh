#!/usr/bin/env bash
set -euo pipefail

COMMAND="${1:-}"

if [[ -z "$COMMAND" ]]; then
  echo "Uso: pr.sh create|edit|comment|verify ..." >&2
  exit 1
fi

shift
PR_NUMBER=""
TITLE=""
BODY_FILE=""
BASE="master"
HEAD=""
REPOSITORY=""
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
UTF8_GUARD="$SCRIPT_DIR/../text/utf8_guard.php"

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

normalize_file() {
  local source="$1"
  local target="$2"
  local field="$3"
  php "$UTF8_GUARD" normalize-file "$source" "$target" "$field"
}

validate_published_text() {
  local value_file="$1"
  local field="$2"
  php "$UTF8_GUARD" validate-file "$value_file" "$field"
}

repository_name() {
  if [[ -z "$REPOSITORY" ]]; then
    REPOSITORY="$(gh repo view --json nameWithOwner --jq .nameWithOwner)"
  fi
  echo "$REPOSITORY"
}

json_payload() {
  local body_file="$1"
  local title_file="$2"
  local target="$3"

  php -r '
    $body = file_get_contents($argv[1]);
    if ($body === false) {
        fwrite(STDERR, "Nao foi possivel ler o corpo normalizado.\n");
        exit(1);
    }
    $payload = ["body" => $body];
    if ($argv[2] !== "") {
        $title = file_get_contents($argv[2]);
        if ($title === false) {
            fwrite(STDERR, "Nao foi possivel ler o titulo normalizado.\n");
            exit(1);
        }
        $payload["title"] = $title;
    }
    $json = json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    if (file_put_contents($argv[3], $json) === false) {
        fwrite(STDERR, "Nao foi possivel gravar o payload JSON.\n");
        exit(1);
    }
  ' "$body_file" "$title_file" "$target"
}

prepare_body() {
  require_file "$BODY_FILE"
  local normalized
  normalized="$(mktemp "${TMPDIR:-/tmp}/contex-pr-body.XXXXXX")"
  normalize_file "$BODY_FILE" "$normalized" "corpo do PR"
  echo "$normalized"
}

prepare_title() {
  local source normalized
  source="$(mktemp "${TMPDIR:-/tmp}/contex-pr-title-source.XXXXXX")"
  normalized="$(mktemp "${TMPDIR:-/tmp}/contex-pr-title.XXXXXX")"
  printf '%s' "$TITLE" > "$source"
  normalize_file "$source" "$normalized" "titulo do PR"
  rm -f "$source"
  echo "$normalized"
}

case "$COMMAND" in
  create)
    [[ -n "$TITLE" ]] || { echo "--title e obrigatorio." >&2; exit 1; }
    [[ -n "$HEAD" ]] || HEAD="$(git branch --show-current)"
    body_file="$(prepare_body)"
    title_file="$(prepare_title)"
    trap 'rm -f "$body_file" "$title_file"' EXIT
    pr_url_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-url.XXXXXX")"
    trap 'rm -f "$body_file" "$title_file" "$pr_url_file"' EXIT
    gh pr create --base "$BASE" --head "$HEAD" --title "$(<"$title_file")" --body-file "$body_file" > "$pr_url_file"
    pr_url="$(<"$pr_url_file")"
    pr_number="${pr_url##*/}"
    if [[ ! "$pr_number" =~ ^[0-9]+$ ]]; then
      echo "A API nao retornou uma URL de PR valida para verificacao." >&2
      exit 1
    fi
    published_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-body-published.XXXXXX")"
    trap 'rm -f "$body_file" "$title_file" "$pr_url_file" "$published_file"' EXIT
    gh pr view "$pr_number" --json body --jq .body > "$published_file"
    validate_published_text "$published_file" "corpo do PR publicado"
    cmp -s "$body_file" "$published_file" || {
      echo "O corpo devolvido pelo GitHub diverge do corpo sanitizado enviado." >&2
      exit 1
    }
    cat "$pr_url_file"
    ;;
  edit)
    [[ -n "$PR_NUMBER" ]] || { echo "--pr e obrigatorio." >&2; exit 1; }
    body_file="$(prepare_body)"
    title_file=""
    if [[ -n "$TITLE" ]]; then
      title_file="$(prepare_title)"
    fi
    payload_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-payload.XXXXXX")"
    trap 'rm -f "$body_file" "$title_file" "$payload_file"' EXIT
    json_payload "$body_file" "$title_file" "$payload_file"
    response_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-edit-response.XXXXXX")"
    trap 'rm -f "$body_file" "$title_file" "$payload_file" "$response_file"' EXIT
    gh api "repos/$(repository_name)/pulls/${PR_NUMBER}" --method PATCH --input "$payload_file" > "$response_file"
    php -r '$json=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); file_put_contents($argv[2], $json["body"] ?? ""); echo $json["html_url"] ?? "";' "$response_file" "$body_file"
    validate_published_text "$body_file" "corpo do PR publicado"
    jq -r '.html_url' "$response_file"
    ;;
  comment)
    [[ -n "$PR_NUMBER" ]] || { echo "--pr e obrigatorio." >&2; exit 1; }
    body_file="$(prepare_body)"
    payload_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-comment-payload.XXXXXX")"
    published_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-comment-published.XXXXXX")"
    trap 'rm -f "$body_file" "$payload_file" "$published_file"' EXIT
    json_payload "$body_file" "" "$payload_file"
    gh api "repos/$(repository_name)/issues/${PR_NUMBER}/comments" \
      --method POST \
      --input "$payload_file" \
      --jq '.body' > "$published_file"
    php "$UTF8_GUARD" validate-file "$published_file" "comentario publicado no PR"
    echo "Comentario publicado e validado em UTF-8, sem mojibake."
    ;;
  verify)
    [[ -n "$PR_NUMBER" ]] || { echo "--pr e obrigatorio." >&2; exit 1; }
    title_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-title-published.XXXXXX")"
    body_file="$(mktemp "${TMPDIR:-/tmp}/contex-pr-body-published.XXXXXX")"
    trap 'rm -f "$title_file" "$body_file"' EXIT
    gh pr view "$PR_NUMBER" --json title --jq .title > "$title_file"
    gh pr view "$PR_NUMBER" --json body --jq .body > "$body_file"
    php "$UTF8_GUARD" validate-file "$title_file" "titulo publicado do PR"
    php "$UTF8_GUARD" validate-file "$body_file" "corpo publicado do PR"
    echo "Titulo e corpo publicados passaram pela validacao UTF-8 e antimojibake."
    ;;
  *)
    echo "Comando desconhecido: $COMMAND" >&2
    exit 1
    ;;
esac
