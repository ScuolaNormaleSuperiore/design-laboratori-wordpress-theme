# AI_BEHAVIOR.md


## Purpose
Operational rules for AI assistants working on this codebase.


## Execution Rules
- Be concise, precise, and action-oriented.
- Work one objective at a time, end-to-end.
- Ask clarifying questions only when ambiguity blocks implementation.
- After meaningful progress, summarize what changed and what remains.
- Keep quality gates active: security, accessibility, maintainability.
- Break substantial work into clear steps and report milestones after each.
- Use local file analysis and shell checks to validate changes before reporting
  completion. Preserve decisions and context already established in the
  session.
- Before significant edits, run `git status --short` and search relevant
  call-sites with `rg`. If unexpected changes overlap the intended edit, stop
  and ask for confirmation.
- Prefer targeted diffs (`apply_patch` for small or medium changes). For
  cross-file refactors, replace call-sites before removing wrappers or dead
  code.
- After each change, run appropriate syntax/lint checks and search for orphan
  references. State anything that could not be verified in the final report.
- Treat `DEV` as a symlinked project context entry point; when searching from the repo, use tools/options that follow symlinks (for example `rg -L`) so files reachable through `DEV` are included.
- For coding/security/style specifics, follow `AGENTS/CODING_STANDARDS.md`.
- During PHPCS remediation, never weaken rules in `phpcs.xml.dist` to silence unresolved findings. If a finding cannot be fixed safely in code, report it in the output and ask the user whether to add/update an entry in `DEV/CODE_REVIEW/ISSUES_TODO.md`.


## Learning Support
When useful, explain the theory behind choices (WordPress internals, security, architecture, standards), especially if the user shows knowledge gaps or asks for deeper understanding.
Keep explanations practical and tied to the current code.


## Trigger Commands
Use the following slash aliases or equivalent trigger phrases and workflows.

### Trigger summary

| Trigger | Slug | Scope | Frasi di attivazione (EN / IT) |
|---|---|---|---|
| **B** | `/audit-url <URL>` | Singola URL — audit runtime (HTML, JS, performance, accessibilità) | "Check URL X" / "Controlla l'URL X", "Audit page X" / "Verifica la pagina X" |
| **C** | `/issues` | `DEV/CODE_REVIEW/ISSUES_TODO.md` — individua le issue aperte e suggerisce da dove partire | "Check if there are new issues" / "Ci sono issue da risolvere?", "Check if there are issues to fix" / "Da dove parto con le issue?" |
| **D** | `/issues-summary` | `DEV/CODE_REVIEW/ISSUES_TODO.md` — tabella numerica per categoria × severità | "I want a tabular issue summary" / "Fammi un riepilogo tabellare delle issue" |
| **F** | `/issues-snapshot` | `DEV/CODE_REVIEW/ISSUES_TODO.md` — tabella a video + snapshot JSON in `tests/e2e/issues-report/reports/` | "Fammi uno snapshot delle issue" / "Genera il report JSON delle issue" |
| **G** | `/issues-triage` | `DEV/CODE_REVIEW/ISSUES_TODO.md` — triage interattivo, una issue alla volta (correggere / trascurare / abbassare priorità) | "Scodiamo le issue" / "Triage interattivo delle issue", "Let's triage the issues one by one" / "Facciamo il dequeue delle issue" |
| **H** | `/code-review [scope]` | Qualsiasi code review del codice — strutturata, multi-sessione, guidata da `DEV/CODE_REVIEW/`: riprende da dove era arrivata e propone la fase successiva | "Avvia la code review" / "Start the code review", "Riprendi la code review" / "Resume the code review", "A che punto è la code review?" / "Code review status", "Mi fai una code review del codice?", "Fai una review completa del file/cartella X" |

> **Code review**: il Trigger H è l'UNICO trigger di code review del progetto. Qualsiasi richiesta di revisione del codice — un singolo file, una cartella o l'intera codebase — passa da lì e segue `DEV/CODE_REVIEW/CODE_REVIEW_PROMPT.txt`. Non esistono più scorciatoie che scansionano il codice fuori da quel flusso.

### Trigger B: URL quality audit (page-level runtime check)
Slash alias: `/audit-url <URL>`

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
- after listing issues, explicitly ask whether to add them to `DEV/CODE_REVIEW/ISSUES_TODO.md`.

### Trigger C: Check for new issues to fix
Slash alias: `/issues`

Trigger phrases (or equivalent wording):
- "Check if there are new issues"
- "Check if there are issues to fix"
- Requests asking to identify pending issues and suggest what to fix next.

Mandatory workflow:
- re-read `DEV/CODE_REVIEW/ISSUES_TODO.md`;
- verify whether open issues are present;
- suggest which issue to fix first based on priority/criticality and impact;
- once an issue is fixed, always update both:
  - `DEV/CODE_REVIEW/ISSUES_TODO.md` (remove/update status),
  - `DEV/CODE_REVIEW/ISSUES_RESOLVED.md` (add resolved entry with date and fix summary).

Expected output:
- concise status summary (open issue count by priority when practical);
- recommended next issue to fix with short rationale;
- after each completed fix, explicit note of updates applied to `DEV/CODE_REVIEW/ISSUES_TODO.md` and `DEV/CODE_REVIEW/ISSUES_RESOLVED.md`.

### Trigger D: Tabular issue summary and start recommendations
Slash alias: `/issues-summary`

Trigger phrases (or equivalent wording):
- "I want a tabular issue summary"
- "I want a tabular issue report"
- Requests asking for a table that summarizes issue counts by category and severity.

Mandatory workflow:
- re-read `DEV/CODE_REVIEW/ISSUES_TODO.md`;
- consider only open issues unless the user asks to include resolved ones;
- build a matrix with:
  - rows = categories + final `Total` row,
  - columns = severities (`Critical`, `High`, `Medium`, `Low`) + final `Total` column;
- fill each cell with the issue count for that category/severity pair;
- include row totals and column totals;
- after the table, always add a standalone line with the overall total issue count;
- always recommend 4-5 issues maximum to start with, ranked by:
  - severity first (`Critical` highest priority),
  - then category priority: `Security`, `Bug`, `Performance`,
  - then practical impact/effort when tie-breaking.

Expected output:
- a concise markdown table with categories on rows and severities on columns, including `Total` row/column;
- keep cell values as plain numbers (no HTML tags in the table output);
- format in bold all `Total` values and the `Critical` cells for `Security`, `Bug`, and `Performance`;
- a standalone line immediately after the table: `Total open issues: N`;
- a numbered shortlist (max 5) of recommended starting issues with a short rationale for each.

### Trigger F: Issues snapshot (tabular report + JSON export)
Slash alias: `/issues-snapshot`

Trigger phrases (or equivalent wording):
- "Fammi uno snapshot delle issue"
- "Genera il report JSON delle issue"
- Requests asking to export or snapshot the current issue backlog as a JSON file.

Mandatory workflow:
1. Re-read `DEV/CODE_REVIEW/ISSUES_TODO.md` in full.
2. Parse all open issues (skip Feature issues with status `Idea` unless asked).
3. Build the summary matrix: rows = categories, columns = severities (Critical / High / Medium / Low / Total).
4. Display the table to the user (same format as Trigger D).
5. Retrieve the current date and time by running the following Bash command and use its output to build the filename timestamp (`YYYYMMDD_HHMM`):
   ```bash
   date +"%Y%m%d_%H%M"
   ```
   Use the result for both the filename suffix and the `generatedAt` ISO field.
6. Build a JSON object with this structure:
   ```json
   {
     "generatedAt": "<ISO timestamp>",
     "summary": {
       "total": N,
       "bySeverity": { "Critical": N, "High": N, "Medium": N, "Low": N },
       "byCategory": {
         "<Category>": { "Critical": N, "High": N, "Medium": N, "Low": N, "total": N }
       }
     },
     "issues": [
       { "title": "...", "priority": "HIGH|MEDIUM|LOW|CRITICAL", "category": "...", "status": "Open", "date": "YYYY-MM-DD", "files": ["..."] }
     ]
   }
   ```
7. Write the JSON to `tests/e2e/issues-report/reports/issues_report_<YYYYMMDD_HHMM>.json` using the Write tool (create the directory if it doesn't exist).
8. Confirm the output file path to the user.

Expected output:
- The tabular summary (same as Trigger D).
- Confirmation line: `JSON saved: tests/e2e/issues-report/reports/issues_report_<ts>.json`

### Trigger G: Interactive issue triage (dequeue one by one)
Slash alias: `/issues-triage`

Trigger phrases (or equivalent wording):
- "Scodiamo le issue" / "Let's triage the issues one by one"
- "Triage interattivo delle issue" / "Facciamo il dequeue delle issue"
- Requests asking to go through open issues one at a time to fix/skip/deprioritize them quickly.

Purpose:
- Smaltire (dequeue) il maggior numero possibile di issue aperte, presentandole **una alla volta** e agendo subito sulla scelta dell'utente.

Mandatory workflow:
1. Re-read `DEV/CODE_REVIEW/ISSUES_TODO.md` in full.
2. Build the working queue with these rules:
   - Include only issues with `Status: Open`.
   - **Exclude** `Category: Feature` entries whose status is `Idea` or `Under Evaluation`.
   - Order by severity `Critical → High → Medium → Low`; within the same severity, order by category priority `Security → Bug → Performance → Accessibility → Refactoring → CodeStyle → Documentation`.
3. Present issues **one at a time** (never dump the whole list). Before presenting each issue, perform a quick **reality check**: open the referenced file(s)/line(s) and confirm the problem still exists in the current code.
   - If the issue is already resolved or is a false positive, do **not** prompt the user: state it briefly, move it to `DEV/CODE_REVIEW/ISSUES_RESOLVED.md` (mark as "already fixed" / "not reproducible") or remove it, and continue to the next issue.
4. Use exactly this presentation format for each issue (fill `Stato reale` with the result of the reality check, and `Raccomandazione` with your own suggested action):

   ```
   Issue #<n> (<x> di <totale in coda>)
   [SEVERITY] Titolo e breve descrizione
   File:
   Tipo:
   Impatto:
   Stato reale:
   Raccomandazione:

   Rispondimi con una di queste:
   - correggere
   - trascurare
   - abbassare priorità
   ```

5. Wait for the user's answer, then act:
   - **correggere** → apply the fix following the standard flow (verify in code → apply minimal edit → run `php -l` / `composer run lint:php` when available → move the issue from `DEV/CODE_REVIEW/ISSUES_TODO.md` to `DEV/CODE_REVIEW/ISSUES_RESOLVED.md` with resolution date + fix summary). Never weaken `phpcs.xml.dist`. If the fix turns out to be risky or non-trivial, warn the user and ask for explicit confirmation before proceeding.
   - **trascurare** → skip only: leave the issue unchanged in `DEV/CODE_REVIEW/ISSUES_TODO.md` and move to the next one (it may reappear in a future run).
   - **abbassare priorità** → lower the severity by exactly one level (`Critical→High`, `High→Medium`, `Medium→Low`; a `Low` stays `Low` and is only skipped), update the `[SEVERITY]` tag in `DEV/CODE_REVIEW/ISSUES_TODO.md`, then move to the next issue.
6. After each action, immediately present the next issue in the queue. Continue until the queue is empty or the user asks to stop.
7. Honor mid-session control phrases: "stop"/"basta"/"pausa" (end the session), "salta"/"next" (same as *trascurare*), "indietro" (re-present the previous issue).

Session hygiene:
- Keep `DEV/CODE_REVIEW/ISSUES_TODO.md` and `DEV/CODE_REVIEW/ISSUES_RESOLVED.md` consistent after every `correggere`/`abbassare priorità` action (no batching that could be lost if the session ends).
- Respect `Repository Scope Boundaries` and the standard `Definition of Done`.

Expected output:
- One issue presented at a time in the exact format above, followed by the concrete action taken after the user's answer.
- On queue completion, a short closing summary: how many issues were corrected, skipped, deprioritized, and how many remain open.


### Trigger H: Structured codebase review (resumable, batch by batch)
Slash alias: `/code-review [scope]`

Trigger phrases (or equivalent wording):
- "Avvia la code review" / "Start the code review"
- "Riprendi la code review" / "Resume the code review" / "Continua la code review"
- "A che punto è la code review?" / "Code review status"
- "Mi fai una code review del codice?" / "Mi fai una verifica completa sul codice della codebase?"
- "Fai una review completa del file X" / "Run a full review on folder X"
- Any request to review code for bugs, security, performance, accessibility or refactoring, whatever its scope.

Scope note:
- This trigger is the project's ONLY code-review entry point. When the user asks
  for a review limited to one file or folder, do not improvise a separate
  workflow: apply the prompt's rules (scope, evidence standard, deduplication,
  output, read-only constraint) to that narrower scope, say explicitly that you
  are running a partial review, and do NOT tick any batch in
  `CODE_REVIEW_PROGRESS.md` — that file records the full run only.

Purpose:
- Eseguire la code review completa della codebase **una fase alla volta**, secondo le regole di `DEV/CODE_REVIEW/CODE_REVIEW_PROMPT.txt`, mantenendo lo stato fra sessioni diverse e non rifacendo mai lavoro già fatto.

Mandatory workflow:
1. Read `DEV/CODE_REVIEW/CODE_REVIEW_PROGRESS.md` **first**, before anything else. It is the state of the run.
2. Read `DEV/CODE_REVIEW/CODE_REVIEW_PROMPT.txt` in full and follow it for the whole review. That file governs scope, method, evidence standard, output and constraints. Where it conflicts with this document or with `CODING_STANDARDS.md`, the AGENTS documents win, as the prompt itself states.
3. Report the status to the user **before doing any review work**, in this exact shape:

   ```
   Code review: <non avviata | in corso | completata>
   Inventario e baseline (fase 0) : <fatto il GG/MM/AAAA | da fare>
   Batch completati               : <n> di 12
   Ultimo batch chiuso            : <nome> (<data>)
   Prossima fase proposta         : <fase 0 | Batch <n> - <nome>>
     Ambito   : <file o cartelle di quella fase>
     Dimensione : <n file, ~n righe>  (dall'inventario dinamico, non dal prompt)
     Da spezzare? : <no | sì, in <n>a/<n>b - vedi "BATCH SIZE AND SPLITTING">
   ```

   - If the review has never started, say so explicitly and propose **fase 0** (inventory + `npm run quality:scan` baseline).
   - If every batch is ticked, say the review is complete and offer either the final report (prompt section 7.2) or a fresh run from `CODE_REVIEW_PROGRESS.template.md`.
   - Compute `Dimensione` from the real inventory (`git ls-files` + line counts), never from memory or from the prompt text.
4. **STOP and wait** for the user's go-ahead. Do not start the proposed phase in the same turn: the point of this trigger is that the user decides when a phase runs.
5. On confirmation, execute **only that one phase**, following the prompt. At the end of the phase: print the one-line progress note, update `DEV/CODE_REVIEW/CODE_REVIEW_PROGRESS.md`, and stop again with the next proposed phase. Never chain two phases without asking.
6. Honor the control phrases: "stop"/"basta"/"pausa" (end the session cleanly, after updating the progress file), "salta" (mark the phase skipped in the progress file, with the reason, and propose the next one).

Writable files (this trigger is otherwise read-only):
- `DEV/CODE_REVIEW/ISSUES_TODO.md`
- `DEV/CODE_REVIEW/CODE_REVIEW_PROGRESS.md`

Nothing else is written: no fix is applied to theme code, and `CODE_REVIEW_PROMPT.txt` is read-only. If the prompt needs correcting, say so and let the user decide.

Expected output:
- The status block above, then the proposed phase, then a stop.
- After each executed phase: the progress note, the issues added, and the next proposed phase.


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
Create or update issues in `DEV/CODE_REVIEW/ISSUES_TODO.md` when you find something in one of these CATEGORIES:
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
1. Move it from `DEV/CODE_REVIEW/ISSUES_TODO.md` to `DEV/CODE_REVIEW/ISSUES_RESOLVED.md`.
2. Add `Resolution date` and `Fix summary`.
3. Add commit/PR references when available.


## Documentation and AGENTS Update Matrix
After code changes, update documentation with this matrix:
- Feature implemented:
  Update `PROJECT.md`, `ARCHITECTURE.md`, and related issue status.
- Bug fixed:
  Move/update the issue in `DEV/CODE_REVIEW/ISSUES_RESOLVED.md`; update `ARCHITECTURE.md` only if runtime behavior changed.
- Refactor without behavior changes:
  Update docs only if architecture/conventions changed; otherwise update issue tracking only.
- Coding rule/tooling/process change:
  Update `CODING_STANDARDS.md` and `AI_BEHAVIOR.md` when process impact exists.
- New/removed AGENTS file:
  Update `AGENTS/AGENTS_README.md` file map.
- Backlog changes:
  Update `DEV/CODE_REVIEW/ISSUES_TODO.md` and, when closed, move entries to `DEV/CODE_REVIEW/ISSUES_RESOLVED.md`.

For security/accessibility/code-quality checks, apply `AGENTS/CODING_STANDARDS.md` checklists.
If a required check fails and cannot be fixed safely in scope, add/update an issue in `DEV/CODE_REVIEW/ISSUES_TODO.md`.


## Definition of Done
Before marking work complete:
- Run `composer run lint:php` when environment/dependencies are available.
- Re-check changed templates/components for escaping and structural validity.
- Update issue tracking (`DEV/CODE_REVIEW/ISSUES_TODO.md` / `DEV/CODE_REVIEW/ISSUES_RESOLVED.md`) when applicable.
- Update AGENTS docs affected by the change (`PROJECT.md`, `ARCHITECTURE.md`, `CODING_STANDARDS.md`, `AI_BEHAVIOR.md`, `AGENTS_README.md` as needed).
- Report a concise summary of what changed, what was verified, and any remaining risks.


## Batch and Mass Updates
- For a global rule update, apply changes consistently across all affected files and report the edited file list.
- For mass updates touching many files, list impact first and request confirmation before applying.


## Git Workflow Usage
Git branch/commit/PR conventions are defined in `AGENTS/GIT_WORKFLOW.md`.
Read it during every bootstrap; apply its branch, commit, and pull-request rules
only when the task includes VCS actions.
