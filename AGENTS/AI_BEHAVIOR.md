# AI_BEHAVIOR.md

## Purpose
Operational rules for AI assistants working on this codebase.

## Canonical Sources
- Reading order and process rules: this file.
- Open issues and priorities: `AGENTS/ISSUES_TODO.md`.
- Issue archive: `AGENTS/ISSUES_RESOLVED.md`.
- Coding rules and JS policy: `AGENTS/CODING_STANDARDS.md`.
- Runtime architecture map: `AGENTS/ARCHITECTURE.md`.
- Product scope: `AGENTS/PROJECT.md`.
- VCS workflow: `AGENTS/GIT_WORKFLOW.md` (apply only for git tasks).

## Execution Rules
- Be concise, precise, and action-oriented.
- Work one objective at a time, end-to-end.
- Ask clarifying questions only when ambiguity blocks implementation.
- After meaningful progress, summarize what changed and what remains.
- Keep quality gates active: security, accessibility, maintainability.
- During PHPCS remediation, never weaken rules in `phpcs.xml.dist` to silence unresolved findings. If a finding cannot be fixed safely in code, report it in the output and ask the user whether to add/update an entry in `AGENTS/ISSUES_TODO.md`.

## Learning Support
When useful, explain the theory behind choices (WordPress internals, security, architecture, standards), especially if the user shows knowledge gaps or asks for deeper understanding.
Keep explanations practical and tied to the current code.

## Trigger Commands
When the user asks: `Fai un controllo completo sul file X` or `Fai un controllo completo sulla cartella X` (or equivalent wording), interpret it as a mandatory full review workflow including:
- bug and security analysis;
- style checks;
- compliance checks against `AGENTS/CODING_STANDARDS.md`;
- HTML correctness checks on touched templates/markup;
- lint execution for the target scope and code fixes for lint-reported issues when safe.
- While running this workflow, provide live progress updates that explicitly state which check is being executed (for example: bug check, security check, style check, HTML check, lint/check-fix step).

Output format for this workflow:
- findings first, ordered by severity, with file/line references;
- then assumptions/open questions;
- then an optional short change summary.

## Excluded Directories
Always ignore these folders for review/refactoring/fixes:
- `vendor/`
- `node_modules/`

## Repository Scope Boundaries
- Modify files only inside this theme repository: `wp-content/themes/design-laboratori-wordpress-theme/`.
- Never modify files outside this repository (for example user/system files, editor extension files, or any path under `.vscode/` not owned by this repo).
- Never modify external WordPress components such as:
  - other themes under `wp-content/themes/`
  - plugins under `wp-content/plugins/`
  - WordPress core files under `wp-admin/`, `wp-includes/`, or root bootstrap files
- Treat third-party/library directories as read-only unless the user explicitly asks for a direct library patch:
  - `vendor/`
  - `node_modules/`
  - `assets/bootstrap-italia/`
- Before staging/commit, verify with `git status --short` that all changed files are inside the allowed repository scope.

## Issue Management

### When to add an issue
Create or update issues in `ISSUES_TODO.md` when you find:
- Security vulnerabilities
- Bugs
- Refactoring opportunities
- Code style violations against `CODING_STANDARDS.md`
- Performance problems
- Accessibility problems
- Documentation gaps
- New features or improvement ideas

Feature ideas not implementation-ready still go to category `Feature` with status like `Idea` or `Under Evaluation`.

### Issue template

```markdown
### [PRIORITY] Short descriptive title
- **Status:** Open
- **Date:** YYYY-MM-DD
- **Category:** Security | Bug | Refactoring | CodeStyle | Performance | Accessibility | Feature | Documentation
- **Description:** Detailed description of the problem
- **Steps to reproduce:** (if applicable)
  1. ...
  2. ...
- **Expected behavior:** (if applicable)
- **Notes:** Additional information, workarounds, references
```

### Priority levels
- `Critical`: security/data-loss/major service breakage.
- `High`: major user impact.
- `Medium`: relevant but non-blocking.
- `Low`: minor impact or polish.

### Resolution flow
When an issue is resolved:
1. Move it from `ISSUES_TODO.md` to `ISSUES_RESOLVED.md`.
2. Add `Resolution date` and `Fix summary`.
3. Add commit/PR references when available.

## Documentation Update Rules
After code changes, update related docs:
- Feature work: `PROJECT.md`, `ARCHITECTURE.md`, and issue files if tracked.
- Bug fixes: `ISSUES_RESOLVED.md`; update other docs only if behavior/rules changed.
- Refactors: `ARCHITECTURE.md` and coding docs only when conventions/runtime changed.
- Path/structure changes: update `AGENTS_README.md` and affected references.

Before closing a task, verify:
- Code follows `CODING_STANDARDS.md`.
- Relevant AGENTS docs were updated.
- Issue tracking is consistent.

## Security Review Checklist (Minimum)
For every bug-fix/feature/refactor touching runtime code, verify at least:
- Input sanitization for external data (`$_GET`, `$_POST`, REST params, options payloads, remote data).
- Context-aware output escaping in templates and admin views (`esc_html`, `esc_attr`, `esc_url`, `wp_kses*`).
- Nonce verification and capability checks for state-changing/admin actions.
- Safe query building (`$wpdb->prepare()`, validated query args for `WP_Query`/tax/meta filters).
- Dependency and integration hygiene: no secrets hardcoded, remote calls validated, unsafe transport disabled.
- Basic accessibility sanity on changed markup (valid structure, labels/aria, keyboard reachability where relevant).

If any checklist item fails and is out of scope to fix immediately, add/update an issue in `ISSUES_TODO.md`.

## Definition of Done
Before marking work complete:
- Run `npm run lint:php` when environment/dependencies are available.
- Re-check changed templates/components for escaping and structural validity.
- Update issue tracking (`ISSUES_TODO.md` / `ISSUES_RESOLVED.md`) when applicable.
- Update AGENTS docs affected by the change (`PROJECT.md`, `ARCHITECTURE.md`, `CODING_STANDARDS.md`, `AI_BEHAVIOR.md`, `AGENTS_README.md` as needed).
- Report a concise summary of what changed, what was verified, and any remaining risks.

## AGENTS Update Rules
- If behavior/process changes: update `AI_BEHAVIOR.md`.
- If architecture/runtime changes: update `ARCHITECTURE.md`.
- If coding constraints change: update `CODING_STANDARDS.md`.
- If backlog changes: update `ISSUES_TODO.md` and, when closed, move to `ISSUES_RESOLVED.md`.
- If AGENTS file list changes: update `AGENTS_README.md` file map.

## Practical Update Matrix
- New feature implemented:
  Update `PROJECT.md` (scope/capability), `ARCHITECTURE.md` (runtime/data model), and related issue status.
- Bug fixed:
  Move/update issue in `ISSUES_RESOLVED.md`; update `ARCHITECTURE.md` only if runtime behavior changed.
- Refactor without behavior changes:
  Update docs only if architecture/conventions actually changed; otherwise only issue tracking.
- Coding rule/tooling change:
  Update `CODING_STANDARDS.md` and this file if process impact exists.
- New/removed AGENTS file:
  Update `AGENTS_README.md` file map.

## Batch and Mass Updates
- For a global rule update, apply changes consistently across all affected files and report the edited file list.
- For mass updates touching many files, list impact first and request confirmation before applying.

## Git Workflow Usage
Git branch/commit/PR conventions are defined in `AGENTS/GIT_WORKFLOW.md`.
Read and apply that file only when the user asks for VCS actions.
