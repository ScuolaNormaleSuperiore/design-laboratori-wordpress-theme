# AGENTS.md

## Project Overview
Design laboratori e centri di ricerca is a WordPress theme developed by the Scuola Normale Superiore with the aim of creating a website model for research structures (centers and laboratories).
Once installed, the environment provides a set of predefined contents that make the setup of the website simple and fast.
This website allows the publication of all information related to a research structure: affiliated staff organized by structure, publications, research projects, research activities (aggregation of projects), as well as news and events related to the activities.
The project aims to highlight and enhance the activities and research staff of the structures.
It is possible to easily test the system locally by using the Dockerfile provided in the project source code or by using the demo site: https://sitofederato.sns.it.

## Architecture Notes
Design Laboratori Italia is a WordPress theme, but once installed it functions as a fully featured website.
In addition to the theme, a set of custom content types is installed, with fields defined using ACF.
The Home Page includes configurable sections that can be enabled or disabled.
In the back office there is a section to configure content types, site sections, import procedures, and advanced features.
Each content type has a custom view for both the item list and the item detail pages.
The site includes a global search feature and a procedure to initialize the menus, the content-types and the taxonomies.


## Main system features:
	- Automatic population of the website (pages and menus).
	- Section for theme and content configuration.
	- Customization of the home page layout.
	- Management of laboratory personnel.
	- Management of laboratory projects.
	- Management of laboratory news and events.
	- Management of laboratory locations.
	- Management of laboratory publications.
	- Management of research areas.
	- Management of laboratory patents.
	- Management of the laboratory blog.
	- Archive pages with pagination.
	- Search across all site contents.
	- Management of notices on the home page.
	- Cookie management.
	- Contact forms.
	- Site map.
	- Home page hero management.
	- Home page carousel with selectable contents.
	- Management of featured contents on the home page.
	- Management of events, news, and publications sections on the home page.
	- Home page banner management.
	- Support for Italian and English languages.
	- Integration with the Brevo newsletter management software.
	- Import of events from Indico.
	- Import of patents from IRIS.

## Documentation
This is the file with the [Documentation](https://github.com/ScuolaNormaleSuperiore/design-laboratori-wordpress-theme/blob/main/README.md).


## Key Directories
- `inc/`: Contains the main classes and the modules of the theme.
- `template-parts/`: contains snippet of code (sections) shared among two or many pages.
- `page-templates/`: Pages that list the custom content-types defined by this theme.
- `assets/`: Contains the images, the icons, the styles, the JavaScript of the theme. Contains also the BootstrapItalia library used by the theme.
- `DEV/`: Contains the Dockerfile and the files to build a local demo of this site.
- `DOC/`: Contains all the documentation available and the schemes of the system.
- `SETUP/`: Contains the export of the ACF fields and utility scripts.
- `scripts/`: Contains JavaScript scripts used by the theme.

## Main files:
- `functions.php`: Entry point of the theme, it creates DLI_LabManager that registers and activates all the components of the system.
- `config_lab.php`: Constants used by the modules of the theme.
- `config_menu.php`: Contains all the menus defined by default by the theme.
- `config_pages.php`: Contains all the pages defined by default by the theme.
- `single-*`: These are the pages with the details of each content-type.
- `page.php`: This is the template of the Page content-type.
- `phpcs.xml.dist`: The rules used by PHPCS.
- `package.json`: The JSON file that contains the needed libraries and the development tools needed by a developer.
- `LICENSE`: The license file of this product.
- `publiccode.yml`: The file with the description of the project used to publish this project on the "Catalogo del Riuso della PA".


## Code Style
### Guidelines
Act as an expert PHP and WordPress developer.
Write code in compliance with the official WordPress plugin and theme guidelines.
Explain the code you write and the inner workings of WordPress, describing standard practices as we move forward.
While developing this project, I want to learn everything needed to become an expert developer of WordPress core, themes, and plugins.

Some rules to follow:

  * Use tabs, not spaces, and place comments in English at the beginning of each file and before every function.
  * Comments must end with a period ".".
  * Use WordPress naming conventions for classes, files, variables, constants, and functions.
  * Align the assignments as required by WP reules, e.g.:
```
$tipo_risorsa     = dli_get_post_main_category( $post, RT_TYPE_TAXONOMY );
$archive_page_obj = dli_get_page_by_post_type( TECHNICAL_RESOURCE_POST_TYPE );
$archive_page     = $archive_page_obj ? get_permalink( $archive_page_obj->ID ) : '';
```
  * Align consecutive assignments using spaces (not tabs) so the `=` signs line up, e.g.:
```
$items   = array();
$options = get_option( 'polylang' );
```


Pay particular attention to the following aspects when writing code:
- Absence of bugs.
- Absence of vulnerabilities: maximum security.
- Responsive pages (mobile-first design).
- Page accessibility (this point is very important).
- Compliance with WordPress best practices.
- Simple, readable, and modular code.

In any case, be concise but precise in your responses.

### Collaboration Expectations
When you have doubts about what to do, ask before writing code and propose alternatives.
Always suggest the next step in order to quickly achieve the requested goal.
Suggest code refactorings whenever you consider them appropriate.
When modifying the codebase, also review and update documentation and tracking files as needed: AGENTS.md, BUGS.txt, REFACTORS.txt, and CLAUDE.md.


## CHANGELOG AND TODO
This is the file with the [ChangeLog and TODO list](https://github.com/ScuolaNormaleSuperiore/design-laboratori-wordpress-theme/blob/main/CHANGELOG.md).


## Setup
## Commands
## Testing
## Build / Assets
## Deployment
## Dependencies
## Configuration
## Known Issues
- Usage and necessity of `scripts/` is unclear and needs verification.
