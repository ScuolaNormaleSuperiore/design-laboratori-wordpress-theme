<?php
/**
 * The Utilità page of Strumenti DLI: only the reload tool, the example pages are made by Importa → Pagine.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_Tools_Admin::render_utils()`.
 */
class DLI_Tools_Utilities_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Print the Utilità page as an administrator who can edit the site configuration.
	 *
	 * @return string The HTML.
	 */
	private function render_utils_page() {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		wp_get_current_user()->add_cap( DLI_EDIT_CONFIG_PERMISSION );
		$_GET['page'] = DLI_Tools_Admin::UTILS_SLUG;

		ob_start();
		DLI_Tools_Admin::render_utils();
		$html = (string) ob_get_clean();
		unset( $_GET['page'] );

		return $html;
	}

	/**
	 * The page keeps the reload tool and has no box for the example pages.
	 */
	public function test_utilities_page_has_only_the_reload_tool() {
		$html = $this->render_utils_page();

		$this->assertSame( 1, substr_count( $html, 'class="postbox dli-tool-panel"' ), 'One box.' );
		$this->assertStringContainsString( 'action=reload', $html, 'The reload tool is there.' );
		$this->assertStringNotContainsString( 'dli_example_action', $html );
		$this->assertStringNotContainsString( 'value="dli_example_content"', $html );
	}

	/**
	 * The form handler of the removed tool is not wired any more.
	 */
	public function test_the_example_pages_handler_is_not_wired() {
		$this->assertFalse( has_action( 'admin_post_dli_example_content' ) );
		$this->assertFalse( class_exists( 'DLI_Example_Content', false ) );
	}
}
