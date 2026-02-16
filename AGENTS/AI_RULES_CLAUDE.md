# AI_RULES_CLAUDE.md

Claude Code-specific entry point.

## Session Start
1. `CLAUDE.md` loads this file automatically — no manual read needed.
2. Follow workflow rules in `AGENTS/AI_BEHAVIOR.md`.
3. Read `AGENTS/PROJECT.md`, then `AGENTS/ARCHITECTURE.md`, then `AGENTS/CODING_STANDARDS.md`.
4. Check `AGENTS/ISSUES_TODO.md` when working on bugs or improvements.
5. Use `AGENTS/AGENTS_README.md` only as file-purpose index.

## Claude Code Notes
- Use plan mode for non-trivial tasks (new features, multi-file refactors, architectural decisions).
- Prefer atomic `Edit` operations over full file rewrites — smaller diffs are easier to review.
- Use `Glob`/`Grep` for directed searches; use the Explore agent for broader codebase discovery.
- Run `composer lint` before finalizing code changes when dependencies are available.
- Keep naming and structure aligned with WordPress conventions.
- When creating commits, follow `AGENTS/GIT_WORKFLOW.md`.
