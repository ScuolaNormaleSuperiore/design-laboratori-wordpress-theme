# AI_RULES_CHATGPT.md

ChatGPT/Codex-specific entry point.


## Session bootstrap
Run this section only once at session start, unless the user explicitly asks to reload AGENTS context.

1. Read `AGENTS/AGENTS_README.md`.
2. Read `AGENTS/PROJECT.md`.
3. Read `AGENTS/ARCHITECTURE.md`.
4. Read `AGENTS/AI_BEHAVIOR.md`.
5. Read `AGENTS/CODING_STANDARDS.md`.
6. Report exactly: `Bootstrap completed` + the full list of files read.


## Reload trigger
Re-run the full "Session bootstrap" only when the user explicitly asks, using phrases like:
- "reload agents"
- "re-read AGENTS"
- "refresh AGENTS context"


## Codex Notes
- Break large tasks into clear steps and report milestones after each.
- Use local file analysis and shell checks to validate changes before reporting done.
- Preserve decisions and context from earlier turns - do not re-derive what was already agreed.
- When modifying files, show minimal targeted diffs rather than full rewrites when possible.
- Execution environment policy:
  - Try commands in WSL first.
  - If required tools are missing (`php`, `node`), switch quickly to approved Windows fallback (`powershell.exe`) and explicitly report that fallback was used.
- Lint policy:
  - Primary command: `composer run lint:php`.
  - If environment blocks it, use Windows PHP fallback with `vendor/bin/phpcs` and always report lint outcome.
- Pre-edit safety checks:
  - Before significant edits, run `git status --short` and `rg` on relevant call-sites.
  - If unexpected changes are found, stop and ask for confirmation.
- Edit strategy:
  - Prefer `apply_patch` for small/medium edits.
  - For cross-file refactors: replace call-sites first, then remove wrappers/dead code.
- Verification minimum:
  - After each change, run syntax/lint checks and search for orphan references with `rg`.
  - Final report must include what could not be verified (for example missing tools/environment limits).
- Issue tracking discipline:
  - When an issue is resolved, update both `AGENTS/ISSUES_TODO.md` and `AGENTS/ISSUES_RESOLVED.md` in the same task.
- Response format preference:
  - Keep outputs brief, numbered, and findings-first.
  - For URL audits, report only high-impact issues unless explicitly asked for exhaustive findings.
- When creating commits, follow `AGENTS/GIT_WORKFLOW.md`.
