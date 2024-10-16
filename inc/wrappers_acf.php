<?php
/**
 * Wrapper functions for ACF (Advanced Custom Fields: https://www.advancedcustomfields.com/).
 * 
 * The plugin used to create custom fields in a post is ACF.
 * 
 * In the code of the theme instead of using the ACF functions (e.g. "get_field" ),
 * please use the corresponding wrapped function (e.g. "dli_get_field").
 * 
 */


if ( ! function_exists( 'dli_get_field' ) ) {
	function dli_get_field( $selector, $post_id = false, $format_value = true ) {
		return get_field( $selector, $post_id, $format_value);
	}
}

if ( ! function_exists( 'dli_update_field' ) ) {
	function dli_update_field( $fieldname, $fieldvalue, $post_id ) {
		update_field( $fieldname, $fieldvalue, $post_id );
	}
}
