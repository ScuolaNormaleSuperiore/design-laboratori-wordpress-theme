# Security Policy

## Supported versions

Security fixes are provided for the latest release only, published from the `main` branch.
Please update to the latest version before reporting an issue.

| Version        | Supported |
|----------------|-----------|
| Latest release | Yes       |
| Older releases | No        |

## Reporting a vulnerability

Please do **not** open a public issue or pull request for a security problem.

Report it privately using GitHub private vulnerability reporting: open the
[Security tab](https://github.com/ScuolaNormaleSuperiore/design-laboratori-wordpress-theme/security/advisories/new)
of this repository and select **Report a vulnerability**.

Please include:
- the theme version and the WordPress and PHP versions in use;
- a description of the problem and its potential impact;
- the steps to reproduce it, or a proof of concept;
- whether you intend to disclose it publicly, and when.

Do not include real personal data, credentials or content from a production site in the report.

## What to expect

- Acknowledgement of your report within **5 working days**.
- An assessment and an indication of the next steps within **15 working days**.
- Credit in the changelog once the fix is released, unless you prefer to stay anonymous.

We ask for coordinated disclosure: please give us a reasonable time to release a fix before
publishing details.

## Scope

In scope: the code of this theme (PHP, JavaScript, CSS, templates, bundled assets).

Out of scope, please report them to the respective projects:
- WordPress core and third-party plugins (ACF, Polylang, CMB2, etc.);
- Bootstrap Italia and other bundled libraries, unless the theme uses them in an unsafe way;
- the configuration, hosting and content of a specific website that uses the theme.
