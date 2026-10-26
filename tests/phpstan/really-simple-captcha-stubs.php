<?php
/**
 * Really Simple CAPTCHA 2.4 declarations for PHPStan, loaded from tests/phpstan/.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! class_exists( 'ReallySimpleCaptcha' ) ) {
	/**
	 * Minimal API used by the theme.
	 */
	class ReallySimpleCaptcha {
		/** @var string */
		public $chars = '';

		/** @var int */
		public $char_length = 0;

		/** @var array<int, int> */
		public $img_size = array();

		/** @var array<int, int> */
		public $bg = array();

		/** @var array<int, int> */
		public $fg = array();

		/** @var int */
		public $font_size = 0;

		/** @var int */
		public $font_char_width = 0;

		/** @var string */
		public $img_type = '';

		/**
		 * @return string
		 */
		public function generate_random_word() {
			return '';
		}

		/**
		 * @param string $prefix Challenge prefix.
		 * @param string $word   Generated challenge word.
		 * @return string|false
		 */
		public function generate_image( $prefix, $word ) {
			return false;
		}

		/**
		 * @param string $prefix   Challenge prefix.
		 * @param string $response User response.
		 * @return bool
		 */
		public function check( $prefix, $response ) {
			return false;
		}
	}
}
