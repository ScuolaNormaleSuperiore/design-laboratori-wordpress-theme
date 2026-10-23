<?php
/**
 * Third-party runtime declarations for PHPStan.
 *
 * These functions are provided by required dependencies that are not loaded
 * during static analysis. This file is scanned by PHPStan only.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	/**
	 * Register an ACF local field group.
	 *
	 * @param array<string, mixed> $field_group Field group configuration.
	 * @return bool Whether the field group was registered.
	 */
	function acf_add_local_field_group( $field_group ) {
		return true;
	}
}

if ( ! function_exists( 'cmb2_get_option' ) ) {
	/**
	 * Retrieve an option value from a CMB2 option group.
	 *
	 * @param string $option_key Option group key.
	 * @param string $key        Option key.
	 * @param mixed  $default    Fallback value.
	 * @return mixed Option value or fallback.
	 */
	function cmb2_get_option( $option_key, $key, $default = false ) {
		return $default;
	}
}
