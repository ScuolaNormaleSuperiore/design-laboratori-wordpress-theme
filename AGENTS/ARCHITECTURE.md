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


## Technology Stack

- Platform: WordPress
- Language: PHP
- Custom fields: ACF
- Frontend framework: Bootstrap Italia
- Build/tooling: see `package.json`
- Code quality: PHPCS


## Quality checks

The canonical commands, prerequisites, `*:gate` variants, and `*:compare`
variants are maintained in the *Sviluppo e verifiche* section of `README.md`.


## References

- Setup and usage details: see `PROJECT.md`.
- Review method: see `AGENTS/CODE_REVIEW.md`; private issue tracking is optional.
