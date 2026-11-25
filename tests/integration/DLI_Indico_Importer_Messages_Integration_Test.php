<?php
/**
 * Integration tests of the messages of the exceptions raised by the Indico importer.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * What comes from the remote server (an HTTP error, a response body) is escaped in the message, which reaches
 * the REST response and the import report.
 */
class DLI_Indico_Importer_Messages_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * A public IP address: the URL passes the safety check of the HTTP API without a DNS lookup.
	 */
	const BASE_URL = 'https://93.184.216.34';

	/**
	 * The message of the exception raised by a private method of the importer.
	 *
	 * @param string $method Method name.
	 * @param array  $args   Arguments.
	 * @return string
	 */
	private function exception_message( $method, array $args ) {
		$importer   = new DLI_IndicoImporter();
		$reflection = new ReflectionMethod( DLI_IndicoImporter::class, $method );
		$reflection->setAccessible( true );
		try {
			$reflection->invokeArgs( $importer, $args );
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
		$this->fail( 'An exception was expected.' );
	}

	/**
	 * Every HTTP request of the test gets the given answer, without reaching the network.
	 *
	 * @param array|WP_Error $answer Response or error.
	 * @return void
	 */
	private function answer_http_requests_with( $answer ) {
		add_filter(
			'pre_http_request',
			static function () use ( $answer ) {
				return $answer;
			}
		);
	}

	/**
	 * Configuration of the category export.
	 *
	 * @return array
	 */
	private function conf() {
		return array(
			'base_url'   => self::BASE_URL,
			'category'   => '1',
			'start_date' => '2026-01-01',
		);
	}

	/**
	 * The error of a failed request to the Indico API is escaped.
	 */
	public function test_the_error_of_the_api_request_is_escaped() {
		$this->answer_http_requests_with( new WP_Error( 'http_request_failed', '<b>errore</b>' ) );

		$message = $this->exception_message( 'get_data_to_import', array( $this->conf() ) );

		$this->assertStringContainsString( 'Errore invocando la Indico REST API: &lt;b&gt;errore&lt;/b&gt;', $message );
		$this->assertStringNotContainsString( '<b>', $message );
	}

	/**
	 * The body of a response of the Indico API that is not 200 is escaped.
	 */
	public function test_the_body_of_a_failed_api_response_is_escaped() {
		$this->answer_http_requests_with(
			array(
				'headers'  => array(),
				'body'     => '<script>alert(1)</script>',
				'response' => array(
					'code'    => 500,
					'message' => 'Internal Server Error',
				),
				'cookies'  => array(),
			)
		);

		$message = $this->exception_message( 'get_data_to_import', array( $this->conf() ) );

		$this->assertStringContainsString( 'HTTP 500. Risposta: &lt;script&gt;', $message );
		$this->assertStringNotContainsString( '<script>', $message );
	}

	/**
	 * The error of a failed request for the page of an event is escaped.
	 */
	public function test_the_error_of_the_event_page_request_is_escaped() {
		$this->answer_http_requests_with( new WP_Error( 'http_request_failed', '<b>errore</b>' ) );

		$message = $this->exception_message( '_get_meta_content', array( self::BASE_URL . '/event/1/', 'og:image' ) );

		$this->assertStringContainsString( 'Errore nel recupero della pagina evento: &lt;b&gt;errore&lt;/b&gt;', $message );
		$this->assertStringNotContainsString( '<b>', $message );
	}
}
