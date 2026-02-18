#!/usr/bin/env bash
set -euo pipefail

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DOMAIN_DIR="$(cd "$REPO_DIR/.." && pwd)"
PUBLIC_SRC="$REPO_DIR/public"
PUBLIC_HTML_DIR="$DOMAIN_DIR/public_html"
TEMPLATE_DIR="$REPO_DIR/deploy/hostinger/public_html"

if [[ ! -d "$PUBLIC_SRC" ]]; then
  echo "No se encontró el directorio público del proyecto en: $PUBLIC_SRC" >&2
  exit 1
fi

if [[ ! -d "$PUBLIC_HTML_DIR" ]]; then
  echo "No se encontró public_html en: $PUBLIC_HTML_DIR" >&2
  exit 1
fi

if command -v rsync >/dev/null 2>&1; then
  rsync -a --delete \
    --exclude='index.php' \
    --exclude='.htaccess' \
    "$PUBLIC_SRC/" "$PUBLIC_HTML_DIR/"
else
  echo "rsync no está disponible; se hará una copia sin limpieza automática." >&2
  (cd "$PUBLIC_SRC" && tar cf - --exclude='index.php' --exclude='.htaccess' .) | (cd "$PUBLIC_HTML_DIR" && tar xpf -)
fi

cp "$TEMPLATE_DIR/index.php" "$PUBLIC_HTML_DIR/index.php"
cp "$TEMPLATE_DIR/.htaccess" "$PUBLIC_HTML_DIR/.htaccess"

echo "Deploy completado: public_html quedó sincronizado con my-portfolio/public"
