#!/usr/bin/env bash
#
# Runs the whole-theme PHPStan gate at the level set in phpstan.neon (the one source of the level,
# also used by the CI through `composer run lint:phpstan`). It is called by .githooks/pre-commit
# and blocks the commit when the analysis reports any error.

set -euo pipefail

repo_root="$(git rev-parse --show-toplevel)"

if [ ! -f "$repo_root/vendor/bin/phpstan" ]; then
	echo "pre-commit: PHPStan is not installed. Run 'composer install' and commit again." >&2
	exit 1
fi

echo "pre-commit: running PHPStan on the whole theme (level from phpstan.neon)..."
(cd "$repo_root" && php vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress --memory-limit=2G)
