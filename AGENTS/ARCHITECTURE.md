# ARCHITECTURE.md


## Architecture Notes

Design Laboratori Italia is a WordPress theme that behaves as a complete site once installed.
It provides custom post types (with ACF fields), configurable homepage sections, content import procedures, and custom templates for list/detail views.
Core runtime features include menu/taxonomy/page initialization and global search.


## Key Directories

- `inc/`: core classes and main modules.
- `template-parts/`: reusable template partials.
- `page-templates/`: archive/list templates for custom content types.
- `assets/`: styles, scripts, icons, images, Bootstrap Italia assets.
- `DOC/`: project documentation and schemes.
- `SETUP/`: ACF exports, Docker setup, and setup utilities.
- `SETUP/npm_scripts/`: project JavaScript scripts.
- `AGENTS/`: AI collaboration documentation.
- `.github/workflows/scorecard.yml`: GitHub Action settimanale che osserva la postura di sicurezza della supply chain del repository e pubblica il report in GitHub Code Scanning.
- `tests/report/`: Report modules that produce results to read, without assertions (status, html, pa11y); `issues-report/` only stores issue snapshots.
- `tests/unit/`, `tests/bootstrap-unit.php`, `phpunit.xml`: PHPUnit unit tests (no WordPress). WordPress core stubs for PHPStan are supplied by the `php-stubs/wordpress-stubs` development dependency; `tests/phpstan/` holds local runtime and optional-plugin stubs.
- `tests/integration/`, `tests/bootstrap-integration.php`, `phpunit-integration.xml`: PHPUnit integration tests on a real WordPress with ACF and Polylang; its `support/` folder prepares the required plugins.
- `tests/e2e/`, `playwright.config.js`: Playwright functional tests on the dedicated Local site `dli-wp-e2e`, with a database baseline restored before and after each run.


## Main Files

- `functions.php`: theme bootstrap and runtime wiring entrypoint.
  - Loads configuration files: `config-lab.php`, `config-pages.php`, `config-menu.php`.
  - Loads wrappers/utilities/modules: `inc/wrappers_polylang.php`, `inc/wrappers_acf.php`, `inc/utils.php`, `inc/actions.php`, `inc/activation.php`.
  - Loads dependencies/admin tooling: `inc/theme-dependencies.php`, `inc/cmb2.php`, `inc/admin/options.php`.
  - Loads and instantiates runtime managers: `inc/classes/class-dli-contentsmanager.php` (with `class-dli-og-wrapper.php`, the Open Graph value object), `inc/classes/class-dli-labmanager.php` (`DLI_LabManager::plugin_setup()`).
- `config-lab.php`: theme constants.
- `config-menu.php`: default menu definitions.
- `config-pages.php`: default page definitions.
- `single-*.php`: detail templates for content types.
- `page.php`: standard page template.
- `inc/pwa.php`: PWA web app manifest — registers the `/manifest.json` virtual endpoint (same rewrite/query_vars/template_redirect pattern as the sitemap endpoints) and the related `<head>` tags (manifest link, theme-color, apple-touch-icon). No service worker.
- `inc/captcha.php`: optional Really Simple CAPTCHA integration — provides explicit CAPTCHA challenge data and validation helpers for the Contact and Newsletter templates.
- `inc/search-autocomplete.php`: site search autocomplete — opt-in, disabled by default (*WP → Configurazione → Altro → Ricerca nel sito*). Registers the public `GET /wp-json/dli/v1/suggest` REST route (namespace `dli/v1`), cached in transients, and enqueues `assets/js/search-autocomplete.js` on the `page-templates/cerca.php` template. `setup_rest_api()` in `inc/classes/class-labmanager.php` carries an explicit exception so this route stays reachable even when the "Abilita REST API" option is off.
- `inc/admin/class-dli-example-content.php`: `DLI_Example_Content` creates (`create( $languages, $author )`) and removes (`remove()`) the example section "Research Data Management" (a top page with four subpages and their own subpages, lorem ipsum of different length), in Italian, English or both; the versions are linked as translations (`dli_save_post_translations()`), an address used by a page of another language stops everything before writing, a rerun creates nothing, every page has the meta `_dli_example_content` and `remove()` deletes only those. The top page and its four subpages (`promote` in the tree) get the field `promuovi_in_home`, so that the section "Card di navigazione" has pages to show; running the tool again turns the promotion on for the pages that an earlier run created without it, and changes nothing else. It is used by the box "Pagine di esempio" of Strumenti DLI → Utilità (`DLI_Tools_Admin::render_example_content_tool()`, form `POST` to `admin-post.php` with the nonce `dli_example_content`, capability `edit_theme_options`, confirmation in the browser; the outcome is shown once through a transient of one minute).
- `inc/home-sections.php`: pure helpers (no WordPress needed) of the homepage sections configured in Configurazione → Sezioni HP. `dli_normalize_hp_section_rows()` makes the saved rows safe: a row that is not an array, or without a section id, is left out; `enabled` and `show_title` are `true` only when saved as `true`; a `background` that is not `white` or `muted` is dropped; other properties are kept; every row carries `instance`, its position in the saved list. `DLI_ContentsManager::get_hp_section_options()` uses it and never writes the options back; `home.php` passes the whole row to the template part. `dli_get_hp_section_background_class( $args, $default )` gives ` section-muted` or nothing (the choice of the row wins, otherwise the default of the section); `dli_sanitize_hp_background()` is the server-side sanitization of the field "Sfondo sezione", shown only for the sections of the catalog (`DLI_HP_SECTIONS`) that have the key `background` (`DLI_ContentsManager::get_hp_sections_with_background()`; none yet). The admin of the page (`inc/admin-js/options.js`, `inc/admin-css/style-admin.css`) puts the up/down arrows that CMB2 creates for the sortable group in the header of each row and the name of the section in its title, using only the public events of CMB2 (`cmb2_add_row`, `cmb2_shift_rows_*`) and no file under `inc/vendor/`.
- `template-parts/home/hp-navigation-section.php` is the homepage section "Card di navigazione" (catalog id `navigation_section`, default background `muted`): it lists up to `max_items` published pages (the number chosen in the row of Sezioni HP, between `DLI_HP_ITEMS_MIN` and `DLI_HP_ITEMS_MAX`; `DLI_HP_NAVIGATION_DEFAULT_ITEMS` (6) when not chosen; the field "Numero massimo di elementi" is shown for the sections of the catalog with the key `max_items`, see `DLI_ContentsManager::get_hp_sections_with_max_items()`, and is checked by `dli_normalize_hp_max_items()` and `dli_sanitize_hp_max_items()`)  of the current language with the field `promuovi_in_home` on, ordered by `menu_order` (the field "Ordine" of the page) and then by title, with `dli_get_promoted_page_cards()`; protected pages are left out before counting and nothing is printed when no page is promoted. Each page is a `template-parts/common/card-nav.php` card in a column chosen by `dli_get_hp_navigation_column_class()` (the last row is centered). Title and subtitle come from Layout HP (`home_navigazione_title`, `home_navigazione_text` and their `_eng` versions); identifiers use the position of the row (`blocco-navigazione-<instance>`).
- `template-parts/home/hp-fasi-section.php` is the homepage section "Navigazione per pagine" (catalog id `fasi_section`, default background `muted`): the groups are the published pages of the current language with the field `promuovi_in_home` on that have published subpages (`dli_get_promoted_page_groups( $max_groups, $max_items )` in `inc/pages.php`; the same shared flag as "Card di navigazione"); groups and subpages are ordered by `menu_order` and then by title, the limits are applied after that, protected and draft pages are left out before counting, and the menus of the site are not read. The numbers come from the row of Sezioni HP (`max_items` groups, `max_subitems` per group, between `DLI_HP_ITEMS_MIN` and `DLI_HP_ITEMS_MAX`; defaults `DLI_HP_FASI_DEFAULT_ITEMS` and `DLI_HP_FASI_DEFAULT_SUBITEMS`, 4 each; see `DLI_ContentsManager::get_hp_sections_with_max_subitems()`). The title of a group is plain text. The same list of links (`template-parts/common/page-link-list.php`) is printed in columns (`d-none d-md-flex`, class from `dli_get_hp_groups_column_class()`) and in an accordion (`d-md-none`, first panel open); the identifiers use the position of the row and the page of the group (`blocco-fasi-<instance>`, `fasi-<instance>-accordion`, `-head-`, `-body-`, `-title-<page>`). Title and subtitle: Layout HP (`home_fasi_title`, `home_fasi_text` and their `_eng` versions).
- `inc/pages.php`: helpers of the base pages. `dli_get_subpage_cards()` returns the direct subpages that can be listed as navigation cards: published, in menu order, without the password-protected ones; the abstract is the page summary (`DLI_Page_Manager` enables the excerpt for pages) or the beginning of the text without tags, blocks and shortcodes, cut at `DLI_PAGE_CARD_EXCERPT_WORDS` (`config-lab.php`). `page.php` prints them before the text, under the visible title «In questa sezione» (`aria-labelledby`), with the partial `template-parts/common/card-nav.php`, only when the page has the `mostra_sottopagine_a_card` field on and is not locked by its own password. `DLI_Page_Manager` also defines `promuovi_in_home` (used by the home sections that list pages).
- `inc/publications.php`: helpers of the publications shared by the detail page, the archive and the home cards. `dli_get_publication_authors()` returns the authors that can be shown (published persons with a name, with the link to their page unless the detail page is disabled). `dli_get_publication_link()` is the single place that decides where the title of a publication leads (the `link_dettaglio` field, like events and news: `scheda` / `sitoweb` = the `url` field / `allegato`; a password-protected publication always leads to its detail page) and whether the address leaves the site; it relies on `dli_manage_item_link()` (`inc/utils.php`), which reads `url` for publications and `sitoweb` for events and news. The publication wrapper (`dli_from_publication_to_wrapped_item()`) carries `link` and `link_external` and has no description or content for a protected publication. The title is printed by the partial `template-parts/common/linked-title.php` (plain text when there is no address, never an empty `href`; a link that leaves the site opens in a new window and says so). The detail address (`/pubblicazione/<slug>/`) is registered by `DLI_Publication_Manager`, which rewrites the rewrite rules once per `REWRITE_RULES_VERSION` (option `dli_publication_rules_version`) so that a site updating the theme needs no manual permalink save.
- `phpcs.xml.dist`: PHP CodeSniffer rules.
- `package.json`: frontend/development tooling dependencies.
- `publiccode.yml`: public administration reuse catalog metadata.


## Content Model

All custom post types have `has_archive=false`: listings are WordPress pages
with a page template, resolved through `DLI_PAGE_PER_CT` and
`dli_get_template_page_url()`. Slugs are constants in `config-lab.php`. Each CPT
is registered by its manager in `inc/classes/` (CPT, taxonomy and ACF field
group together), instantiated by `DLI_LabManager::plugin_setup()`.

| Post type | Detail template | Listing template (`page-templates/`) |
|---|---|---|
| `persona`, `progetto`, `evento`, `notizia`, `brevetto`, `luogo`, `spinoff`, `risorsa-tecnica`, `indirizzo-di-ricerca` | `single-<slug>.php` | `persone`, `progetti`, `eventi`, `notizie`, `brevetti`, `luoghi`, `spinoff`, `risorse-tecniche`, `ricerca` |
| `pubblicazione` | `single-pubblicazione.php` | `pubblicazioni.php` |
| `banner`, `sponsor`, `tipologia-persona` | none (`public=false`) | none |

- Core `post` (blog) is extended by `Post_Manager`; core `page` by `DLI_Page_Manager`.
- Taxonomies: `struttura` (persona), `tipologia-luogo` (luogo), `tipo-pubblicazione`,
  `area-tematica` (brevetto), `settore-attivita` (spinoff), `tipo-risorsa-tecnica`.
  Events, news and posts use the core `category`. `HOLDER_TAXONOMY` is defined in
  `config-lab.php` but not registered.
- ACF field groups are defined in PHP (`acf_add_local_field_group()` in each
  manager). The JSON files in `SETUP/ACF_Custom_Fields/` are exports only and are
  not loaded by code.
- Unified item shape for listings: `DLI_POST_WRAPPER` (`config-lab.php`).


## Runtime Components (`inc/classes/`)

- `DLI_LabManager` (`class-dli-labmanager.php`): singleton; instantiates every manager
  and importer, sets languages, image sizes, upload limits and permalinks, filters
  REST access (`setup_rest_api`) and applies security tweaks (no generator, no XML-RPC).
- `DLI_AuthorizationManager`: role `dli_super_editor` (Editor plus
  `edit_theme_options` and `dli_edit_site_configuration`).
- `DLI_PolylangManager`: which post types and taxonomies Polylang translates.
- One manager per content type (events, news, patents, people, people types,
  places, projects, publications, research activities, spin-offs, sponsors,
  technical resources, banners, posts, pages).
- `DLI_ContentsManager`: static query helpers, site search, homepage section
  helpers, Open Graph data.
- Public email and PEC addresses are sanitized and obfuscated with
  `antispambot()` before template output; the people table omits email data
  entirely when its email column is disabled.
- `Newsletter_Manager`: Brevo newsletter subscription and validation.
- Importers: `DLI_BaseImporter` (abstract), `DLI_IndicoImporter` (events),
  `DLI_IrisPatentImporter` (patents); `inc/admin/class-dli-people-file-importer.php`
  is a separate CSV people import with dry-run.


## Conventions and Intentional Patterns

- Polylang wrappers (`inc/wrappers_polylang.php`): always call `dli_*` wrappers,
  never `pll_*` directly. Each is guarded by `function_exists()`; without Polylang
  readers fall back to `DLI_DEFAULT_LANGUAGE` and writers do nothing. The theme
  does not activate without Polylang.
- ACF wrappers (`inc/wrappers_acf.php`): use `dli_get_field()` / `dli_update_field()`.
- Options: `dli_get_option( $field, $option_key )` (CMB2 options page
  "Configurazione", base key `dli_options`); booleans are the strings `'true'` /
  `'false'`; `dli_get_option_by_lang()` appends `_eng` for non-Italian.
- Helpers in `inc/utils.php` are guarded by `function_exists()`; this is intentional.
- Class naming is mixed (`DLI_*Manager` and `Event_Manager`-style); follow the
  existing name in the file you edit.
- Italian is the default language; the English version is a separate
  Polylang-linked post.


## Public Endpoints and Hooks

- REST `GET dli/v1/suggest` (search autocomplete; opt-in).
- REST `POST custom/v1/indico-import` and `POST custom/v1/iris-patent-import`:
  registered only when `rest_api_enabled` is `'true'`; HTTP Basic auth and
  `manage_options` required.
- Rewrites: `/sitemap.xml`, `/sitemap-it.xml`, `/sitemap-en.xml` (`inc/sitemap.php`)
  and `/manifest.json` (`inc/pwa.php`).
- WP-Cron: `dli_indico_import_job`, `dli_iris_patent_import_job`.
- The theme registers no shortcodes and no AJAX handlers; there are no WP-CLI commands.
- Activation (`after_switch_theme`, `inc/activation.php`): creates default pages,
  taxonomy terms, IT/EN menus and custom tables.


## Configuration and Caching

- Constants in `config-lab.php`: languages, slugs per content type and language,
  upload limits, pagination, roles/capabilities, `SUGGESTED_PLUGINS`,
  `DLI_ITEM_LINK`, `DLI_HP_SECTIONS` (homepage sections; enabling and ordering
  from the `homepage_sections` option).
- Importer settings live in the option groups `indico` and `iris`; never print
  or log their credentials.
- Caching: transients only, 24h safety TTL, invalidated on `save_post` and
  `save_post_luogo`; `dli_suggest_<md5>` uses the configured `cache_minutes`.
  Details in `DOC/HowTo/Transient e caching nel tema.md`.


## Technology Stack

- Platform: WordPress
- Language: PHP
- Custom fields: ACF
- Frontend framework: Bootstrap Italia
- Build/tooling: see `package.json`
- Code quality: PHPCS


## Quality checks

This is the canonical list of quality-check commands for AI agents. Run them
from the theme root. The human-facing `README.md` (*Sviluppo e verifiche*)
and `tests/README.md` describe the same checks for contributors. Checks come in
three families: static checks and tests (run with Composer) and Reports (run with npm).

- PHP lint (canonical): `composer run lint:php`. Automatic fixes, only when
  needed: `composer run lint:php:fix`. The commit gate also runs PHPCS on the staged PHP,
  CSS and SCSS files (`.githooks/check-staged-phpcs.sh`, errors only, staged content) and
  blocks the commit on any error, including errors that were already in the file.
- Static analysis: `composer run lint:phpstan` runs the whole theme at the required
  level 4 (`phpstan.neon`). It is also the commit gate: `.githooks/pre-commit` runs it
  through `.githooks/check-phpstan-level4.sh` and blocks the commit on any error.
  `composer run lint:phpstan:level5` is the non-blocking whole-theme progress check
  toward level 5.
- Unit tests: `composer test:unit` (PHPUnit, no WordPress; also run by `.githooks/pre-commit`).
- Integration tests: `composer test:integration` (real WordPress, ACF and Polylang; database `dli_wordpress_test`; the first run downloads the plugins into the ignored `tests/integration/.cache/`; not run by the hook).
- E2E tests: `composer test:e2e` (also `test:e2e:headed` and `test:e2e:ui`; Playwright on the Local site `dli-wp-e2e`; needs `DLI_E2E_SITE_PATH`; restores the site to its baseline before and after the run; not run by the hook).
- All tests: `composer test:all` runs the unit, integration and E2E suites in this order and stops at the first one that fails; it needs the setup of all three (not run by the hook). `composer.json` sets `config.process-timeout` to 1200 seconds: Composer's default of 300 seconds stops the E2E suite as soon as it takes longer, and that suite grows with every batch of the porting.
- Runtime scan: `npm run status:scan -- <baseUrl>` (HTTP, PHP and JavaScript
  errors, broken resources, timeouts). Requires Chromium, installed once with
  `npm run status:scan:install-browser`.
- Accessibility scan (WCAG 2 AA, desktop and 320 px): `npm run pa11y:scan -- <baseUrl>`.
- HTML validity: `npm run html:scan -- <baseUrl>`; requires Java 8+ on `PATH`.
- Dependency vulnerabilities: `composer audit` and `npm audit --omit=dev`.
- OpenSSF Scorecard runs remotely on GitHub after changes to the default branch, branch-protection changes and on a weekly schedule. It is observational: it does not run locally and does not block commits.
- `status` and `html` have a `*:gate` variant (exit code 1 on failure) and a
  `*:compare` variant (compares the two most recent reports); `pa11y:scan:gate`
  exits with code 2 when WCAG errors exceed the threshold. Options and report
  formats are documented in `tests/report/*/README.md`.


## References

- Setup and usage details: see `PROJECT.md`.
- Review method: see `AGENTS/CODE_REVIEW.md`; private issue tracking is optional.
