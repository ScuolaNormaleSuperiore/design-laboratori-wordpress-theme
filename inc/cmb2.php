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
