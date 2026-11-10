<?php
/**
 * The reserved area: where its users land, the admin bar, the access only through single sign-on and the access
 * box of the page "Area riservata".
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The access rules of `inc/reserved-area.php` for the pending user and the reserved area user.
 */
class DLI_Reserved_Area_Access_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The page "Area riservata".
	 *
	 * @var int
	 */
	private $page_id = 0;

	/**
	 * Create the page and start with the reserved area switched off.
	 */
	public function set_up() {
		parent::set_up();
		$this->page_id = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => 'Area riservata',
				'post_status' => 'publish',
			)
		);
		$this->configure( 'false', 'false' );
	}

	/**
	 * Put back the user, the screen and the admin file.
	 */
	public function tear_down() {
		wp_set_current_user( 0 );
		set_current_screen( 'front' );
		unset( $GLOBALS['pagenow'] );
		parent::tear_down();
	}

	/**
	 * Save the settings of the tab, with the page of the test.
	 *
	 * @param string $enabled   Main switch, 'true' or 'false'.
	 * @param string $saml_only Access only through SAML, 'true' or 'false'.
	 */
	private function configure( $enabled, $saml_only ) {
		$this->set_theme_options(
			DLI_RESERVED_AREA_OPTION_KEY,
			array(
				'reserved_area_enabled'   => $enabled,
				'reserved_area_page_id'   => (string) $this->page_id,
				'reserved_area_saml_only' => $saml_only,
			)
		);
	}

	/**
	 * Create a user with a role.
	 *
	 * @param string $role Role slug.
	 * @return WP_User
	 */
	private function user( $role ) {
		return get_userdata( self::factory()->user->create( array( 'role' => $role ) ) );
	}

	/**
	 * The roles of the reserved area.
	 *
	 * @return string[]
	 */
	private function reserved_roles() {
		return array( DLI_PENDING_USER_ROLE_SLUG, DLI_RESERVED_AREA_USER_ROLE_SLUG );
	}

	// ---- Where the users land (L3) ----

	/**
	 * The address of the reserved area is the chosen page, or the home without one.
	 */
	public function test_reserved_area_url_is_the_chosen_page_or_the_home() {
		$this->configure( 'true', 'false' );
		$this->assertSame( get_permalink( $this->page_id ), dli_get_reserved_area_url() );

		$this->set_theme_options( DLI_RESERVED_AREA_OPTION_KEY, array( 'reserved_area_enabled' => 'true', 'reserved_area_page_id' => '' ) );
		$this->assertSame( dli_get_language_home_url( dli_current_language() ), dli_get_reserved_area_url() );
	}

	/**
	 * After the login a user of the reserved area who would go to the dashboard goes to the page instead.
	 */
	public function test_login_to_the_dashboard_goes_to_the_page() {
		$this->configure( 'true', 'false' );
		foreach ( $this->reserved_roles() as $role ) {
			$this->assertSame( get_permalink( $this->page_id ), apply_filters( 'login_redirect', admin_url(), '', $this->user( $role ) ), $role );
			$this->assertSame( get_permalink( $this->page_id ), apply_filters( 'login_redirect', '', '', $this->user( $role ) ), $role );
		}
	}

	/**
	 * A user who logs in from a page of the site goes back to that page.
	 */
	public function test_login_from_a_page_goes_back_to_it() {
		$this->configure( 'true', 'false' );
		$back = home_url( '/area-a/' );

		$this->assertSame( $back, apply_filters( 'login_redirect', $back, $back, $this->user( DLI_RESERVED_AREA_USER_ROLE_SLUG ) ) );
	}

	/**
	 * Other users, a failed login and a reserved area switched off keep the address of WordPress.
	 */
	public function test_login_redirect_is_unchanged_otherwise() {
		$this->configure( 'true', 'false' );
		$this->assertSame( admin_url(), apply_filters( 'login_redirect', admin_url(), '', $this->user( 'editor' ) ) );
		$this->assertSame( admin_url(), apply_filters( 'login_redirect', admin_url(), '', new WP_Error( 'failed' ) ) );

		$this->configure( 'false', 'false' );
		$this->assertSame( admin_url(), apply_filters( 'login_redirect', admin_url(), '', $this->user( DLI_PENDING_USER_ROLE_SLUG ) ) );
	}

	/**
	 * A user of the reserved area who opens the dashboard is sent to the page; the requests of the admin that
	 * serve the front end are left alone.
	 */
	public function test_admin_request_is_sent_to_the_page() {
		$this->configure( 'true', 'false' );
		set_current_screen( 'dashboard' );
		foreach ( $this->reserved_roles() as $role ) {
			wp_set_current_user( $this->user( $role )->ID );
			$this->assertSame( get_permalink( $this->page_id ), dli_get_reserved_area_admin_redirect(), $role );
		}

		$GLOBALS['pagenow'] = 'admin-post.php';
		$this->assertSame( '', dli_get_reserved_area_admin_redirect(), 'admin-post.php is left alone.' );

		$GLOBALS['pagenow'] = 'index.php';
		add_filter( 'wp_doing_ajax', '__return_true' );
		$this->assertSame( '', dli_get_reserved_area_admin_redirect(), 'An AJAX request is left alone.' );
		remove_filter( 'wp_doing_ajax', '__return_true' );
	}

	/**
	 * Other users, the front end and a reserved area switched off are not redirected.
	 */
	public function test_admin_redirect_only_for_the_roles_in_the_admin() {
		$this->configure( 'true', 'false' );
		set_current_screen( 'dashboard' );
		wp_set_current_user( $this->user( 'editor' )->ID );
		$this->assertSame( '', dli_get_reserved_area_admin_redirect(), 'An editor uses the dashboard.' );

		wp_set_current_user( $this->user( DLI_PENDING_USER_ROLE_SLUG )->ID );
		set_current_screen( 'front' );
		$this->assertSame( '', dli_get_reserved_area_admin_redirect(), 'The front end is not redirected.' );

		set_current_screen( 'dashboard' );
		$this->configure( 'false', 'false' );
		$this->assertSame( '', dli_get_reserved_area_admin_redirect(), 'Nothing happens with the reserved area off.' );
	}

	/**
	 * The admin bar is hidden from the users of the reserved area only.
	 */
	public function test_admin_bar_is_hidden_from_the_reserved_area_users() {
		$this->configure( 'true', 'false' );
		foreach ( $this->reserved_roles() as $role ) {
			wp_set_current_user( $this->user( $role )->ID );
			$this->assertFalse( apply_filters( 'show_admin_bar', true ), $role );
		}

		wp_set_current_user( $this->user( 'editor' )->ID );
		$this->assertTrue( apply_filters( 'show_admin_bar', true ), 'The editor keeps the bar.' );

		$this->configure( 'false', 'false' );
		wp_set_current_user( $this->user( DLI_PENDING_USER_ROLE_SLUG )->ID );
		$this->assertTrue( apply_filters( 'show_admin_bar', true ), 'Nothing changes with the reserved area off.' );
	}

	// ---- Access only through single sign-on (L5) ----

	/**
	 * With the access only through SAML, a user of the reserved area has no password field, no reset and no login
	 * with a password.
	 */
	public function test_saml_only_closes_the_password_ways() {
		$this->configure( 'true', 'true' );
		foreach ( $this->reserved_roles() as $role ) {
			$user = $this->user( $role );

			$this->assertFalse( apply_filters( 'show_password_fields', true, $user ), "$role: no password field." );
			$this->assertFalse( apply_filters( 'allow_password_reset', true, $user->ID ), "$role: no password reset." );
			$this->assertWPError( apply_filters( 'wp_authenticate_user', $user, 'secret' ), "$role: no login with a password." );
		}
	}

	/**
	 * The login with a password through the real WordPress function is refused, with a message that names the
	 * single sign-on.
	 */
	public function test_saml_only_refuses_wp_authenticate() {
		$this->configure( 'true', 'true' );
		$user_id = self::factory()->user->create(
			array(
				'role'       => DLI_RESERVED_AREA_USER_ROLE_SLUG,
				'user_login' => 'utente-ar',
				'user_pass'  => 'Password-di-prova-1',
			)
		);

		$result = wp_authenticate( 'utente-ar', 'Password-di-prova-1' );

		$this->assertWPError( $result );
		$this->assertSame( 'dli_reserved_area_saml_only', $result->get_error_code() );
		$this->assertGreaterThan( 0, $user_id );
	}

	/**
	 * The REST API cannot give a password to a user of the reserved area, not even an administrator's request.
	 */
	public function test_saml_only_refuses_a_password_through_rest() {
		$this->configure( 'true', 'true' );
		$target = $this->user( DLI_RESERVED_AREA_USER_ROLE_SLUG );
		$admin  = $this->user( 'administrator' );
		if ( is_multisite() ) {
			grant_super_admin( $admin->ID );
		}
		wp_set_current_user( $admin->ID );

		$request = new WP_REST_Request( 'POST', '/wp/v2/users/' . $target->ID );
		$request->set_param( 'password', 'Nuova-password-1' );
		$response = rest_do_request( $request );

		$this->assertSame( 403, $response->get_status() );

		$request = new WP_REST_Request( 'POST', '/wp/v2/users/' . $target->ID );
		$request->set_param( 'first_name', 'Mario' );
		$this->assertSame( 200, rest_do_request( $request )->get_status(), 'Other fields can still be changed.' );
	}

	/**
	 * Other users, and the users of the reserved area while the setting is off, keep the passwords.
	 */
	public function test_saml_only_leaves_the_others_alone() {
		$this->configure( 'true', 'true' );
		foreach ( array( 'editor', DLI_SUPER_EDITOR_ROLE_SLUG, 'administrator' ) as $role ) {
			$user = $this->user( $role );
			$this->assertTrue( apply_filters( 'show_password_fields', true, $user ), $role );
			$this->assertTrue( apply_filters( 'allow_password_reset', true, $user->ID ), $role );
			$this->assertSame( $user, apply_filters( 'wp_authenticate_user', $user, 'secret' ), $role );
		}

		foreach ( array( array( 'true', 'false' ), array( 'false', 'true' ) ) as $settings ) {
			$this->configure( $settings[0], $settings[1] );
			$user = $this->user( DLI_RESERVED_AREA_USER_ROLE_SLUG );
			$this->assertTrue( apply_filters( 'show_password_fields', true, $user ) );
			$this->assertTrue( apply_filters( 'allow_password_reset', true, $user->ID ) );
			$this->assertSame( $user, apply_filters( 'wp_authenticate_user', $user, 'secret' ) );
		}
	}

	// ---- Access box of the page "Area riservata" (L4) ----

	/**
	 * Render the access box as the page template does.
	 *
	 * @return string
	 */
	private function render_box() {
		ob_start();
		dli_render_reserved_area_access( get_post( $this->page_id ) );
		return (string) ob_get_clean();
	}

	/**
	 * Nothing is printed on another page, or with the reserved area switched off.
	 */
	public function test_box_only_on_the_chosen_page() {
		$this->configure( 'true', 'false' );
		$other = self::factory()->post->create( array( 'post_type' => 'page' ) );
		ob_start();
		dli_render_reserved_area_access( get_post( $other ) );
		$this->assertSame( '', trim( (string) ob_get_clean() ) );

		$this->configure( 'false', 'false' );
		$this->assertSame( '', trim( $this->render_box() ) );
	}

	/**
	 * A visitor sees the login button, which starts the single sign-on when WP SAML Auth is there and comes back
	 * to the page.
	 */
	public function test_visitor_sees_the_login_button() {
		$this->configure( 'true', 'false' );
		$html = $this->render_box();

		$this->assertStringContainsString( 'class="btn btn-primary"', $html );
		$this->assertStringContainsString( esc_url( dli_get_reserved_area_login_url( get_permalink( $this->page_id ) ) ), $html );
		$this->assertStringContainsString( 'redirect_to=' . rawurlencode( get_permalink( $this->page_id ) ), dli_get_reserved_area_login_url( get_permalink( $this->page_id ) ) );
	}

	/**
	 * The login address asks WP SAML Auth for the single sign-on only when the plugin is active.
	 */
	public function test_login_url_asks_for_saml_only_with_the_plugin() {
		$url = dli_get_reserved_area_login_url( home_url( '/' ) );
		$this->assertSame( class_exists( 'WP_SAML_Auth' ), false !== strpos( $url, 'action=wp-saml-auth' ) );

		add_filter( 'dli_reserved_area_saml_available', '__return_true' );
		$this->assertStringContainsString( 'action=wp-saml-auth', dli_get_reserved_area_login_url( home_url( '/' ) ) );
		remove_filter( 'dli_reserved_area_saml_available', '__return_true' );
	}

	/**
	 * A pending user reads that the request waits for approval; an approved user does not.
	 */
	public function test_logged_in_users_see_their_state_and_the_logout() {
		$this->configure( 'true', 'false' );

		wp_set_current_user( $this->user( DLI_PENDING_USER_ROLE_SLUG )->ID );
		$html = $this->render_box();
		$this->assertStringContainsString( 'in attesa di approvazione', $html );
		$this->assertStringContainsString( 'action=logout', $html );
		$this->assertStringNotContainsString( 'btn btn-primary', $html );

		wp_set_current_user( $this->user( DLI_RESERVED_AREA_USER_ROLE_SLUG )->ID );
		$html = $this->render_box();
		$this->assertStringNotContainsString( 'in attesa di approvazione', $html );
		$this->assertStringContainsString( 'action=logout', $html );
	}

	/**
	 * The box is found also on the translation of the chosen page.
	 */
	public function test_box_on_the_translation_of_the_page() {
		$this->configure( 'true', 'false' );
		$language = PLL()->model->languages->add(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => false,
				'flag'   => 'gb',
			)
		);
		$this->assertNotWPError( $language );
		$english = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
			)
		);
		dli_set_post_language( $this->page_id, 'it' );
		dli_set_post_language( $english, 'en' );
		dli_save_post_translations(
			array(
				'it' => $this->page_id,
				'en' => $english,
			)
		);

		$this->assertTrue( dli_is_reserved_area_page( get_post( $english ) ) );
		$this->assertTrue( dli_is_reserved_area_page( get_post( $this->page_id ) ) );
	}
}
