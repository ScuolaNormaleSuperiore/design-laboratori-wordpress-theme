# ISSUES TODO
Last update: 2026-03-04 (importer hardening, taxonomy validation, and date handling refactoring resolved)

---


## 🔒 Security

### [HIGH] Unescaped page links and names in sitemap template — XSS via page title
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Security
- **Description:** `mappasito.php` outputs page links and names from `dli_get_site_tree()` using direct `echo` concatenation without escaping. Page titles containing `<script>` or special characters would render as raw HTML. URLs are similarly unescaped. The language switcher links also use `get_site_url()` output directly without `esc_url()`.
- **Expected behavior:** Wrap all links with `esc_url()` and all names with `esc_html()`.
- **Files affected:** `page-templates/mappasito.php` (lines 40, 47-49, 56-58, 64-68, 87-90)

---

### [HIGH] Widespread unescaped output across home card and section templates
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Security
- **Description:** Multiple home and section templates echo post titles, links, dates, descriptions, ACF option fields, and archive URLs without `esc_html()`/`esc_url()`. This creates XSS vectors if post titles or ACF fields contain HTML. Also includes `sezione-contatti.php` assembling `tel:`/`mailto:` hrefs with `esc_attr()` instead of `esc_url()`, and `sezione-allegati.php` with unescaped `$section_id` in `<section id=...>`.
- **Files affected:**
  - `template-parts/common/sezione-contatti.php` (lines 19, 38)
  - `template-parts/common/sezione-allegati.php` (line 8)
  - `template-parts/common/sezione-box-notizia.php` (line 21)
  - `template-parts/common/paginazione.php` (lines 45, 58)
  - `template-parts/header/secondary-menu.php` (lines 26, 41, 53)
  - `page.php` (lines 26, 63-66, 68, 73, 84, 106, 138)

---

### [HIGH] External links with `target="_blank"` miss `rel="noopener noreferrer"` in multiple theme templates
- **Status:** Open
- **Date:** 2026-02-19
- **Category:** Security
- **Description:** Several external links open in a new tab using `target="_blank"` but omit `rel="noopener noreferrer"`. This leaves `window.opener` available to the opened page (reverse-tabnabbing risk) and weakens tab isolation.
- **Expected behavior:** Add `rel="noopener noreferrer"` to all external links using `target="_blank"`.
- **Files affected:** `header.php` (line 62), `template-parts/common/social.php`, `template-parts/common/social_footer.php`, `template-parts/common/sezione-video.php`, `template-parts/common/sezione-contatti.php`, `template-parts/common/sezione-allegati.php`

---


## 🐛 Bug

### [HIGH] Missing template parts break homepage cards for Indirizzi and Strutture
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Bug
- **Description:** Homepage sections call `get_template_part( 'template-parts/servizio/card' )` and `get_template_part( 'template-parts/struttura/card', 'large' )`, but the referenced files are missing from the theme. WordPress fails silently, so the card blocks are not rendered.
- **Expected behavior:** Restore missing partials or update references to existing template-part paths.
- **Files affected:** `template-parts/home/indirizzi.php` (line 33), `template-parts/home/strutture.php` (line 54)

---

### [HIGH] Homepage site presentation outputs invalid nested paragraphs (`<p>` inside `<p>`)
- **Status:** Open
- **Date:** 2026-02-19
- **Category:** Bug
- **Description:** `template-parts/home/site-presentation.php` wraps `wpautop()` output in an extra `<p>`. Since `wpautop()` already generates paragraph tags, the rendered HTML contains nested `<p>` elements and invalid structure.
- **Expected behavior:** Remove the outer `<p>` wrapper and render `wpautop()` output directly in a neutral container (for example `<div>`).
- **Files affected:** `template-parts/home/site-presentation.php` (lines 19-21)

---

### [HIGH] Debug output left in production — pagination indices displayed in HTML
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** A debug `echo` renders pagination indices (`0 - 2 - 5`) directly in the page HTML for every "indirizzo di ricerca" card. This was clearly a debugging statement that was never removed and is visible to end users.
- **Expected behavior:** Remove the debug echo entirely.
- **Files affected:** `template-parts/common/sezione-indirizzidiricerca.php` (line 39)

---

### [HIGH] `People_Manager` uses `.` instead of `,` in `__()` — text domain concatenated into string
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** Two `__()` calls use string concatenation (`.`) instead of comma (`,`) for the text domain argument: `__( 'Rimuovi Immagine' . 'design_laboratori_italia' )`. This concatenates the domain into the translatable string, produces a mangled string (e.g., `"Rimuovi Immaginelaboratori_italia"`), and translation never works because the text domain is never passed to `__()`.
- **Expected behavior:** Replace `.` with `,` in both calls.
- **Files affected:** `inc/classes/class-peoplemanager.php` (lines 80-81)

---

### [HIGH] JS: `setTimeout(e(), 500)` calls function immediately — debounce completely broken
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** In the resize handler for accessibility/hamburger menu, `setTimeout(e(), 500)` invokes `e()` immediately (parentheses call the function) and passes its return value (`undefined`) to `setTimeout`. The debounce is completely non-functional — every resize event fires the full DOM recalculation with no delay.
- **Expected behavior:** Pass function reference without calling it: `setTimeout(e, 500)`.
- **Files affected:** `assets/js/laboratori.js`

---

### [HIGH] JS: `speed: number = 800` pollutes global scope in 6+ carousel initializations
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** Splide carousel options contain `speed: number = 800`. The expression `number = 800` is a runtime assignment to the undeclared global variable `window.number`. This appears in 6+ carousel instantiation blocks. The speed value happens to be `800` by accident (the assignment expression evaluates to `800`), but a global `number` variable is silently polluted on every carousel mount, potentially conflicting with other libraries.
- **Expected behavior:** Replace all occurrences of `speed: number = 800` with `speed: 800`.
- **Files affected:** `assets/js/laboratori.js` (multiple Splide initialization blocks)

---

### [HIGH] JS: Skiplink event listener crashes when anchor element is absent
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** The skiplink block calls `document.querySelector(".skiplink")?.querySelector('[href="#menu-principale"]').addEventListener(...)`. Optional chaining `?.` applies only to the inner `querySelector` — if `.skiplink` exists but lacks the target anchor, `querySelector` returns `null` and `null.addEventListener()` throws an uncaught `TypeError`. This crashes the entire DOMContentLoaded handler, preventing all subsequent JS in that block from executing.
- **Expected behavior:** Store the result and use optional chaining on the `addEventListener` call: `skipAnchor?.addEventListener(...)`.
- **Files affected:** `assets/js/laboratori.js`

---

### [HIGH] JS: DOM elements re-inserted on every scroll event without presence check
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** The sticky nav scroll handler moves real DOM nodes via `insertAdjacentElement` on every scroll event without checking whether the node is already in its target parent. This causes repeated re-flows. The handler has no throttle or debounce, so it fires on every pixel of scroll (60+ times per second). If either target node is `null`, the access causes a crash.
- **Expected behavior:** Add a guard (`if (!target.contains(node))`), debounce the scroll handler, and null-check all queried elements.
- **Files affected:** `assets/js/laboratori.js`

---

### [MEDIUM] Empty `twitter:image` meta tag in homepage SEO output
- **Status:** Open
- **Date:** 2026-02-13
- **Category:** Bug
- **Description:** Homepage HTML emits `<meta name="twitter:image" content="">` when no OG image is available. Empty social image metadata can degrade preview quality and produce inconsistent behavior across platforms.
- **Expected behavior:** Omit `twitter:image` when image URL is empty, or provide a valid fallback image URL.
- **Files affected:** `template-parts/header/seo_tags.php`, `inc/classes/class-contents-manager.php`

---

### [MEDIUM] `get_field('descrizione_breve')` called without post ID outside main loop in `sezione-eventi.php`
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Bug
- **Description:** At line 59, `get_field( 'descrizione_breve' )` is called without specifying the post ID. The template iterates events in a custom `foreach` (not a standard WP loop with `setup_postdata()`), so ACF falls back to the global `$post` (the page itself) instead of the current event. The same field is correctly fetched with `$id` at line 23 but the second call at line 59 omits it.
- **Expected behavior:** Pass the event ID: `get_field( 'descrizione_breve', $id )`, or use the already-fetched `$desc` variable.
- **Files affected:** `template-parts/common/sezione-eventi.php` (line 59)

---

### [MEDIUM] Undefined `$num_results` and null `$the_query` when search nonce fails in `cerca.php`
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Bug
- **Description:** When a search string is present but the nonce check fails, `$the_query` remains `null` and `$num_results` is never assigned. Downstream code and the pagination template receive undefined/null values. Additionally, the pagination call at line 217 passes `'query' => $the_query` where `$the_query` is `null`, and the template likely calls `$query->max_num_pages`, causing a fatal `TypeError`.
- **Expected behavior:** Initialize `$num_results = 0` before the conditional block, guard against null `$the_query` in the pagination template call, and add a null guard in the while loop at line 151.
- **Files affected:** `page-templates/cerca.php` (lines 34-53, 151, 217)

---

### [MEDIUM] `esc_html()` escapes `<BR />` tags in error message display in `newsletter.php`
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Bug
- **Description:** Error messages are joined with `<BR />` as separator, then the entire string is passed through `esc_html()`. This converts the line-break tags to visible text (`&lt;BR /&gt;`) instead of rendering actual line breaks.
- **Expected behavior:** Use `wp_kses()` with an allowed `<br>` tag, or join with newlines and wrap each message in a separate `<p>` element.
- **Files affected:** `page-templates/newsletter.php` (lines 135-136)

---

### [MEDIUM] `get_post()->post_name` without null check in `dli_get_page_slug_anchestors()`
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Bug
- **Description:** `get_post( $ancestor )->post_name` is called without checking if `get_post()` returns `null`. If an ancestor post was permanently deleted, `get_post()` returns `null` and the `->post_name` access causes a fatal error.
- **Expected behavior:** Check the return value of `get_post()` before accessing properties. Skip or handle null results gracefully.
- **Files affected:** `inc/utils.php` (line 1102)

---

### [MEDIUM] `count()` on potentially `false` return from `wp_get_attachment_image_src()` in OG data builder
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Bug
- **Description:** In `DLI_ContentsManager::fill_og_data()`, `wp_get_attachment_image_src()` can return `false` when a thumbnail ID is valid but the attachment file was deleted. The code then calls `count( $img_array )` on `false`, which throws a `TypeError` in PHP 8+.
- **Expected behavior:** Validate `$img_array` with `is_array()` before calling `count()`.
- **Files affected:** `inc/classes/class-contents-manager.php` (lines 48-52, 69-70)

---

### [MEDIUM] `echo the_content()` — double-echo anti-pattern in news/post detail
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** `the_content()` already echoes directly and returns `null`. Wrapping it in `echo` is semantically wrong and a recognized WPCS anti-pattern. Currently harmless (echo null outputs nothing), but misleading and would cause double output if the function is ever changed to return.
- **Expected behavior:** Remove the `echo` wrapper: `<?php the_content(); ?>`.
- **Files affected:** `single-notizia.php` (line 130), `single-post.php` (line 130)

---

### [MEDIUM] `wp_reset_postdata()` called at wrong scope in `persone.php`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** `wp_reset_postdata()` is called at line 185 immediately after creating `$categorie_persone` query but before iterating it. The inner `wp_reset_postdata()` at line 339 resets the inner `$persone` query but never the outer `$categorie_persone`. After the outer loop, `$post` points to the last persona rather than the page.
- **Expected behavior:** Remove premature call at line 185. After the outer loop completes, call `wp_reset_postdata()` once to restore the page's global `$post`.
- **Files affected:** `page-templates/persone.php` (lines 185, 339)

---

### [MEDIUM] Stray `>` character after `<img>` tag in hero sections — visible in output
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** After the closing `>` of the hero `<img>` tag, there is a stray `>` on the next line, rendered as a visible `>` text node in the HTML output before the `<figcaption>`.
- **Expected behavior:** Remove the stray `>`.
- **Files affected:** `single-evento.php` (line 60), `single-brevetto.php` (line 51), `single-spinoff.php` (line 41)

---

### [MEDIUM] `str_contains()` on potentially null `$posizione` in `single-luogo.php` — TypeError in PHP 8+
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** `dli_get_field('posizione_gps')` can return `null`/`false`. `str_contains()` in PHP 8.1+ requires a `string` first argument — passing `null` raises a `TypeError`.
- **Expected behavior:** Guard with `is_string( $posizione )` before calling `str_contains()`.
- **Files affected:** `single-luogo.php` (lines 117, 162)

---

### [MEDIUM] `sanitize_text_field()` used for `$per_page` instead of `absint()` across 8 archive templates
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** `$per_page` is initialized as an integer constant but overridden from `$_GET` via `sanitize_text_field()` which returns a string. This causes type inconsistency — strict comparisons like `$per_page === DLI_POSTS_PER_PAGE` fail. The correct sanitizer for a numeric value is `absint()`.
- **Expected behavior:** Replace `sanitize_text_field( $_GET['per_page'] )` with `absint( $_GET['per_page'] )`.
- **Files affected:** `page-templates/notizie.php`, `page-templates/eventi.php`, `page-templates/pubblicazioni.php`, `page-templates/brevetti.php`, `page-templates/spinoff.php`, `page-templates/risorse-tecniche.php`, `page-templates/ricerca.php`, `page-templates/archive-progetti.php`

---

### [MEDIUM] `Banner_Manager::custom_layout()` checks `NEWS_POST_TYPE` instead of `BANNER_POST_TYPE`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** The method is hooked to `edit_form_after_title` to display "Descrizione banner" heading only on Banner edit screens. Instead it checks `NEWS_POST_TYPE`, so the heading appears on News edits and never on Banner edits.
- **Expected behavior:** Change `NEWS_POST_TYPE` to `BANNER_POST_TYPE` on line 77.
- **Files affected:** `inc/classes/class-bannermanager.php` (line 77)

---

### [MEDIUM] Nested `<h2>` inside `<h2>` in archive hero — invalid HTML
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** `archive.php` hero template contains a directly nested `<h2>` inside another `<h2>`. Browsers auto-close the first before the second, producing broken markup and affecting heading structure/SEO.
- **Expected behavior:** Remove the outer `<h2>` wrapper, leaving only the inner one.
- **Files affected:** `template-parts/hero/archive.php` (lines 6-9)

---

### [MEDIUM] Unsafe `[0]` dereference on potentially null option in `featured-contents.php`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** `dli_get_option('featured_contents_' . $index, 'homepage')[0]` — `dli_get_option()` can return `null` or non-array. Immediately dereferencing `[0]` on `null` causes a `TypeError` in PHP 8+.
- **Expected behavior:** Check `is_array()` and `!empty([0])` before access.
- **Files affected:** `template-parts/home/featured-contents.php` (line 17)

---

### [MEDIUM] `$og_data` properties accessed without null checks in `social_sharing.php`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** Unlike `seo_tags.php` which checks `isset()` before OG data properties, `social_sharing.php` accesses `$og_data->url` and `$og_data->shared_title` directly. If `get_og_data()` returns null or incomplete, this causes a fatal error. Share URLs also lack `esc_url()` wrapping.
- **Expected behavior:** Add null/property checks, and wrap share URLs with `esc_url()`.
- **Files affected:** `template-parts/common/social_sharing.php` (lines 5-8)

---

### [MEDIUM] `$menu_items` variable overwritten inside its own `foreach` loop in `dli_get_site_tree()`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** At line 950, `$menu_items` is reassigned inside the outer `foreach` loop that iterates over it. PHP `foreach` operates on a copy so the loop itself survives, but any code after the inner assignment references the wrong data. This is fragile and error-prone.
- **Expected behavior:** Use a distinct variable name for the inner nav menu items (e.g., `$nav_menu_items`).
- **Files affected:** `inc/utils.php` (lines 950-984)

---

### [MEDIUM] Trailing comma in every `@font-face` `src` declaration — invalid CSS
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Bug
- **Description:** Every custom `@font-face` block in `fonts.css` has a trailing comma after the last `src` format entry. This is invalid CSS that can cause font load failures in strict parsers, CSS preprocessors, and fails CSS validation.
- **Expected behavior:** Remove trailing commas from `src` lists.
- **Files affected:** `assets/css/fonts.css` (12 `@font-face` blocks)

---


## 🔧 Refactoring

### [MEDIUM] Centralize event date rendering
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Event date rendering logic is duplicated across multiple files
- **Solution:** Create a helper (e.g., dli_get_event_date_parts()) and/or a template partial to render the calendar badge
- **Files affected:**
  - template-parts/home/hp-list-event.php
  - template-parts/home/card-eventi.php
  - template-parts/common/sezione-eventi.php
  - template-parts/common/sezione-box-evento.php
  - page-templates/eventi.php
- **Notes:** This removes duplication and improves maintainability

---

### [MEDIUM] Consolidate ACF date parsing/formatting
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** ACF date parsing and formatting logic is scattered across multiple files
- **Solution:** Expose a single utility for ACF date strings (e.g., dli_get_acf_date_parts()) that returns day, month name, year, timestamp. Use it in templates and importers.
- **Notes:** Safe date helpers already implemented in inc/utils.php: dli_get_datetime_from_format() and dli_format_date_from_format()

---

### [MEDIUM] Normalize translated month name usage
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Code wraps dli_get_monthname() with __() unnecessarily
- **Solution:** Avoid wrapping dli_get_monthname() with __() since the function already returns translated strings. Replace with a single call to dli_get_monthname()
- **Notes:** This is redundant and can cause translation issues

---

### [MEDIUM] Add safe helper for archive page lookup
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Archive page lookup is not null-safe and can cause fatal errors
- **Solution:** Create a helper that returns archive page link/title with null-safe defaults to prevent fatal errors across templates
- **Notes:** Improves error handling and user experience

---

### [MEDIUM] Centralize breadcrumb building
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Breadcrumb building logic (build_content_path()) is not properly centralized
- **Solution:** Move build_content_path() logic into a dedicated, reusable helper that safely handles missing parents and missing archive pages, with unit-like checks
- **Notes:** Improves code reusability and reliability

---

### [MEDIUM] Consolidate item wrappers
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Several dli_from_*_to_wrapped_item functions share a common structure with duplicated code
- **Solution:** Consider a base wrapper function that accepts overrides to reduce duplication and improve consistency
- **Notes:** Reduces code duplication and improves maintainability

---

### [MEDIUM] Unify link rendering logic
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Link rendering logic (external link/attachment/detail page) is inconsistent across templates
- **Solution:** Centralize external link/attachment/detail page logic in one helper (dli_manage_item_link exists). Ensure all templates use it to avoid inconsistent behavior.
- **Notes:** Improves consistency and maintainability

---

### [MEDIUM] Normalize image metadata fallback
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Image metadata retrieval with fallbacks is not standardized
- **Solution:** Provide a single helper to retrieve image metadata with fallbacks and placeholders, and use it consistently across templates
- **Notes:** Improves reliability and reduces code duplication

---

### [MEDIUM] Reduce repeated WP_Query patterns
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Common query parameters for home carousels/cards are repeated in multiple places
- **Solution:** Extract common query parameters for home carousels/cards into named helpers to avoid drift and simplify changes
- **Notes:** Improves maintainability and reduces risk of inconsistencies

---

### [MEDIUM] Improve pagination handling
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Pagination logic ($paged and $per_page) is repeated across archive templates
- **Solution:** Create a helper to sanitize and compute $paged and $per_page for archive templates, used by eventi/notizie/progetti/etc.
- **Notes:** Reduces duplication and improves consistency

---

### [MEDIUM] Move template `define()` constants to central config
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Multiple `page-templates/*` and `template-parts/*` files declare constants with `define()` at runtime, which risks duplicate-definition warnings and spreads configuration across views.
- **Solution:** Move these constants to `config_lab.php` (or dedicated config file) and reference them from templates.
- **Files affected:** `page-templates/*.php`, `template-parts/common/*.php`, `config_lab.php`
- **Notes:** Keeps templates presentation-only and avoids side effects during rendering.

---

### [MEDIUM] Centralize archive/filter query param parsing
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** GET parameter parsing for filters (`per_page`, categories, years, search terms, taxonomy arrays) is repeated with slight differences across archive templates.
- **Solution:** Introduce shared helpers to parse/sanitize filter inputs and return normalized params for WP_Query builders.
- **Files affected:** `page-templates/blog.php`, `page-templates/eventi.php`, `page-templates/notizie.php`, `page-templates/brevetti.php`, `page-templates/risorse-tecniche.php`, `page-templates/spinoff.php`, `page-templates/progetti.php`, `page-templates/pubblicazioni.php`
- **Notes:** Reduces drift and lowers maintenance cost.

---

### [MEDIUM] Extract shared "card/list item" rendering helpers
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Home and archive templates duplicate logic for title/date/description/image/link rendering with similar wrappers and escaping patterns.
- **Solution:** Add reusable template partials or helper renderers for common card variants (news/event/publication/project/resource).
- **Files affected:** `template-parts/home/*.php`, `template-parts/common/sezione-*.php`, `page-templates/*.php`
- **Notes:** Improves consistency and simplifies future UI changes.

---

### [MEDIUM] Refactor header composition into focused partials/helpers
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** `header.php` contains many responsibilities (language selector, login action, menu bootstrap, search link, branding, options lookup), making changes risky.
- **Solution:** Split into dedicated partials/helpers (`header-brand`, `header-lang-switcher`, `header-auth-link`, `header-search`, `header-nav-shell`) with explicit escaped outputs.
- **Files affected:** `header.php`, `template-parts/header/*.php`
- **Notes:** Improves readability and testability of header behavior.

---

### [MEDIUM] Introduce typed wrapper accessors for option arrays
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** Refactoring
- **Description:** Direct array key access from option payloads (e.g., messages/selectors/meta wrappers) is widespread and brittle when keys are missing.
- **Solution:** Add accessor helpers returning defaults (string/url/bool/list) and replace direct array dereferencing in templates.
- **Files affected:** `template-parts/header/alert.php`, `header.php`, `template-parts/header/metatags.php`, `template-parts/header/seo_tags.php`, `inc/utils.php`
- **Notes:** Reduces warnings and clarifies expected data contracts.

---


## 🎨 CodeStyle

### [MEDIUM] Standardize output escaping strategy with small utility layer
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** CodeStyle
- **Description:** Escaping strategy is inconsistent across templates (mixed `esc_attr`/`esc_html`/raw echo), causing repetitive fixes and review overhead.
- **Solution:** Introduce tiny output helpers (e.g., safe text/url/attr wrappers) and enforce per-context escaping conventions in templates.
- **Files affected:** `header.php`, `template-parts/**/*.php`, `page-templates/**/*.php`
- **Notes:** Primarily a maintainability refactor; also reduces future security regressions.

---

### [LOW] `esc_attr()` used for HTML element content instead of `esc_html()` in `footer.php`
- **Status:** Open
- **Date:** 2026-02-12
- **Category:** CodeStyle
- **Description:** Lines 33-34 use `esc_attr()` to escape text content inside `<h2>` and `<h3>` elements. `esc_attr()` is designed for HTML attribute values, not element content. This can cause double-encoding of special characters (e.g., `&` rendered as `&amp;amp;`).
- **Expected behavior:** Use `esc_html()` for text content inside HTML elements.
- **Files affected:** `footer.php` (lines 33-34)

---


## ⚡ Performance

### [HIGH] Newsletter subscription blocks frontend PHP worker — `wp_remote_post()` to Brevo API has no explicit timeout
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Performance
- **Description:** `Newsletter_Manager::subscribe_user()` calls `wp_remote_post()` to the Brevo API without an explicit `timeout` argument, defaulting to WordPress's 5-second limit. Unlike the cron-based patent importer (already tracked), this call is triggered synchronously during a user-facing newsletter form submission. If the Brevo API is slow or unreachable, the frontend PHP worker is blocked for the full default timeout, degrading user experience and exhausting web server worker capacity under concurrent form submissions.
- **Expected behavior:** Set an explicit `timeout` (e.g., 10 seconds) and optionally a `redirection` limit. Handle timeout/error responses gracefully and display a user-friendly error without exposing internal details.
- **Files affected:** `inc/classes/class-newslettermanager.php` (line 140)

---

### [HIGH] Publications archive performs full dataset scan and per-item meta lookup on each request
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Performance
- **Description:** `pubblicazioni.php` loads all publication IDs with `posts_per_page => -1` to build the year filter, then iterates each ID and calls `get_post_meta()` individually. This creates linear DB and memory cost that grows with content size and slows archive rendering.
- **Expected behavior:** Build year options with cached/aggregated query logic (for example transient-backed year list) and avoid full scans on every page load.
- **Files affected:** `page-templates/pubblicazioni.php` (lines 54-70)

---

### [HIGH] Static CSS/JS are served without compression and with revalidation-heavy cache headers
- **Status:** Open
- **Date:** 2026-02-19
- **Category:** Performance
- **Description:** Homepage probes show large static assets (for example `bootstrap-italia.bundle.min.js` ~1.0 MB and `bootstrap-italia-custom.min.css` ~618 KB) served without `Content-Encoding` (`gzip`/`br`) and with `Cache-Control: no-cache, public, must-revalidate, proxy-revalidate`. This increases transfer cost and recurrent validation work.
- **Expected behavior:** Enable Brotli/Gzip for text assets and serve versioned static CSS/JS with long-lived immutable cache headers (for example `public, max-age=31536000, immutable`).
- **Files affected:** `functions.php` (asset enqueue strategy), web server configuration for static assets

---

### [HIGH] `dli_get_all_place_types_with_results()` runs N+1 uncached WP_Query calls
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** For every place type term, a separate `WP_Query` is executed (N+1 pattern), with no caching. Called from `page-templates/luoghi.php` on every page load.
- **Expected behavior:** Cache via transient, or replace N queries with a single query + PHP grouping.
- **Files affected:** `inc/utils.php` (lines 847-885)

---

### [HIGH] N+1 WP_Query per person category in `persone.php` archive
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** For every `tipologia-persona` term, a separate `WP_Query` is fired with `posts_per_page => -1`. With 5-10 categories, this is 6-11 DB queries on every page load. No caching is applied.
- **Expected behavior:** Load all persons in a single query, group by category in PHP. Alternatively, use transient caching.
- **Files affected:** `page-templates/persone.php` (lines 175-242)

---

### [HIGH] Unthrottled scroll handlers cause layout thrashing in JS
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** Multiple scroll handlers in `laboratori.js` fire on every scroll event without any throttle/debounce. They read `scrollTop()`, `.offset().top`, call `window.matchMedia()`, and perform DOM mutations (`insertAdjacentElement`, `addClass/removeClass`). This forces synchronous layout reflows on every frame (60+ per second during scrolling).
- **Expected behavior:** Wrap in `requestAnimationFrame` or throttle. Use `IntersectionObserver` instead where possible.
- **Files affected:** `assets/js/laboratori.js`

---

### [HIGH] `dli_get_all_categories_by_ct()` loads all post IDs (`numberposts => -1`) before `get_terms()`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** The helper first executes `get_posts( fields => 'ids', numberposts => -1 )` for a full post type and then passes that large ID list to `get_terms( object_ids => ... )`. This pattern scales poorly on large datasets and runs on multiple archive templates, increasing DB and memory cost per request.
- **Expected behavior:** Replace with taxonomy-driven queries (`get_terms` with `hide_empty => true` and proper taxonomy/post-type constraints) or cache normalized category lists with transients.
- **Files affected:** `inc/utils.php` (lines 655-670), `page-templates/blog.php`, `page-templates/notizie.php`, `page-templates/eventi.php`, `page-templates/brevetti.php`, `page-templates/spinoff.php`, `page-templates/risorse-tecniche.php`

---

### [HIGH] Homepage sponsor section uses unbounded query and N+1 uncached metadata calls per item
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Performance
- **Description:** `template-parts/home/hp-sponsor-section.php` runs a WP_Query with `posts_per_page => -1` (loading all sponsors into memory), then inside the foreach loop calls `dli_get_image_metadata()` and `dli_get_field('link_esterno', ...)` per sponsor. `dli_get_image_metadata()` triggers `get_the_post_thumbnail_url()`, `get_post_thumbnail_id()`, `get_the_title()`, `get_post_meta()` for alt text, and `wp_get_attachment_caption()` — approximately 4-5 postmeta queries per sponsor — all uncached across calls. For 10 sponsors, this generates 50+ uncached DB queries on every homepage load, on top of the full dataset load. Similarly, `hp-banners-section.php` issues 4 separate `dli_get_field()` calls per banner inside the loop, adding ~4 postmeta lookups per banner.
- **Expected behavior:** Cap the sponsor query with a reasonable `posts_per_page` limit. Preload postmeta for all items with `update_post_meta_cache` or a single `update_postmeta_cache()` call. Alternatively, use transient caching for the rendered section.
- **Files affected:** `template-parts/home/hp-sponsor-section.php` (lines 12-21, 39-42), `template-parts/home/hp-banners-section.php` (lines 29-34)

---

### [MEDIUM] `get_related_items()` uses `LIKE` on postmeta — full table scan on every call
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** Uses `LIKE '"ID"'` pattern on serialized ACF relationship data in `wp_postmeta`, forcing a full table scan without index usage. On sites with many posts and relationships, this is O(n) per call. Same pattern in `get_projects_by_event_id()`.
- **Expected behavior:** Add transient caching per post ID, or use bi-directional ACF relationship queries.
- **Files affected:** `inc/classes/class-contents-manager.php` (lines 441-458)

---

### [MEDIUM] `get_carousel_items()` and `card-eventi.php` use unbounded `posts_per_page => -1`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** `get_carousel_items()` loads all carousel-eligible posts with no upper limit. `card-eventi.php` loads all promoted events but only uses the first result (`$query->posts[0]`). As content grows, these queries load hundreds of posts into memory unnecessarily.
- **Expected behavior:** Add reasonable `posts_per_page` limits (e.g., 20 for carousel, 1 for card-eventi).
- **Files affected:** `inc/classes/class-contents-manager.php` (lines 461-494), `template-parts/home/card-eventi.php` (line 14)

---

### [MEDIUM] `dli_translate()` queries custom translations DB table on every call without caching
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** Each call to `dli_translate()` executes a `$wpdb->get_var()` query. No in-memory or transient caching is used. On a page with multiple translated strings, this generates multiple DB queries. Additionally, there is no table-existence check — if the table doesn't exist, every call logs a DB error.
- **Expected behavior:** Add static/transient cache keyed by `$text.$domain.$lang`. Check table existence on first call.
- **Files affected:** `inc/utils.php` (lines 1312-1330)

---

### [MEDIUM] `dli_disable_all_comments()` calls `update_option()` on every page load via `after_setup_theme`
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** Hooked to `after_setup_theme` (fires on every request), it reads and potentially writes the `default_comment_status` option on every page load. Should only need to run once.
- **Expected behavior:** Run once at theme activation, or use `pre_option_default_comment_status` filter to override in memory without DB writes.
- **Files affected:** `inc/actions.php` (lines 6-12)

---

### [MEDIUM] Unthrottled `window.resize` handlers (2 handlers) and carousel initialization delays
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** Two separate resize handlers fire without debouncing, running jQuery DOM queries and CSS class mutations continuously during resize drags. Additionally, Splide carousels are initialized inside `setTimeout` with arbitrary 600-800ms delays, causing visible FOUC (Flash of Unstyled Content) before carousel layout takes effect.
- **Expected behavior:** Debounce resize handlers (150-200ms). Use proper initialization triggers (`load` event or font/image readiness) instead of arbitrary timeouts.
- **Files affected:** `assets/js/laboratori.js`

---

### [MEDIUM] `main.js` enqueued in `<head>` without defer — render-blocking
- **Status:** Open
- **Date:** 2026-02-17
- **Category:** Performance
- **Description:** `wp_enqueue_script('dli-main-js', ...)` is called without the `$in_footer = true` argument, so WordPress places it in `<head>` as render-blocking. The script only contains utility functions that don't need to run at parse time. Also, `load_pagination_script()` inlines JS via `wp_footer` hook, bypassing the WordPress asset pipeline (no browser caching, no defer/async, no versioning).
- **Expected behavior:** Add `$in_footer = true` to the enqueue call or add `defer` via `script_loader_tag` filter. Move the pagination script to an enqueued file.
- **Files affected:** `functions.php` (lines 162-163, 189-238)

---


## ♿ Accessibility

### [HIGH] `aria-current="page"` applicato incondizionatamente a tutti i link del menu in `top-menu.php`
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** In `template-parts/header/top-menu.php` (line 44), l'attributo `aria-current="page"` è hardcoded su ogni `<li>` del menu accessorio, indipendentemente dal confronto con l'URL corrente. Di conseguenza tutti i link vengono annunciati dagli screen reader come "pagina corrente", rendendo impossibile orientarsi nella navigazione.
- **Expected behavior:** Applicare `aria-current="page"` condizionalmente solo quando `$dli_active_class === 'active'`, allineando il comportamento agli altri menu del tema.
- **Files affected:** `template-parts/header/top-menu.php` (line 44)

---

### [HIGH] SVG `aria-labelledby` punta a ID inesistenti — pattern difuso in template e single-page
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** In molti template, le icone SVG usano `aria-labelledby="NomeTesto"` dove il valore è una stringa letterale (es. `"Chevron Left"`, `"Burger"`, `"Close"`, `"Expand"`, `"Arrow right"`) ma nessun elemento con quell'`id` esiste nel DOM. `aria-labelledby` richiede un ID di elemento; quando il riferimento è spezzato, i browser/AT si comportano in modo inconsistente (possono ignorarlo o fallback a `aria-label` se presente). Il pattern corretto è aggiungere `id="nome-id"` all'elemento `<title>` interno SVG e referenziarlo, oppure usare solo `aria-label`.
- **Steps to reproduce:**
  1. Ispezionare l'HTML prodotto in qualsiasi pagina single-*.php con DevTools Accessibility panel.
  2. Verificare che `aria-labelledby="Chevron Left"` non risolva ad alcun elemento nel DOM.
- **Expected behavior:** Aggiungere `id` univoci agli elementi `<title>` SVG e referenziarli in `aria-labelledby`, oppure rimuovere `aria-labelledby` e usare solo `aria-label` sull'elemento SVG.
- **Files affected:** `single-evento.php` (line 152), `single-progetto.php` (line 123), `single-persona.php` (line 137), `single-luogo.php` (line 95), `single-indirizzo-di-ricerca.php` (line 86), `header.php` (line 193), `template-parts/header/top-menu.php` (line 15), `page-templates/contatti.php` (lines 144, 160), `page-templates/newsletter.php` (lines 112, 128, 148), `template-parts/header/alert.php` (line 46), `template-parts/common/social.php` (line 76), `template-parts/common/social-footer.php` (line 75)

---

### [MEDIUM] `it-back-button` usa `<a href="#" role="button">` — link non navigabile da tastiera con Spazio
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** I pulsanti "indietro" del navscroll nei template single usano `<a class="it-back-button" href="#" role="button">`. Un elemento `<a>` con `role="button"` viene annunciato come pulsante dagli screen reader, ma il comportamento da tastiera differisce: i link si attivano con Invio ma non con Barra Spaziatrice (comportamento atteso per i pulsanti). Inoltre `href="#"` scorre la pagina in cima quando cliccato senza JS. La soluzione corretta è usare `<button type="button">`.
- **Expected behavior:** Sostituire `<a class="it-back-button" href="#" role="button">` con `<button class="it-back-button" type="button">` in tutti i single template, aggiornando di conseguenza eventuali selettori JS/CSS.
- **Files affected:** `single-evento.php` (line 151), `single-progetto.php` (line 122), `single-persona.php` (line 136), `single-luogo.php` (line 94), `single-indirizzo-di-ricerca.php` (line 85), `single-brevetto.php` (line 118), `single-spinoff.php` (line 115), `single-risorsa-tecnica.php` (line 107)

---

### [MEDIUM] Link footer `href="#"` per sezioni "Contatti" e "Link utili" — link non funzionali
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** I titoli di sezione del footer "Contatti" e "Link utili" sono avvolti in `<a href="#">`, producendo link che non navigano da nessuna parte. Gli screen reader annunciano questi come link attivi ma la loro attivazione non ha effetto (scorre in cima alla pagina). Devono essere risolti puntando alla pagina corretta, oppure rimossi e il titolo reso testo puro.
- **Expected behavior:** Sostituire `href="#"` con l'URL reale della pagina corrispondente (recuperato con `dli_get_option` o `get_permalink`), oppure rimuovere il tag `<a>` lasciando solo il testo nel `<h4>`.
- **Files affected:** `footer.php` (lines 46, 100)

---

### [MEDIUM] Input CAPTCHA nel modulo contatti privo di `<label>` associato
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** Il campo `<input id="captcha-field">` nel form contatti non ha un elemento `<label for="captcha-field">` associato. Solo il `placeholder` comunica lo scopo del campo, ma il placeholder non è accessibile come label (scompare durante la digitazione e non è riconosciuto come etichetta da tutti gli screen reader).
- **Expected behavior:** Aggiungere `<label for="captcha-field">` con testo descrittivo (es. "Codice di conferma") prima dell'input, oppure aggiungere `aria-label` sull'input.
- **Files affected:** `page-templates/contatti.php` (line 291)

---

### [MEDIUM] Progress bar navscroll priva di `aria-label` descrittivo in 8 template single
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** Tutti i template single che includono il navscroll contengono `<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">`. Il `role="progressbar"` richiede un nome accessibile (`aria-label` o `aria-labelledby`); senza di esso gli screen reader annunciano semplicemente "0%" senza contesto, rendendo l'elemento privo di significato.
- **Expected behavior:** Aggiungere `aria-label="Avanzamento nella pagina"` (o equivalente tradotto) a ciascun elemento `.it-navscroll-progressbar`.
- **Files affected:** `single-evento.php` (lines 147, 163), `single-brevetto.php` (lines 114, 129), `single-persona.php` (lines 132, 156), `single-luogo.php` (lines 90, 105), `single-indirizzo-di-ricerca.php` (lines 81, 98), `single-spinoff.php` (lines 110, 126), `single-risorsa-tecnica.php` (lines 100, 133), `single-progetto.php` (lines 117, 142)

---

### [MEDIUM] SVG icona espandi in `top-menu.php` combina `aria-hidden="true"` e `role="img"` in modo contraddittorio
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** In `template-parts/header/top-menu.php` (line 15), il tag SVG dell'icona "espandi" ha contemporaneamente `aria-hidden="true"` (che rimuove l'elemento dall'albero di accessibilità) e `role="img"`, `aria-labelledby="Expand"`, `aria-label="Expand"` (che lo trattano come immagine significativa). Gli attributi sono in conflitto: `aria-hidden="true"` prevale e rende l'icona invisibile all'AT, rendendo `role`, `aria-labelledby` e `aria-label` ininfluenti. Dato che il link genitore contiene già il testo visibile "News e contatti", l'icona decorativa dovrebbe essere solo `aria-hidden="true"` senza altri attributi ARIA.
- **Expected behavior:** Rimuovere `role="img"`, `aria-labelledby` e `aria-label` dall'SVG, lasciando solo `aria-hidden="true"`.
- **Files affected:** `template-parts/header/top-menu.php` (line 15)

---

### [LOW] `<table>` della famiglia brevettuale priva di `<caption>` in `single-brevetto.php`
- **Status:** Open
- **Date:** 2026-03-03
- **Category:** Accessibility
- **Description:** La tabella della "Famiglia brevettuale" in `single-brevetto.php` (line 284) usa `<th scope="col">` correttamente ma manca di `<caption>`. Per WCAG 1.3.1, le tabelle dati devono avere un titolo/descrizione programmabile; il `<caption>` è il metodo preferito. Senza `<caption>`, gli screen reader annunciano la tabella senza contesto identificativo.
- **Expected behavior:** Aggiungere `<caption>Famiglia brevettuale</caption>` come primo figlio del `<table>` (può essere visually-hidden se il design lo richiede, ma deve esistere nel DOM).
- **Files affected:** `single-brevetto.php` (line 284)

---


## ✨ Feature

### [HIGH] Unified editorial dashboard for key content health
- **Status:** Idea
- **Date:** 2026-02-12
- **Category:** Feature
- **Description:** Give administrators one place to see broken links, missing featured images, untranslated posts, and outdated events.
- **Impact area:** Editors, Administrators
- **Technical notes:** Add a custom admin page with widgets fed by `WP_Query` and lightweight validation checks.

---

### [MEDIUM] Content quality assistant in the editor
- **Status:** Idea
- **Date:** 2026-02-12
- **Category:** Feature
- **Description:** Help authors publish higher-quality content with pre-publish checks (missing excerpt, missing alt text, empty SEO fields).
- **Impact area:** Editors
- **Technical notes:** Hook into post save and Gutenberg notices with non-blocking warnings and optional hard rules.

---


## 📖 Documentation

_No missing documentation at the moment_

---


## 📝 Notes

For issue categories, priority levels, templates, and workflow rules see `AI_BEHAVIOR.md` § "Issue Management".

**Project-specific reference:**
Safe date helpers have been implemented: dli_get_datetime_from_format() and dli_format_date_from_format() in inc/utils.php
