#!/usr/bin/env bash
#
# Runs the whole-theme PHPStan level-4 gate. It is called by .githooks/pre-commit and
# blocks the commit when the analysis reports any error.

set -euo pipefail

repo_root="$(git rev-parse --show-toplevel)"

if [ ! -f "$repo_root/vendor/bin/phpstan" ]; then
	echo "pre-commit: PHPStan is not installed. Run 'composer install' and commit again." >&2
	exit 1
fi

echo "pre-commit: running PHPStan level 4 on the whole theme..."
(cd "$repo_root" && php vendor/bin/phpstan analyse --configuration=phpstan.neon --level=4 --no-progress --memory-limit=2G)
