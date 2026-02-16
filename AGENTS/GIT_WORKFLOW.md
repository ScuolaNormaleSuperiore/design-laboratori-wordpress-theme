# GIT_WORKFLOW.md

Use these rules only when the task includes branch/commit/PR work.

## Branches

- Base branch: `main`.
- Never commit directly to `main`.
- Branch name format: `<prefix>/<camelCaseName>`.
- Prefixes: `feature/`, `bugfix/`, `refactor/`, `docs/`.
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

## Pre-commit checks

- Run `npm run lint:php` when possible.
- Do not commit secrets, API keys, or `.env` files.
- Do not include unrelated changes in the same commit.

## Pull Requests

- Open PR from feature branch to `main`.
- Write clear title and summary.
- Reference related issues from `ISSUES_TODO.md` when applicable.
