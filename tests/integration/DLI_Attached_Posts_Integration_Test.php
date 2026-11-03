<?php
/**
 * Regression coverage for the bundled Attached Posts field's edit links.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Run the library's asset setup with WordPress URLs and permissions.
 */
class DLI_Attached_Posts_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The JavaScript template must contain the edit URL, not a boolean cast to "1".
	 */
	public function test_edit_link_template_preserves_the_wordpress_url() {
		global $post;

		$previous_post = $post;
		$previous_user = get_current_user_id();
		$admin_id      = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$post_id       = self::factory()->post->create();
		wp_set_current_user( $admin_id );
		$post = get_post( $post_id );

		try {
			// wp_localize_script() decodes HTML entities before emitting JSON.
			$edit_url = get_edit_post_link( $post_id, 'raw' );
			$this->assertNotEmpty( $edit_url );

			$setup = new ReflectionMethod( WDS_CMB2_Attached_Posts_Field::class, 'setup_scripts' );
			$setup->setAccessible( true );
			$setup->invoke( null );

			$script = wp_scripts()->get_data( 'cmb2-attached-posts-field', 'data' );
			$this->assertIsString( $script );
			$this->assertSame( 1, preg_match( '/var CMBAP = (\{[^\n]+\});/', $script, $matches ) );
			$config = json_decode( $matches[1], true );

			$expected = str_replace( (string) $post_id, 'REPLACEME', $edit_url );
			$this->assertSame( $expected, $config['edit_link_template'] );
			$this->assertSame( $edit_url, str_replace( 'REPLACEME', (string) $post_id, $config['edit_link_template'] ) );
		} finally {
			$post = $previous_post;
			wp_set_current_user( $previous_user );
		}
	}
}
