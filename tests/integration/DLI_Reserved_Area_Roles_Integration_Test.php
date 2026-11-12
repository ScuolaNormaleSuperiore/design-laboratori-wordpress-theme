<?php
/**
 * What the two roles of the reserved area have in common: a role with no WordPress capability (or with the theme
 * marker only) whose user cannot edit the own profile, also through REST.
 *
 * The role "Utente in attesa" and the role "Utente area riservata" follow the same code
 * (`DLI_AuthorizationManager`), so each rule is checked once for both roles with a data provider. What is specific to
 * one role is in `DLI_Pending_User_Role_Integration_Test` and `DLI_Reserved_Area_User_Role_Integration_Test`.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_AuthorizationManager::add_pending_user_role()`, `add_reserved_area_user_role()` and the rule that closes the
 * own profile.
 */
class DLI_Reserved_Area_Roles_Integration_Test extends DLI_Integration_Test_Case {

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
	 * The two roles: slug, capabilities that the role has and the method that creates it.
	 *
	 * @return array<string, array{0: string, 1: array<string, bool>, 2: string}>
	 */
	public function roles() {
		return array(
			'pending user'       => array( DLI_PENDING_USER_ROLE_SLUG, array(), 'add_pending_user_role' ),
			'reserved area user' => array( DLI_RESERVED_AREA_USER_ROLE_SLUG, array( DLI_RESERVED_AREA_ACCESS_CAP => true ), 'add_reserved_area_user_role' ),
		);
	}

	/**
	 * The role exists with its capabilities and no other: none for the pending user, the theme marker for the user
	 * of the reserved area.
	 *
	 * @dataProvider roles
	 *
	 * @param string               $slug         Role slug.
	 * @param array<string, bool> $capabilities Capabilities of the role.
	 */
	public function test_role_exists_with_its_capabilities( $slug, $capabilities ) {
		$role = get_role( $slug );

		$this->assertNotNull( $role, 'The role must be registered by the theme.' );
		$this->assertSame( $capabilities, $role->capabilities );
	}

	/**
	 * The user cannot edit the own profile, which WordPress otherwise allows to everybody.
	 *
	 * @dataProvider roles
	 *
	 * @param string $slug Role slug.
	 */
	public function test_user_with_the_role_cannot_edit_the_own_profile( $slug ) {
		$this->assertNotNull( get_role( $slug ) );
		$user_id = $this->login_as( $slug );

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
	 * The REST route that updates the own user answers 403 to the role, whichever field is sent.
	 *
	 * @dataProvider roles
	 *
	 * @param string $slug Role slug.
	 */
	public function test_rest_update_of_the_own_user_is_refused( $slug ) {
		$this->assertNotNull( get_role( $slug ) );
		$this->login_as( $slug );

		foreach ( array(
			'first_name' => 'Cambiato',
			'email'      => 'altro@example.org',
		) as $field => $value ) {
			$request = new WP_REST_Request( 'POST', '/wp/v2/users/me' );
			$request->set_param( $field, $value );
			$response = rest_do_request( $request );

			$this->assertSame( 403, $response->get_status(), "The update of '$field' must be refused." );
		}
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
	 * Creating the role again does not rewrite a capability added to it in the meantime.
	 *
	 * @dataProvider roles
	 *
	 * @param string               $slug         Role slug.
	 * @param array<string, bool> $capabilities Capabilities of the role (not used here).
	 * @param string               $method       Method of `DLI_AuthorizationManager` that creates the role.
	 */
	public function test_creating_the_role_again_keeps_the_capabilities_added_later( $slug, $capabilities, $method ) {
		$role = get_role( $slug );
		$this->assertNotNull( $role );
		$role->add_cap( 'dli_test_added_capability' );

		( new DLI_AuthorizationManager() )->$method();

		$this->assertTrue( get_role( $slug )->has_cap( 'dli_test_added_capability' ) );
	}
}
