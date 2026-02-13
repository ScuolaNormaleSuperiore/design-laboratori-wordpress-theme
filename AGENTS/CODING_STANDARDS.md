# CODING_STANDARDS.md

## Purpose

Coding rules for this project.
Write code according to official WordPress standards and project-specific conventions.

## Learning Support Requirement

When useful, explain the theory behind choices (WordPress internals, security, architecture, standards), especially if the user shows knowledge gaps or asks for deeper understanding.
Keep explanations practical and tied to the current code.

## Quality Priorities

- Correctness and robustness.
- Security first (sanitize input, escape output, capability checks, nonces).
- Accessibility (WCAG-oriented decisions).
- Readability and maintainability.
- Compliance with WordPress Coding Standards.

## Core PHP/WordPress Rules

### Formatting and documentation

- Use tabs for indentation.
- Comments and docblocks must be in English.
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

- Write Vanilla JavaScript only, unless the user explicitly requests and approves another library/framework.
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
- Run:
  - `npm run lint:php`
  - `npm run lint:php:fix` (when needed)
