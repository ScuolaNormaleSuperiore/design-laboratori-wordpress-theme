<?php

if ( is_plugin_active( plugin_basename( 'really-simple-captcha/really-simple-captcha.php' ) ) ) {
	if ( class_exists( 'ReallySimpleCaptcha' ) ) {
		$captcha_enabled          = true;
		$captcha_obj              = new ReallySimpleCaptcha();
		$captcha_obj->chars       = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$captcha_obj->char_length = '4';
		// Width/Height dimensions of CAPTCHA image.
		$captcha_obj->img_size = array( '72', '24' );
		// Font color of CAPTCHA characters, in RGB (0 – 255).
		$captcha_obj->fg = array( '0', '0', '0' );
		// Background color of CAPTCHA image, in RGB (0 – 255).
		$captcha_obj->bg = array( '255', '255', '255' );
		// Font Size of CAPTCHA characters.
		$captcha_obj->font_size = '16';
		// Width between CAPTCHA characters.
		$captcha_obj->font_char_width = '15';
		// CAPTCHA image type. Can be 'png', 'jpeg', or 'gif'.
		$captcha_obj->img_type    = 'png';
		$captcha_obj_word         = $captcha_obj->generate_random_word();
		$captcha_obj_prefix       = mt_rand();
		$captcha_obj_image_name   = $captcha_obj->generate_image( $captcha_obj_prefix, $captcha_obj_word );
		$captcha_obj_image_url    = get_bloginfo( 'wpurl' ) . '/wp-content/plugins/really-simple-captcha/tmp/';
		$captcha_obj_image_src    = $captcha_obj_image_url . $captcha_obj_image_name;
		$captcha_obj_image_width  = $captcha_obj->img_size[0];
		$captcha_obj_image_height = $captcha_obj->img_size[1];
	}
}
