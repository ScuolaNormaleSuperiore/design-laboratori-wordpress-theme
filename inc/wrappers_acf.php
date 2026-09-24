<?php
/**
 * Wrapper functions for ACF (Advanced Custom Fields: https://www.advancedcustomfields.com/).
 *
 * The plugin used to create custom fields in a post is ACF.
 *
 * In the code of the theme instead of using the ACF functions (e.g. "get_field" ),
 * please use the corresponding wrapped function (e.g. "dli_get_field").
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_field' ) ) {
	/**
	 * Wrapper around ACF's get_field().
	 *
	 * @param string   $selector     Field name or key.
	 * @param int|bool $post_id      Post ID to get the field value for, or false for the current post.
	 * @param bool     $format_value Whether to apply ACF's formatting logic to the value.
	 *
	 * @return mixed
	 */
	function dli_get_field( $selector, $post_id = false, $format_value = true ) {
		return get_field( $selector, $post_id, $format_value );
	}
}

if ( ! function_exists( 'dli_update_field' ) ) {
	/**
	 * Wrapper around ACF's update_field().
	 *
	 * @param string $fieldname  Field name or key.
	 * @param mixed  $fieldvalue Value to save.
	 * @param int    $post_id    Post ID to update the field value for.
	 *
	 * @return void
	 */
	function dli_update_field( $fieldname, $fieldvalue, $post_id ) {
		update_field( $fieldname, $fieldvalue, $post_id );
	}
}
