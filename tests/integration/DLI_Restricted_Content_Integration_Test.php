<?php
/**
 * A content that Groups has reserved answers 401 to a visitor and 403 to a logged-in user without permission, not 404.
 *
 * Groups is not installed in the suite. Its way of hiding a content is reproduced: a `posts_where` filter that takes the
 * content out of the queries (so WordPress finds nothing and answers 404) and a stand-in of its class
 * `Groups_Post_Access` that says who can read.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! class_exists( 'Groups_Post_Access' ) ) {
	/**
	 * Stand-in for the class of Groups that tells whether a user can read a post: the IDs that the test reserves are
	 * unreadable for everybody, the others are readable.
	 */
	class Groups_Post_Access {

		/**
		 * IDs of the posts that the test reserves.
		 *
		 * @var int[]
		 */
		public static $denied = array();

		/**
		 * Whether the current user can read a post.
		 *
		 * @param int      $post_id Post ID.
		 * @param int|null $user_id User ID (not used by the stand-in).
		 * @return bool
		 */
		public static function user_can_read_post( $post_id, $user_id = null ) {
			return ! in_array( (int) $post_id, self::$denied, true );
		}
	}
}

/**
 * `dli_handle_restricted_content()` and the template `restricted-content.php`.
 */
class DLI_Restricted_Content_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * HTTP status codes that WordPress was asked to send while the test ran.
	 *
	 * @var int[]
	 */
	private $status_codes = array();

	/**
	 * Switch the reserved area on, run with the Italian texts and start with nothing reserved.
	 */
	public function set_up() {
		parent::set_up();
		$this->use_italian_texts();
		$this->set_theme_options( DLI_RESERVED_AREA_OPTION_KEY, array( 'reserved_area_enabled' => 'true' ) );
		Groups_Post_Access::$denied = array();
		$this->status_codes         = array();
		add_filter( 'status_header', array( $this, 'record_status' ), 10, 2 );
		add_filter( 'posts_where', array( $this, 'hide_reserved_posts' ) );
	}

	/**
	 * Forget what the test reserved and load the translations again.
	 */
	public function tear_down() {
		Groups_Post_Access::$denied = array();
		dli_restricted_content_state( false );
		wp_set_current_user( 0 );
		$this->set_permalink_structure( '' );
		$this->restore_theme_translations();
		parent::tear_down();
	}

	/**
	 * Record a status code and leave the header as it is.
	 *
	 * @param string $header      Header text.
	 * @param int    $status_code Status code.
	 * @return string The header.
	 */
	public function record_status( $header, $status_code ) {
		$this->status_codes[] = (int) $status_code;
		return $header;
	}

	/**
	 * What Groups does to a query: the reserved posts are not in it.
	 *
	 * @param string $where The WHERE clause.
	 * @return string
	 */
	public function hide_reserved_posts( $where ) {
		global $wpdb;
		if ( Groups_Post_Access::$denied ) {
			$where .= " AND {$wpdb->posts}.ID NOT IN (" . implode( ',', array_map( 'intval', Groups_Post_Access::$denied ) ) . ')';
		}
		return $where;
	}

	/**
	 * Create a published post of a type and reserve it.
	 *
	 * @param string $post_type Post type.
	 * @param array  $args      More arguments of the post.
	 * @return int Post ID.
	 */
	private function reserved_post( $post_type, array $args = array() ) {
		$post_id                      = self::factory()->post->create( array_merge( array( 'post_type' => $post_type ), $args ) );
		Groups_Post_Access::$denied[] = $post_id;
		return $post_id;
	}

	/**
	 * Open the address of a post and run the handler of the theme.
	 *
	 * @param int $post_id Post ID.
	 */
	private function visit( $post_id ) {
		$this->go_to( get_permalink( $post_id ) );
		dli_handle_restricted_content();
	}

	/**
	 * The page of the response, as the theme template prints it.
	 *
	 * @return string
	 */
	private function render_response() {
		return $this->render_theme_template( 'restricted-content.php' );
	}

	/**
	 * A visitor who is not logged in gets 401 and the button to log in, which comes back to the content.
	 */
	public function test_a_visitor_gets_401_and_the_login_button() {
		$post_id = $this->reserved_post( 'page', array( 'post_title' => 'Verbali del consiglio' ) );

		$this->visit( $post_id );

		$this->assertSame( 401, end( $this->status_codes ), 'The last status sent must be 401.' );
		$this->assertFalse( is_404(), 'The request is no longer a 404.' );
		$html = $this->render_response();
		$this->assertStringContainsString( 'Contenuto riservato', $html );
		$this->assertStringContainsString( 'class="btn btn-primary"', $html );
		$this->assertStringContainsString( esc_url( dli_get_reserved_area_login_url( get_permalink( $post_id ) ) ), $html );
	}

	/**
	 * The response never says what the content is called.
	 */
	public function test_the_response_does_not_show_the_title_of_the_content() {
		$post_id = $this->reserved_post( 'page', array( 'post_title' => 'Verbali del consiglio' ) );

		$this->visit( $post_id );

		$this->assertStringNotContainsString( 'Verbali del consiglio', $this->render_response() );
		$title = apply_filters( 'document_title_parts', array( 'title' => 'Verbali del consiglio' ) );
		$this->assertStringNotContainsString( 'Verbali', (string) $title['title'] );
	}

	/**
	 * A logged-in user without permission gets 403 and no button to log in again.
	 */
	public function test_a_logged_in_user_without_permission_gets_403() {
		$post_id = $this->reserved_post( 'page' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );

		$this->visit( $post_id );

		$this->assertSame( 403, end( $this->status_codes ) );
		$html = $this->render_response();
		$this->assertStringContainsString( 'Non hai i permessi per vedere questo contenuto', $html );
		$this->assertStringNotContainsString( 'class="btn btn-primary"', $html, 'Logging in again would change nothing.' );
		$this->assertStringContainsString( 'action=logout', $html );
	}

	/**
	 * A pending user gets 403 and reads that the request waits for approval.
	 */
	public function test_a_pending_user_gets_403_and_the_waiting_message() {
		$post_id = $this->reserved_post( 'page' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => DLI_PENDING_USER_ROLE_SLUG ) ) );

		$this->visit( $post_id );

		$this->assertSame( 403, end( $this->status_codes ) );
		$this->assertStringContainsString( 'in attesa di approvazione', $this->render_response() );
	}

	/**
	 * The response is for one request only: not indexed, not cached, with its own template.
	 */
	public function test_the_response_is_not_indexed_and_has_its_own_template() {
		$this->visit( $this->reserved_post( 'page' ) );

		$robots = apply_filters( 'wp_robots', array() );
		$this->assertArrayHasKey( 'noindex', $robots );
		$this->assertSame( 'restricted-content.php', basename( apply_filters( 'template_include', 'index.php' ) ) );
	}

	/**
	 * The 401 carries a challenge with its own scheme that names the login address; it is not Basic, so the browser
	 * shows no credentials dialog.
	 */
	public function test_the_401_sends_a_challenge_with_the_login_address() {
		$post_id = $this->reserved_post( 'page' );

		$this->visit( $post_id );

		$state     = dli_restricted_content_state();
		$login_url = dli_get_reserved_area_login_url( get_permalink( $post_id ) );
		$this->assertSame( 'DLI-SSO realm="Area riservata", login_uri="' . $login_url . '"', $state['challenge'] );
		$this->assertStringNotContainsString( 'Basic', $state['challenge'] );
	}

	/**
	 * The challenge is for the 401 only: a logged-in user who gets 403 is not asked to authenticate.
	 */
	public function test_the_403_sends_no_challenge() {
		$post_id = $this->reserved_post( 'page' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );

		$this->visit( $post_id );

		$this->assertSame( '', dli_restricted_content_state()['challenge'] );
	}

	/**
	 * Every public type that Groups can reserve is covered, not only the pages.
	 *
	 * @dataProvider public_types
	 *
	 * @param string $post_type Post type.
	 */
	public function test_every_public_type_gets_401( $post_type ) {
		$this->visit( $this->reserved_post( $post_type ) );

		$this->assertSame( 401, end( $this->status_codes ), "$post_type: a visitor gets 401." );
		$this->assertSame( 'restricted-content.php', basename( apply_filters( 'template_include', 'index.php' ) ) );
	}

	/**
	 * The public types of the theme and the core ones.
	 *
	 * @return array<string, array{0: string}>
	 */
	public function public_types() {
		return array(
			'page'        => array( 'page' ),
			'post'        => array( 'post' ),
			'news'        => array( NEWS_POST_TYPE ),
			'event'       => array( EVENT_POST_TYPE ),
			'person'      => array( PEOPLE_POST_TYPE ),
			'publication' => array( PUBLICATION_POST_TYPE ),
		);
	}

	/**
	 * With pretty addresses the page is found by its path, also a subpage.
	 */
	public function test_a_page_is_found_by_its_path_with_pretty_addresses() {
		$this->set_permalink_structure( '/%postname%/' );
		$parent = self::factory()->post->create( array( 'post_type' => 'page', 'post_name' => 'consiglio' ) );
		$child  = self::factory()->post->create( array( 'post_type' => 'page', 'post_name' => 'verbali', 'post_parent' => $parent ) );
		Groups_Post_Access::$denied[] = $child;

		$this->visit( $child );

		$this->assertSame( 401, end( $this->status_codes ) );
		$this->assertSame( 'restricted-content.php', basename( apply_filters( 'template_include', 'index.php' ) ) );
	}

	/**
	 * With the date in the address, the right address gets 401.
	 */
	public function test_the_right_dated_address_of_a_reserved_post_gets_401() {
		$this->set_permalink_structure( '/%year%/%monthnum%/%postname%/' );
		$post_id = $this->reserved_post( 'post', array( 'post_name' => 'verbale', 'post_date' => '2026-05-10 10:00:00' ) );

		$this->visit( $post_id );

		$this->assertSame( 401, end( $this->status_codes ) );
	}

	/**
	 * An address that only ends with the slug of a reserved post does not identify it: it stays a 404.
	 */
	public function test_a_wrong_path_that_ends_with_the_slug_of_a_reserved_post_stays_a_404() {
		$this->set_permalink_structure( '/%year%/%monthnum%/%postname%/' );
		$this->reserved_post( 'post', array( 'post_name' => 'verbale', 'post_date' => '2026-05-10 10:00:00' ) );

		$this->go_to( home_url( '/1999/01/verbale/' ) );
		dli_handle_restricted_content();

		$this->assertTrue( is_404() );
		$this->assertNull( dli_restricted_content_state() );
		$this->assertNotContains( 401, $this->status_codes );
	}

	/**
	 * Two contents of the same hierarchical type with the same slug under different parents: the one asked for is
	 * the one found, even when the other, public, comes first.
	 */
	public function test_the_same_slug_under_different_parents_finds_the_reserved_one() {
		register_post_type( 'dli_hier', array( 'public' => true, 'hierarchical' => true, 'rewrite' => array( 'slug' => 'dli-hier' ) ) );
		$this->set_permalink_structure( '/%postname%/' );
		$parent_a = self::factory()->post->create( array( 'post_type' => 'dli_hier', 'post_name' => 'aaa' ) );
		$parent_b = self::factory()->post->create( array( 'post_type' => 'dli_hier', 'post_name' => 'bbb' ) );
		self::factory()->post->create( array( 'post_type' => 'dli_hier', 'post_name' => 'verbale', 'post_parent' => $parent_a ) );
		$reserved = $this->reserved_post( 'dli_hier', array( 'post_name' => 'verbale', 'post_parent' => $parent_b ) );

		$this->visit( $reserved );

		$this->assertSame( 401, end( $this->status_codes ) );
		unregister_post_type( 'dli_hier' );
	}

	/**
	 * The handler is on the real hook, and runs before the canonical redirect of WordPress, which would otherwise send
	 * a request for a reserved content somewhere else before the theme answers.
	 */
	public function test_the_handler_runs_on_template_redirect_before_the_canonical_redirect() {
		$priority = has_action( 'template_redirect', 'dli_handle_restricted_content' );
		$this->assertIsInt( $priority );
		$this->assertLessThan( (int) has_action( 'template_redirect', 'redirect_canonical' ), $priority );
	}

	/**
	 * A content that does not exist is still a 404.
	 */
	public function test_an_address_that_does_not_exist_is_still_a_404() {
		$this->go_to( home_url( '/?page_id=987654' ) );
		dli_handle_restricted_content();

		$this->assertTrue( is_404() );
		$this->assertNotContains( 401, $this->status_codes );
		$this->assertNotContains( 403, $this->status_codes );
		$this->assertSame( 'index.php', apply_filters( 'template_include', 'index.php' ) );
	}

	/**
	 * A draft is never revealed, even when it is reserved.
	 */
	public function test_a_draft_is_never_revealed() {
		$post_id = $this->reserved_post( 'page', array( 'post_status' => 'draft' ) );

		$this->go_to( home_url( '/?page_id=' . $post_id ) );
		dli_handle_restricted_content();

		$this->assertTrue( is_404() );
		$this->assertNotContains( 401, $this->status_codes );
	}

	/**
	 * A content that Groups has not reserved stays what it is: found, with no response of ours.
	 */
	public function test_a_readable_content_is_not_touched() {
		$post_id = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->visit( $post_id );

		$this->assertFalse( is_404() );
		$this->assertSame( 'index.php', apply_filters( 'template_include', 'index.php' ) );
	}

	/**
	 * A detail page that the theme switches off on purpose stays a 404, whatever Groups says.
	 */
	public function test_a_detail_page_switched_off_by_the_theme_stays_a_404() {
		$post_id = $this->reserved_post( PEOPLE_POST_TYPE );
		$this->set_acf_fields( $post_id, array( 'disattiva_pagina_dettaglio' => 1 ) );

		$this->visit( $post_id );

		$this->assertTrue( is_404() );
		$this->assertNotContains( 401, $this->status_codes );
	}

	/**
	 * With the reserved area off a reserved content is a plain 404, as before, and nothing is looked up.
	 */
	public function test_with_the_reserved_area_off_nothing_changes_and_nothing_is_queried() {
		$post_id = $this->reserved_post( 'page' );
		$this->set_theme_options( DLI_RESERVED_AREA_OPTION_KEY, array( 'reserved_area_enabled' => 'false' ) );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertFalse( dli_is_reserved_area_enabled() );

		$queries = get_num_queries();
		dli_handle_restricted_content();

		$this->assertSame( $queries, get_num_queries(), 'No query may be added.' );
		$this->assertTrue( is_404() );
		$this->assertNotContains( 401, $this->status_codes );
	}

	/**
	 * On a request that is not a 404 the handler adds no query: the pages of the site do not pay for it.
	 */
	public function test_a_normal_request_adds_no_query() {
		$post_id = self::factory()->post->create( array( 'post_type' => 'page' ) );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertFalse( is_404() );
		$this->assertTrue( dli_is_reserved_area_enabled() );

		$queries = get_num_queries();
		dli_handle_restricted_content();

		$this->assertSame( $queries, get_num_queries(), 'No query may be added.' );
	}
}
