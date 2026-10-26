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

define( 'DLI_THEMA_PATH', '' );
define( 'DLI_POST_PER_PAGE_VALUES_COMBINED', array() );
