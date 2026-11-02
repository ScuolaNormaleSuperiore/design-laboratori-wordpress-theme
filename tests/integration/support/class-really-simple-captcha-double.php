<?php
/**
 * In-memory replacement of the optional Really Simple CAPTCHA plugin, used only by the integration test process.
 *
 * It behaves like the plugin where the theme depends on it: a challenge is created by `generate_image()` for a
 * prefix and stays valid until `remove()` is called for that prefix; `check()` does not consume it. The challenge
 * `test-prefix` / `ABCD` is there at the start of each test (`reset()`), so a test can submit a valid form
 * without generating one.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! class_exists( 'ReallySimpleCaptcha' ) ) {
	/**
	 * Minimal in-memory CAPTCHA.
	 */
	class ReallySimpleCaptcha {
		public $chars;
		public $char_length;
		public $img_size;
		public $fg;
		public $bg;
		public $font_size;
		public $font_char_width;
		public $img_type;

		/**
		 * The valid challenges: answer by prefix.
		 *
		 * @var array<string, string>
		 */
		public static $challenges = array();

		/**
		 * The prefixes passed to remove(), in order.
		 *
		 * @var string[]
		 */
		public static $removed = array();

		/**
		 * Back to the start of a test: the known challenge only, nothing removed.
		 */
		public static function reset() {
			self::$challenges = array( 'test-prefix' => 'ABCD' );
			self::$removed    = array();
		}

		public function generate_random_word() {
			return 'ABCD';
		}

		public function generate_image( $prefix, $word ) {
			self::$challenges[ (string) $prefix ] = (string) $word;
			return 'test-captcha.png';
		}

		public function check( $prefix, $response ) {
			return isset( self::$challenges[ (string) $prefix ] ) && self::$challenges[ (string) $prefix ] === (string) $response;
		}

		public function remove( $prefix ) {
			self::$removed[] = (string) $prefix;
			unset( self::$challenges[ (string) $prefix ] );
			return true;
		}
	}
}
