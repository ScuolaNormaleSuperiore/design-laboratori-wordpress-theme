# CLAUDE.md

## Full Project Context
Read `AGENTS.md` for complete project overview, architecture, data model, hooks, modules, implementation status, known bugs, and current gaps.

## Quick Reference

### What is this?
Design laboratori e centri di ricerca is a WordPress theme developed by the Scuola Normale Superiore with the aim of creating a website model for research structures (centers and laboratories).
Once installed, the environment provides a set of predefined contents that make the setup of the website simple and fast.
This website allows the publication of all information related to a research structure: affiliated staff organized by structure, publications, research projects, research activities (aggregation of projects), as well as news and events related to the activities.
The project aims to highlight and enhance the activities and research staff of the structures.

### Commands
- `composer lint` — Check code style.
- `composer lint:fix` — Auto-fix code style.
- `composer test:unit` — Run unit tests.
- `composer test:integration` — Run integration tests.
- `composer test:all` — Run all tests.

### Code Style
- Act as an expert PHP and WordPress developer.
- Use **tabs** for indentation, not spaces.
- Comments in **English**, at the beginning of each file and before every function, ending with a period.
- WordPress naming conventions for classes, files, variables, constants, and functions.
- Align assignments as required by WP coding standards.
- Text domain: `design_laboratori_italia`.
- Package: `@package Design_Laboratori_Italia`.

### Key Priorities
- No bugs, no vulnerabilities (maximum security).
- Responsive pages (mobile-first).
- Accessibility (very important).
- WordPress best practices compliance.
- Simple, readable, modular code.

### Critical Bugs to Be Aware Of
- `uninstall.php` references undefined variables and will crash.
- Autocomplete REST endpoint is open to anonymous users (permission/nonce checks commented out).
- See `AGENTS.md` "Known Bugs" section for full details.

### Collaboration
- When in doubt, ask before writing code and propose alternatives.
- Always suggest the next step to quickly achieve the goal.
- Suggest refactorings when appropriate.
- When modifying the codebase, also review and update AGENTS.md, BUGS.txt, REFACTORS.txt, and CLAUDE.md as needed.
- When given a global modification rule, apply changes in batch without requesting per-file confirmation and provide the final list of edited files.
- Be concise but precise.
