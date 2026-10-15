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
- `tests/e2e/`: automated quality scanning modules (status, quality, ux, html).


## Main Files

- `functions.php`: theme bootstrap and runtime wiring entrypoint.
  - Loads configuration files: `config-lab.php`, `config-pages.php`, `config-menu.php`.
  - Loads wrappers/utilities/modules: `inc/wrappers_polylang.php`, `inc/wrappers_acf.php`, `inc/utils.php`, `inc/actions.php`, `inc/activation.php`.
  - Loads dependencies/admin tooling: `inc/theme-dependencies.php`, `inc/cmb2.php`, `inc/admin/options.php`.
  - Loads and instantiates runtime managers: `inc/classes/class-contents-manager.php`, `inc/classes/class-labmanager.php` (`DLI_LabManager::plugin_setup()`).
- `config-lab.php`: theme constants.
- `config-menu.php`: default menu definitions.
- `config-pages.php`: default page definitions.
- `single-*.php`: detail templates for content types.
- `page.php`: standard page template.
- `inc/pwa.php`: PWA web app manifest — registers the `/manifest.json` virtual endpoint (same rewrite/query_vars/template_redirect pattern as the sitemap endpoints) and the related `<head>` tags (manifest link, theme-color, apple-touch-icon). No service worker.
- `inc/search-autocomplete.php`: site search autocomplete — opt-in, disabled by default (*WP → Configurazione → Altro → Ricerca nel sito*). Registers the public `GET /wp-json/dli/v1/suggest` REST route (namespace `dli/v1`), cached in transients, and enqueues `assets/js/search-autocomplete.js` on the `page-templates/cerca.php` template. `setup_rest_api()` in `inc/classes/class-labmanager.php` carries an explicit exception so this route stays reachable even when the "Abilita REST API" option is off.
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
| `pubblicazione` | none (`DLI_PUBLICATION_HAS_DETAIL` is false) | `pubblicazioni.php` |
| `banner`, `sponsor`, `tipologia-persona` | none (`public=false`) | none |

- Core `post` (blog) is extended by `Post_Manager`; core `page` by `Page_Manager`.
- Taxonomies: `struttura` (persona), `tipologia-luogo` (luogo), `tipo-pubblicazione`,
  `area-tematica` (brevetto), `settore-attivita` (spinoff), `tipo-risorsa-tecnica`.
  Events, news and posts use the core `category`. `HOLDER_TAXONOMY` is defined in
  `config-lab.php` but not registered.
- ACF field groups are defined in PHP (`acf_add_local_field_group()` in each
  manager). The JSON files in `SETUP/ACF_Custom_Fields/` are exports only and are
  not loaded by code.
- Unified item shape for listings: `DLI_POST_WRAPPER` (`config-lab.php`).


## Runtime Components (`inc/classes/`)

- `DLI_LabManager` (`class-labmanager.php`): singleton; instantiates every manager
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
describes the same checks for contributors.

- PHP lint (canonical): `composer run lint:php`. Automatic fixes, only when
  needed: `composer run lint:php:fix`.
- Static analysis: `composer run lint:phpstan`.
- Runtime scan: `npm run status:scan -- <baseUrl>` (HTTP, PHP and JavaScript
  errors, broken resources, timeouts). Requires Chromium, installed once with
  `npm run status:scan:install-browser`.
- Accessibility and responsive scan: `npm run ux:scan -- <baseUrl>`.
- HTML validity: `npm run html:scan -- <baseUrl>`; requires Java 8+ on `PATH`.
- Static code quality (PHP syntax, PHPCS, PHPStan, Composer and NPM audit):
  `npm run quality:scan` (no URL).
- Each scan has a `*:gate` variant (exit code 1 on failure) and a `*:compare`
  variant (compares the two most recent reports). Options and report formats are
  documented in `tests/e2e/*/README.md`.


## References

- Setup and usage details: see `PROJECT.md`.
- Review method: see `AGENTS/CODE_REVIEW.md`; private issue tracking is optional.
