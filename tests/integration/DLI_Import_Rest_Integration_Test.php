<?php
/**
 * Integration tests of the REST routes that start the Indico and IRIS imports, and of the switch of the REST API.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `POST custom/v1/indico-import` and `POST custom/v1/iris-patent-import`: registered only when the REST API is on,
 * POST only, HTTP Basic authentication of a user who can `manage_options`. The imports are off in the test site, so
 * an authorized call answers "module disabled" without reaching any remote service.
 */
class DLI_Import_Rest_Integration_Test extends DLI_Integration_Test_Case {

	const PASSWORD = 'dli-test-password-9c1a';

	/**
	 * The two routes, one data set each.
	 *
	 * @return array<string, array<int, string>>
	 */
	public static function routes() {
		return array(
			'indico' => array( '/custom/v1/indico-import' ),
			'iris'   => array( '/custom/v1/iris-patent-import' ),
		);
	}

	/**
	 * Turn the REST API of the theme on or off and rebuild the server, so that `rest_api_init` runs with the option.
	 *
	 * @param bool $enabled Whether the option is on.
	 */
	private function set_rest_api( $enabled ) {
		$this->set_theme_options( 'setup', array( 'rest_api_enabled' => $enabled ? 'true' : 'false' ) );
		$GLOBALS['wp_rest_server'] = null;
		rest_get_server();
	}

	/**
	 * Drop the REST server built with this test's options.
	 */
	public function tear_down() {
		$GLOBALS['wp_rest_server'] = null;
		parent::tear_down();
	}

	/**
	 * Create a user with the known password.
	 *
	 * @param string $role Role.
	 * @return string The login.
	 */
	private function create_user( $role ) {
		$login = 'rest_' . $role . '_' . wp_generate_password( 6, false );
		self::factory()->user->create(
			array(
				'role'       => $role,
				'user_login' => $login,
				'user_pass'  => self::PASSWORD,
			)
		);
		return $login;
	}

	/**
	 * The value of a Basic Authorization header.
	 *
	 * @param string $login    Login.
	 * @param string $password Password.
	 * @return string
	 */
	private function basic( $login, $password ) {
		return 'Basic ' . base64_encode( $login . ':' . $password ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- A test builds the header of HTTP Basic authentication.
	}

	/**
	 * Call a route.
	 *
	 * @param string      $route         Route.
	 * @param string|null $authorization Value of the Authorization header, or null for none.
	 * @param string      $method        HTTP method.
	 * @return WP_REST_Response
	 */
	private function call( $route, $authorization = null, $method = 'POST' ) {
		$request = new WP_REST_Request( $method, $route );
		if ( null !== $authorization ) {
			$request->set_header( 'Authorization', $authorization );
		}
		return rest_do_request( $request );
	}

	/**
	 * With the REST API off the route does not exist.
	 *
	 * @dataProvider routes
	 * @param string $route Route.
	 */
	public function test_the_route_does_not_exist_while_the_rest_api_is_off( $route ) {
		$this->set_rest_api( false );

		$response = $this->call( $route, $this->basic( $this->create_user( 'administrator' ), self::PASSWORD ) );

		$this->assertSame( 404, $response->get_status() );
		$this->assertSame( 'rest_no_route', $response->get_data()['code'] );
	}

	/**
	 * With the REST API on the route is POST only.
	 *
	 * @dataProvider routes
	 * @param string $route Route.
	 */
	public function test_the_route_accepts_only_post( $route ) {
		$this->set_rest_api( true );
		$this->assertArrayHasKey( $route, rest_get_server()->get_routes() );

		$response = $this->call( $route, $this->basic( $this->create_user( 'administrator' ), self::PASSWORD ), 'GET' );

		$this->assertSame( 404, $response->get_status(), 'A GET finds no route.' );
	}

	/**
	 * No credentials, no import.
	 *
	 * @dataProvider routes
	 * @param string $route Route.
	 */
	public function test_a_request_without_credentials_is_refused( $route ) {
		$this->set_rest_api( true );

		$response = $this->call( $route );

		$this->assertSame( 401, $response->get_status() );
		$this->assertSame( 'rest_not_logged_in', $response->get_data()['code'] );
	}

	/**
	 * A header that is not Basic, not base64 or without a password is refused.
	 *
	 * @dataProvider routes
	 * @param string $route Route.
	 */
	public function test_malformed_credentials_are_refused( $route ) {
		$this->set_rest_api( true );
		$headers = array(
			'another scheme'    => 'Bearer abc',
			'not base64'        => 'Basic !!!not-base64!!!',
			'without a colon'   => 'Basic ' . base64_encode( 'only-a-login' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- A test builds the header of HTTP Basic authentication.
			'an empty password' => $this->basic( $this->create_user( 'administrator' ), '' ),
		);

		foreach ( $headers as $name => $header ) {
			$response = $this->call( $route, $header );

			$this->assertSame( 401, $response->get_status(), $name );
			$this->assertSame( 'rest_authentication_failed', $response->get_data()['code'], $name );
		}
	}

	/**
	 * A wrong password and an unknown user are refused.
	 *
	 * @dataProvider routes
	 * @param string $route Route.
	 */
	public function test_wrong_credentials_are_refused( $route ) {
		$this->set_rest_api( true );
		$login = $this->create_user( 'administrator' );

		foreach ( array(
			'a wrong password' => $this->basic( $login, 'sbagliata' ),
			'an unknown user'  => $this->basic( 'utente_che_non_esiste', self::PASSWORD ),
		) as $name => $header ) {
			$response = $this->call( $route, $header );

			$this->assertSame( 401, $response->get_status(), $name );
			$this->assertSame( 'rest_authentication_failed', $response->get_data()['code'], $name );
		}
	}

	/**
	 * A user who cannot manage the options is refused, even with the right password.
	 *
	 * @dataProvider routes
	 * @param string $route Route.
	 */
	public function test_a_user_who_cannot_manage_options_is_refused( $route ) {
		$this->set_rest_api( true );

		foreach ( array( 'editor', 'subscriber' ) as $role ) {
			$response = $this->call( $route, $this->basic( $this->create_user( $role ), self::PASSWORD ) );

			$this->assertSame( 401, $response->get_status(), $role );
			$this->assertSame( __( 'Utente non autorizzato', 'design_laboratori_italia' ), $response->get_data()['message'], $role );
		}
	}

	/**
	 * An administrator with the right password reaches the importer. The import is off, so it answers without any remote call.
	 *
	 * @dataProvider routes
	 * @param string $route Route.
	 */
	public function test_an_administrator_reaches_the_importer( $route ) {
		$this->set_rest_api( true );

		$response = $this->call( $route, $this->basic( $this->create_user( 'administrator' ), self::PASSWORD ) );

		$this->assertSame( 500, $response->get_status() );
		$this->assertSame( MSG_MODULE_DISABLED, $response->get_data()['message'] );
	}

	/**
	 * The REST API of the site: off for a visitor, and still open to a logged in user.
	 */
	public function test_the_rest_api_switch_refuses_a_visitor_only() {
		$manager = DLI_LabManager::get_instance();
		$this->set_theme_options( 'setup', array( 'rest_api_enabled' => 'false' ) );

		wp_set_current_user( 0 );
		$refused = $manager->setup_rest_api( null );
		$this->assertWPError( $refused );
		$this->assertSame( 'rest_disabled', $refused->get_error_code() );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );
		$this->assertNull( $manager->setup_rest_api( null ), 'A logged in user is not refused by the switch.' );

		$this->set_theme_options( 'setup', array( 'rest_api_enabled' => 'true' ) );
		wp_set_current_user( 0 );
		$this->assertNull( $manager->setup_rest_api( null ), 'With the REST API on a visitor is not refused.' );
	}
}
