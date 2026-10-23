#!/usr/bin/env bash
# Vérifie la syntaxe de tous les fichiers PHP avec le binaire PHP courant.
# Avec VENDOR=1, vérifie aussi les dépendances (vendor/).
# Usage : scripts/php-lint.sh   (ex. PHP=php8.2 scripts/php-lint.sh)
set -u
cd "$(dirname "$0")/.."
PHP_BIN="${PHP:-php}"
echo "Version : $("$PHP_BIN" -r 'echo PHP_VERSION;')"
status=0
excl=(-not -path './.git/*')
[ "${VENDOR:-0}" = "1" ] || excl+=(-not -path './vendor/*')
while IFS= read -r -d '' f; do
  "$PHP_BIN" -l "$f" >/dev/null || status=1
done < <(find . -name '*.php' "${excl[@]}" -print0)
[ "$status" -eq 0 ] && echo "OK" || echo "Erreurs de syntaxe détectées"
exit "$status"
