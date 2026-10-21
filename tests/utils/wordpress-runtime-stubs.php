<?php
/**
 * WordPress runtime constants for PHPStan.
 *
 * WordPress defines these while bootstrapping the application. They are not
 * part of php-stubs/wordpress-stubs because their values depend on each
 * installation. This file is loaded only through PHPStan's scanFiles.
 *
 * @package Design_Laboratori_Italia
 */

define( 'ABSPATH', '' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'DAY_IN_SECONDS', 86400 );
define( 'MB_IN_BYTES', 1048576 );
