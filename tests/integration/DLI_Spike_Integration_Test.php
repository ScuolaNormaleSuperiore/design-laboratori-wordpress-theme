<?php
/**
 * Spike: the theme, ACF and Polylang load inside the WordPress test framework.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Smoke tests of the integration environment.
 */
class DLI_Spike_Integration_Test extends WP_UnitTestCase {

	/**
	 * The working copy of the theme is the active theme.
	 */
	public function test_theme_is_active() {
		$this->assertSame( 'design-laboratori-wordpress-theme', get_stylesheet() );
		$this->assertTrue( function_exists( 'dli_get_option' ) );
	}

	/**
	 * ACF and Polylang are loaded and the mandatory-plugin check passes.
	 */
	public function test_mandatory_plugins_are_active() {
		$this->assertTrue( function_exists( 'acf_add_local_field_group' ) );
		$this->assertTrue( function_exists( 'pll_the_languages' ) );
		$this->assertTrue( dli_check_mandatory_plugins() );
	}

	/**
	 * The Super Editor role is created on init, with the theme capabilities.
	 */
	public function test_super_editor_role_exists() {
		$role = get_role( DLI_SUPER_EDITOR_ROLE_SLUG );

		$this->assertNotNull( $role );
		$this->assertTrue( $role->has_cap( 'edit_theme_options' ) );
		$this->assertTrue( $role->has_cap( DLI_EDIT_CONFIG_PERMISSION ) );
	}

	/**
	 * Theme activation does not run in the test framework: calling its hook by
	 * hand creates the default pages.
	 */
	public function test_after_switch_theme_creates_default_pages() {
		$before = count( get_posts( array( 'post_type' => 'page', 'numberposts' => -1, 'post_status' => 'any' ) ) );

		do_action( 'after_switch_theme' );

		$after = count( get_posts( array( 'post_type' => 'page', 'numberposts' => -1, 'post_status' => 'any' ) ) );
		$this->assertGreaterThan( $before, $after );
	}

	/**
	 * The persona content type is registered.
	 */
	public function test_persona_post_type_is_registered() {
		$this->assertTrue( post_type_exists( 'persona' ) );
	}
}
