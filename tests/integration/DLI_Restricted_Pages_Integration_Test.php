<?php
/**
 * The lists of pages of the theme respect a restriction plugin (such as Groups) that hides pages from a query.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The helpers of `inc/pages.php` that list pages to the visitors: subpage cards, promoted cards, groups of links,
 * the index of a page and the navigation by pages.
 */
class DLI_Restricted_Pages_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * The user who may read the restricted pages.
	 *
	 * @var int
	 */
	private $reader_id = 0;

	/**
	 * The pages hidden by the restriction stand-in.
	 *
	 * @var int[]
	 */
	private $restricted = array();

	/**
	 * Make Italian the current language and hide the restricted pages from everybody but the reader.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;
		PLL()->curlang           = PLL()->model->get_language( 'it' );
		$this->reader_id         = self::factory()->user->create( array( 'role' => DLI_RESERVED_AREA_USER_ROLE_SLUG ) );
		add_filter( 'the_posts', array( $this, 'hide_restricted' ) );
	}

	/**
	 * Remove the stand-in and put back the language and the user.
	 */
	public function tear_down() {
		remove_filter( 'the_posts', array( $this, 'hide_restricted' ) );
		PLL()->curlang = $this->previous_language;
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * Stand-in for a restriction plugin such as Groups, which hides from a query the contents the current user may
	 * not read. Like Groups, it acts only on the queries that run the filters.
	 *
	 * @param WP_Post[] $posts Posts found by a query.
	 * @return WP_Post[]
	 */
	public function hide_restricted( $posts ) {
		if ( get_current_user_id() === $this->reader_id ) {
			return $posts;
		}
		return array_values(
			array_filter(
				$posts,
				function ( $post ) {
					return ! in_array( (int) $post->ID, $this->restricted, true );
				}
			)
		);
	}

	/**
	 * Create a published page in Italian.
	 *
	 * @param string $title      Title.
	 * @param int    $parent     Parent page.
	 * @param bool   $promoted   Whether it is promoted in the home.
	 * @param bool   $restricted Whether the stand-in hides it.
	 * @return int Page ID.
	 */
	private function page( $title, $parent = 0, $promoted = false, $restricted = false ) {
		$page_id = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => $title,
				'post_status' => 'publish',
				'post_parent' => $parent,
			)
		);
		dli_set_post_language( $page_id, 'it' );
		if ( $promoted ) {
			$this->set_acf_fields( $page_id, array( 'promuovi_in_home' => 1 ) );
		}
		if ( $restricted ) {
			$this->restricted[] = $page_id;
		}
		return $page_id;
	}

	/**
	 * Run a callback as a visitor and as the reader.
	 *
	 * @param callable $callback Returns the titles listed.
	 * @return array{0: string[], 1: string[]} The titles for the visitor and for the reader.
	 */
	private function as_visitor_and_reader( callable $callback ) {
		wp_set_current_user( 0 );
		$visitor = $callback();
		wp_set_current_user( $this->reader_id );
		$reader = $callback();
		wp_set_current_user( 0 );
		return array( $visitor, $reader );
	}

	/**
	 * The cards "In questa sezione" leave out a restricted subpage for a visitor.
	 */
	public function test_subpage_cards() {
		$parent = $this->page( 'Ricerca' );
		$this->page( 'Pubblica', $parent );
		$this->page( 'Riservata', $parent, false, true );

		list( $visitor, $reader ) = $this->as_visitor_and_reader(
			function () use ( $parent ) {
				return wp_list_pluck( dli_get_subpage_cards( $parent ), 'title' );
			}
		);

		$this->assertSame( array( 'Pubblica' ), $visitor );
		$this->assertSame( array( 'Pubblica', 'Riservata' ), $reader );
	}

	/**
	 * The cards of the home leave out a restricted promoted page for a visitor.
	 */
	public function test_promoted_page_cards() {
		$this->page( 'Pubblica', 0, true );
		$this->page( 'Riservata', 0, true, true );

		list( $visitor, $reader ) = $this->as_visitor_and_reader(
			function () {
				return wp_list_pluck( dli_get_promoted_page_cards( 6, 1 ), 'title' );
			}
		);

		$this->assertSame( array( 'Pubblica' ), $visitor );
		$this->assertSame( array( 'Pubblica', 'Riservata' ), $reader );
	}

	/**
	 * The index of a page leaves out a restricted column and a restricted link for a visitor.
	 */
	public function test_page_index() {
		$root   = $this->page( 'Indice' );
		$open   = $this->page( 'Colonna pubblica', $root );
		$closed = $this->page( 'Colonna riservata', $root, false, true );
		$this->page( 'Link pubblico', $open );
		$this->page( 'Link riservato', $open, false, true );
		$this->page( 'Link della colonna riservata', $closed );

		list( $visitor, $reader ) = $this->as_visitor_and_reader(
			function () use ( $root ) {
				$index  = dli_get_page_index( $root, 4, 6 );
				$titles = array();
				foreach ( (array) ( $index['groups'] ?? array() ) as $group ) {
					$titles[] = $group['title'] . ': ' . implode( ', ', wp_list_pluck( $group['items'], 'title' ) );
				}
				return $titles;
			}
		);

		$this->assertSame( array( 'Colonna pubblica: Link pubblico' ), $visitor );
		$this->assertSame( array( 'Colonna pubblica: Link pubblico, Link riservato', 'Colonna riservata: Link della colonna riservata' ), $reader );
	}

	/**
	 * The navigation by pages leaves out a restricted group for a visitor.
	 */
	public function test_promoted_page_groups() {
		$open   = $this->page( 'Gruppo pubblico', 0, true );
		$closed = $this->page( 'Gruppo riservato', 0, true, true );
		$this->page( 'Voce pubblica', $open );
		$this->page( 'Voce riservata', $closed );

		list( $visitor, $reader ) = $this->as_visitor_and_reader(
			function () {
				return wp_list_pluck( dli_get_promoted_page_groups( 4, 4 ), 'title' );
			}
		);

		$this->assertSame( array( 'Gruppo pubblico' ), $visitor );
		$this->assertSame( array( 'Gruppo pubblico', 'Gruppo riservato' ), $reader );
	}
}
