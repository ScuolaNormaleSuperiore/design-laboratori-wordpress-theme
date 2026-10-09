# AI_BOOTSTRAP.md

## Purpose
This directory provides AI-oriented directives and project context to support development, maintenance, bug fixing, and security hardening.


## Session bootstrap
Run this section only once at session start, unless the user explicitly asks to reload AGENTS context. First read the root entry point for the active agent: `AGENTS.md` for ChatGPT Codex, or `CLAUDE.md` for Claude Code. Then read only these versioned instruction files in this exact order:

1. `AGENTS/AI_BOOTSTRAP.md` (this file).
2. `AGENTS/PROJECT.md`.
3. `AGENTS/ARCHITECTURE.md`.
4. `AGENTS/AI_BEHAVIOR.md`.
5. `AGENTS/CODING_STANDARDS.md`.
6. `AGENTS/GIT_WORKFLOW.md`.
7. Ensure `AGENTS/ISSUES_TODO.md` and `AGENTS/ISSUES_RESOLVED.md` exist. If missing, create local Markdown files containing only `# ISSUES TODO` and `# ISSUES RESOLVED`, respectively. These files are ignored by Git and must never be committed. Do not read either file during bootstrap. On the operator's machine they may be symlinks to a private folder outside the repository: treat them as normal files and write through the link; if a tool refuses to write through the symlink, write to the real target path it reports. If a symlink is dangling (its target is unreachable), do not recreate or replace it; report it to the operator and continue the bootstrap.
8. Do not read `AGENTS/CODE_REVIEW.md` during bootstrap; read it only when Trigger F (`/code-review`) runs.
9. Report exactly: `Bootstrap completed` + the full list of files read, including the applicable root entry point.
10. List the skills available in the current session. Explicitly state whether applicable official WordPress skills are loaded; if they are not, notify the human operator.
11. List the available trigger commands from `AGENTS/AI_BEHAVIOR.md` using the format: `Trigger X` (`/slug`) — brief description.
12. State: `Local issue files ensured: AGENTS/ISSUES_TODO.md, AGENTS/ISSUES_RESOLVED.md (created if missing; never commit them).`
13. State whether the `design-system-italia` MCP server (Bootstrap Italia components and tokens) is installed and usable in the current session, for example by calling its `ping` tool. If it is not available, say so and notify the human operator.
14. Ask the human operator which project task they want to work on.


## Common agent rules
- For WordPress work, follow the `Official WordPress Skills` section of `AGENTS/AI_BEHAVIOR.md`: use the applicable official skills, notify the human operator when one is not loaded, and ask which source to follow when a skill conflicts with an instruction in `AGENTS/`.


## File Map
- `../AGENTS.md` / `../CLAUDE.md`: agent-specific entry points and instructions (Codex/ChatGPT and Claude Code).
- `PROJECT.md`: project description, product scope, main features, roles.
- `ARCHITECTURE.md`: architecture overview: key directories and main files, data/content model, runtime components, conventions and intentional patterns, public endpoints and hooks, configuration, technology stack, canonical quality-check commands, references.
- `CODING_STANDARDS.md`: coding constraints and quality standards.
- `AI_BEHAVIOR.md`: workflow rules and issue management.
- `CODE_REVIEW.md`: public review method, evidence, coverage and project plan; read only by Trigger F.
- `CODE_REVIEW_PROGRESS.md`: ignored local state of a full code-review run; created and read only by Trigger F, never at bootstrap; never committed.
- `ISSUES_TODO.md`: ignored local backlog, ensured during bootstrap and read by issue workflows.
- `ISSUES_RESOLVED.md`: ignored local archive, ensured during bootstrap and searched only when required.
- `GIT_WORKFLOW.md`: branch, commit, and PR conventions; read during bootstrap
  and apply only when the task includes VCS work.


## Reload trigger
Re-run the full "Session bootstrap" only when the user explicitly asks, using phrases like:
- "reload agents"
- "re-read AGENTS"
- "refresh AGENTS context"
