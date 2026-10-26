#!/usr/bin/env bash
#
# Runs the whole-theme PHPStan level-4 gate. Add this script to pre-commit only
# after phpstan.neon is set to level 4 and the analysis has no errors.

set -euo pipefail

repo_root="$(git rev-parse --show-toplevel)"

if [ ! -f "$repo_root/vendor/bin/phpstan" ]; then
	echo "pre-commit: PHPStan is not installed. Run 'composer install' and commit again." >&2
	exit 1
fi

echo "pre-commit: running PHPStan level 4 on the whole theme..."
(cd "$repo_root" && php vendor/bin/phpstan analyse --configuration=phpstan.neon --level=4 --no-progress --memory-limit=2G)
