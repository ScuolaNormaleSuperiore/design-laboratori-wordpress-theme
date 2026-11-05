#!/usr/bin/env bash
#
# Runs PHPCS (phpcs.xml.dist, errors only) on the PHP, CSS and SCSS files changed between a
# base commit and HEAD. It is called by .github/workflows/ci.yml and can be run locally:
#
#   bash .github/scripts/phpcs-changed.sh [<base-commit>]
#
# Like the pre-commit hook (.githooks/check-staged-phpcs.sh) it reports errors only, and an
# error already present in a touched file fails the run. Unlike the hook, which reads the git
# index, this script reads the files of the checked-out commit.
#
# Base commit:
# - a push passes the commit before the push (github.event.before), a pull request passes the
#   tip of the target branch;
# - when it is missing, all zeros (first push of a branch) or no longer reachable (force-push),
#   the files of the last commit are checked instead; a repository with a single commit checks
#   all the files of that commit.

set -euo pipefail

repo_root="$(git rev-parse --show-toplevel)"
cd "$repo_root"

base="${1:-}"
zeros='0000000000000000000000000000000000000000'
range=''

if [ -n "$base" ] && [ "$base" != "$zeros" ] && git cat-file -e "${base}^{commit}" 2>/dev/null \
	&& git merge-base "$base" HEAD >/dev/null 2>&1; then
	range="${base}...HEAD"
elif git rev-parse --verify --quiet 'HEAD~1^{commit}' >/dev/null; then
	echo "phpcs-changed: base commit missing or not reachable; checking the files of the last commit."
	range='HEAD~1...HEAD'
else
	echo "phpcs-changed: single commit; checking all the files of that commit."
	range=''
fi

if [ -n "$range" ]; then
	changed="$(git diff --name-only --diff-filter=ACMR "$range" || true)"
else
	changed="$(git diff-tree --no-commit-id --name-only -r --diff-filter=ACMR --root HEAD || true)"
fi

files="$(printf '%s\n' "$changed" | grep -E '\.(php|css|scss)$' || true)"

if [ -z "$files" ]; then
	echo "phpcs-changed: no PHP, CSS or SCSS files changed."
	exit 0
fi

if [ ! -f vendor/bin/phpcs ]; then
	echo "phpcs-changed: PHPCS is not installed. Run 'composer install'." >&2
	exit 1
fi

mapfile -t file_list <<< "$files"

echo "phpcs-changed: running PHPCS (errors only) on:"
printf '  %s\n' "${file_list[@]}"

# The ruleset excludes tests, vendor, generated and third-party files by itself.
php vendor/bin/phpcs --standard=phpcs.xml.dist --warning-severity=0 --report=full -q "${file_list[@]}"
