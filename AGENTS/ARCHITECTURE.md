# ARCHITECTURE.md

## Architecture Notes

Design Laboratori Italia is a WordPress theme, but once installed it functions as a fully featured website.

In addition to the theme, a set of custom content types is installed, with fields defined using ACF (Advanced Custom Fields).

The Home Page includes configurable sections that can be enabled or disabled.

In the back office there is a section to configure content types, site sections, import procedures, and advanced features.

Each content type has a custom view for both the item list and the item detail pages.

The site includes a global search feature and a procedure to initialize the menus, the content-types and the taxonomies.

## Key Directories

**`inc/`**
Contains the main classes and the modules of the theme.

**`template-parts/`**
Contains snippet of code (sections) shared among two or many pages.

**`page-templates/`**
Pages that list the custom content-types defined by this theme.

**`assets/`**
Contains the images, the icons, the styles, the JavaScript of the theme. Contains also the BootstrapItalia library used by the theme.

**`DEV/`**
Contains the Dockerfile and the files to build a local demo of this site.

**`DOC/`**
Contains all the documentation available and the schemes of the system.

**`SETUP/`**
Contains the export of the ACF fields and utility scripts.

**`scripts/`**
Contains JavaScript scripts used by the theme.

**`AGENTS/`**
Contains AI assistant documentation and configuration files.

## Main Files

**`functions.php`**
Entry point of the theme, it creates `DLI_LabManager` that registers and activates all the components of the system.

**`config_lab.php`**
Constants used by the modules of the theme.

**`config_menu.php`**
Contains all the menus defined by default by the theme.

**`config_pages.php`**
Contains all the pages defined by default by the theme.

**`single-*`**
These are the pages with the details of each content-type.

**`page.php`**
This is the template of the Page content-type.

**`phpcs.xml.dist`**
The rules used by PHPCS (PHP Code Sniffer).

**`package.json`**
The JSON file that contains the needed libraries and the development tools needed by a developer.

**`LICENSE`**
The license file of this product.

**`publiccode.yml`**
The file with the description of the project used to publish this project on the "Catalogo del Riuso della PA".

## Technology Stack

- **Platform:** WordPress
- **Language:** PHP
- **Custom Fields:** Advanced Custom Fields (ACF)
- **Frontend Framework:** Bootstrap Italia
- **Build Tools:** See `package.json` for details
- **Code Quality:** PHPCS for PHP code style checking

## Setup and Configuration

Detailed setup instructions are available in the main documentation (see PROJECT.md for links).

## Commands

- `composer lint` — Check code style
- `composer lint:fix` — Auto-fix code style
- `composer test:unit` — Run unit tests
- `composer test:integration` — Run integration tests
- `composer test:all` — Run all tests

## Known Issues

**Usage and necessity of `scripts/` is unclear and needs verification.**

For a complete list of issues and bugs, see `ISSUES_TODO.md`.

---

## Notes for Adapting to Other Projects

When using this documentation structure for other projects, replace the sections above with:

- **Architecture Notes:** High-level description of the system architecture
- **Key Directories:** List of main directories with their purposes
- **Main Files:** Important files with brief descriptions
- **Technology Stack:** List of technologies, frameworks, and libraries used
- **Setup and Configuration:** Basic setup information or links to detailed guides
- **Commands:** Available CLI commands for development tasks
