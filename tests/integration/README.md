# Integration tests

PHPUnit tests that run against a real WordPress (the test framework of
`wordpress-develop`), with the real ACF and Polylang plugins and the theme
active. They need a dedicated database: the framework drops and recreates its
tables (prefix `wptests_`) on every run.

## Requirements

- PHP 8.0 or later with `mysqli`, `zip` and `openssl`, and Composer (`composer install`).
- A checkout of `wordpress-develop` (default `C:/WordpressDEV/wordpress-develop`).
- An **empty** database used only by the tests. Never the database of a site.

  ```sql
  CREATE DATABASE IF NOT EXISTS dli_wordpress_test
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
  ```

- `wp-tests-config.php` in the root of `wordpress-develop` (start from
  `wp-tests-config-sample.php`), with `DB_NAME` set to the test database, the
  host and port of your MySQL/MariaDB and `$table_prefix = 'wptests_';`.

## Run

```
composer test:integration
```

The command first runs `tests/utils/fetch-test-plugins.php`, then PHPUnit with
`phpunit-integration.xml`.

## Plugins

ACF and Polylang are downloaded from `downloads.wordpress.org` the first time
the tests run, only when they are missing, in `tests/integration/.cache/plugins/`
(ignored by Git, never part of the release package). The version actually
downloaded is the latest stable one and is recorded in `.versions.json` in the
same folder. To refresh a plugin, delete its folder from the cache.

## Environment variables

| Variable | Meaning | Default |
|---|---|---|
| `WP_TESTS_DIR` | `tests/phpunit` folder of `wordpress-develop` | `C:/WordpressDEV/wordpress-develop/tests/phpunit` |
| `WP_TESTS_CONFIG_FILE_PATH` | alternative `wp-tests-config.php` | the one in the root of `wordpress-develop` |
| `DLI_TEST_PLUGINS_DIR` | folder with the plugins to load | `tests/integration/.cache/plugins` |

## Notes

- Polylang loads its API only when it has a context; with an empty database it
  has none, so the bootstrap forces `PLL_Frontend` (`pll_context` filter).
- The default pages are not created on activation in the test framework: call
  `do_action( 'after_switch_theme' )` in the test that needs them.
- The Super Editor role is created on `init`, not on theme activation.
- Every test runs in a transaction that is rolled back at the end.
- Extend `DLI_Integration_Test_Case` (`tests/integration/support/`): it creates the
  Italian language, which Polylang needs for any query by language, and offers
  `create_post()` and `set_theme_options()`.
- CMB2 keeps the options it has read in memory for the whole process: change
  theme options with `set_theme_options()`, not with `update_option()`.
