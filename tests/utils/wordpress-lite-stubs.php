<?php
/**
 * Lightweight third-party stubs for PHPStan.
 *
 * These declarations are intentionally minimal and are only used
 * by static analysis to reduce false positives in this theme.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

if ( ! class_exists( 'CMB2_Boxes' ) ) {
	class CMB2_Boxes {
		/**
		 * @return array<string, mixed>
		 */
		public static function get_all() {
			return array();
		}
	}
}

if ( ! class_exists( 'CMB2' ) ) {
	class CMB2 {
		/**
		 * @param array<string, mixed> $field    Field configuration.
		 * @param int                  $position Field position.
		 * @return string|false
		 */
		public function add_field( $field, $position = 0 ) {
			return false;
		}

		/**
		 * @param string               $parent_field_id Parent field identifier.
		 * @param array<string, mixed> $field           Field configuration.
		 * @param int                  $position        Field position.
		 * @return array<string, string>|false
		 */
		public function add_group_field( $parent_field_id, $field, $position = 0 ) {
			return false;
		}
	}
}

if ( ! class_exists( 'CMB2_Options_Hookup' ) ) {
	class CMB2_Options_Hookup {
		/** @var CMB2 */
		public $cmb;

		/** @var string */
		public $option_key = '';

		/** @return void */
		public function options_page_metabox() {}
	}
}

if ( ! defined( 'CMB2_VERSION' ) ) {
	define( 'CMB2_VERSION', '2.11.0' );
}

if ( ! function_exists( 'new_cmb2_box' ) ) {
	/**
	 * @param array<string, mixed> $meta_box_config Metabox configuration.
	 * @return CMB2
	 */
	function new_cmb2_box( $meta_box_config ) {
		return new CMB2();
	}
}

if ( ! function_exists( 'get_field' ) ) {
	/**
	 * @param string|int $selector     ACF field name or key.
	 * @param mixed      $post_id      Post ID or ACF context.
	 * @param bool       $format_value Whether to format the value.
	 * @return mixed
	 */
	function get_field( $selector, $post_id = false, $format_value = true ) {
		return null;
	}
}

if ( ! function_exists( 'update_field' ) ) {
	/**
	 * @param string|int $selector ACF field name or key.
	 * @param mixed      $value    Value to store.
	 * @param mixed      $post_id  Post ID or ACF context.
	 * @return int|bool
	 */
	function update_field( $selector, $value, $post_id = false ) {
		return false;
	}
}

if ( ! function_exists( 'tgmpa' ) ) {
	/**
	 * @param array<int, array<string, mixed>> $plugins Plugin definitions.
	 * @param array<string, mixed>              $config  TGM configuration.
	 * @return void
	 */
	function tgmpa( $plugins, $config = array() ) {}
}
