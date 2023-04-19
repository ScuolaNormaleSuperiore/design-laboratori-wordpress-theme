<?php


/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Plugin:
 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
 */
require_once get_template_directory() . '/inc/vendor/TGM-Plugin-Activation/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'dsi_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function dsi_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'     => 'Advanced Custom Fields',
			'slug'     => 'advanced-custom-fields',
			'required' => true,
		),
		array(
			'name'     => 'ACF OpenStreetMap Field',
			'slug'     => 'acf-openstreetmap-field',
			'required' => true,
		),
		array(
			'name'     => 'Disable Gutenberg',
			'slug'     => 'disable-gutenberg',
			'required' => true,
		),
		array(
			'name'     => 'Members',
			'slug'     => 'members',
			'required' => true,
		),
		array(
			'name'     => 'Better aria label support',
			'slug'     => 'better-aria-label-support',
			'required' => true,
		),
		// array(
		// 	'name'     => 'Contact Form 7',
		// 	'slug'     => 'contact-form-7',
		// 	'required' => true,
		// ),
		// array(
		// 	'name'     => 'Contact Form 7 Database Addon – CFDB7',
		// 	'slug'     => 'contact-form-cfdb7',
		// 	'required' => true,
		// ),
		array(
			'name'     => 'Polylang - Multilanguage support',
			'slug'     => 'polylang',
			'required' => true,
		),
		array(
			'name'     => 'CookieYes - GDPR Cookie Consent',
			'slug'     => 'cookie-law-info',
			'required' => true,
		),
		array(
			'name'     => 'WP Mail SMTP',
			'slug'     => 'wp-mail-smtp',
			'required' => true,
		),
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'design_laboratori_italia',
		// Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',
		// Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins',
		// Menu slug.
		'parent_slug'  => 'themes.php',
		// Parent menu slug.
		'capability'   => 'edit_theme_options',
		// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,
		// Show admin notices or not.
		'dismissable'  => true,
		// If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',
		// If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,
		// Automatically activate plugins after installation or not.
		'message'      => '',
		// Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
