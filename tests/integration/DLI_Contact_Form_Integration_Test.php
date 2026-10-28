<?php
/**
 * Integration tests for the contact form submission flow.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! class_exists( 'ReallySimpleCaptcha' ) ) {
	/**
	 * Minimal in-memory CAPTCHA replacement used only by this test process.
	 */
	class ReallySimpleCaptcha {
		public $chars;
		public $char_length;
		public $img_size;
		public $fg;
		public $bg;
		public $font_size;
		public $font_char_width;
		public $img_type;

		public function generate_random_word() {
			return 'ABCD';
		}

		public function generate_image( $prefix, $word ) {
			return 'test-captcha.png';
		}

		public function check( $prefix, $response ) {
			return 'test-prefix' === $prefix && 'ABCD' === $response;
		}
	}
}

/**
 * The form is rendered with a valid CAPTCHA and wp_mail() is intercepted
 * before WordPress can reach a mail transport.
 */
class DLI_Contact_Form_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Captured wp_mail() calls.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	private $mail_calls = array();

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
	 * Configure the form and its in-memory CAPTCHA.
	 */
	public function set_up() {
		parent::set_up();

		$this->set_theme_options(
			'dli_options',
			array(
				'email_laboratorio' => 'laboratorio@example.test',
				'nome_laboratorio'  => 'Laboratorio di prova',
			)
		);
		add_filter( 'pre_option_active_plugins', array( $this, 'enable_test_captcha_plugin' ), 20 );
	}

	/**
	 * Remove filters before the base case clears its option cache.
	 */
	public function tear_down() {
		remove_filter( 'pre_option_active_plugins', array( $this, 'enable_test_captcha_plugin' ), 20 );
		remove_filter( 'pre_wp_mail', array( $this, 'capture_successful_mail' ), 10 );
		remove_filter( 'pre_wp_mail', array( $this, 'reject_mail' ), 10 );
		parent::tear_down();
	}

	/**
	 * Record a successful mail without handing it to a mail transport.
	 *
	 * @param null|bool $preempt Existing short-circuit value.
	 * @param array     $args    wp_mail() arguments.
	 * @return bool
	 */
	public function capture_successful_mail( $preempt, $args ) {
		$this->mail_calls[] = $args;
		return true;
	}

	/**
	 * Simulate a mail transport failure without sending a message.
	 *
	 * @param null|bool $preempt Existing short-circuit value.
	 * @param array     $args    wp_mail() arguments.
	 * @return bool
	 */
	public function reject_mail( $preempt, $args ) {
		$this->mail_calls[] = $args;
		return false;
	}

	/**
	 * Render the template under a submitted form request and retain normal globals.
	 *
	 * @param array<string, string> $post_data Sanitized test form data.
	 * @return string Rendered page HTML.
	 */
	private function render_contact_form( array $post_data ) {
		$original_post = $_POST;
		$_POST         = $post_data;

		try {
			return $this->render_theme_template( 'page-templates/contatti.php' );
		} finally {
			$_POST = $original_post;
		}
	}

	/**
	 * Data of a valid submission, with optional replacements.
	 *
	 * @param array<string, string> $overrides Fields to replace.
	 * @return array<string, string>
	 */
	private function valid_form_data( array $overrides = array() ) {
		return array_merge(
			array(
				'contatti_nonce_field' => wp_create_nonce( 'sf_contatti_nonce' ),
				'forminviato'          => 'yes',
				'nomecognome'          => 'Ada Lovelace',
				'indirizzoemail'       => 'ada@example.test',
				'testomessaggio'       => 'Messaggio di prova',
				'captcha-prefix'       => 'test-prefix',
				'captcha-field'        => 'ABCD',
			),
			$overrides
		);
	}

	/**
	 * Intercept every wp_mail() call as a success, so that a message sent by mistake never reaches a transport.
	 */
	private function capture_mail() {
		add_filter( 'pre_wp_mail', array( $this, 'capture_successful_mail' ), 10, 2 );
	}

	/**
	 * A valid form sends the expected mail and shows the success message.
	 */
	public function test_valid_submission_sends_contact_mail_and_shows_success() {
		add_filter( 'pre_wp_mail', array( $this, 'capture_successful_mail' ), 10, 2 );

		$html = $this->render_contact_form(
			array(
				'contatti_nonce_field' => wp_create_nonce( 'sf_contatti_nonce' ),
				'forminviato'          => 'yes',
				'nomecognome'          => 'Ada Lovelace',
				'indirizzoemail'       => 'ada@example.test',
				'testomessaggio'       => 'Messaggio di prova',
				'captcha-prefix'       => 'test-prefix',
				'captcha-field'        => 'ABCD',
			)
		);

		$this->assertStringContainsString( 'alert-success', $html );
		$this->assertCount( 1, $this->mail_calls );
		$this->assertSame( 'laboratorio@example.test', $this->mail_calls[0]['to'] );
		$this->assertStringContainsString( '[FormContatti] Email dal sito: Laboratorio di prova', $this->mail_calls[0]['subject'] );
		$this->assertSame( 'Messaggio di prova', $this->mail_calls[0]['message'] );
		$this->assertStringContainsString( 'Reply-To: ada@example.test', $this->mail_calls[0]['headers'] );
	}

	/**
	 * A mail transport failure is visible to the visitor and never becomes success.
	 */
	public function test_mail_failure_shows_an_error_without_success() {
		add_filter( 'pre_wp_mail', array( $this, 'reject_mail' ), 10, 2 );

		$html = $this->render_contact_form(
			array(
				'contatti_nonce_field' => wp_create_nonce( 'sf_contatti_nonce' ),
				'forminviato'          => 'yes',
				'nomecognome'          => 'Ada Lovelace',
				'indirizzoemail'       => 'ada@example.test',
				'testomessaggio'       => 'Messaggio di prova',
				'captcha-prefix'       => 'test-prefix',
				'captcha-field'        => 'ABCD',
			)
		);

		$this->assertStringContainsString( 'alert-danger', $html );
		$this->assertStringNotContainsString( 'alert-success', $html );
		$this->assertCount( 1, $this->mail_calls );
	}

	/**
	 * A refused submission shows an error, never a success, and sends nothing.
	 *
	 * @param string $html Rendered page HTML.
	 */
	private function assert_refused_without_mail( $html ) {
		$this->assertStringContainsString( 'alert-danger', $html );
		$this->assertStringNotContainsString( 'alert-success', $html );
		$this->assertCount( 0, $this->mail_calls );
	}

	/**
	 * The form is shown, without any message, when nothing was submitted.
	 */
	public function test_an_unsubmitted_form_shows_no_message() {
		$this->capture_mail();

		$html = $this->render_contact_form( array() );

		$this->assertStringContainsString( 'id="formcontatti"', $html );
		$this->assertStringContainsString( 'name="contatti_nonce_field"', $html );
		$this->assertStringNotContainsString( 'alert-danger', $html );
		$this->assertStringNotContainsString( 'alert-success', $html );
		$this->assertCount( 0, $this->mail_calls );
	}

	/**
	 * A wrong CAPTCHA answer is refused and nothing is sent.
	 */
	public function test_a_wrong_captcha_is_refused() {
		$this->capture_mail();

		$this->assert_refused_without_mail( $this->render_contact_form( $this->valid_form_data( array( 'captcha-field' => 'WRONG' ) ) ) );
	}

	/**
	 * A wrong CAPTCHA prefix is refused too.
	 */
	public function test_a_wrong_captcha_prefix_is_refused() {
		$this->capture_mail();

		$this->assert_refused_without_mail( $this->render_contact_form( $this->valid_form_data( array( 'captcha-prefix' => 'other-prefix' ) ) ) );
	}

	/**
	 * A form without a valid nonce is refused before anything else is checked.
	 */
	public function test_a_wrong_nonce_is_refused() {
		$this->capture_mail();

		$this->assert_refused_without_mail( $this->render_contact_form( $this->valid_form_data( array( 'contatti_nonce_field' => 'wrong' ) ) ) );
	}

	/**
	 * A form without a nonce at all is refused.
	 */
	public function test_a_missing_nonce_is_refused() {
		$this->capture_mail();
		$data = $this->valid_form_data();
		unset( $data['contatti_nonce_field'] );

		$this->assert_refused_without_mail( $this->render_contact_form( $data ) );
	}

	/**
	 * The name and the message are required.
	 */
	public function test_the_required_fields_are_enforced() {
		$this->capture_mail();

		$this->assert_refused_without_mail( $this->render_contact_form( $this->valid_form_data( array( 'nomecognome' => '' ) ) ) );
		$this->assert_refused_without_mail( $this->render_contact_form( $this->valid_form_data( array( 'testomessaggio' => '' ) ) ) );
	}

	/**
	 * An address that is not an email is refused.
	 */
	public function test_an_invalid_email_is_refused() {
		$this->capture_mail();

		$this->assert_refused_without_mail( $this->render_contact_form( $this->valid_form_data( array( 'indirizzoemail' => 'non-una-email' ) ) ) );
	}

	/**
	 * Line breaks in the address cannot add headers to the message.
	 */
	public function test_line_breaks_in_the_sender_address_cannot_inject_headers() {
		$this->capture_mail();

		$this->render_contact_form( $this->valid_form_data( array( 'indirizzoemail' => "ada@example.test\r\nBcc: intruso@example.test" ) ) );

		// The address is cleaned up (its line break and spaces are dropped), so whatever is sent has
		// exactly the From and Reply-To headers and no extra header line such as Bcc.
		foreach ( $this->mail_calls as $call ) {
			foreach ( preg_split( '/\r\n|\r|\n/', trim( $call['headers'] ) ) as $line ) {
				$this->assertMatchesRegularExpression( '/^(From|Reply-To): \S+$/', $line );
			}
		}
		$this->assertStringNotContainsString( 'intruso@', implode( "\n", array_column( $this->mail_calls, 'to' ) ) );
	}

	/**
	 * Asking for a receipt sends a second message to the visitor, after the one to the laboratory.
	 */
	public function test_a_receipt_copy_is_sent_to_the_visitor() {
		$this->capture_mail();

		$html = $this->render_contact_form( $this->valid_form_data( array( 'ricevuta' => 'on' ) ) );

		$this->assertStringContainsString( 'alert-success', $html );
		$this->assertCount( 2, $this->mail_calls );
		$this->assertSame( 'laboratorio@example.test', $this->mail_calls[0]['to'] );
		$this->assertSame( 'ada@example.test', $this->mail_calls[1]['to'] );
		$this->assertStringContainsString( '(' . esc_html__( 'Ricevuta', 'design_laboratori_italia' ) . ')', $this->mail_calls[1]['subject'] );
	}

	/**
	 * Without the receipt option only the laboratory is written to.
	 */
	public function test_no_receipt_is_sent_unless_requested() {
		$this->capture_mail();

		$this->render_contact_form( $this->valid_form_data() );

		$this->assertCount( 1, $this->mail_calls );
		$this->assertSame( 'laboratorio@example.test', $this->mail_calls[0]['to'] );
	}

	/**
	 * Without the CAPTCHA plugin the form cannot be sent: the page says so and nothing is mailed.
	 */
	public function test_without_the_captcha_plugin_the_form_cannot_be_sent() {
		remove_filter( 'pre_option_active_plugins', array( $this, 'enable_test_captcha_plugin' ), 20 );
		$this->capture_mail();

		$this->assert_refused_without_mail( $this->render_contact_form( $this->valid_form_data() ) );
	}
}
