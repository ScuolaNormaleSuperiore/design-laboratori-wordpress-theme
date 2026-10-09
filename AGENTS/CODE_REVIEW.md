# CODE_REVIEW.md

## Purpose and authority

Public review rules for Trigger F in `AGENTS/AI_BEHAVIOR.md`. Read this file
with `AGENTS/ARCHITECTURE.md` and `AGENTS/CODING_STANDARDS.md`.
Use applicable official WordPress skills. If these documents or a skill
conflict, stop the affected work, explain the practical pros and cons, and ask
the human operator which instruction to follow.

## Scope and coverage

Review first-party code and its call-sites, including PHP, template markup,
JavaScript, CSS/SCSS, configuration and maintained test tools in the agreed
scope. Exclude third-party internals. Verify provenance before identifying
another library as external. Calls into excluded code remain in scope.
Use excluded assets or documentation as evidence when necessary; do not claim
they were reviewed in full. Inspect generated outputs against their source;
do not duplicate a source finding against its generated copy.
Inspect opaque files with safe metadata/listing tools, never by executing or
importing them. Report inaccessible or unsupported formats as coverage limits.
Respect the project-specific exclusions and plan below; propose scope changes
rather than silently widening an existing review.

## Inventory, baseline, and phases

For a full review, begin with inventory and baseline before any code batch.
Enumerate current tracked files, account for locally untracked first-party
files separately, and record every scoped path as read, inspected, excluded,
or pending, with the reason for exclusions. Derive counts from actual files.
Run the available static quality checks documented by the repository, limited
to first-party material where supported. Record command, time, exit status,
tool versions when relevant, and error totals. If a check cannot run, record
the reason; never infer a successful baseline or reconstruct it from memory.
Distinguish pre-existing quality debt from proven regressions against a
recorded baseline. Consolidate repetitive lint findings by root cause; retain
independently verified bugs even when the quality gate already fails.

Build phases from the project plan and current inventory. Split large phases
into explicit sub-batches, targeting roughly 3,000 reviewable lines per batch.
Do not hard-code historical counts or merge batches just to finish faster.
Review one authorized phase at a time. A scoped review uses the same evidence
rules without marking a full-run batch complete.
Preserve an existing operator-supplied plan and completed phases; reconcile
new or changed paths rather than resetting history.

## Checks

- Correctness: edge cases, null/empty values, PHP compatibility, hook signatures,
  query state, pagination, rewrites, optional ACF/Polylang guards and consumers.
- Security: trust boundaries, sanitization and unslashing, contextual escaping,
  prepared SQL, authorization and nonces where required by the action,
  authenticated/public AJAX or REST handlers, uploads, redirects, remote URLs,
  DOM insertion, command execution and sensitive data in tooling.
  A nonce is not authorization; public actions require their own abuse controls.
- Accessibility: semantic HTML, names and labels, headings, keyboard and focus,
  ARIA relationships, contrast, reflow, forms and dynamic status updates.
- Performance: repeated queries, unbounded results, metadata filtering,
  options/autoload, caches and invalidation, remote calls, asset loading and
  work performed on every request. Explain workload and measurable impact.
- Maintainability: duplicated or dead logic, complexity, project naming,
  internationalization, source/output consistency and dependency integration.
- Tooling: meaningful assertions, exit codes, failure propagation, safe writes,
  deterministic comparisons, version consistency and scanner blind spots.
- Report style/documentation findings only with concrete impact. Do not create
  a cosmetic sweep; accessibility remains a mandatory quality requirement.

## Evidence and deduplication

Read complete relevant functions/files and check call-sites before reporting.
Every confirmed finding needs a file and line, triggering input/state,
expected and observed behavior, impact, and a practical correction proposal.
Identify who controls sensitive input and the actual execution path. Neither
an unescaped value nor a search hit alone proves an exploitable vulnerability.
Honor intentional patterns documented in the project standards.
Use conservative severity levels from `AGENTS/AI_BEHAVIOR.md`.
Keep unverified hypotheses in a separate "Assumptions and open questions"
section; never file them as confirmed defects.
Deduplicate against findings from this run and `AGENTS/ISSUES_TODO.md`. Extend an existing root cause rather than creating duplicates.
Propose closure of stale backlog entries with current evidence; do not close
them during a read-only review without authorization.
Do not load a resolved archive in full. Search only when requested history or
a specific suspected regression makes it necessary.

## Execution and persistence

Review code without changing it. Do not weaken lint rules or mutate Git state.
Run relevant non-mutating static checks. Runtime checks require an authorized
target; prefer cheap read-only checks when allowed by the operator/project.
Otherwise give the exact suggested command and what confirms or refutes the
hypothesis. Never claim an unexecuted check passed.
Generated diagnostic output is permitted only in existing ignored tool-report
locations; check the worktree afterward for unintended writes.
Use the operator timezone for dates. Mask secrets and personal data in reports.

Results and phase checkpoints are delivered in chat. Bootstrap ensures
`AGENTS/ISSUES_TODO.md` and `AGENTS/ISSUES_RESOLVED.md` exist but does not read
them. Use the TODO file for deduplication; search the resolved archive only for
requested history or a specific suspected regression. Both are ignored local
files and must never be committed. Cross-session review resumption requires an
operator-supplied checkpoint. Never invent past progress. Reset or overwrite a
run only with explicit authorization.

## Output

After each phase, report files read versus inspected, confirmed findings,
unverified questions, checks and limitations, and the next proposed phase.
Number issue lists. Each confirmed finding uses the issue template in
`AGENTS/AI_BEHAVIOR.md`, with file/line evidence in the description when
the template has no dedicated field.
For the final report, include category/severity counts for findings of this
run, Critical/High rationale, up to five recommended corrections, assumptions,
and coverage reconciliation. Keep "findings in this run" distinct from the total open entries in
`AGENTS/ISSUES_TODO.md`.
A full run is complete only when scoped coverage is reconciled and the final
report is delivered.

## Project-specific plan

Preserve the existing sixteen-batch plan when resuming a recorded run:
1. Bootstrap and configuration.
2. First-party integration layer under inc/.
3. Core service classes.
4. Content types and importers.
5. Admin options.
6. Admin tooling and walkers.
7. Structural root templates.
8. Detail templates (retain recorded 8a/8b splits).
9. Filtered archives.
10. Forms and other pages.
11. Common/header/hero template parts.
12. Home and people template parts.
13. First-party assets, configuration and repository automation.
14. Scan scripts.
15. Comparator/report scripts.
16. Coverage reconciliation and final report.

Standing exclusions inherited from the previous review: vendor/, node_modules/,
inc/vendor/, assets/bootstrap-italia/, third-party component scripts and
styles (verify provenance), media/font/icon folders, SETUP/, standalone project
documentation, translation catalogues, and test material other than maintained
scan/compare/report scripts. Exported ACF declaration bodies are inspected
through targeted field/location/consumer checks; surrounding registration,
guards and callbacks receive full review.
The baseline uses the available quality scanner, or individual configured
static tools if the scanner cannot run. Record unavailable tools explicitly.
Project prefixes and wrapper conventions come from CODING_STANDARDS.md and
ARCHITECTURE.md, not from guessed generic WordPress naming.
