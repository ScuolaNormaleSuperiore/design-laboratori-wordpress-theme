# AI_BEHAVIOR.md

## Purpose

Operational rules for AI assistants working on this codebase.

## Communication and Collaboration

- Be concise, precise, and action-oriented.
- Ask clarifying questions only when ambiguity blocks correct implementation.
- Explain tradeoffs when multiple technical options are valid.
- Be transparent about uncertainty or limitations.
- After meaningful progress, summarize what changed and what remains.
- Protect quality: do not compromise security, accessibility, or maintainability.

## Problem-Solving and Review Mindset

- Analyze context before editing.
- Work one objective at a time, end-to-end.
- Flag drift from the user objective and propose refocus.
- Look for: security issues, accessibility gaps, performance bottlenecks, duplication, weak error handling, unclear naming, documentation gaps.
- If you spot improvements not implemented now, add them to `ISSUES_TODO.md`.

## Excluded Directories

Always ignore these folders for review/refactoring/fixes:
- `vendor/`
- `node_modules/`
- `assets/bootstrap-italia/`

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

Feature ideas that are not implementation-ready still go to `ISSUES_TODO.md` under category `Feature` with status like `Idea` or `Under Evaluation`.

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
2. Add resolution date and fix summary.
3. Keep issue statistics aligned.
4. Reference related commit/PR when available.

## Documentation Update Rules

After code changes, update related docs:
- Feature work: `PROJECT.md`, `ARCHITECTURE.md`, and `ISSUES_RESOLVED.md` (if tracked).
- Bug fixes: `ISSUES_RESOLVED.md`; update `ARCHITECTURE.md` and `CODING_STANDARDS.md` when relevant.
- Refactors: `ARCHITECTURE.md`, `ISSUES_RESOLVED.md`, and coding rules if new conventions were introduced.
- Path/structure changes: update architecture and agent startup references.

Before closing a task, verify:
- Code follows `CODING_STANDARDS.md`.
- Relevant AGENTS docs were updated.
- Issue tracking is consistent.

## Batch and Mass Updates

- For a global rule update, apply changes consistently across all affected files and report the edited file list.
- For mass updates touching many files, list impact first and request confirmation before applying.

## Git Workflow Usage

Git branch/commit/PR conventions are defined in `AGENTS/GIT_WORKFLOW.md`.
Read and apply that file only when the user asks for VCS actions.

## Learning and Explanations

When useful for the user's growth, explain the relevant theory and WordPress best practices behind your implementation choices.
Do this especially when the user shows gaps or asks for deeper understanding.
