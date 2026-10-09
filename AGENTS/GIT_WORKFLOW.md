# GIT_WORKFLOW.md

Read this file during bootstrap. Apply these rules only when the task includes
branch, commit, or pull-request work.


## Branches

- Base branch: `main`.
- Never commit directly to `main`.
- Branch name format: `<prefix>/<camelCaseName>`.
- Prefixes: `features/`, `bugfix/`, `refactor/`, `docs/`.
- Examples:
  - `features/addSpinoff`
  - `bugfix/fixContactForm`
  - `refactor/centralizeRendering`
  - `docs/updateArchitecture`


## Commits

- Commit messages in English.
- Prefer prefixes: `Bug-fix:`, `Refactor:`, `Feature:`, `Docs:`.
- First line under 72 characters.
- Avoid generic messages like `fix` or `update`.

Examples:
- `Feature: Add spinoff content type and archive page`
- `Bug-fix: Fix XSS vulnerability in contact form submission`
- `Refactor: Centralize event date rendering logic`
- `Docs: Update ARCHITECTURE.md after menu refactor`


## AI attribution in commits

- Do NOT include `Co-Authored-By:` trailers that mention AI tools (Claude, ChatGPT, or any other AI assistant) in commit messages.
- Show a message if the commit is on the main branch and ask for confirmation.
- Run the PHP lint command documented in `README.md` when possible.
- Do not commit secrets, API keys, or `.env` files.
- Before every commit, scan staged files for sensitive data (for example: API keys, tokens, passwords, private keys, auth headers, cookies, personal data). If potential sensitive data is found, stop the commit flow, report only its type and file/path (never its value), and ask the user for explicit confirmation before proceeding.

## Pre-commit checks

- Run the PHP lint command documented in `README.md` when possible.
- Do not commit secrets, API keys, or `.env` files.
- Do not include unrelated changes in the same commit.
- Automatic secret scan is enforced by `.githooks/pre-commit`.
- Install once per clone: `git config core.hooksPath .githooks`.
- Manual fallback: no standalone helper script is currently tracked in the repository; rely on `.githooks/pre-commit`.

## Pull Requests

- Open PR from feature branch to `main`.
- Write clear title and summary.
- Reference related issues from `DEV/CODE_REVIEW/ISSUES_TODO.md` when applicable.
