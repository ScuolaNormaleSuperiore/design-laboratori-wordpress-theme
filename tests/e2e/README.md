# Functional (E2E) tests

Playwright tests that drive a real browser against a dedicated WordPress site
(Local site `dli-wp-e2e`). They check behaviour; the scanners in `tests/report/`
produce reports and are a different thing.

## Requirements

- A Local site `dli-wp-e2e`, a clean WordPress with the theme, ACF and Polylang
  linked into `wp-content` (junctions to the working copy) and **activated**
  (plugins first, then the theme). Permalinks saved once.
- `DB_HOST` in the site's `wp-config.php` set to the host and port of its MariaDB
  (for example `127.0.0.1:10059`, instead of `localhost`), so that WP-CLI and
  `mysql` started from a normal shell reach the database. The port is the one
  Local assigned to the site and it can change: check it after restarting Local.
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

Read today:

| Variable | Meaning | Default |
|---|---|---|
| `DLI_E2E_BASE_URL` | URL of the E2E site | `https://dli-wp-e2e.local` |
| `DLI_E2E_ADMIN_USER` | administrator user name | `e2e-admin` |
| `DLI_E2E_ADMIN_PASSWORD` | administrator password | `password` |

Planned, for the reset of the site to a baseline (**no script reads them yet**):

| Variable | Meaning | Value on a Local setup |
|---|---|---|
| `DLI_E2E_SITE_PATH` | folder of the WordPress installation of the E2E site | `...\Local Sites\dli-wp-e2e\app\public` |
| `DLI_E2E_WP_CLI_PHAR` | `wp-cli.phar` bundled with Local | `C:\Program Files (x86)\Local\resources\extraResources\bin\wp-cli\wp-cli.phar` |
| `DLI_E2E_PHP_BIN` | PHP used to run WP-CLI | `php` from the `PATH`, or the PHP of the Local site (`...\Local\lightning-services\php-<version>\bin\win64\php.exe`) |
| `DLI_E2E_MYSQL_BIN_DIR` | folder with `mysql` and `mysqldump` | `...\Local\lightning-services\mariadb-<version>\bin\win32\bin` |
| `DLI_E2E_SKIP_RESET` | `1` to skip the reset, for debugging | unset |
| `DLI_E2E_SUPER_EDITOR_USER`, `DLI_E2E_SUPER_EDITOR_PASSWORD` | Super Editor test user | created by the baseline |
| `DLI_E2E_EDITOR_USER`, `DLI_E2E_EDITOR_PASSWORD` | Editor test user | created by the baseline |

The defaults match the test users below, so on a correctly prepared site no variable
is needed. Example of an override (PowerShell):

```
$env:DLI_E2E_ADMIN_PASSWORD = 'another-password'
npm run test:e2e
```

## Test users

The baseline (`fixtures/baseline.sql`) contains these users. The password is the same
for all of them and is deliberately trivial: the site is a local test site that is not
reachable from outside. **Never reuse these credentials on a real site.**

| User | Password | Role | What it is for |
|---|---|---|---|
| `e2e-admin` | `password` | Administrator | everything, including the Configurazione page |
| `e2e-super-editor` | `password` | Super Editor (`dli_super_editor`) | can open the Configurazione page |
| `e2e-editor` | `password` | Editor | cannot open the Configurazione page: the negative side of the permission tests |

## Baseline

`fixtures/baseline.sql` is a dump of the database of `dli-wp-e2e` in a known state:
theme, ACF, ACF OpenStreetMap Field and Polylang active, permalinks `/%postname%/`,
languages `it` and `en`, the 42 pages and menu items created by the theme activation,
the three users above and no other content. It has no real personal data, no
transients and no sessions.

Restore it by hand (PowerShell, from the theme root; `mysql` must be on the `PATH`,
for example the `bin` folder of the MariaDB of Local; use forward slashes in the path
to the SQL file, or `mysql` fails on the backslashes):

```
php "C:\Program Files (x86)\Local\resources\extraResources\bin\wp-cli\wp-cli.phar" --path="<site>\app\public" db import "<theme root>/tests/e2e/fixtures/baseline.sql"
```

Regenerate it, after preparing the site in the state you want:

1. Delete the transients: `wp db query "DELETE FROM wp_options WHERE option_name REGEXP 'transient'"`
   (do not write `%` in the query: `cmd.exe` expands it).
2. Export: `wp db export "<theme root>/tests/e2e/fixtures/baseline.sql" --skip-extended-insert --skip-comments`
   (one row per line, so that a change shows up as a small diff).
3. Check that the file has no real names, emails or local paths.

The automatic restore before each run (`global-setup.js`) does not exist yet.

## Layout

- `specs/`: the test files (`*.spec.js`).
- `support/auth.js`: login helpers.
- `test-results/` and `playwright-report/`: generated output, ignored by Git.

## Status

Only the first two specs exist (home page, administrator login). The baseline
exists and restores correctly by hand, but nothing restores it automatically
between runs yet: do not rely on the content of the site being the same between
runs until `global-setup.js` is in place.
