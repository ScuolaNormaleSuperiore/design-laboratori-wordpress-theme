<?php
/**
 * The CAPTCHA of the newsletter page: a challenge is used once, answered or not.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `page-templates/newsletter.php` with the in-memory CAPTCHA and Brevo intercepted at the HTTP boundary, so that no
 * subscription is ever sent and the API token never reaches the network.
 */
class DLI_Newsletter_Page_Captcha_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Requests that reached the HTTP mock.
	 *
	 * @var int
	 */
	private $requests = 0;

	/**
	 * Add the optional CAPTCHA plugin to the active plugins seen by the theme.
	 *
	 * @param string[] $plugins Active plugin paths.
	 * @return string[]
	 */
	public function enable_test_captcha_plugin( $plugins ) {
		$plugins[] = 'really-simple-captcha/really-simple-captcha.php';
		return array_unique( $plugins );
	}

	/**
	 * Accept every Brevo request and count it.
	 *
	 * @return array
	 */
	public function accept_brevo_request() {
		++$this->requests;

		return array(
			'headers'  => array(),
			'body'     => '',
			'response' => array(
				'code'    => 201,
				'message' => 'Created',
			),
		);
	}

	/**
	 * Enable the newsletter, its CAPTCHA and the HTTP mock.
	 */
	public function set_up() {
		parent::set_up();
		ReallySimpleCaptcha::reset();

		$this->set_theme_options(
			'setup',
			array(
				'newsletter_enabled'     => 'true',
				'newsletter_api_token'   => 'test-token',
				'newsletter_list_id'     => '42',
				'newsletter_template_id' => '7',
			)
		);
		add_filter( 'pre_option_active_plugins', array( $this, 'enable_test_captcha_plugin' ), 20 );
		add_filter( 'pre_http_request', array( $this, 'accept_brevo_request' ), 10, 0 );
	}

	/**
	 * Remove the filters before the base case clears its option cache.
	 */
	public function tear_down() {
		remove_filter( 'pre_option_active_plugins', array( $this, 'enable_test_captcha_plugin' ), 20 );
		remove_filter( 'pre_http_request', array( $this, 'accept_brevo_request' ), 10 );
		parent::tear_down();
	}

	/**
	 * Submit the form and return the rendered page.
	 *
	 * @param array<string, string> $overrides Fields to replace.
	 * @return string
	 */
	private function submit( array $overrides = array() ) {
		$original_post = $_POST;
		$_POST         = array_merge(
			array(
				'newsletter_nonce_field' => wp_create_nonce( 'sf_newsletter_nonce' ),
				'form_sent'              => 'yes',
				'user_name'              => 'Ada',
				'user_surname'           => 'Lovelace',
				'user_mail'              => 'ada@example.test',
				'captcha-prefix'         => 'test-prefix',
				'captcha-field'          => 'ABCD',
			),
			$overrides
		);

		try {
			return $this->render_theme_template( 'page-templates/newsletter.php' );
		} finally {
			$_POST = $original_post;
		}
	}

	/**
	 * A challenge is used once: the same challenge and answer, sent again, subscribe nobody.
	 */
	public function test_a_captcha_can_be_used_only_once() {
		$this->submit();
		$this->assertSame( 1, $this->requests, 'The first use subscribes.' );
		$this->assertContains( 'test-prefix', ReallySimpleCaptcha::$removed );

		$replay = $this->submit();
		$this->assertSame( 1, $this->requests, 'The replay subscribes nobody.' );
		$this->assertStringNotContainsString( 'alert-success', $replay );
	}

	/**
	 * A wrong answer consumes the challenge too.
	 */
	public function test_a_wrong_answer_consumes_the_challenge() {
		$this->submit( array( 'captcha-field' => 'WRONG' ) );
		$this->assertSame( 0, $this->requests );
		$this->assertContains( 'test-prefix', ReallySimpleCaptcha::$removed, 'A wrong answer consumes the challenge.' );

		$this->submit();
		$this->assertSame( 0, $this->requests, 'The right answer to the consumed challenge is refused.' );
	}

	/**
	 * After a submission the form carries a new challenge that can be answered.
	 */
	public function test_the_form_comes_back_with_a_new_challenge() {
		$html = $this->submit( array( 'captcha-field' => 'WRONG' ) );

		$this->assertSame( 1, preg_match( '#name="captcha-prefix"[^>]*value="([^"]*)"#', $html, $found ), 'The form carries a challenge.' );
		$this->assertNotSame( 'test-prefix', $found[1] );
		$this->assertArrayHasKey( $found[1], ReallySimpleCaptcha::$challenges );
	}
}
