# CODING_STANDARDS.md


## Purpose

Coding rules for this project.
Write code according to official WordPress standards and project-specific conventions.


## Quality Priorities

- Correctness and robustness.
- Security first (sanitize input, escape output, capability checks, nonces).
- Accessibility (WCAG-oriented decisions).
- Readability and maintainability.
- Compliance with WordPress Coding Standards.


## Accessibility for Italian Public Administration

This is a project for the Italian Public Administration. Accessibility is a
mandatory requirement, not an optional UI enhancement. Every change to public
content, templates, styles, scripts, forms, or administration interfaces must
preserve or improve accessibility.

### Applicable framework

- **Law 9 January 2004, no. 4 (Legge Stanca)** and its implementing measures:
  accessibility obligations for the websites and digital services of public
  bodies.
- **Directive (EU) 2016/2102**: accessibility of public-sector websites and
  mobile applications.
- **AgID Guidelines on the accessibility of IT tools**: technical requirements,
  verification methods, accessibility statement, monitoring, and feedback
  mechanism. Apply the current AgID technical requirements, including the
  applicable WCAG success criteria.
- **AgID Guidelines for the design of PA websites and digital services**:
  mandatory requirements are marked as such in the Guidelines; usability,
  accessible information architecture, clear content, and inclusive design are
  part of compliance.
- When processing personal data, also apply **Regulation (EU) 2016/679
  (GDPR)**, the Italian Privacy Code, and the Garante's rules on cookies and
  tracking tools.

Authoritative references:

- https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4
- https://eur-lex.europa.eu/eli/dir/2016/2102/oj
- https://www.agid.gov.it/it/design-servizi/accessibilita/linee-guida-accessibilita-pa
- https://www.agid.gov.it/it/design-servizi/linee-guida-design-servizi-digitali-pa

The public body that deploys the theme remains responsible for its accessibility
statement, feedback mechanism, content, configuration, and periodic
assessments. Theme changes must make those obligations achievable and must not
introduce barriers.

### Mandatory implementation rules

- Use semantic HTML landmarks and native controls before ARIA. Do not use ARIA
  to compensate for invalid HTML or missing behaviour.
- Ensure every interactive element is keyboard operable, has a visible focus
  state, and does not create a keyboard trap.
- Give controls, form fields, errors, status messages, icons, images, embeds,
  tables, and headings an accessible name or equivalent text alternative where
  required by their purpose.
- Keep heading levels, landmarks, labels, IDs, ARIA relationships, and language
  attributes valid, unique, and coherent across reusable templates.
- Do not communicate information, errors, or state through colour, position,
  sound, or shape alone. Meet the applicable contrast requirements.
- Support browser zoom and narrow viewports without loss of content,
  functionality, focus visibility, or required controls.
- Make dynamic updates understandable to assistive technologies; manage focus
  deliberately for dialogs, disclosures, validation, and asynchronous results.
- Provide captions, transcripts, alternative text, and accessible document
  formats when publishing the corresponding media or documents.
- Prefer tested Bootstrap Italia components and patterns; preserve their
  accessibility behaviour when customizing them.

- Design and implement every new feature so that it is accessible from its
  first release; accessibility remediation must not be deferred by default.
- When a change requires a Bootstrap Italia component, its markup, variants,
  design tokens, or component-specific accessibility guidance, use the Filo
  MCP server (`design-system-italia`) to retrieve the current data before
  implementation. Treat its output as implementation guidance and confirm
  normative requirements against the authoritative AgID sources listed above.

### Pre-merge accessibility checklist

- Check the affected page with keyboard only: navigation, visible focus, menus,
  dialogs, forms, errors, and close/return behaviour.
- Check semantic structure, accessible names, alternative text, heading order,
  language attributes, and ARIA validity.
- Check contrast, text resizing/zoom, reflow, and responsive layouts.
- Run `npm run ux:scan -- <baseUrl>` when a local or test URL is available.
  Automated checks support review but do not replace manual testing.
- For a change that may affect the published accessibility statement or feedback
  mechanism, notify the project owner so the deployed site's assessment and
  declaration can be updated.


## Core PHP/WordPress Rules

### Formatting and documentation

- Use tabs for indentation.
- New comments and docblocks must be in English.
- Legacy comments/docblocks in other languages can remain temporarily; when touching nearby code, prefer incremental migration to English.
- Add a file header docblock.
- Add function docblocks with params/return.

### Naming conventions

- Classes: `Class_Name_With_Underscores`
- Functions: `function_name_with_underscores`
- Variables: `$variable_name_with_underscores`
- Constants: `CONSTANT_NAME_UPPERCASE`
- Files: `file-name-with-hyphens.php`

### Code structure

- Prefer small, focused functions.
- Use early returns to reduce nesting.
- Keep nesting shallow.
- Extract complex conditions into clearly named variables.
- Align consecutive assignments when it improves readability.

### Security

- Sanitize all external input (`sanitize_text_field`, `sanitize_email`, `esc_url_raw`, etc.).
- Escape all output by context (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- Use nonces for state-changing actions.
- Check capabilities (`current_user_can`) before privileged operations.

### WordPress-specific implementation

- Prefer WordPress APIs over raw PHP alternatives.
- Prefix custom functions/classes (project prefix: `dli_`).
- Use `$wpdb->prepare()` for dynamic SQL.
- Enqueue scripts/styles with WordPress enqueue APIs.


## Frontend Standards (HTML, CSS, JS)

Official references:
- HTML: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/html/
- CSS: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/
- JavaScript: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/

### HTML

- Prefer Bootstrap Italia components/patterns before custom structures.
- Keep semantic, valid, well-formed HTML.
- Use lowercase tags/attributes and quote all attribute values.
- Keep mixed PHP/HTML indentation coherent.

### CSS

- Prefer Bootstrap Italia components, patterns, and utility classes before custom CSS.
- Use tabs for indentation.
- One selector/property per line; keep declarations explicit and readable.
- Follow WordPress CSS formatting conventions.
- Prefer shorthand and consistent value style (`0` without units where valid, unitless `line-height` where appropriate).

### JavaScript

- For new features/refactors, prefer Vanilla JavaScript.
- Existing jQuery-based areas are considered legacy and can be maintained when editing those files.
- Do not introduce new jQuery usage in new modules unless explicitly approved by the user.
- Use tabs, semicolons, and braces consistently.
- Prefer `const`/`let` over `var`.
- Use single quotes and descriptive camelCase names.
- Keep lines readable.


## Internationalization (i18n)

- All user-facing strings must be translatable.
- Use the proper escaping i18n helpers (`esc_html__`, `esc_html_e`, etc.).
- Use translator comments for formatted strings.


## Testing and Checks

- Write testable code (small units, clear dependencies).
- Add tests for non-trivial logic when practical.
- Use the canonical commands and prerequisites in the `README.md` section
  *Sviluppo e verifiche*. Run PHP lint after PHP changes and its automatic fix
  command only when needed.
