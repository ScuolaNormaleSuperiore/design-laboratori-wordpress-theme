#!/usr/bin/env bash
set -euo pipefail

echo "[ALM pre-commit] Running secret scan..." >&2

if ! command -v git >/dev/null 2>&1; then
	exit 0
fi

if git rev-parse --verify HEAD >/dev/null 2>&1; then
	diff_output="$(git diff --cached --unified=0 --no-color --diff-filter=ACMRTUXB)"
else
	# Initial commit: compare against empty tree.
	empty_tree="$(git hash-object -t tree /dev/null)"
	diff_output="$(git diff --cached --unified=0 --no-color --diff-filter=ACMRTUXB "$empty_tree")"
fi

if [ -z "$diff_output" ]; then
	exit 0
fi

added_lines="$(printf '%s\n' "$diff_output" | awk '
	/^\+\+\+ / { next }
	/^\+/ {
		sub(/^\+/, "", $0)
		print
	}
')"

if [ -z "$added_lines" ]; then
	exit 0
fi

# High-signal secret patterns only (to reduce false positives).
patterns=(
	'-----BEGIN (RSA |EC |DSA |OPENSSH |PGP )?PRIVATE KEY-----'
	'(AKIA|ASIA)[A-Z0-9]{16}'
	'gh[pousr]_[A-Za-z0-9]{36,255}'
	'github_pat_[A-Za-z0-9_]{20,}'
	'xox[baprs]-[A-Za-z0-9-]{10,}'
	'sk-[A-Za-z0-9]{20,}'
	'[A-Za-z][A-Za-z0-9+.-]*://[^[:space:]]+:[^[:space:]]+@[^[:space:]]+'
	'(api[_-]?key|client[_-]?secret|access[_-]?token|refresh[_-]?token|password|passwd|pwd|secret)[[:space:]]*[:=][[:space:]]*["\x27][^"\x27]{8,}["\x27]'
)

found=0
for pattern in "${patterns[@]}"; do
	if printf '%s\n' "$added_lines" | grep -E -i -n "$pattern" >/tmp/alm_secret_scan_matches.$$ 2>/dev/null; then
		if [ "$found" -eq 0 ]; then
			echo "[ALM pre-commit] Potential secret detected in staged changes:" >&2
			found=1
		fi
		cat /tmp/alm_secret_scan_matches.$$ >&2
	fi
	rm -f /tmp/alm_secret_scan_matches.$$ >/dev/null 2>&1 || true

done

if [ "$found" -eq 1 ]; then
	echo >&2
	echo "Commit blocked. Remove secrets or move safe examples outside staged changes." >&2
	echo "If this is a false positive, adjust the pattern list in DEV/SETUP/check-staged-secrets.sh." >&2
	exit 1
fi

exit 0
