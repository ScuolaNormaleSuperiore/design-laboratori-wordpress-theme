<?php
/**
 * PWA support — web app manifest and related <head> tags.
 *
 * Provides:
 *  - dli_pwa_manifest_data()      Builds the manifest.json data array.
 *  - dli_pwa_rewrite_rules()      Registers the /manifest.json virtual endpoint.
 *  - dli_pwa_query_vars()         Registers the dli_pwa query variable.
 *  - dli_pwa_template_redirect()  Outputs manifest.json and exits.
 *  - dli_pwa_maybe_flush_rewrite_rules() One-time rewrite flush for the new rule.
 *  - dli_pwa_head_tags()          Prints the manifest/theme-color/apple-touch-icon tags.
 *
 * Endpoint registered:
 *  /manifest.json → web app manifest (name/icons/theme_color)
 *
 * No service worker is registered: manifest + icons only, by design, to keep
 * the site's runtime behavior unchanged.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_pwa_manifest_data' ) ) {
	/**
	 * Build the web app manifest data.
	 *
	 * @return array
	 */
	function dli_pwa_manifest_data() {
		$site_name = get_bloginfo( 'name' );
		$theme_uri = get_template_directory_uri();
		$is_custom = 'custom' === dli_get_option( 'choose_style', 'setup' );

		return array(
			'name'             => $site_name,
			'short_name'       => $site_name,
			'start_url'        => home_url( '/' ),
			'scope'            => home_url( '/' ),
			'display'          => 'standalone',
			'background_color' => '#ffffff',
			// SNS Ottanio when the custom Bootstrap Italia palette is active, otherwise the default institutional blue.
			'theme_color'      => $is_custom ? '#00728d' : '#0066cc',
			'lang'             => dli_current_language(),
			'icons'            => array(
				array(
					'src'   => $theme_uri . '/assets/img/pwa/icon-192.png',
					'sizes' => '192x192',
					'type'  => 'image/png',
				),
				array(
					'src'   => $theme_uri . '/assets/img/pwa/icon-512.png',
					'sizes' => '512x512',
					'type'  => 'image/png',
				),
			),
		);
	}
}

if ( ! function_exists( 'dli_pwa_rewrite_rules' ) ) {
	/**
	 * Register the /manifest.json rewrite rule.
	 *
	 * @return void
	 */
	function dli_pwa_rewrite_rules() {
		add_rewrite_rule( '^manifest\.json$', 'index.php?dli_pwa=manifest', 'top' );
	}
}
add_action( 'init', 'dli_pwa_rewrite_rules' );

if ( ! function_exists( 'dli_pwa_query_vars' ) ) {
	/**
	 * Register the dli_pwa query variable.
	 *
	 * @param array $vars Existing public query variables.
	 * @return array
	 */
	function dli_pwa_query_vars( $vars ) {
		$vars[] = 'dli_pwa';
		return $vars;
	}
}
add_filter( 'query_vars', 'dli_pwa_query_vars' );

if ( ! function_exists( 'dli_pwa_template_redirect' ) ) {
	/**
	 * Output manifest.json when the virtual endpoint is requested.
	 *
	 * @return void
	 */
	function dli_pwa_template_redirect() {
		if ( 'manifest' !== get_query_var( 'dli_pwa' ) ) {
			return;
		}

		header( 'Content-Type: application/manifest+json; charset=UTF-8' );
		echo wp_json_encode( dli_pwa_manifest_data() );
		exit;
	}
}
add_action( 'template_redirect', 'dli_pwa_template_redirect' );

if ( ! function_exists( 'dli_pwa_maybe_flush_rewrite_rules' ) ) {
	/**
	 * Flush rewrite rules once after the /manifest.json rule is introduced,
	 * since the existing after_switch_theme flush (inc/activation.php) only
	 * runs on theme activation, not on this deploy.
	 *
	 * @return void
	 */
	function dli_pwa_maybe_flush_rewrite_rules() {
		$rules_version = '1';
		if ( get_option( 'dli_pwa_rules_version' ) !== $rules_version ) {
			flush_rewrite_rules();
			update_option( 'dli_pwa_rules_version', $rules_version );
		}
	}
}
add_action( 'init', 'dli_pwa_maybe_flush_rewrite_rules', 20 );

if ( ! function_exists( 'dli_pwa_head_tags' ) ) {
	/**
	 * Print the manifest link, theme-color meta and apple-touch-icon tags.
	 *
	 * @return void
	 */
	function dli_pwa_head_tags() {
		$manifest_data = dli_pwa_manifest_data();
		?>
		<link rel="manifest" href="<?php echo esc_url( home_url( '/manifest.json' ) ); ?>">
		<meta name="theme-color" content="<?php echo esc_attr( $manifest_data['theme_color'] ); ?>">
		<link rel="apple-touch-icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/img/pwa/apple-touch-icon.png' ); ?>">
		<?php
	}
}
add_action( 'wp_head', 'dli_pwa_head_tags' );
