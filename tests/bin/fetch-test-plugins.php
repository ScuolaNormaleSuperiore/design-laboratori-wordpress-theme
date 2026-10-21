<?php
/**
 * Downloads the plugins needed by the integration tests.
 *
 * Fetches the latest stable release of each plugin from downloads.wordpress.org
 * (HTTPS) into the cache folder, only when the plugin is missing from it. The
 * version and the SHA-256 of what was downloaded are written to
 * `.versions.json` in the cache folder, so a failing run can be traced to the
 * plugin versions it used. To refresh a plugin, delete its folder in the cache.
 *
 * The cache folder is `DLI_TEST_PLUGINS_DIR` or, by default,
 * `tests/integration/.cache/plugins/` (ignored by Git).
 *
 * Usage: php tests/bin/fetch-test-plugins.php
 *
 * @package Design_Laboratori_Italia
 */

$dli_plugins = array(
	'advanced-custom-fields' => 'acf.php',
	'polylang'               => 'polylang.php',
);

$dli_cache_dir = getenv( 'DLI_TEST_PLUGINS_DIR' );
if ( false === $dli_cache_dir || '' === $dli_cache_dir ) {
	$dli_cache_dir = dirname( __DIR__ ) . '/integration/.cache/plugins';
}
$dli_cache_dir = rtrim( str_replace( '\\', '/', $dli_cache_dir ), '/' );

if ( ! is_dir( $dli_cache_dir ) && ! mkdir( $dli_cache_dir, 0777, true ) && ! is_dir( $dli_cache_dir ) ) {
	fwrite( STDERR, "Cannot create the cache folder: $dli_cache_dir\n" );
	exit( 1 );
}

$dli_versions_file = $dli_cache_dir . '/.versions.json';
$dli_versions      = array();
if ( is_readable( $dli_versions_file ) ) {
	$dli_decoded = json_decode( (string) file_get_contents( $dli_versions_file ), true );
	if ( is_array( $dli_decoded ) ) {
		$dli_versions = $dli_decoded;
	}
}

foreach ( $dli_plugins as $dli_slug => $dli_main_file ) {
	if ( is_file( "$dli_cache_dir/$dli_slug/$dli_main_file" ) ) {
		echo "[cached] $dli_slug\n";
		continue;
	}

	$dli_url = "https://downloads.wordpress.org/plugin/$dli_slug.latest-stable.zip";
	echo "[download] $dli_url\n";

	$dli_context = stream_context_create(
		array(
			'http' => array(
				'timeout'    => 60,
				'user_agent' => 'dli-integration-tests',
			),
		)
	);
	$dli_zip     = @file_get_contents( $dli_url, false, $dli_context );
	if ( false === $dli_zip || '' === $dli_zip ) {
		fwrite( STDERR, "Download failed: $dli_url\n" );
		exit( 1 );
	}

	$dli_tmp = tempnam( sys_get_temp_dir(), 'dli' );
	file_put_contents( $dli_tmp, $dli_zip );

	$dli_archive = new ZipArchive();
	if ( true !== $dli_archive->open( $dli_tmp ) ) {
		unlink( $dli_tmp );
		fwrite( STDERR, "Invalid zip archive for $dli_slug\n" );
		exit( 1 );
	}
	$dli_archive->extractTo( $dli_cache_dir );
	$dli_archive->close();
	unlink( $dli_tmp );

	if ( ! is_file( "$dli_cache_dir/$dli_slug/$dli_main_file" ) ) {
		fwrite( STDERR, "Main file $dli_slug/$dli_main_file not found after extraction\n" );
		exit( 1 );
	}

	$dli_header = (string) file_get_contents( "$dli_cache_dir/$dli_slug/$dli_main_file", false, null, 0, 8192 );
	preg_match( '/^[ \t\/*#@]*Version:\s*(.+)$/mi', $dli_header, $dli_match );

	$dli_versions[ $dli_slug ] = array(
		'version'    => isset( $dli_match[1] ) ? trim( $dli_match[1] ) : 'unknown',
		'sha256'     => hash( 'sha256', $dli_zip ),
		'downloaded' => gmdate( 'c' ),
	);
	echo '           version ' . $dli_versions[ $dli_slug ]['version'] . "\n";
}

file_put_contents( $dli_versions_file, json_encode( $dli_versions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
exit( 0 );
