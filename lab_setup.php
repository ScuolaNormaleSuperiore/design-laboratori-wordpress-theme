<?php

add_action( 'init', 'dli_lab_setup', 0 );

/**
 * Check plugin dependencies.
 *
 * @return boolean
 */
function check_dependencies() {
	$result = true;
	if ( ! class_exists( 'ACF' ) ) {
		error_log( 'The plugin ACF (advanced-custom-fields) is missing, please install and activate it: https://wordpress.org/plugins/advanced-custom-fields' );
		$result = false;
	}
	if ( ! class_exists( 'Members_Plugin' ) ) {
		error_log( 'The plugin Members is missing, please install and activate it: https://wordpress.org/plugins/members' );
		$result = false;
	}
	if ( ! function_exists( 'pll_the_languages' ) ) {
		error_log( 'The plugin Polylang  is missing, please install and activate it: https://wordpress.org/plugins/polylang/' );
		$result = false;
	}
	return $result;
}

/**
 * Install custom post types and taxonomies.
 *
 * @return void
 */
function dli_lab_setup() {
	if ( check_dependencies() ) {
		// SETUP THE POST TYPES  USED BY THE THEME.
		if ( ! class_exists( 'LabManager' ) ) {
			include_once 'classes/class-labmanager.php';
			global $lab_manager;
			$lab_manager = new LabManager();
			$lab_manager->plugin_setup();
		}
	}
}

