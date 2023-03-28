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
