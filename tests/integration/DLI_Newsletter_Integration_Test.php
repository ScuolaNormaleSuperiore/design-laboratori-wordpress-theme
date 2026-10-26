<?php
/**
 * Integration tests for the newsletter subscription client.
 *
 * @package Design_Laboratori_Italia
 */

require_once get_template_directory() . '/inc/classes/class-newslettermanager.php';

/**
 * Brevo is intercepted at the WordPress HTTP boundary, so these tests never
 * send subscriptions or expose the configured API token to the network.
 */
class DLI_Newsletter_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Last request received by the HTTP mock.
	 *
	 * @var array<string, mixed>
	 */
	private $request = array();

	/**
	 * Configure the newsletter as an enabled, otherwise ordinary integration.
	 */
	public function set_up() {
		parent::set_up();

		$this->set_theme_options(
			'setup',
			array(
				'newsletter_enabled'     => 'true',
				'newsletter_api_token'   => 'test-token',
				'newsletter_list_id'     => '42',
				'newsletter_template_id' => '7',
			)
		);
	}

	/**
	 * Return an accepted response and retain the outgoing request for assertions.
	 *
	 * @param false|array|WP_Error $preempt Whether another filter already handled the request.
	 * @param array                 $args    Request arguments.
	 * @param string                $url     Requested URL.
	 * @return array
	 */
	public function accept_brevo_request( $preempt, $args, $url ) {
		$this->request = array(
			'args' => $args,
			'url'  => $url,
		);

		return array(
			'headers'  => array(),
			'body'     => '',
			'response' => array(
				'code'    => 201,
				'message' => 'Created',
			),
			'cookies'  => array(),
		);
	}

	/**
	 * Return an upstream failure without making a network request.
	 *
	 * @param false|array|WP_Error $preempt Whether another filter already handled the request.
	 * @param array                 $args    Request arguments.
	 * @param string                $url     Requested URL.
	 * @return array
	 */
	public function reject_brevo_request( $preempt, $args, $url ) {
		return array(
			'headers'  => array(),
			'body'     => '',
			'response' => array(
				'code'    => 500,
				'message' => 'Service unavailable',
			),
			'cookies'  => array(),
		);
	}

	/**
	 * A successful double opt-in request contains the expected subscription data.
	 */
	public function test_subscription_is_sent_to_brevo_and_accepted() {
		add_filter( 'pre_http_request', array( $this, 'accept_brevo_request' ), 10, 3 );

		$manager = new Newsletter_Manager();
		$manager->setup( 'Ada', 'Lovelace', 'ada@example.test', '' );
		$result = $manager->subscribe_user();

		remove_filter( 'pre_http_request', array( $this, 'accept_brevo_request' ), 10 );

		$this->assertSame( 201, $result['code'] );
		$this->assertSame( 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation', $this->request['url'] );
		$this->assertSame( 'test-token', $this->request['args']['headers']['api-key'] );

		$payload = json_decode( $this->request['args']['body'], true );
		$this->assertSame( 'ada@example.test', $payload['email'] );
		$this->assertSame( array( '42' ), $payload['includeListIds'] );
		$this->assertSame( '7', $payload['templateId'] );
		$this->assertSame( 'Ada', $payload['attributes']['FNAME'] );
		$this->assertSame( 'Lovelace', $payload['attributes']['LNAME'] );
		$this->assertStringEndsWith( '?after_confirm=yes', $payload['redirectionUrl'] );
	}

	/**
	 * An upstream failure is returned to the template for its error message.
	 */
	public function test_subscription_returns_an_upstream_error() {
		add_filter( 'pre_http_request', array( $this, 'reject_brevo_request' ), 10, 3 );

		$manager = new Newsletter_Manager();
		$manager->setup( 'Ada', 'Lovelace', 'ada@example.test', '' );
		$result = $manager->subscribe_user();

		remove_filter( 'pre_http_request', array( $this, 'reject_brevo_request' ), 10 );

		$this->assertSame( 500, $result['code'] );
		$this->assertSame( 'Service unavailable', $result['message'] );
	}
}
