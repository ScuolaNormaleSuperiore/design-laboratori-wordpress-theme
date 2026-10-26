# Contributing

Thank you for contributing to Design Laboratori Italia. This document explains how to
propose changes that are safe, accessible, and maintainable for organisations
using the theme.

## Code of conduct

Be respectful, constructive, and inclusive in issues, pull requests, and code
review. Focus discussion on the work, welcome different perspectives, and avoid
sharing personal or confidential information.

## Before you start

- Read the [README.md](README.md) and the applicable project documentation.
- For code changes, follow [AGENTS/CODING_STANDARDS.md](AGENTS/CODING_STANDARDS.md),
  especially its WordPress security, internationalisation, and accessibility rules.
- If you use a coding assistant, ask it to read [AGENTS.md](AGENTS.md) before it
  changes the project.
- Keep environment-specific configuration, credentials, personal data, and private
  development notes out of issues, commits, and pull requests.

## Reporting bugs

Open an issue with:

- the theme, WordPress, PHP, and relevant plugin versions;
- clear steps to reproduce the problem;
- expected and actual behaviour;
- relevant, redacted logs or screenshots; and
- the accessibility or user impact, when applicable.

Search existing issues first and do not publish credentials, private URLs, or
exploitable details of a security vulnerability.

## Proposing features

Explain the user or public-administration need, the proposed behaviour, and any
impact on accessibility, translations, performance, or backward compatibility.
Describe alternatives when they help reviewers evaluate the proposal.

## Development workflow

1. Create a branch from `main`; do not commit directly to `main`.
2. Use a focused branch name such as `features/addSearchFilter` or
   `bugfix/fixEventArchive`.
3. Keep the change focused and update documentation when behaviour, architecture,
   or contributor workflow changes.
4. Do not modify third-party directories unless a maintainer explicitly requests it.

See [AGENTS/GIT_WORKFLOW.md](AGENTS/GIT_WORKFLOW.md) for the project branch,
commit, and pull-request conventions.

## Testing and quality checks

Run the checks relevant to your change from the theme root. There are three families:
static checks (`composer`), tests (`composer`) and reports (`npm`):

- `composer run lint:php` and `composer run lint:phpstan` for PHP coding standards and
  static analysis;
- `composer test:unit` for the unit tests (also run by the `pre-commit` hook, which blocks
  the commit when one fails);
- `composer test:integration` when the change depends on WordPress (roles and permissions,
  REST routes, queries, content types), and `composer test:e2e` for what the user sees or
  the admin flows; both need a prepared environment (see below);
- `npm run status:scan`, `npm run html:scan:gate` and `npm run pa11y:scan`, each followed by
  `-- https://your-site.example`, for runtime status, rendered HTML validation and
  accessibility, when you change templates or styles.

The checks that run on every commit (secret scan, whole-theme PHPStan, PHPCS on the staged
files, unit tests, commit-message check) are listed in
[tests/README.md](tests/README.md#cosa-controlla-il-commit); five of the six block the commit.

See [tests/README.md](tests/README.md) for what each family is for and how to run it, and
the README in each suite folder (`tests/unit/`, `tests/integration/`, `tests/e2e/`,
`tests/report/`) for prerequisites and configuration. Tests and reports are not included
in the release archives, only in the cloned repository.

## Pull requests

- Open a pull request from your feature branch to `main`.
- Use a clear title and explain the problem, solution, tests run, and any remaining
  risks.
- Link the related public issue when one exists.
- Include screenshots or recordings for visible interface changes.
- Do not include secrets, `.env` files, private issue-tracker content, personal
  data, or unrelated changes.

## Security vulnerabilities

Do not report a suspected vulnerability in a public issue or pull request. Contact
the maintainers through a private channel agreed for the project; use GitHub
private security reporting if it is enabled. Provide only the information needed
to reproduce and assess the issue, and do not publish exploit details until a fix
has been coordinated.

## License and attribution

By submitting a contribution, you agree that it can be distributed under the
project's [GPL-3.0-or-later license](LICENSE). Preserve applicable copyright,
licence, and third-party attribution notices.
