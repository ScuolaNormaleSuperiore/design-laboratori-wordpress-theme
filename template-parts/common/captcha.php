<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

if ( is_plugin_active( plugin_basename( 'really-simple-captcha/really-simple-captcha.php' ) ) ) {
	if ( class_exists( 'ReallySimpleCaptcha' ) ) {
		$dli_captcha_enabled          = true;
		$dli_captcha_obj              = new ReallySimpleCaptcha();
		$dli_captcha_obj->chars       = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$dli_captcha_obj->char_length = '4';
		// Width/Height dimensions of CAPTCHA image.
		$dli_captcha_obj->img_size = array( '72', '24' );
		// Font color of CAPTCHA characters, in RGB (0 – 255).
		$dli_captcha_obj->fg = array( '0', '0', '0' );
		// Background color of CAPTCHA image, in RGB (0 – 255).
		$dli_captcha_obj->bg = array( '255', '255', '255' );
		// Font Size of CAPTCHA characters.
		$dli_captcha_obj->font_size = '16';
		// Width between CAPTCHA characters.
		$dli_captcha_obj->font_char_width = '15';
		// CAPTCHA image type. Can be 'png', 'jpeg', or 'gif'.
		$dli_captcha_obj->img_type    = 'png';
		$dli_captcha_obj_word         = $dli_captcha_obj->generate_random_word();
		$dli_captcha_obj_prefix       = wp_rand();
		$dli_captcha_obj_image_name   = $dli_captcha_obj->generate_image( $dli_captcha_obj_prefix, $dli_captcha_obj_word );
		$dli_captcha_obj_image_url    = get_bloginfo( 'wpurl' ) . '/wp-content/plugins/really-simple-captcha/tmp/';
		$dli_captcha_obj_image_src    = $dli_captcha_obj_image_url . $dli_captcha_obj_image_name;
		$dli_captcha_obj_image_width  = $dli_captcha_obj->img_size[0];
		$dli_captcha_obj_image_height = $dli_captcha_obj->img_size[1];
	}
}
