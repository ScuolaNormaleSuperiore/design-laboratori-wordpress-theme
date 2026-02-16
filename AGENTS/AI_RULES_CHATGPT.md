# AI_RULES_CHATGPT.md

ChatGPT/Codex-specific entry point.

## Session Start
1. Read `AGENTS/AGENTS_README.md`.
2. Read `AGENTS/AI_BEHAVIOR.md`.
3. Read `AGENTS/PROJECT.md`.
4. Read `AGENTS/ARCHITECTURE.md`.
5. Read `AGENTS/CODING_STANDARDS.md`.
6. Read `AGENTS/ISSUES_TODO.md`.
7. Report exactly: `Bootstrap completed` + the full list of files read.

## Codex Notes
- Break large tasks into clear steps and report milestones after each.
- Use local file analysis and shell checks to validate changes before reporting done.
- Preserve decisions and context from earlier turns - do not re-derive what was already agreed.
- When modifying files, show minimal targeted diffs rather than full rewrites when possible.
- Run `npm run lint:php` before finalizing code changes when dependencies are available.
- When creating commits, follow `AGENTS/GIT_WORKFLOW.md`.
