# CLAUDE.md

## Full Project Context
Read `AGENTS.md` for complete project overview, architecture, data model, hooks, modules, implementation status, known bugs, and current gaps.

## Quick Reference

### What is this?
See `AGENTS.md` for the project overview and scope.

### Commands
- `composer lint` — Check code style.
- `composer lint:fix` — Auto-fix code style.
- `composer test:unit` — Run unit tests.
- `composer test:integration` — Run integration tests.
- `composer test:all` — Run all tests.

### Code Style
See `AGENTS.md` for the complete code style rules.

### Key Priorities
See `AGENTS.md` for project priorities.

### Critical Bugs to Be Aware Of
- `uninstall.php` references undefined variables and will crash.
- Autocomplete REST endpoint is open to anonymous users (permission/nonce checks commented out).
- See `AGENTS.md` "Known Bugs" section for full details.

### Collaboration
This file is the source of truth for collaboration and workflow expectations.
- When in doubt, ask before writing code and propose alternatives.
- Always suggest the next step to quickly achieve the goal.
- Suggest refactorings when appropriate.
- When modifying the codebase, also review and update AGENTS.md, BUGS.txt, REFACTORS.txt, and CLAUDE.md as needed.
- When given a global modification rule, apply changes in batch without requesting per-file confirmation and provide the final list of edited files.
- Be concise but precise.
