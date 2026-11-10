<?php
/**
 * The role "Utente area riservata": an approved user of the reserved area, with only the theme capability.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_AuthorizationManager::add_reserved_area_user_role()` and the rule that closes the own profile.
 */
class DLI_Reserved_Area_User_Role_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Slug of the role, written out so a missing role fails the test instead of stopping it.
	 */
	const ROLE = 'dli_reserved_area_user';

	/**
	 * Theme capability that marks an approved user of the reserved area.
	 */
	const ACCESS_CAP = 'dli_reserved_area_access';

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
	 * The role exists and has the theme capability only.
	 */
	public function test_role_exists_with_only_the_theme_capability() {
		$role = get_role( self::ROLE );

		$this->assertNotNull( $role, 'The role must be registered by the theme.' );
		$this->assertSame( array( self::ACCESS_CAP => true ), $role->capabilities );
	}

	/**
	 * The constants name the role and the capability that the code checks.
	 */
	public function test_constants_name_the_role_and_the_capability() {
		$this->assertSame( self::ROLE, DLI_RESERVED_AREA_USER_ROLE_SLUG );
		$this->assertSame( 'Utente area riservata', DLI_RESERVED_AREA_USER_ROLE_NAME );
		$this->assertSame( self::ACCESS_CAP, DLI_RESERVED_AREA_ACCESS_CAP );
	}

	/**
	 * The user has the theme capability but cannot read, write or manage anything in WordPress.
	 */
	public function test_user_with_the_role_has_no_wordpress_capability() {
		$this->assertNotNull( get_role( self::ROLE ) );
		$this->login_as( self::ROLE );

		$this->assertTrue( current_user_can( self::ACCESS_CAP ) );
		foreach ( array( 'read', 'edit_posts', 'edit_pages', 'upload_files', 'list_users', 'manage_options', DLI_EDIT_CONFIG_PERMISSION ) as $capability ) {
			$this->assertFalse( current_user_can( $capability ), "The role must not have '$capability'." );
		}
	}

	/**
	 * The pending user is still a separate role, without the theme capability.
	 */
	public function test_pending_user_role_is_kept_without_the_theme_capability() {
		$this->assertNotNull( get_role( DLI_PENDING_USER_ROLE_SLUG ) );
		$this->login_as( DLI_PENDING_USER_ROLE_SLUG );

		$this->assertFalse( current_user_can( self::ACCESS_CAP ) );
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
	 * The REST route that updates the own user answers 403 to the role.
	 */
	public function test_rest_update_of_the_own_user_is_refused() {
		$this->assertNotNull( get_role( self::ROLE ) );
		$this->login_as( self::ROLE );

		$request = new WP_REST_Request( 'POST', '/wp/v2/users/me' );
		$request->set_param( 'email', 'altro@example.org' );
		$response = rest_do_request( $request );

		$this->assertSame( 403, $response->get_status() );
	}

	/**
	 * An administrator can still edit a user with the role.
	 */
	public function test_administrator_can_edit_a_user_with_the_role() {
		$this->assertNotNull( get_role( self::ROLE ) );
		$target_id = self::factory()->user->create( array( 'role' => self::ROLE ) );
		$admin_id  = $this->login_as( 'administrator' );
		if ( is_multisite() ) {
			grant_super_admin( $admin_id );
		}

		$this->assertTrue( current_user_can( 'edit_user', $target_id ) );
	}

	/**
	 * Creating the role again does not rewrite a capability added to it in the meantime.
	 */
	public function test_creating_the_role_again_keeps_the_capabilities_added_later() {
		$role = get_role( self::ROLE );
		$this->assertNotNull( $role );
		$role->add_cap( 'dli_test_added_capability' );

		( new DLI_AuthorizationManager() )->add_reserved_area_user_role();

		$this->assertTrue( get_role( self::ROLE )->has_cap( 'dli_test_added_capability' ) );
	}
}
