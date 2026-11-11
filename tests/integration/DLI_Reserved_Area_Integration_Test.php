<?php
/**
 * The reserved area: the tab "Area riservata" of the configuration, its settings and the promotion of a pending
 * user who enters a group of the Groups plugin.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! class_exists( 'Groups_Group' ) ) {
	/**
	 * Stand-in for the group class of the Groups plugin, which the test suite does not install: only the lookup of
	 * a group by ID, with the groups the test declares.
	 */
	class Groups_Group {

		/**
		 * Names of the groups the test declares, by ID.
		 *
		 * @var array<int, string>
		 */
		public static $names = array();

		/**
		 * The group with an ID, or false.
		 *
		 * @param int $group_id Group ID.
		 * @return object|false
		 */
		public static function read( $group_id ) {
			if ( ! isset( self::$names[ (int) $group_id ] ) ) {
				return false;
			}
			return (object) array(
				'group_id' => (int) $group_id,
				'name'     => self::$names[ (int) $group_id ],
			);
		}
	}
}

/**
 * `inc/reserved-area.php`.
 */
class DLI_Reserved_Area_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * ID of the box of the tab.
	 */
	const BOX_ID = 'dli_options_area_riservata';

	/**
	 * Group IDs used by the tests.
	 */
	const GROUP_REGISTERED = 1;
	const GROUP_AREA_A     = 2;

	/**
	 * Declare the groups and start every test with the reserved area switched off.
	 */
	public function set_up() {
		parent::set_up();
		Groups_Group::$names = array(
			self::GROUP_REGISTERED => 'Registered',
			self::GROUP_AREA_A     => 'Area A',
		);
		// Explicit values: CMB2 does not empty its in-memory copy of a group when given an empty array.
		$this->configure(
			array(
				'reserved_area_enabled'   => 'false',
				'reserved_area_page_id'   => '',
				'reserved_area_saml_only' => 'false',
			)
		);
	}

	/**
	 * Remove the box and put the current user back to nobody.
	 */
	public function tear_down() {
		CMB2_Boxes::remove( self::BOX_ID );
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * Save the settings of the tab.
	 *
	 * @param array<string, mixed> $values Values.
	 */
	private function configure( array $values ) {
		$this->set_theme_options( DLI_RESERVED_AREA_OPTION_KEY, $values );
	}

	/**
	 * The tab is registered for the administrator only.
	 */
	public function test_tab_is_registered_for_the_administrator_only() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => DLI_SUPER_EDITOR_ROLE_SLUG ) ) );
		dli_register_reserved_area_options();
		$this->assertFalse( CMB2_Boxes::get( self::BOX_ID ), 'The tab is not registered for the Super Editor.' );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		dli_register_reserved_area_options();
		$cmb = CMB2_Boxes::get( self::BOX_ID );
		$this->assertInstanceOf( CMB2::class, $cmb, 'The tab is registered for the administrator.' );
		$this->assertSame( DLI_ADMIN_EDIT_CONFIG_PERMISSION, $cmb->prop( 'capability' ) );
		$this->assertSame( 'dli_options', $cmb->prop( 'tab_group' ) );
		foreach ( array( 'reserved_area_enabled', 'reserved_area_page_id', 'reserved_area_saml_only' ) as $field_id ) {
			$this->assertNotFalse( $cmb->get_field( $field_id ), "The tab has the field $field_id." );
		}
	}

	/**
	 * The two settings that depend on the main switch carry the class that the script of the page disables.
	 */
	public function test_dependent_fields_carry_the_class_of_the_script() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		dli_register_reserved_area_options();
		$cmb = CMB2_Boxes::get( self::BOX_ID );

		foreach ( array( 'reserved_area_page_id', 'reserved_area_saml_only' ) as $field_id ) {
			$this->assertStringContainsString( 'dli-reserved-area-dependent', (string) $cmb->get_field( $field_id )->args( 'classes' ) );
		}
		$this->assertStringNotContainsString( 'dli-reserved-area-dependent', (string) $cmb->get_field( 'reserved_area_enabled' )->args( 'classes' ) );
	}

	/**
	 * By default the reserved area is switched off and so are its settings.
	 */
	public function test_reserved_area_is_off_by_default() {
		delete_option( DLI_RESERVED_AREA_OPTION_KEY );
		cmb2_options( DLI_RESERVED_AREA_OPTION_KEY )->set( array( 'unrelated' => 'x' ) );

		$this->assertFalse( dli_is_reserved_area_enabled() );
		$this->assertFalse( dli_is_reserved_area_saml_only() );
		$this->assertSame( 0, dli_get_reserved_area_page_id() );
	}

	/**
	 * With the main switch on, the settings are read.
	 */
	public function test_settings_are_read_when_the_reserved_area_is_on() {
		$page_id = self::factory()->post->create( array( 'post_type' => 'page' ) );
		$this->configure(
			array(
				'reserved_area_enabled'   => 'true',
				'reserved_area_page_id'   => (string) $page_id,
				'reserved_area_saml_only' => 'true',
			)
		);

		$this->assertTrue( dli_is_reserved_area_enabled() );
		$this->assertTrue( dli_is_reserved_area_saml_only() );
		$this->assertSame( $page_id, dli_get_reserved_area_page_id() );
	}

	/**
	 * With the main switch off, the other settings are ignored even when saved.
	 */
	public function test_settings_are_ignored_when_the_reserved_area_is_off() {
		$page_id = self::factory()->post->create( array( 'post_type' => 'page' ) );
		$this->configure(
			array(
				'reserved_area_enabled'   => 'false',
				'reserved_area_page_id'   => (string) $page_id,
				'reserved_area_saml_only' => 'true',
			)
		);

		$this->assertFalse( dli_is_reserved_area_saml_only() );
		$this->assertSame( 0, dli_get_reserved_area_page_id() );
	}

	/**
	 * A pending user who enters a group created by the administrator becomes a reserved area user.
	 */
	public function test_pending_user_is_promoted_when_entering_a_group() {
		$this->configure( array( 'reserved_area_enabled' => 'true' ) );
		$user_id = self::factory()->user->create( array( 'role' => DLI_PENDING_USER_ROLE_SLUG ) );

		do_action( 'groups_created_user_group', $user_id, self::GROUP_AREA_A );

		$roles = get_userdata( $user_id )->roles;
		$this->assertContains( DLI_RESERVED_AREA_USER_ROLE_SLUG, $roles );
		$this->assertNotContains( DLI_PENDING_USER_ROLE_SLUG, $roles );
	}

	/**
	 * The administrator gives the first group from the profile of the user.
	 *
	 * Groups reports the membership while the profile is being saved (`edit_user_profile_update`), and WordPress then
	 * saves the role that the form still shows, which is the pending one: the promotion must survive that save.
	 */
	public function test_the_promotion_survives_the_save_of_the_profile() {
		$this->configure( array( 'reserved_area_enabled' => 'true' ) );
		$user_id = self::factory()->user->create( array( 'role' => DLI_PENDING_USER_ROLE_SLUG ) );
		$report  = function ( $id ) {
			do_action( 'groups_created_user_group', $id, self::GROUP_AREA_A );
		};
		add_action( 'edit_user_profile_update', $report );

		// What edit_user() does: the action first, then the save with the role of the form.
		do_action( 'edit_user_profile_update', $user_id );
		wp_update_user(
			array(
				'ID'   => $user_id,
				'role' => DLI_PENDING_USER_ROLE_SLUG,
			)
		);
		remove_action( 'edit_user_profile_update', $report );

		$roles = get_userdata( $user_id )->roles;
		$this->assertContains( DLI_RESERVED_AREA_USER_ROLE_SLUG, $roles );
		$this->assertNotContains( DLI_PENDING_USER_ROLE_SLUG, $roles );
	}

	/**
	 * Control: saving the profile of a pending user who is in no group leaves the user pending.
	 */
	public function test_saving_the_profile_without_a_group_does_not_promote() {
		$this->configure( array( 'reserved_area_enabled' => 'true' ) );
		$user_id = self::factory()->user->create( array( 'role' => DLI_PENDING_USER_ROLE_SLUG ) );

		do_action( 'edit_user_profile_update', $user_id );
		wp_update_user(
			array(
				'ID'   => $user_id,
				'role' => DLI_PENDING_USER_ROLE_SLUG,
			)
		);

		$this->assertSame( array( DLI_PENDING_USER_ROLE_SLUG ), array_values( get_userdata( $user_id )->roles ) );
	}

	/**
	 * The group Registered, which Groups gives to every new user, does not promote anybody.
	 */
	public function test_entering_the_registered_group_does_not_promote() {
		$this->configure( array( 'reserved_area_enabled' => 'true' ) );
		$user_id = self::factory()->user->create( array( 'role' => DLI_PENDING_USER_ROLE_SLUG ) );

		do_action( 'groups_created_user_group', $user_id, self::GROUP_REGISTERED );

		$this->assertSame( array( DLI_PENDING_USER_ROLE_SLUG ), array_values( get_userdata( $user_id )->roles ) );
	}

	/**
	 * A group that cannot be read (deleted, or Groups not answering) does not promote anybody.
	 */
	public function test_an_unknown_group_does_not_promote() {
		$this->configure( array( 'reserved_area_enabled' => 'true' ) );
		$user_id = self::factory()->user->create( array( 'role' => DLI_PENDING_USER_ROLE_SLUG ) );

		do_action( 'groups_created_user_group', $user_id, 999 );

		$this->assertSame( array( DLI_PENDING_USER_ROLE_SLUG ), array_values( get_userdata( $user_id )->roles ) );
	}

	/**
	 * With the reserved area switched off nobody is promoted.
	 */
	public function test_no_promotion_when_the_reserved_area_is_off() {
		$user_id = self::factory()->user->create( array( 'role' => DLI_PENDING_USER_ROLE_SLUG ) );

		do_action( 'groups_created_user_group', $user_id, self::GROUP_AREA_A );

		$this->assertSame( array( DLI_PENDING_USER_ROLE_SLUG ), array_values( get_userdata( $user_id )->roles ) );
	}

	/**
	 * A user created by WP SAML Auth gets a neutral slug, so the email used as username does not appear in the
	 * address of the author or in the REST API.
	 */
	public function test_user_created_by_saml_gets_a_neutral_slug() {
		$user_id = self::factory()->user->create(
			array(
				'role'       => DLI_PENDING_USER_ROLE_SLUG,
				'user_login' => 'mario.rossi@example.org',
				'user_email' => 'mario.rossi@example.org',
			)
		);
		$this->assertStringContainsString( 'mario', get_userdata( $user_id )->user_nicename, 'WordPress builds the slug from the email.' );

		do_action( 'wp_saml_auth_new_user_authenticated', get_userdata( $user_id ), array() );

		clean_user_cache( $user_id );
		$user = get_userdata( $user_id );
		$this->assertSame( 'utente-' . $user_id, $user->user_nicename );
		$this->assertSame( 'mario.rossi@example.org', $user->user_login, 'The username does not change.' );
		$this->assertSame( 'mario.rossi@example.org', $user->user_email, 'The email does not change.' );
	}

	/**
	 * Only the pending user is promoted: the role of any other user who enters a group does not change.
	 */
	public function test_other_roles_are_not_changed_by_a_group() {
		$this->configure( array( 'reserved_area_enabled' => 'true' ) );
		foreach ( array( 'subscriber', 'editor', DLI_RESERVED_AREA_USER_ROLE_SLUG ) as $role ) {
			$user_id = self::factory()->user->create( array( 'role' => $role ) );

			do_action( 'groups_created_user_group', $user_id, self::GROUP_AREA_A );

			$this->assertSame( array( $role ), array_values( get_userdata( $user_id )->roles ), "The role $role does not change." );
		}
	}
}
