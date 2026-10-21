<?php
/**
 * CAPTCHA helpers for the optional Really Simple CAPTCHA plugin.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_captcha_data' ) ) {
	/**
	 * Create a CAPTCHA challenge when the optional plugin is available.
	 *
	 * @return array{enabled: bool, instance: ReallySimpleCaptcha|null, image_src: string, image_width: int, image_height: int, prefix: string}
	 */
	function dli_get_captcha_data() {
		$dli_captcha_data = array(
			'enabled'      => false,
			'instance'     => null,
			'image_src'    => '',
			'image_width'  => 0,
			'image_height' => 0,
			'prefix'       => '',
		);

		if (
			! function_exists( 'is_plugin_active' ) ||
			! is_plugin_active( plugin_basename( 'really-simple-captcha/really-simple-captcha.php' ) ) ||
			! class_exists( 'ReallySimpleCaptcha' )
		) {
			return $dli_captcha_data;
		}

		$dli_captcha_instance                  = new ReallySimpleCaptcha();
		$dli_captcha_instance->chars           = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$dli_captcha_instance->char_length     = 4;
		$dli_captcha_instance->img_size        = array( 72, 24 );
		$dli_captcha_instance->fg              = array( 0, 0, 0 );
		$dli_captcha_instance->bg              = array( 255, 255, 255 );
		$dli_captcha_instance->font_size       = 16;
		$dli_captcha_instance->font_char_width = 15;
		$dli_captcha_instance->img_type        = 'png';

		$dli_captcha_word       = $dli_captcha_instance->generate_random_word();
		$dli_captcha_prefix     = (string) wp_rand();
		$dli_captcha_image_name = $dli_captcha_instance->generate_image( $dli_captcha_prefix, $dli_captcha_word );

		if ( ! is_string( $dli_captcha_image_name ) || '' === $dli_captcha_image_name ) {
			return $dli_captcha_data;
		}

		return array(
			'enabled'      => true,
			'instance'     => $dli_captcha_instance,
			'image_src'    => plugins_url( 'tmp/' . $dli_captcha_image_name, 'really-simple-captcha/really-simple-captcha.php' ),
			'image_width'  => (int) $dli_captcha_instance->img_size[0],
			'image_height' => (int) $dli_captcha_instance->img_size[1],
			'prefix'       => $dli_captcha_prefix,
		);
	}
}

if ( ! function_exists( 'dli_validate_captcha' ) ) {
	/**
	 * Validate a user response against a generated CAPTCHA challenge.
	 *
	 * @param array{enabled: bool, instance: ReallySimpleCaptcha|null, image_src: string, image_width: int, image_height: int, prefix: string} $captcha_data CAPTCHA challenge data.
	 * @param string                                                                                                                           $prefix       Submitted challenge prefix.
	 * @param string                                                                                                                           $response     Submitted user response.
	 * @return bool True when the response is valid.
	 */
	function dli_validate_captcha( $captcha_data, $prefix, $response ) {
		$dli_captcha_instance = $captcha_data['instance'] ?? null;

		if ( empty( $captcha_data['enabled'] ) || ! $dli_captcha_instance instanceof ReallySimpleCaptcha ) {
			return false;
		}

		return $dli_captcha_instance->check( $prefix, $response );
	}
}
