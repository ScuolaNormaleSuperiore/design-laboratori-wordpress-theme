<?php
/**
 * Integration tests for the configuration page assets.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The options script is queued only after CMB2 makes its JavaScript available.
 */
class DLI_Options_Assets_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The screen active before each test.
	 *
	 * @var WP_Screen|null
	 */
	private $previous_screen;

	/**
	 * Prepare the configuration screen and an empty state for the tested script.
	 */
	public function set_up() {
		parent::set_up();

		global $current_screen;
		$this->previous_screen = $current_screen;

		set_current_screen( 'toplevel_page_dli_options' );
		wp_dequeue_script( 'dli_options_dialog' );
		wp_deregister_script( 'dli_options_dialog' );
	}

	/**
	 * Restore the screen and remove the theme script from the shared script registry.
	 */
	public function tear_down() {
		global $current_screen;
		$current_screen = $this->previous_screen;

		wp_dequeue_script( 'dli_options_dialog' );
		wp_deregister_script( 'dli_options_dialog' );

		parent::tear_down();
	}

	/**
	 * The theme script waits for CMB2 and does not declare a handle before CMB2 registers it.
	 */
	public function test_options_script_is_enqueued_after_cmb2_footer_hook() {
		dli_options_assets();

		$this->assertFalse( wp_script_is( 'dli_options_dialog', 'enqueued' ) );

		wp_register_script( 'cmb2-scripts', 'https://example.org/cmb2.js', array(), '1.0', true );
		wp_enqueue_script( 'cmb2-scripts' );
		do_action( 'cmb2_footer_enqueue' );

		$this->assertTrue( wp_script_is( 'dli_options_dialog', 'enqueued' ) );

		$scripts = wp_scripts();
		$this->assertNotContains( 'cmb2-scripts', $scripts->registered['dli_options_dialog']->deps );
		$this->assertLessThan(
			array_search( 'dli_options_dialog', $scripts->queue, true ),
			array_search( 'cmb2-scripts', $scripts->queue, true )
		);
	}
}
