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

## Learning Support

When useful, explain the theory behind choices (WordPress internals, security, architecture, standards), especially if the user shows knowledge gaps or asks for deeper understanding.
Keep explanations practical and tied to the current code.

## Excluded Directories

Always ignore these folders for review/refactoring/fixes:
- `vendor/`
- `node_modules/`

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
