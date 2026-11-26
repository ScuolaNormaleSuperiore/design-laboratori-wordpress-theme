<?php
/**
 * Makes Italian the deterministic locale of the E2E site and of its test users, after the baseline is imported.
 *
 * Run by tests/e2e/support/reset.js with WP-CLI inside the wp-env E2E environment:
 *
 *     wp eval-file wp-content/themes/design-laboratori-wordpress-theme/tests/e2e/support/force-italian.php
 *
 * Polylang stores its default-language slug in its option array. The baseline has Italian and English content, so the
 * translation scenarios can still visit /en/ explicitly, while ordinary browser and admin scenarios start in Italian.
 *
 * Development only: never loaded by the theme and never shipped (tests/ is left out of the release zip).
 *
 * @package Design_Laboratori_Italia
 */

update_option( 'WPLANG', 'it_IT' );

$dli_polylang = get_option( 'polylang' );
if ( ! is_array( $dli_polylang ) || ! array_key_exists( 'default_lang', $dli_polylang ) ) {
	WP_CLI::error( 'The baseline has no Polylang configuration with a default language.' );
}
$dli_polylang['default_lang'] = 'it';
update_option( 'polylang', $dli_polylang );

foreach ( array( 'e2e-admin', 'e2e-super-editor', 'e2e-editor' ) as $dli_login ) {
	$dli_user = get_user_by( 'login', $dli_login );
	if ( ! $dli_user ) {
		WP_CLI::error( "The baseline has no user $dli_login." );
	}
	update_user_meta( $dli_user->ID, 'locale', 'it_IT' );
}

WP_CLI::log( 'Italian set for the site, Polylang and the three E2E users.' );
