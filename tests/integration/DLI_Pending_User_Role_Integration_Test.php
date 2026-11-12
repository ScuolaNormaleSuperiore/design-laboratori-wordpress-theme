<?php
/**
 * The role "Utente in attesa": a user who is authenticated and can do nothing, not even edit the own profile.
 *
 * What it shares with the role of the reserved area (the role exists, the own profile is closed, also through REST,
 * the role is created again without losing a capability) is in `DLI_Reserved_Area_Roles_Integration_Test`.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_AuthorizationManager::add_pending_user_role()`.
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
}
