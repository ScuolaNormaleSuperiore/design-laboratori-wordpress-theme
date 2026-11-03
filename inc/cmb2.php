<?php
/**
 * Loads CMB2 and its addons, and wires the custom field types used by the theme.
 *
 * @package Design_Laboratori_Italia
 */

defined( 'ABSPATH' ) || exit;

require 'vendor/CMB2/init.php';
require 'vendor/CMB2-conditional-logic/cmb2-conditional-logic.php';
require 'vendor/CMB2-field-Leaflet-Geocoder/cmb-field-leaflet-map.php';
require 'vendor/cmb2-attached-posts/cmb2-attached-posts-field.php';
require 'vendor/CMB2-taxonomy-hierarchy-child.php';

add_filter(
	'pw_cmb2_field_select2_asset_path',
	function ( $var ) {
		return get_stylesheet_directory_uri() . '/inc/vendor/cmb-field-select2-master';
	}
);
require 'vendor/cmb-field-select2-master/cmb-field-select2.php';

/**
 * Filters the base URL used by CMB2 to enqueue its own CSS/JS assets.
 *
 * @param string $url Default CMB2 assets URL.
 *
 * @return string
 */
function dli_update_cmb2_meta_box_url( $url ) {
	/*
	 * The theme ships its own copy of CMB2 (inc/vendor/CMB2), so the assets URL is
	 * forced on purpose: with a symlinked or non-standard directory layout CMB2 could
	 * compute a broken URL for its css/js, like:
	 * http://SITEURL/wp-content/plugins/Users/jt/Sites/CMB2/cmb2/js/cmb2.js?ver=X.X.X
	 *
	 * The incoming $url is ignored intentionally. No other plugin is expected to
	 * bundle a different CMB2 copy alongside the theme.
	 */
	return get_stylesheet_directory_uri() . '/inc/vendor/CMB2/';
}
add_filter( 'cmb2_meta_box_url', 'dli_update_cmb2_meta_box_url' );

/**
 * Lets a user save an options page of the theme only when the user has the capability of the page.
 *
 * CMB2 saves an options page through `admin-post.php`, where `CMB2_Options_Hookup::save_options()` checks the nonce of
 * the form but not the capability of the user: the capability of the page only shows it in the menu. A nonce says that
 * a form was sent on purpose, not that the user may send it, so a request that does not come from the page is refused
 * here when the user has no right to it. The boxes of the other objects (posts, terms, users) are not touched.
 *
 * @param bool $can_save Whether CMB2 allows the box to save.
 * @param CMB2 $cmb      The box that is saving.
 * @return bool Whether the box can save.
 */
function dli_options_can_save_by_capability( $can_save, $cmb ) {
	if ( ! $can_save || ! in_array( 'options-page', $cmb->box_types(), true ) ) {
		return $can_save;
	}

	$capability = $cmb->prop( 'capability' );

	return $capability ? current_user_can( $capability ) : $can_save;
}
add_filter( 'cmb2_can_save', 'dli_options_can_save_by_capability', 10, 2 );
