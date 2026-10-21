<?php
/**
 * Integration tests of the search autocomplete REST endpoint (dli/v1/suggest).
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The endpoint is opt-in, public and returns only published content.
 */
class DLI_Search_Autocomplete_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Enable the feature through the same option the options page writes.
	 *
	 * @param array $overrides Settings to add or replace.
	 */
	private function enable_autocomplete( array $overrides = array() ) {
		$this->set_theme_options(
			'setup',
			array_merge(
				array(
					'search_autocomplete_enabled'       => 'true',
					'search_autocomplete_cache_minutes' => 0,
				),
				$overrides
			)
		);
		$this->reset_rest_server();
	}

	/**
	 * Rebuild the REST server so that rest_api_init runs with the current options.
	 */
	private function reset_rest_server() {
		$GLOBALS['wp_rest_server'] = null;
		rest_get_server();
	}

	/**
	 * Call the endpoint.
	 *
	 * @param string $query Search text.
	 * @return WP_REST_Response
	 */
	private function suggest( $query ) {
		$request = new WP_REST_Request( 'GET', '/dli/v1/suggest' );
		$request->set_param( 'q', $query );
		return rest_do_request( $request );
	}

	/**
	 * Create a news item in Italian.
	 *
	 * @param string $title  Title.
	 * @param string $status Post status.
	 * @return int
	 */
	private function create_news( $title, $status = 'publish' ) {
		return $this->create_post( NEWS_POST_TYPE, $title, $status );
	}

	/**
	 * Drop the REST server built with this test's options.
	 */
	public function tear_down() {
		$GLOBALS['wp_rest_server'] = null;
		parent::tear_down();
	}

	/**
	 * The feature is off by default: the route does not exist.
	 */
	public function test_route_is_not_registered_by_default() {
		$this->reset_rest_server();

		$this->assertArrayNotHasKey( '/dli/v1/suggest', rest_get_server()->get_routes() );
	}

	/**
	 * Once enabled, the route is registered.
	 */
	public function test_route_is_registered_when_enabled() {
		$this->enable_autocomplete();

		$this->assertArrayHasKey( '/dli/v1/suggest', rest_get_server()->get_routes() );
	}

	/**
	 * A query shorter than the minimum length returns no suggestions.
	 */
	public function test_short_query_returns_no_suggestions() {
		$this->create_news( 'Neutrino detector' );
		$this->enable_autocomplete();

		$response = $this->suggest( 'Ne' );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( array(), $response->get_data() );
	}

	/**
	 * Published content is suggested with title and type; drafts are not.
	 */
	public function test_only_published_content_is_suggested() {
		$this->create_news( 'Neutrino detector published' );
		$this->create_news( 'Neutrino detector draft', 'draft' );
		$this->enable_autocomplete();

		$response = $this->suggest( 'Neutrino' );
		$data     = $response->get_data();

		$this->assertSame( 200, $response->get_status() );
		$this->assertCount( 1, $data );
		$this->assertSame( 'Neutrino detector published', $data[0]['title'] );
		$this->assertSame( NEWS_POST_TYPE, $data[0]['type'] );
	}

	/**
	 * The number of suggestions never exceeds the configured maximum.
	 */
	public function test_results_are_limited_to_the_configured_maximum() {
		for ( $i = 1; $i <= 4; $i++ ) {
			$this->create_news( "Quasar observation $i" );
		}
		$this->enable_autocomplete( array( 'search_autocomplete_max_results' => 2 ) );

		$data = $this->suggest( 'Quasar' )->get_data();

		$this->assertCount( 2, $data );
	}
}
