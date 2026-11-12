<?php
/**
 * The login screen of the reserved area: the texts of WP SAML Auth in Italian and the logo of the site in place of the
 * WordPress one.
 *
 * WP SAML Auth is not installed in the suite: its filter `wp_saml_auth_login_strings` is applied by hand with the
 * English strings that the plugin gives. The logo and the link of the screen are the core hooks of `wp-login.php`.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_reserved_area_login_strings()`, `dli_reserved_area_login_logo_style()` and the filters of the logo.
 */
class DLI_Reserved_Area_Login_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The strings that WP SAML Auth gives to its filter.
	 *
	 * @var array<string, string>
	 */
	const PLUGIN_STRINGS = array(
		'title'     => 'Use one-click authentication:',
		'button'    => 'Sign In',
		'alt_title' => 'Or, sign in with WordPress:',
	);

	/**
	 * Run with the Italian texts of the theme, and start with the reserved area on, no logo and a clean login style.
	 */
	public function set_up() {
		parent::set_up();
		$this->use_italian_texts();
		$this->configure_area( 'true' );
		$this->configure_logo( '' );
		wp_deregister_style( 'login' );
		wp_register_style( 'login', false );
	}

	/**
	 * Load the translations of the theme again and put the style of the login back.
	 */
	public function tear_down() {
		wp_deregister_style( 'login' );
		$this->restore_theme_translations();
		parent::tear_down();
	}

	/**
	 * Switch the reserved area on or off.
	 *
	 * @param string $enabled 'true' or 'false'.
	 */
	private function configure_area( $enabled ) {
		$this->set_theme_options( DLI_RESERVED_AREA_OPTION_KEY, array( 'reserved_area_enabled' => $enabled ) );
	}

	/**
	 * Set the logo of the header in the configuration.
	 *
	 * @param string $url Address of the logo, or an empty string for none.
	 */
	private function configure_logo( $url ) {
		$this->set_theme_options( 'dli_options', array( 'logo_laboratorio' => $url ) );
	}

	/**
	 * The CSS that the theme adds to the style of the login screen.
	 *
	 * @return string
	 */
	private function login_css() {
		do_action( 'login_enqueue_scripts' );
		return implode( "\n", (array) wp_styles()->get_data( 'login', 'after' ) );
	}

	/**
	 * The three texts of the screen are in Italian, in place of the English ones of the plugin.
	 */
	public function test_the_login_texts_are_in_italian() {
		$strings = apply_filters( 'wp_saml_auth_login_strings', self::PLUGIN_STRINGS );

		$this->assertSame( 'Autenticati con le credenziali istituzionali:', $strings['title'] );
		$this->assertSame( 'Accedi', $strings['button'] );
		$this->assertSame( 'Oppure accedi con le credenziali di WordPress:', $strings['alt_title'] );
	}

	/**
	 * A string that a version of the plugin may add is not lost.
	 */
	public function test_a_string_that_the_theme_does_not_know_is_kept() {
		$strings = apply_filters( 'wp_saml_auth_login_strings', self::PLUGIN_STRINGS + array( 'new_string' => 'Another text' ) );

		$this->assertSame( 'Another text', $strings['new_string'] );
	}

	/**
	 * With the reserved area off the texts of the plugin are not touched.
	 */
	public function test_the_texts_are_not_changed_with_the_reserved_area_off() {
		$this->configure_area( 'false' );

		$this->assertSame( self::PLUGIN_STRINGS, apply_filters( 'wp_saml_auth_login_strings', self::PLUGIN_STRINGS ) );
	}

	/**
	 * The logo of the header replaces the WordPress one on the login screen.
	 */
	public function test_the_logo_of_the_site_is_on_the_login_screen() {
		$this->configure_logo( 'https://example.org/wp-content/uploads/logo.svg' );

		$css = $this->login_css();

		$this->assertStringContainsString( 'url("https://example.org/wp-content/uploads/logo.svg")', $css );
		$this->assertStringContainsString( '.login h1 a', $css );
	}

	/**
	 * Without a configured logo the screen uses the default logo of the theme, as the header does, never the
	 * WordPress one.
	 */
	public function test_without_a_logo_the_default_logo_of_the_theme_is_used() {
		$css = $this->login_css();

		$this->assertStringContainsString( 'url("' . dli_get_default_logo() . '")', $css );
	}

	/**
	 * The address goes into the CSS safely: markup and quotes in it cannot close the rule or the style.
	 */
	public function test_a_hostile_logo_address_cannot_break_out_of_the_css() {
		$this->configure_logo( 'https://example.org/a.svg");}</style><script>alert(1)</script>' );

		$css = $this->login_css();

		$this->assertStringNotContainsString( '<', $css );
		$this->assertStringNotContainsString( '>', $css );
		$this->assertSame( 1, preg_match_all( '/url\("[^"]*"\)/', $css ), 'One well-formed url() in the CSS.' );
	}

	/**
	 * The logo leads to the home of the site and is named by the name of the site, in place of «Powered by WordPress».
	 */
	public function test_the_logo_leads_to_the_home_and_is_named_by_the_site() {
		update_option( 'blogname', 'Laboratorio <b>Prova</b>' );

		$this->assertSame( home_url( '/' ), apply_filters( 'login_headerurl', 'https://wordpress.org/' ) );
		$this->assertSame( 'Laboratorio &lt;b&gt;Prova&lt;/b&gt;', apply_filters( 'login_headertext', 'Powered by WordPress' ), 'The text is printed as it is by wp-login.php, so it is escaped.' );
	}

	/**
	 * With the reserved area off the login screen keeps the logo, the link and the text of WordPress.
	 */
	public function test_the_login_screen_is_not_changed_with_the_reserved_area_off() {
		$this->configure_area( 'false' );
		$this->configure_logo( 'https://example.org/wp-content/uploads/logo.svg' );

		$this->assertSame( 'https://wordpress.org/', apply_filters( 'login_headerurl', 'https://wordpress.org/' ) );
		$this->assertSame( 'Powered by WordPress', apply_filters( 'login_headertext', 'Powered by WordPress' ) );
		$this->assertSame( '', $this->login_css() );
	}
}
