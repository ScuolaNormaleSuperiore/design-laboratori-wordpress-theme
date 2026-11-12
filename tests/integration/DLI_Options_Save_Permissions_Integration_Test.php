<?php
/**
 * Saving the options pages of the theme ("Configurazione"): who can do it, also without opening the page.
 *
 * CMB2 saves an options page through `admin-post.php?action=<option_key>` and checks the nonce of the form; the
 * capability that the theme gives to the page (`dli_edit_site_configuration`, or `manage_options` for the reserved
 * tabs) is what shows the page in the menu. A request that does not come from the page must not be able to save
 * if the user has no right to the page.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The save handler of CMB2 (`CMB2_Options_Hookup::save_options()`) on the real boxes of the theme.
 */
class DLI_Options_Save_Permissions_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The save handlers that the test attached, to be detached afterwards.
	 *
	 * @var array<int, string>
	 */
	private $attached = array();

	/**
	 * The boxes that the test registered, to be removed afterwards: the registry of CMB2 is shared by all the tests.
	 *
	 * @var array<int, string>
	 */
	private $registered_boxes = array();

	/**
	 * The request that was in place before the test.
	 *
	 * @var array<string, mixed>
	 */
	private $previous_post = array();

	/**
	 * The admin file that was in place before the test.
	 *
	 * @var string|null
	 */
	private $previous_pagenow = null;

	/**
	 * Keep the request and make the handler end with an exception instead of `exit`.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_post    = $_POST;
		$this->previous_pagenow = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : null;
		add_filter( 'wp_redirect', array( $this, 'stop_at_redirect' ) );
	}

	/**
	 * Detach what the test attached and restore the request, the boxes and the user.
	 */
	public function tear_down() {
		remove_filter( 'wp_redirect', array( $this, 'stop_at_redirect' ) );
		foreach ( $this->attached as $option_key ) {
			remove_all_actions( 'admin_post_' . $option_key );
		}
		foreach ( $this->registered_boxes as $box_id ) {
			CMB2_Boxes::remove( $box_id );
		}
		$_POST            = $this->previous_post;
		$_REQUEST         = $this->previous_post;
		$GLOBALS['pagenow'] = $this->previous_pagenow;
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * The handler ends with a redirect and `exit`: the redirect is turned into an exception, which the test catches.
	 *
	 * @throws RuntimeException Always.
	 */
	public function stop_at_redirect() {
		throw new RuntimeException( 'redirect' );
	}

	/**
	 * Create a user with a role.
	 *
	 * @param string $role Role.
	 * @return int User ID.
	 */
	private function create_user( $role ) {
		return self::factory()->user->create( array( 'role' => $role ) );
	}

	/**
	 * Register the boxes of the options pages as WordPress does, on a request of the admin of a user.
	 *
	 * The boxes are registered on `cmb2_admin_init`, which does not run outside the admin; the tabs reserved to the
	 * administrator are registered only when the user of the request is one.
	 *
	 * @param int $user_id The user of the request.
	 */
	private function register_boxes_as( $user_id ) {
		wp_set_current_user( $user_id );
		dli_register_main_options_metabox();
		dli_register_setup_options();
		foreach ( array( 'dli_options_socials', 'dli_options_indico', 'dli_options_iris', 'dli_setup_menu' ) as $box_id ) {
			if ( CMB2_Boxes::get( $box_id ) ) {
				$this->registered_boxes[] = $box_id;
			}
		}
	}

	/**
	 * Send the save request of an options page as a user, the way the form of the page does: with the nonce of the
	 * box, which the user is given only by the page. The request does not go through the page.
	 *
	 * @param int                  $user_id    The user.
	 * @param string               $box_id     The id of the CMB2 box.
	 * @param string               $option_key The option the box saves into.
	 * @param array<string, mixed> $values     The values of the fields.
	 */
	private function save_as( $user_id, $box_id, $option_key, array $values ) {
		// The request is the one of `admin-post.php`: CMB2 takes the type of the object (an options page) from the file
		// and from the action, so they are in place before the boxes are registered, as in a real request.
		$GLOBALS['pagenow'] = 'admin-post.php';
		$_POST              = array( 'action' => $option_key );
		$this->register_boxes_as( $user_id );
		$cmb = CMB2_Boxes::get( $box_id );
		$this->assertInstanceOf( CMB2::class, $cmb, "The box $box_id is registered." );

		if ( ! in_array( $option_key, $this->attached, true ) ) {
			$hookup = new CMB2_Options_Hookup( $cmb, $option_key );
			add_action( 'admin_post_' . $option_key, array( $hookup, 'save_options' ) );
			$this->attached[] = $option_key;
		}

		wp_set_current_user( $user_id );
		$_POST    = array_merge(
			$values,
			array(
				'action'      => $option_key,
				'submit-cmb'  => 'Save',
				$cmb->nonce() => wp_create_nonce( $cmb->nonce() ),
			)
		);
		$_REQUEST = $_POST;

		try {
			do_action( 'admin_post_' . $option_key );
			$this->fail( 'The handler should end with a redirect.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect', $exception->getMessage() );
		}
	}

	/**
	 * A user without the right to the page cannot save it, even sending the request by hand.
	 */
	public function test_editor_and_subscriber_cannot_save_an_options_page() {
		foreach ( array( 'editor', 'subscriber' ) as $role ) {
			$this->set_theme_options( 'socials', array( 'facebook' => 'https://example.org/original' ) );

			$this->save_as( $this->create_user( $role ), 'dli_options_socials', 'socials', array( 'facebook' => 'https://example.org/changed-by-' . $role ) );

			$this->assertSame( 'https://example.org/original', cmb2_options( 'socials' )->get( 'facebook' ), "A $role cannot save the page." );
		}
	}

	/**
	 * The Super Editor and the administrator can save the pages of the configuration.
	 */
	public function test_super_editor_and_administrator_can_save_an_options_page() {
		foreach ( array( DLI_SUPER_EDITOR_ROLE_SLUG, 'administrator' ) as $role ) {
			$this->set_theme_options( 'socials', array( 'facebook' => 'https://example.org/original' ) );

			$this->save_as( $this->create_user( $role ), 'dli_options_socials', 'socials', array( 'facebook' => 'https://example.org/changed-by-' . $role ) );

			$this->assertSame( 'https://example.org/changed-by-' . $role, cmb2_options( 'socials' )->get( 'facebook' ), "A $role can save the page." );
		}
	}

	/**
	 * The tabs Indico, Iris and Altro are reserved to the administrator: for any other user they are not even
	 * registered, so no handler exists that could save them.
	 */
	public function test_reserved_tabs_exist_only_for_the_administrator() {
		$reserved = array( 'dli_options_indico', 'dli_options_iris', 'dli_setup_menu' );

		$this->register_boxes_as( $this->create_user( DLI_SUPER_EDITOR_ROLE_SLUG ) );
		foreach ( $reserved as $box_id ) {
			$this->assertFalse( CMB2_Boxes::get( $box_id ), "$box_id is not registered for the Super Editor." );
		}
		$this->assertInstanceOf( CMB2::class, CMB2_Boxes::get( 'dli_options_socials' ), 'The pages of the configuration are registered for the Super Editor.' );

		foreach ( $this->registered_boxes as $box_id ) {
			CMB2_Boxes::remove( $box_id );
		}
		$this->registered_boxes = array();

		$this->register_boxes_as( $this->create_user( 'administrator' ) );
		foreach ( $reserved as $box_id ) {
			$this->assertInstanceOf( CMB2::class, CMB2_Boxes::get( $box_id ), "$box_id is registered for the administrator." );
		}
	}
}
