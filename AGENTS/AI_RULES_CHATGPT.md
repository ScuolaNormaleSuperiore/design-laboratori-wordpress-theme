# AI_RULES_CHATGPT.md

ChatGPT/Codex-specific entry point.

## Session Start
1. Read this file first.
2. Follow workflow rules in `AGENTS/AI_BEHAVIOR.md`.
3. Read `AGENTS/PROJECT.md`, then `AGENTS/ARCHITECTURE.md`, then `AGENTS/CODING_STANDARDS.md`.
4. Check `AGENTS/ISSUES_TODO.md` when working on bugs or improvements.
5. Use `AGENTS/AGENTS_README.md` only as file-purpose index.

## Codex Notes
- Break large tasks into clear steps and report milestones after each.
- Use local file analysis and shell checks to validate changes before reporting done.
- Preserve decisions and context from earlier turns — do not re-derive what was already agreed.
- When modifying files, show minimal targeted diffs rather than full rewrites when possible.
- Run `composer lint` before finalizing code changes when dependencies are available.
- When creating commits, follow `AGENTS/GIT_WORKFLOW.md`.
