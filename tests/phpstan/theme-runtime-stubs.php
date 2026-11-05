<?php
/**
 * Theme constants whose runtime definitions PHPStan cannot infer.
 *
 * The theme defines these in config-lab.php with define(). The values below
 * model only the types needed by static analysis; they are never loaded by
 * WordPress and live under tests/phpstan/.
 *
 * @package Design_Laboratori_Italia
 */

// The theme root, as plugin_dir_path( __FILE__ ) gives in config-lab.php. It must be a real path:
// an empty string makes PHPStan check the file "/inc/classes/..." in require_once, which is an
// absolute path on Linux (where the CI runs) and does not exist.
define( 'DLI_THEMA_PATH', __DIR__ . '/../..' );
define( 'DLI_POST_PER_PAGE_VALUES_COMBINED', array() );
