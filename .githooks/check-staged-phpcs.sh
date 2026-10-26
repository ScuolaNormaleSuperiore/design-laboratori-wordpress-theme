#!/usr/bin/env bash
#
# Runs PHPCS (phpcs.xml.dist) on the staged PHP, CSS and SCSS files and blocks the commit when
# any of them has an error. Warnings are not reported: they do not block. It is called by
# .githooks/pre-commit.
#
# The staged content (the index) is checked, not the working tree, so a partially staged file is
# judged for what is actually being committed. Files excluded by phpcs.xml.dist (tests, vendor,
# generated files, ...) are skipped by PHPCS itself.

set -euo pipefail

repo_root="$(git rev-parse --show-toplevel)"

staged="$(git diff --cached --name-only --diff-filter=ACMR | grep -E '\.(php|css|scss)$' || true)"

if [ -z "$staged" ]; then
	exit 0
fi

if [ ! -f "$repo_root/vendor/bin/phpcs" ]; then
	echo "pre-commit: PHPCS is not installed. Run 'composer install' and commit again." >&2
	exit 1
fi

echo "pre-commit: running PHPCS (errors only) on the staged files..."

cd "$repo_root"
failed=0

while IFS= read -r file; do
	if ! git show ":$file" | php vendor/bin/phpcs --standard=phpcs.xml.dist --warning-severity=0 --stdin-path="$file" --report=full -q -; then
		failed=1
	fi
done <<< "$staged"

if [ "$failed" -ne 0 ]; then
	echo "pre-commit: PHPCS found errors in the staged files. Fix them and stage the files again." >&2
	exit 1
fi
