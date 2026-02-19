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
- `DEV/`: local demo/dev environment (Docker-related files).
- `DOC/`: project documentation and schemes.
- `SETUP/`: ACF exports and setup utilities.
- `scripts/`: project JavaScript scripts.
- `AGENTS/`: AI collaboration documentation.


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


## Commands

- `composer run lint:php`
- `composer run lint:php:fix`


## References

- Setup and usage details: see `PROJECT.md`.
- Open technical issues: see `ISSUES_TODO.md`.
