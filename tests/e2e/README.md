# Functional (E2E) tests

Playwright tests that drive a real browser against a dedicated WordPress site
(Local site `dli-wp-e2e`). They check behaviour; the scanners in `tests/report/`
produce reports and are a different thing.

## Requirements

- A Local site `dli-wp-e2e`, a clean WordPress with the theme, ACF and Polylang
  linked into `wp-content` (junctions to the working copy) and **activated**
  (plugins first, then the theme). Permalinks saved once.
- Node.js and `npm install`.
- Chromium for the installed Playwright version, installed once with
  `npm run status:scan:install-browser` (the same browser the scanners use).

## Run

```
npm run test:e2e           # headless
npm run test:e2e:headed    # visible browser
npm run test:e2e:ui        # Playwright UI mode
```

## Environment variables

| Variable | Meaning | Default |
|---|---|---|
| `DLI_E2E_BASE_URL` | URL of the E2E site | `https://dli-wp-e2e.local` |
| `DLI_E2E_ADMIN_USER` | administrator user name | none (required by the login specs) |
| `DLI_E2E_ADMIN_PASSWORD` | administrator password | none (required by the login specs) |

Credentials are only read from the environment: never write them in a file of
the repository. Example (PowerShell):

```
$env:DLI_E2E_ADMIN_USER = 'admin'
$env:DLI_E2E_ADMIN_PASSWORD = '...'
npm run test:e2e
```

## Layout

- `specs/`: the test files (`*.spec.js`).
- `support/auth.js`: login helpers.
- `test-results/` and `playwright-report/`: generated output, ignored by Git.

## Status

Only the first two specs exist (home page, administrator login). The reset of
the site to a known baseline between runs (WP-CLI and `baseline.sql`) is not in
place yet: do not rely on the content of the site being the same between runs.
