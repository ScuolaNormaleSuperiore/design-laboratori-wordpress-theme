# AI_RULES_CLAUDE.md

Claude Code-specific entry point.

## Session bootstrap
Run this section only once at session start, unless the user explicitly asks to reload AGENTS context.

1. Read `AGENTS/AGENTS_README.md`.
2. Read `AGENTS/AI_BEHAVIOR.md`.
3. Read `AGENTS/PROJECT.md`.
4. Read `AGENTS/ARCHITECTURE.md`.
5. Read `AGENTS/CODING_STANDARDS.md`.
6. Read `AGENTS/ISSUES_TODO.md`.
7. Report exactly: `Bootstrap completed` + the full list of files read.

## Reload trigger
Re-run the full "Session bootstrap" only when the user explicitly asks, using phrases like:
- "reload agents"
- "re-read AGENTS"
- "refresh AGENTS context"

## Claude Code Notes
- Use plan mode for non-trivial tasks (new features, multi-file refactors, architectural decisions).
- Prefer atomic `Edit` operations over full file rewrites — smaller diffs are easier to review.
- Use `Glob`/`Grep` for directed searches; use the Explore agent for broader codebase discovery.
- Run `composer run lint:php` before finalizing code changes when dependencies are available.
- Keep naming and structure aligned with WordPress conventions.
- When creating commits, follow `AGENTS/GIT_WORKFLOW.md`.
