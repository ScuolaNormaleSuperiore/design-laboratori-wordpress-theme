<?php

require 'vendor/CMB2/init.php';
require 'vendor/CMB2-conditional-logic/cmb2-conditional-logic.php';
require 'vendor/CMB2-field-Leaflet-Geocoder/cmb-field-leaflet-map.php';
require 'vendor/cmb2-attached-posts/cmb2-attached-posts-field.php';
require 'vendor/CMB2-taxonomy-hierarchy-child.php';

add_filter(
	'pw_cmb2_field_select2_asset_path',
	function ( $var ) { return get_stylesheet_directory_uri() . '/inc/vendor/cmb-field-select2-master'; } );

require 'vendor/cmb-field-select2-master/cmb-field-select2.php';

function update_cmb2_meta_box_url( $url ) {
	/*
	 * If you use a symlink, the css/js urls may have an odd path stuck in the middle, like:
	 * http://SITEURL/wp-content/plugins/Users/jt/Sites/CMB2/cmb2/js/cmb2.js?ver=X.X.X
	 * Or something like that.
	 * 
	 * INSTEAD of completely replacing the URL,
	 * It is best to do a str_replace. This ensures you only change the url if it's 
	 * pointing to the broken resource. This ensures that if another version of CMB2
	 * is loaded (i.e. in a 3rd part plugin), that their correct URL will load,
	 * rather than forcing yours.
	 */
	return get_stylesheet_directory_uri() . '/inc/vendor/CMB2/';
}
add_filter( 'cmb2_meta_box_url', 'update_cmb2_meta_box_url' );
