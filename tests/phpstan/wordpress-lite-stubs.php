<?php
/**
 * Lightweight WordPress stubs for PHPStan.
 *
 * These declarations are intentionally minimal and are only used
 * by static analysis to reduce false positives in this theme.
 */

if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {}
}

if ( ! class_exists( 'WP_Query' ) ) {
	class WP_Query {
		/** @var array<int, WP_Post> */
		public $posts = array();

		/**
		 * @param array<string, mixed>|string $query
		 */
		public function __construct( $query = '' ) {}
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		/**
		 * @param string $code
		 * @param string $message
		 * @param mixed  $data
		 */
		public function __construct( $code = '', $message = '', $data = null ) {}
	}
}

if ( ! class_exists( 'WP_REST_Request' ) ) {
	class WP_REST_Request {}
}

if ( ! class_exists( 'WP_REST_Response' ) ) {
	class WP_REST_Response {
		/**
		 * @param mixed $data
		 * @param int   $status
		 * @param array<string, string> $headers
		 */
		public function __construct( $data = null, $status = 200, $headers = array() ) {}
	}
}

if ( ! class_exists( 'WP_REST_Server' ) ) {
	class WP_REST_Server {
		public const READABLE   = 'GET';
		public const CREATABLE  = 'POST';
		public const EDITABLE   = 'PUT';
		public const DELETABLE  = 'DELETE';
		public const ALLMETHODS = 'GET,POST,PUT,DELETE';
	}
}

if ( ! class_exists( 'Walker_Nav_Menu' ) ) {
	class Walker_Nav_Menu {}
}

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
