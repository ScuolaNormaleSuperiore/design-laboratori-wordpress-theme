<?php
/**
 * The public header for users of the reserved area.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The public header identifies any authenticated user and offers a safe logout.
 */
class DLI_Reserved_Area_Header_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Set the public login control on.
	 */
	public function set_up() {
		parent::set_up();
		$this->set_theme_options(
			'setup',
			array(
				'login_button_visible' => 'true',
			)
		);
		$this->set_theme_options(
			DLI_RESERVED_AREA_OPTION_KEY,
			array(
				'reserved_area_enabled' => 'true',
			)
		);
	}

	/**
	 * Put back the anonymous visitor.
	 */
	public function tear_down() {
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * A reserved-area user sees their name as the profile icon tooltip and a logout link, not the login link to the dashboard.
	 */
	public function test_authenticated_user_sees_a_greeting_and_logout_in_the_public_header() {
		$user_id = self::factory()->user->create(
			array(
				'display_name' => 'Mario Rossi',
				'role'         => DLI_RESERVED_AREA_USER_ROLE_SLUG,
			)
		);
		wp_set_current_user( $user_id );

		$html = $this->render_theme_template( 'header.php' );

		$this->assertStringContainsString( 'title="Mario Rossi"', $html );
		$this->assertStringNotContainsString( 'Ciao, Mario Rossi', $html );
		$this->assertStringContainsString( 'action=logout', $html );
		$this->assertStringNotContainsString( esc_url( admin_url() ), $html );
	}

	/**
 * An authenticated user outside the reserved area sees no duplicate access control.
	 */
	public function test_authenticated_user_outside_the_reserved_area_sees_no_access_control() {
		$user_id = self::factory()->user->create(
			array(
				'role' => 'editor',
			)
		);
		wp_set_current_user( $user_id );

		$html = $this->render_theme_template( 'header.php' );

		$this->assertStringNotContainsString( esc_url( admin_url() ), $html );
		$this->assertStringNotContainsString( 'action=logout', $html );
		$this->assertStringNotContainsString( '>Accedi<', $html );
	}
}
