# CLAUDE.md

Claude Code-specific entry point.

## Bootstrap
Read this file first. Then read `AGENTS/AI_BOOTSTRAP.md` and follow its
`Session bootstrap` sequence exactly.

## Claude Code Notes
- Use plan mode for non-trivial tasks (new features, multi-file refactors, architectural decisions).
- Prefer atomic `Edit` operations over full file rewrites — smaller diffs are easier to review.
- Use `Glob`/`Grep` for directed searches; use the Explore agent for broader codebase discovery.

## Execution environment
- On Windows use PowerShell or Git Bash, each with its own syntax; do not assume WSL.
- If `php`, `composer` or `node` are not on the `PATH`, report it instead of installing them.
