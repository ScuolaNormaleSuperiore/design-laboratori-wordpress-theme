<?php
/**
 * Integration tests for the contact form submission flow.
 *
 * @package Design_Laboratori_Italia
 */

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
		ReallySimpleCaptcha::reset();

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

	/**
	 * The text of the error box of a rendered page.
	 *
	 * @param string $html Rendered page HTML.
	 * @return string Inner HTML of the box, or an empty string when there is none.
	 */
	private function error_box( $html ) {
		return 1 === preg_match( '#<div id="contatti_messaggi">.*?<div class="alert alert-danger[^>]*>(.*?)</div>#s', $html, $found ) ? $found[1] : '';
	}

	/**
	 * How many line break tags an HTML fragment holds, whether WordPress writes them as `<br />` or as `<br>`.
	 *
	 * The filter `wp_kses_post()` of WordPress 7.2 writes the void tags without the closing slash, the older
	 * versions keep it: the test must not depend on which one it is.
	 *
	 * @param string $html HTML fragment.
	 * @return int Number of line break tags.
	 */
	private function count_line_breaks( $html ) {
		return preg_match_all( '#<br\s*/?>#i', $html );
	}

	/**
	 * A refused form lists every message once, in a fixed order, separated by a line break.
	 */
	public function test_the_messages_of_a_refused_form_come_in_order() {
		$this->capture_mail();

		$box = $this->error_box( $this->submit( array( 'nomecognome' => '', 'indirizzoemail' => '' ) ) );
		$required = strpos( $box, esc_html__( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' ) );
		$email    = strpos( $box, esc_html__( 'Indicare un indirizzo email valido.', 'design_laboratori_italia' ) );
		$this->assertNotFalse( $required );
		$this->assertGreaterThan( $required, $email, 'The email message comes after the required fields message.' );
		$this->assertSame( 1, $this->count_line_breaks( $box ), 'One line break between two messages, none before the first.' );

		// Without the CAPTCHA plugin the message of the service comes first: three messages, two line breaks.
		remove_filter( 'pre_option_active_plugins', array( $this, 'enable_test_captcha_plugin' ), 20 );
		$box         = $this->error_box( $this->submit( array( 'nomecognome' => '', 'indirizzoemail' => '' ) ) );
		$unavailable = strpos( $box, esc_html__( 'Il servizio di verifica non è temporaneamente disponibile. Riprova più tardi.', 'design_laboratori_italia' ) );
		$required    = strpos( $box, esc_html__( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' ) );
		$this->assertNotFalse( $unavailable );
		$this->assertGreaterThan( $unavailable, $required, 'The service message comes before the required fields one.' );
		$this->assertSame( 2, $this->count_line_breaks( $box ) );
	}

	/**
	 * A wrong nonce is the only message: nothing else is validated and nothing is sent.
	 */
	public function test_a_wrong_nonce_is_the_only_message() {
		$this->capture_mail();

		$box = $this->error_box( $this->submit( array( 'contatti_nonce_field' => 'wrong', 'nomecognome' => '', 'testomessaggio' => '' ) ) );

		$this->assertStringContainsString( esc_html__( 'Valore di Nonce errato.', 'design_laboratori_italia' ), $box );
		$this->assertStringNotContainsString( esc_html__( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' ), $box );
		$this->assertCount( 0, $this->mail_calls );
	}

	/**
	 * Submit a form and return how many mails were sent so far.
	 *
	 * @param array<string, string> $overrides Fields to replace.
	 * @return string Rendered page HTML.
	 */
	private function submit( array $overrides = array() ) {
		return $this->render_contact_form( $this->valid_form_data( $overrides ) );
	}

	/**
	 * A CAPTCHA is used once: the same challenge and answer, sent again, are refused.
	 */
	public function test_a_captcha_can_be_used_only_once() {
		$this->capture_mail();

		$first = $this->submit();
		$this->assertStringContainsString( 'alert-success', $first );
		$this->assertCount( 1, $this->mail_calls );
		$this->assertContains( 'test-prefix', ReallySimpleCaptcha::$removed, 'The challenge is consumed by its first use.' );

		$replay = $this->submit();
		$this->assertStringContainsString( 'alert-danger', $replay );
		$this->assertStringNotContainsString( 'alert-success', $replay );
		$this->assertCount( 1, $this->mail_calls, 'The replay sends nothing.' );
	}

	/**
	 * A wrong answer consumes the challenge too: guessing the answer to the same image again is not possible.
	 */
	public function test_a_wrong_answer_consumes_the_challenge() {
		$this->capture_mail();

		$this->assert_refused_without_mail( $this->submit( array( 'captcha-field' => 'WRONG' ) ) );
		$this->assertContains( 'test-prefix', ReallySimpleCaptcha::$removed, 'A wrong answer consumes the challenge.' );

		$this->assert_refused_without_mail( $this->submit() );
	}

	/**
	 * After a refused submission the form comes back with a new challenge that can be answered.
	 */
	public function test_a_refused_form_comes_back_with_a_new_challenge() {
		$this->capture_mail();

		$html = $this->submit( array( 'captcha-field' => 'WRONG' ) );

		$this->assertSame( 1, preg_match( '#name="captcha-prefix"[^>]*value="([^"]*)"#', $html, $found ), 'The form carries a challenge.' );
		$this->assertNotSame( '', $found[1] );
		$this->assertNotSame( 'test-prefix', $found[1], 'It is a new one, not the one that was just consumed.' );
		$this->assertArrayHasKey( $found[1], ReallySimpleCaptcha::$challenges, 'The new image can be answered.' );
	}

	/**
	 * A prefix that was never issued consumes nothing that exists, and is refused.
	 */
	public function test_an_unknown_prefix_is_refused_and_leaves_the_other_challenges_alone() {
		$this->capture_mail();
		ReallySimpleCaptcha::$challenges['other'] = 'ZZZZ';

		$this->assert_refused_without_mail( $this->submit( array( 'captcha-prefix' => 'unknown' ) ) );

		$this->assertArrayHasKey( 'other', ReallySimpleCaptcha::$challenges, 'A challenge nobody used is not touched.' );
	}
}
