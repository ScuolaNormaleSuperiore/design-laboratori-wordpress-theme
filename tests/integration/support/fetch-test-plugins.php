<?php
/**
 * Downloads the plugins needed by the integration tests.
 *
 * Fetches each plugin from downloads.wordpress.org (HTTPS) into the cache
 * folder, only when the plugin is missing from it. By default it is the latest
 * stable release. A version can be pinned with `DLI_ACF_VERSION` and
 * `DLI_POLYLANG_VERSION` (a dotted number such as `6.8.10`): then exactly that
 * release is downloaded, and a cached copy of another version is replaced. The
 * version and the SHA-256 of what was downloaded are written to
 * `.versions.json` in the cache folder, so a failing run can be traced to the
 * plugin versions it used. Without a pinned version, to refresh a plugin,
 * delete its folder in the cache.
 *
 * The cache folder is `DLI_TEST_PLUGINS_DIR` or, by default,
 * `tests/integration/.cache/plugins/` (ignored by Git).
 *
 * Usage: php tests/integration/support/fetch-test-plugins.php
 *
 * @package Design_Laboratori_Italia
 */

require_once __DIR__ . '/plugin-versions.php';

$dli_plugins = array(
	'advanced-custom-fields' => 'acf.php',
	'polylang'               => 'polylang.php',
);

$dli_cache_dir = getenv( 'DLI_TEST_PLUGINS_DIR' );
if ( false === $dli_cache_dir || '' === $dli_cache_dir ) {
	$dli_cache_dir = dirname( __DIR__ ) . '/.cache/plugins';
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
	$dli_variable = dli_test_plugin_version_variable( $dli_slug );
	$dli_pinned   = (string) getenv( $dli_variable );
	if ( '' !== $dli_pinned && ! dli_test_plugin_is_valid_version( $dli_pinned ) ) {
		fwrite( STDERR, "$dli_variable is not a version (use a dotted number such as 6.8.10).\n" );
		exit( 1 );
	}

	$dli_cached_file = "$dli_cache_dir/$dli_slug/$dli_main_file";
	if ( is_file( $dli_cached_file ) ) {
		$dli_cached_version = dli_test_plugin_header_version( (string) file_get_contents( $dli_cached_file, false, null, 0, 8192 ) );
		if ( dli_test_plugin_cache_is_usable( $dli_pinned, $dli_cached_version ) ) {
			echo "[cached] $dli_slug\n";
			continue;
		}
		echo "[replace] $dli_slug: cached version '$dli_cached_version', pinned $dli_pinned\n";
		$dli_old = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( "$dli_cache_dir/$dli_slug", FilesystemIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ( $dli_old as $dli_entry ) {
			if ( $dli_entry->isDir() ) {
				rmdir( $dli_entry->getPathname() );
			} else {
				unlink( $dli_entry->getPathname() );
			}
		}
		rmdir( "$dli_cache_dir/$dli_slug" );
	}

	$dli_url = dli_test_plugin_zip_url( $dli_slug, $dli_pinned );
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
		// PHP may be unable to verify certificates (e.g. a portable PHP without cacert.pem): try the system curl.
		$dli_curl_out = tempnam( sys_get_temp_dir(), 'dli' );
		exec( 'curl -fsSL --max-time 60 -o ' . escapeshellarg( $dli_curl_out ) . ' ' . escapeshellarg( $dli_url ), $dli_curl_log, $dli_curl_status );
		$dli_zip = ( 0 === $dli_curl_status ) ? file_get_contents( $dli_curl_out ) : false;
		unlink( $dli_curl_out );
	}
	if ( ! is_string( $dli_zip ) || '' === $dli_zip ) {
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

	$dli_installed = dli_test_plugin_header_version( (string) file_get_contents( "$dli_cache_dir/$dli_slug/$dli_main_file", false, null, 0, 8192 ) );
	if ( '' !== $dli_pinned && $dli_installed !== $dli_pinned ) {
		fwrite( STDERR, "Asked for $dli_slug $dli_pinned but the archive holds '$dli_installed'.\n" );
		exit( 1 );
	}

	$dli_versions[ $dli_slug ] = array(
		'version'    => '' !== $dli_installed ? $dli_installed : 'unknown',
		'sha256'     => hash( 'sha256', $dli_zip ),
		'downloaded' => gmdate( 'c' ),
	);
	echo '           version ' . $dli_versions[ $dli_slug ]['version'] . "\n";
}

file_put_contents( $dli_versions_file, json_encode( $dli_versions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
exit( 0 );
