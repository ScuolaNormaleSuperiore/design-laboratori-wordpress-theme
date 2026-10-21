# Functional (E2E) tests

Playwright tests that drive a real browser against a dedicated WordPress site
(Local site `dli-wp-e2e`). They check behaviour; the scanners in `tests/report/`
produce reports and are a different thing.

## Requirements

- A Local site `dli-wp-e2e`, a clean WordPress with the theme, ACF and Polylang
  linked into `wp-content` (junctions to the working copy) and **activated**
  (plugins first, then the theme). Permalinks saved once.
- `DB_HOST` in the site's `wp-config.php` set to the host and port of its MariaDB
  (for example `127.0.0.1:10059`, instead of `localhost`), so that the reset script,
  started from a normal shell, reaches the database. The port is the one Local
  assigned to the site and it can change: check it after restarting Local.
- Node.js, `npm install`, and PHP with `mysqli` on the `PATH` (the reset script uses
  it; no `mysql` client and no WP-CLI are needed).
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
| `DLI_E2E_SITE_PATH` | folder of the WordPress installation of the E2E site (the one with `wp-config.php`), for example `C:\Users\<user>\Local Sites\dli-wp-e2e\app\public` | none: **required**, unless the reset is skipped |
| `DLI_E2E_SKIP_RESET` | `1` to skip the reset before and after the run, for debugging a spec | unset |
| `DLI_E2E_BASE_URL` | URL of the E2E site | `https://dli-wp-e2e.local` |
| `DLI_E2E_ADMIN_USER` | administrator user name | `e2e-admin` |
| `DLI_E2E_ADMIN_PASSWORD` | administrator password | `password` |
| `DLI_E2E_SUPER_EDITOR_USER`, `DLI_E2E_SUPER_EDITOR_PASSWORD` | Super Editor user | `e2e-super-editor`, `password` |
| `DLI_E2E_EDITOR_USER`, `DLI_E2E_EDITOR_PASSWORD` | Editor user | `e2e-editor`, `password` |

`DLI_E2E_SITE_PATH` depends on the machine, so set it once as a user environment
variable (on Windows: *System Properties → Environment Variables*, then open a new
terminal). The other defaults match the test users below, so no other variable is
needed on a correctly prepared site. Example of an override (PowerShell):

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

### Automatic restore

`npm run test:e2e` restores the baseline **before and after** the run
(`global-setup.js` and `global-teardown.js`, which call `support/reset.js`, which runs
`support/reset-baseline.php`). The PHP script reads the connection data from the
`wp-config.php` of the site (`DLI_E2E_SITE_PATH`), runs the dump with `mysqli` and drops
and recreates every table. Nothing but PHP is needed.

For safety it refuses to import when the database does not belong to the site of the
baseline: the `siteurl` host stored in the database must be the one in the dump
(`dli-wp-e2e.local`), and the table prefix must be the same. So pointing
`DLI_E2E_SITE_PATH` at a development site does not wipe it, and the site must be
called `dli-wp-e2e`. To debug a spec without the restore, set `DLI_E2E_SKIP_RESET=1`.

To restore it by hand: `php tests/e2e/support/reset-baseline.php <site folder> tests/e2e/fixtures/baseline.sql`.

### Regenerate

Only maintainers regenerate the baseline, after preparing the site in the state they
want. It needs WP-CLI and `mysqldump` (for example the ones bundled with Local; put
the `bin` folder of the MariaDB of Local on the `PATH`):

1. Delete the transients: `wp db query "DELETE FROM wp_options WHERE option_name REGEXP 'transient'"`
   (do not write `%` in the query: `cmd.exe` expands it).
2. Export: `wp db export "<theme root>/tests/e2e/fixtures/baseline.sql" --skip-extended-insert --skip-comments`
   (one row per line, so that a change shows up as a small diff; use forward slashes
   in the path).
3. Mask the institutional addresses that the theme puts in the default Privacy policy
   page (`protocollo@pec.sns.it`, `dpo@sns.it`, `privacy@sns.it`): replace every letter
   and digit with `x` and keep `@` and the dots, **without changing the length** (the
   dump contains serialized data, where the length of each string is written).
4. Check that the file has no real names, emails, IP addresses, tokens or local paths,
   and that every serialized value can still be read.

The dump was made with MariaDB 10.11 (the one of Local).

## Layout

- `specs/`: the test files (`*.spec.js`): `home` (the home page loads), `admin-login`
  (the administrator logs in), `config-permissions` (administrator and Super Editor open
  the Configurazione page, the Editor gets a 403).
- `support/auth.js`: login helpers (`loginAsAdmin`, `loginAsSuperEditor`, `loginAsEditor`).
- `support/reset.js` and `support/reset-baseline.php`: restore of the baseline.
- `global-setup.js` and `global-teardown.js`: run the restore before and after the tests.
- `fixtures/baseline.sql`: the baseline.
- `test-results/` and `playwright-report/`: generated output, ignored by Git.

## Status

Five tests in three specs, all green, with the baseline restored before and after the
run. They are the starting set that proves the infrastructure; the real coverage
(options, people views, search, language) is the next phase. The assertions wait up to
15 s, because login and the first admin page are slow on a local site.
