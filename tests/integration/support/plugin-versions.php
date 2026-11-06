<?php
/**
 * Helpers that choose which version of a test plugin the integration suite downloads.
 *
 * Pure functions with no WordPress and no network: they are used by `fetch-test-plugins.php` and covered by the
 * unit test `DLI_Test_Plugin_Versions_Unit_Test`.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Environment variable that pins the version of a test plugin.
 *
 * @param string $slug Plugin slug on wordpress.org.
 * @return string Variable name, or an empty string for a plugin the suite does not download.
 */
function dli_test_plugin_version_variable( $slug ) {
	$variables = array(
		'advanced-custom-fields' => 'DLI_ACF_VERSION',
		'polylang'               => 'DLI_POLYLANG_VERSION',
	);

	return isset( $variables[ $slug ] ) ? $variables[ $slug ] : '';
}

/**
 * Whether a string is a plugin version: two to four numbers separated by dots, nothing else.
 *
 * The value comes from the environment and ends up in an address, so it is checked before it is used.
 *
 * @param mixed $version Value to check.
 * @return bool
 */
function dli_test_plugin_is_valid_version( $version ) {
	return is_string( $version ) && 1 === preg_match( '/\A[0-9]+(?:\.[0-9]+){1,3}\z/', $version );
}

/**
 * Address of the zip of a plugin on wordpress.org.
 *
 * @param string $slug    Plugin slug (letters, digits and hyphens).
 * @param string $version Pinned version, or an empty string for the latest stable release.
 * @return string
 * @throws InvalidArgumentException When the slug or the version is not valid.
 */
function dli_test_plugin_zip_url( $slug, $version ) {
	if ( 1 !== preg_match( '/\A[a-z0-9-]+\z/', $slug ) ) {
		throw new InvalidArgumentException( 'Not a plugin slug.' );
	}
	if ( '' === $version ) {
		return "https://downloads.wordpress.org/plugin/$slug.latest-stable.zip";
	}
	if ( ! dli_test_plugin_is_valid_version( $version ) ) {
		throw new InvalidArgumentException( 'Not a plugin version.' );
	}

	return "https://downloads.wordpress.org/plugin/$slug.$version.zip";
}

/**
 * Version written in the header of the main file of a plugin.
 *
 * @param string $contents Beginning of the main file.
 * @return string The version, or an empty string when there is no `Version:` line.
 */
function dli_test_plugin_header_version( $contents ) {
	if ( 1 !== preg_match( '/^[ \t\/*#@]*Version:\s*(.+)$/mi', $contents, $match ) ) {
		return '';
	}

	return trim( $match[1] );
}

/**
 * Whether a plugin already in the cache can be used.
 *
 * With no pinned version any cached copy is used (the cache is refreshed by deleting it). With one, the cached
 * copy must be exactly that version: a copy of unknown version cannot be trusted and is downloaded again.
 *
 * @param string $pinned Pinned version, or an empty string.
 * @param string $cached Version read from the cached copy, or an empty string when unknown.
 * @return bool
 */
function dli_test_plugin_cache_is_usable( $pinned, $cached ) {
	return '' === $pinned || $pinned === $cached;
}
