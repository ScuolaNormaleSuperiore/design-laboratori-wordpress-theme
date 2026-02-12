# WISH LIST
Last update: 2026-02-12

This file collects feature ideas and improvements that are desirable but not yet approved as actionable issues.

## How to use this file

- Store exploratory ideas and medium/long-term opportunities.
- Keep entries concise and focused on user value.
- Move an item to `ISSUES_TODO.md` only when approved and ready for planning/implementation.

## Wish Template

```markdown
### [PRIORITY] Short feature title
- **Status:** Idea | Under Evaluation | Planned
- **Date:** YYYY-MM-DD
- **Expected value:** Why this matters
- **Impact area:** Users | Editors | Administrators | Developers
- **Technical notes:** Optional implementation hints
```

---

### [HIGH] Unified editorial dashboard for key content health
- **Status:** Idea
- **Date:** 2026-02-12
- **Expected value:** Give administrators one place to see broken links, missing featured images, untranslated posts, and outdated events.
- **Impact area:** Editors, Administrators
- **Technical notes:** Add a custom admin page with widgets fed by WP_Query and lightweight validation checks.

---

### [HIGH] Granular role/capability matrix for custom content types
- **Status:** Idea
- **Date:** 2026-02-12
- **Expected value:** Improve governance by allowing fine-grained permissions per post type and workflow step.
- **Impact area:** Administrators, Editors
- **Technical notes:** Introduce custom roles/caps mapped to post types and critical actions (publish, import, configure options).

---

### [MEDIUM] Content quality assistant in the editor
- **Status:** Idea
- **Date:** 2026-02-12
- **Expected value:** Help authors publish higher-quality content with pre-publish checks (missing excerpt, missing alt text, empty SEO fields).
- **Impact area:** Editors
- **Technical notes:** Hook into post save and Gutenberg notices with non-blocking warnings and optional hard rules.

---

### [MEDIUM] Smart search with faceted filters
- **Status:** Idea
- **Date:** 2026-02-12
- **Expected value:** Improve discoverability with filterable results by content type, year, area, and language.
- **Impact area:** Users
- **Technical notes:** Extend current search template with normalized query params and reusable filter components.

---

### [MEDIUM] Scheduled data integrity checks and admin alerts
- **Status:** Idea
- **Date:** 2026-02-12
- **Expected value:** Detect configuration/content regressions early (orphaned relations, invalid dates, missing taxonomy terms).
- **Impact area:** Administrators, Developers
- **Technical notes:** Add WP-Cron jobs plus a compact admin notification/reporting mechanism.

---

### [LOW] Public API endpoints for selected content collections
- **Status:** Under Evaluation
- **Date:** 2026-02-12
- **Expected value:** Enable future integrations (institutional portals, mobile apps, external catalogues) without scraping HTML.
- **Impact area:** Developers
- **Technical notes:** Expose curated REST routes with strict schema, caching, and capability checks where needed.
