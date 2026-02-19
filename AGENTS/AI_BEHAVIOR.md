# AI_BEHAVIOR.md


## Purpose
Operational rules for AI assistants working on this codebase.


## Execution Rules
- Be concise, precise, and action-oriented.
- Work one objective at a time, end-to-end.
- Ask clarifying questions only when ambiguity blocks implementation.
- After meaningful progress, summarize what changed and what remains.
- Keep quality gates active: security, accessibility, maintainability.
- For coding/security/style specifics, follow `AGENTS/CODING_STANDARDS.md`.
- During PHPCS remediation, never weaken rules in `phpcs.xml.dist` to silence unresolved findings. If a finding cannot be fixed safely in code, report it in the output and ask the user whether to add/update an entry in `AGENTS/ISSUES_TODO.md`.


## Learning Support
When useful, explain the theory behind choices (WordPress internals, security, architecture, standards), especially if the user shows knowledge gaps or asks for deeper understanding.
Keep explanations practical and tied to the current code.


## Trigger Commands
Use the following trigger patterns and workflows.

### Trigger A: Full code review (file/folder scope)
Trigger phrases (or equivalent wording):
- "Run a full review on file X"
- "Run a full review on folder X"

Mandatory workflow:
- bug and security analysis;
- style checks;
- compliance checks against `AGENTS/CODING_STANDARDS.md`;
- execute a lint of the file/folder and fix the reported errors when possible (ina safe way);
- HTML correctness checks on touched templates/markup;
- lint execution for the target scope and code fixes for lint-reported issues when safe.

Execution notes:
- Provide live progress updates and explicitly name the check being executed (for example: bug check, security check, style check, HTML check, lint/check-fix step).

Expected output:
- findings first, ordered by severity, with file/line references;
- then assumptions/open questions;
- then an optional short change summary.

### Trigger B: URL quality audit (page-level runtime check)
Trigger phrases (or equivalent wording):
- "Check URL X"
- "Audit page X"
- Requests asking to audit an URL for HTML/JS errors, efficiency, responsiveness, accessibility, and loading performance.

Mandatory workflow:
- download/fetch the target URL HTML;
- verify produced HTML correctness/coherence;
- check for HTML and JavaScript errors (as far as the environment allows);
- assess loading efficiency (blocking assets, caching/compression signals, oversized resources);
- assess responsive behavior signals (viewport, layout patterns, obvious structural issues);
- assess accessibility issues (semantic structure, ARIA consistency, missing labels/attributes, invalid relationships);
- assess loading performance with concrete measurements when possible.

Scope filter:
- report only impactful and relevant issues (skip low-value noise unless requested).

Expected output:
- a numbered and concise list of detected issues, ordered by severity/impact, with evidence (file/line when mapped to theme templates, or runtime evidence from fetched HTML/headers);
- after listing issues, explicitly ask whether to add them to `AGENTS/ISSUES_TODO.md`.

### Trigger C: Check for new issues to fix
Trigger phrases (or equivalent wording):
- "Check if there are new issues"
- "Check if there are issues to fix"
- Requests asking to identify pending issues and suggest what to fix next.

Mandatory workflow:
- re-read `AGENTS/ISSUES_TODO.md`;
- verify whether open issues are present;
- suggest which issue to fix first based on priority/criticality and impact;
- once an issue is fixed, always update both:
  - `AGENTS/ISSUES_TODO.md` (remove/update status),
  - `AGENTS/ISSUES_RESOLVED.md` (add resolved entry with date and fix summary).

Expected output:
- concise status summary (open issue count by priority when practical);
- recommended next issue to fix with short rationale;
- after each completed fix, explicit note of updates applied to `ISSUES_TODO.md` and `ISSUES_RESOLVED.md`.


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
Create or update issues in `ISSUES_TODO.md` when you find something in one of these CATEGORIES:
- Security vulnerabilities
- Bugs
- Refactoring opportunities
- Code style violations against `CODING_STANDARDS.md`
- Performance problems
- Accessibility problems
- Documentation gaps
- New features or improvement ideas

Feature ideas not implementation-ready still go to category `Feature` with status like `Idea` or `Under Evaluation`.

### Priority levels
The SEVERITIES of the issues are:

- `Critical`: security/data-loss/major service breakage.
- `High`: major user impact.
- `Medium`: relevant but non-blocking.
- `Low`: minor impact or polish.

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

### Resolution flow
When an issue is resolved:
1. Move it from `ISSUES_TODO.md` to `ISSUES_RESOLVED.md`.
2. Add `Resolution date` and `Fix summary`.
3. Add commit/PR references when available.


## Documentation and AGENTS Update Matrix
After code changes, update documentation with this matrix:
- Feature implemented:
  Update `PROJECT.md`, `ARCHITECTURE.md`, and related issue status.
- Bug fixed:
  Move/update the issue in `ISSUES_RESOLVED.md`; update `ARCHITECTURE.md` only if runtime behavior changed.
- Refactor without behavior changes:
  Update docs only if architecture/conventions changed; otherwise update issue tracking only.
- Coding rule/tooling/process change:
  Update `CODING_STANDARDS.md` and `AI_BEHAVIOR.md` when process impact exists.
- New/removed AGENTS file:
  Update `AGENTS/AGENTS_README.md` file map.
- Backlog changes:
  Update `AGENTS/ISSUES_TODO.md` and, when closed, move entries to `AGENTS/ISSUES_RESOLVED.md`.

For security/accessibility/code-quality checks, apply `AGENTS/CODING_STANDARDS.md` checklists.
If a required check fails and cannot be fixed safely in scope, add/update an issue in `AGENTS/ISSUES_TODO.md`.


## Definition of Done
Before marking work complete:
- Run `composer run lint:php` when environment/dependencies are available.
- Re-check changed templates/components for escaping and structural validity.
- Update issue tracking (`ISSUES_TODO.md` / `ISSUES_RESOLVED.md`) when applicable.
- Update AGENTS docs affected by the change (`PROJECT.md`, `ARCHITECTURE.md`, `CODING_STANDARDS.md`, `AI_BEHAVIOR.md`, `AGENTS_README.md` as needed).
- Report a concise summary of what changed, what was verified, and any remaining risks.


## Batch and Mass Updates
- For a global rule update, apply changes consistently across all affected files and report the edited file list.
- For mass updates touching many files, list impact first and request confirmation before applying.


## Git Workflow Usage
Git branch/commit/PR conventions are defined in `AGENTS/GIT_WORKFLOW.md`.
Read and apply that file only when the user asks for VCS actions.
