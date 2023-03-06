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


if(!function_exists("dli_get_field")) {
	function dli_get_field( $fied_name ) {
		return get_field( $fied_name );
	}

}
