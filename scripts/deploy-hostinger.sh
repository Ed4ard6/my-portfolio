#!/usr/bin/env bash
set -euo pipefail

show_help() {
  cat <<'HELP'
Uso:
  bash scripts/deploy-hostinger.sh [--public-html-dir RUTA] [--repo-dir RUTA]

Qué hace:
  1) Sincroniza my-portfolio/public -> public_html (css, js, img, etc.).
  2) Reemplaza public_html/index.php y public_html/.htaccess con plantillas versionadas.

Opciones:
  --public-html-dir RUTA   Ruta absoluta o relativa a public_html.
                           Si no se define, usa ../public_html desde el repo.
  --repo-dir RUTA          Ruta del repo (si no se define, auto-detección).
  -h, --help               Muestra esta ayuda.

Ejemplos:
  # Producción (estructura esperada: .../my-portfolio y .../public_html)
  bash scripts/deploy-hostinger.sh

  # Local Laragon
  bash scripts/deploy-hostinger.sh --public-html-dir "C:/laragon/www/public_html"
HELP
}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEFAULT_REPO_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

REPO_DIR="${DEFAULT_REPO_DIR}"
PUBLIC_HTML_DIR=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --public-html-dir)
      PUBLIC_HTML_DIR="$2"
      shift 2
      ;;
    --repo-dir)
      REPO_DIR="$2"
      shift 2
      ;;
    -h|--help)
      show_help
      exit 0
      ;;
    *)
      echo "Opción no reconocida: $1" >&2
      show_help >&2
      exit 1
      ;;
  esac
done

REPO_DIR="$(cd "$REPO_DIR" && pwd)"
PUBLIC_SRC="$REPO_DIR/public"
TEMPLATE_DIR="$REPO_DIR/deploy/hostinger/public_html"

if [[ -z "$PUBLIC_HTML_DIR" ]]; then
  DOMAIN_DIR="$(cd "$REPO_DIR/.." && pwd)"
  PUBLIC_HTML_DIR="$DOMAIN_DIR/public_html"
fi

if [[ ! -d "$PUBLIC_SRC" ]]; then
  echo "No se encontró el directorio público del proyecto en: $PUBLIC_SRC" >&2
  exit 1
fi

if [[ ! -d "$PUBLIC_HTML_DIR" ]]; then
  echo "No se encontró public_html en: $PUBLIC_HTML_DIR" >&2
  echo "Tip: crea la carpeta o pasa --public-html-dir con la ruta correcta." >&2
  exit 1
fi

if [[ ! -f "$TEMPLATE_DIR/index.php" || ! -f "$TEMPLATE_DIR/.htaccess" ]]; then
  echo "Faltan plantillas en: $TEMPLATE_DIR" >&2
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

echo "✅ Sync completado"
echo "   source: $PUBLIC_SRC"
echo "   target: $PUBLIC_HTML_DIR"
