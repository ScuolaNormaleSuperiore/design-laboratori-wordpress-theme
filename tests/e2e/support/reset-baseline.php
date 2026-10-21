<?php
/**
 * Restores the database of the E2E site from the baseline dump.
 *
 * Needs only PHP with mysqli: no `mysql` client and no WP-CLI. The connection
 * data are read from the `wp-config.php` of the site.
 *
 * Safety check: the import drops and recreates every table, so it is refused
 * unless the `siteurl` host stored in the database of the site is the same as
 * the one in the baseline. This stops the reset from wiping a development site
 * when DLI_E2E_SITE_PATH points to the wrong folder.
 *
 * Usage: php reset-baseline.php <site folder> <baseline.sql>
 *
 * @package Design_Laboratori_Italia
 */

mysqli_report( MYSQLI_REPORT_OFF );

/**
 * Print an error and stop.
 *
 * @param string $message Message.
 */
function dli_fail( $message ) {
	fwrite( STDERR, "reset-baseline: $message\n" );
	exit( 1 );
}

/**
 * Read a string constant defined in wp-config.php.
 *
 * @param string $config Content of wp-config.php.
 * @param string $name   Constant name.
 * @return string
 */
function dli_config_value( $config, $name ) {
	if ( ! preg_match( "/define\(\s*'" . $name . "'\s*,\s*'((?:[^'\\\\]|\\\\.)*)'\s*\)/", $config, $match ) ) {
		dli_fail( "$name not found in wp-config.php" );
	}
	return strtr( $match[1], array( '\\\\' => '\\', "\\'" => "'" ) );
}

if ( $argc < 3 ) {
	dli_fail( 'usage: php reset-baseline.php <site folder> <baseline.sql>' );
}

$site_path   = rtrim( $argv[1], '/\\' );
$sql_file    = $argv[2];
$config_file = $site_path . '/wp-config.php';

if ( ! is_readable( $config_file ) ) {
	dli_fail( "wp-config.php not found in $site_path (check DLI_E2E_SITE_PATH)" );
}
if ( ! is_readable( $sql_file ) ) {
	dli_fail( "baseline not found: $sql_file" );
}

$config = (string) file_get_contents( $config_file );
$sql    = (string) file_get_contents( $sql_file );

$db_name = dli_config_value( $config, 'DB_NAME' );
$db_user = dli_config_value( $config, 'DB_USER' );
$db_pass = dli_config_value( $config, 'DB_PASSWORD' );
$db_host = dli_config_value( $config, 'DB_HOST' );
$prefix  = preg_match( '/\$table_prefix\s*=\s*\'([^\']*)\'/', $config, $match ) ? $match[1] : 'wp_';

// DB_HOST can be "host", "host:port" or "host:/path/to/socket".
$port   = null;
$socket = null;
if ( false !== strpos( $db_host, ':' ) ) {
	list( $db_host, $suffix ) = explode( ':', $db_host, 2 );
	if ( ctype_digit( $suffix ) ) {
		$port = (int) $suffix;
	} else {
		$socket = $suffix;
	}
}

// The baseline must belong to the same table prefix and to the same site.
if ( false === strpos( $sql, "INSERT INTO `{$prefix}options`" ) ) {
	dli_fail( "the baseline has no table {$prefix}options: table prefix mismatch" );
}
if ( ! preg_match( "/'siteurl','([^']*)'/", $sql, $match ) ) {
	dli_fail( 'siteurl not found in the baseline' );
}
$baseline_host = (string) parse_url( $match[1], PHP_URL_HOST );

$db = @new mysqli( $db_host, $db_user, $db_pass, $db_name, $port, $socket );
if ( $db->connect_errno ) {
	dli_fail( 'cannot connect to the database of the site (error ' . $db->connect_errno . '): check DB_HOST and that the site is running' );
}
$db->set_charset( 'utf8mb4' );

$result = $db->query( "SELECT option_value FROM `{$prefix}options` WHERE option_name = 'siteurl'" );
$row    = $result ? $result->fetch_row() : null;
if ( ! $row ) {
	dli_fail( "cannot read siteurl from {$prefix}options: is WordPress installed in this database?" );
}
$current_host = (string) parse_url( $row[0], PHP_URL_HOST );
if ( $current_host !== $baseline_host ) {
	dli_fail( "refusing to import: the database belongs to '$current_host', the baseline to '$baseline_host'" );
}

// Import: the dump has one statement per line and drops each table before creating it.
if ( ! $db->multi_query( $sql ) ) {
	dli_fail( 'import failed: ' . $db->error );
}
do {
	$partial = $db->store_result();
	if ( $partial ) {
		$partial->free();
	}
} while ( $db->more_results() && $db->next_result() );
if ( $db->errno ) {
	dli_fail( 'import failed: ' . $db->error );
}

$tables = $db->query( 'SHOW TABLES' );
echo 'baseline restored (' . ( $tables ? $tables->num_rows : '?' ) . " tables) on $current_host\n";
exit( 0 );
