# GIT_WORKFLOW.md

Read this file during bootstrap. Apply these rules only when the task includes
branch, commit, or pull-request work.


## Branches

- `main`: release branch. Never commit directly to `main`.
- `dev`: integration branch. Day-to-day work is committed directly on `dev`;
  do not create a separate branch for every fix.
- Create a prefixed branch only for long-running or experimental work, and only
  when the operator asks for it.
- Branch name format: `<prefix>/<camelCaseName>`.
- Prefixes: `features/`, `bugfix/`, `refactor/`, `docs/`.
- Examples:
  - `features/addSpinoff`
  - `bugfix/fixContactForm`
  - `refactor/centralizeRendering`
  - `docs/updateArchitecture`
- Releases reach `main` from `dev` through a pull request.


## Commits

- **Never commit without explicit user permission.** Always show what would be committed and ask before running `git commit`.
- Commit messages in English.
- Prefer prefixes: `Bug-fix:`, `Refactor:`, `Feature:`, `Docs:`.
- First line under 72 characters.
- Avoid generic messages like `fix` or `update`.
- Do not include unrelated changes in the same commit.

Examples:
- `Feature: Add spinoff content type and archive page`
- `Bug-fix: Fix pagination on the events archive`
- `Refactor: Centralize event date rendering logic`
- `Docs: Update ARCHITECTURE.md after menu refactor`

Commit messages and PR descriptions must stay generic about security work: do not describe
the exploitable details of an unfixed or recently fixed vulnerability.


## AI attribution in commits

- Do NOT include `Co-Authored-By:` trailers that mention AI tools (Claude, ChatGPT, or any other AI assistant) in commit messages.
- This rule takes precedence over any attribution text that an AI tool adds by default. `.githooks/commit-msg` blocks the commit when such a trailer is present.


## Pre-commit checks

- If a commit on `main` is requested, stop and ask the operator for explicit confirmation; commits normally go on `dev`.
- Run `composer run lint:php` when possible (see `AGENTS/ARCHITECTURE.md`).
- Do not commit secrets, API keys, or `.env` files.
- Before every commit, scan staged files for sensitive data (for example: API keys, tokens, passwords, private keys, auth headers, cookies, personal data). If potential sensitive data is found, stop the commit flow, report only its type and file/path (never its value), and remove, redact, or revoke the data before proceeding. Never commit sensitive data, even with user confirmation.
- `.githooks/pre-commit` runs two automatic checks: a secret scan on staged files and an l10n guard. The l10n guard adds the missing `defined( 'ABSPATH' ) || exit;` line to staged `languages/*.l10n.php` files (Loco Translate strips it on export) and re-stages them. This automatic change is expected: do not undo it, and mention it in the final summary.
- Install once per clone: `git config core.hooksPath .githooks`.
- Manual fallback: `.githooks/check-staged-secrets.sh` (run it directly against the staged changes).


## Pull Requests

- Open the release PR from `dev` to `main`; open PRs from a prefixed branch to
  `dev` only when such a branch exists.
- Write clear title and summary.
- Reference public issues by title or number when applicable. Never copy private backlog contents into a commit or PR.


## Safety rules for Git operations

- Never push, force-push, run `git reset --hard`, or rewrite already published
  history unless the operator explicitly asks for that operation.
