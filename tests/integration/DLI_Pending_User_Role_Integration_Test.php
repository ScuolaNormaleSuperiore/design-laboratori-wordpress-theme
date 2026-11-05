<?php
/**
 * The role "Utente in attesa": a user who is authenticated and can do nothing, not even edit the own profile.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_AuthorizationManager::add_pending_user_role()` and the rule that closes the own profile.
 */
class DLI_Pending_User_Role_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Slug of the role, written out so a missing role fails the test instead of stopping it.
	 */
	const ROLE = 'dli_pending_user';

	/**
	 * Put the current user back to nobody.
	 */
	public function tear_down() {
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * Create a user with a role and make it the current user.
	 *
	 * @param string $role Role slug.
	 * @return int User ID.
	 */
	private function login_as( $role ) {
		$user_id = self::factory()->user->create( array( 'role' => $role ) );
		wp_set_current_user( $user_id );
		return $user_id;
	}

	/**
	 * The role exists and has no capability at all.
	 */
	public function test_role_exists_without_any_capability() {
		$role = get_role( self::ROLE );

		$this->assertNotNull( $role, 'The role must be registered by the theme.' );
		$this->assertSame( array(), $role->capabilities );
	}

	/**
	 * The user is authenticated but cannot read, write or manage anything.
	 */
	public function test_user_with_the_role_can_do_nothing() {
		$this->assertNotNull( get_role( self::ROLE ) );
		$this->login_as( self::ROLE );

		$this->assertTrue( is_user_logged_in() );
		foreach ( array( 'read', 'edit_posts', 'edit_pages', 'upload_files', 'list_users', 'manage_options', DLI_EDIT_CONFIG_PERMISSION ) as $capability ) {
			$this->assertFalse( current_user_can( $capability ), "The role must not have '$capability'." );
		}
	}

	/**
	 * The user cannot edit the own profile, which WordPress otherwise allows to everybody.
	 */
	public function test_user_with_the_role_cannot_edit_the_own_profile() {
		$this->assertNotNull( get_role( self::ROLE ) );
		$user_id = $this->login_as( self::ROLE );

		$this->assertFalse( current_user_can( 'edit_user', $user_id ) );
	}

	/**
	 * Control: a subscriber can edit the own profile, so the rule above is the theme's and not a default.
	 */
	public function test_subscriber_can_still_edit_the_own_profile() {
		$user_id = $this->login_as( 'subscriber' );

		$this->assertTrue( current_user_can( 'edit_user', $user_id ) );
	}

	/**
	 * The REST route that updates the own user answers 403 to the role.
	 */
	public function test_rest_update_of_the_own_user_is_refused() {
		$this->assertNotNull( get_role( self::ROLE ) );
		$this->login_as( self::ROLE );

		$request = new WP_REST_Request( 'POST', '/wp/v2/users/me' );
		$request->set_param( 'first_name', 'Cambiato' );
		$response = rest_do_request( $request );

		$this->assertSame( 403, $response->get_status() );
	}

	/**
	 * Control: a subscriber can update the own user through the same route.
	 */
	public function test_rest_update_of_the_own_user_is_allowed_to_a_subscriber() {
		$this->login_as( 'subscriber' );

		$request = new WP_REST_Request( 'POST', '/wp/v2/users/me' );
		$request->set_param( 'first_name', 'Cambiato' );
		$response = rest_do_request( $request );

		$this->assertSame( 200, $response->get_status() );
	}

	/**
	 * The rule is for the role only: other users still edit themselves and nobody gains or loses a capability.
	 */
	public function test_other_roles_are_not_changed() {
		$this->assertSame( array( 'read' => true, 'level_0' => true ), get_role( 'subscriber' )->capabilities );
		$this->assertTrue( get_role( 'editor' )->has_cap( 'edit_posts' ) );
		$this->assertTrue( get_role( DLI_SUPER_EDITOR_ROLE_SLUG )->has_cap( DLI_EDIT_CONFIG_PERMISSION ) );
		$this->assertTrue( get_role( 'administrator' )->has_cap( 'manage_options' ) );

		$editor_id = $this->login_as( 'editor' );
		$this->assertTrue( current_user_can( 'edit_user', $editor_id ) );
	}

	/**
	 * Creating the role again does not rewrite a capability added to it in the meantime.
	 */
	public function test_creating_the_role_again_keeps_the_capabilities_added_later() {
		$role = get_role( self::ROLE );
		$this->assertNotNull( $role );
		$role->add_cap( 'dli_test_added_capability' );

		( new DLI_AuthorizationManager() )->add_pending_user_role();

		$this->assertTrue( get_role( self::ROLE )->has_cap( 'dli_test_added_capability' ) );
	}
}
