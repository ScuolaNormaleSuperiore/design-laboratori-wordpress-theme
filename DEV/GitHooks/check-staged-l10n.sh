#!/usr/bin/env bash
# Ensure staged languages/*.l10n.php files contain the ABSPATH guard.
# Loco Translate regenerates these files and strips the guard on every export.
# If the guard is missing it is injected automatically and the file is re-staged.
set -euo pipefail

echo "[ALM pre-commit] Running l10n guard check..." >&2

if ! command -v git >/dev/null 2>&1; then
	exit 0
fi

# Collect staged .l10n.php files under languages/.
staged_l10n="$(git diff --cached --name-only --diff-filter=ACMR | grep '^languages/.*\.l10n\.php$' || true)"

if [ -z "$staged_l10n" ]; then
	exit 0
fi

repo_root="$(git rev-parse --show-toplevel)"
fixed=0

while IFS= read -r file; do
	abs="$repo_root/$file"
	if ! grep -q "defined.*ABSPATH" "$abs" 2>/dev/null; then
		echo "[ALM pre-commit] ABSPATH guard missing — auto-fixing: $file" >&2
		# Insert the guard on the line immediately after <?php.
		awk 'NR==1 && /^<\?php$/ { print; print "defined( '"'"'ABSPATH'"'"' ) || exit;"; next } { print }' "$abs" > "$abs.tmp" && mv "$abs.tmp" "$abs"
		git add "$abs"
		fixed=1
	fi
done <<< "$staged_l10n"

if [ "$fixed" -eq 1 ]; then
	echo "[ALM pre-commit] ABSPATH guard added and files re-staged." >&2
fi

exit 0
