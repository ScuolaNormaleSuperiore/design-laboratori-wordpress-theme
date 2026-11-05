<?php
/**
 * The archive of a category answers 404: the theme has no page for it.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `category.php` and the pages that are filtered by category with `?cat[]=`.
 */
class DLI_Category_Archive_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * HTTP status codes that WordPress was asked to send while the test ran.
	 *
	 * @var int[]
	 */
	private $status_codes = array();

	/**
	 * Remember the status codes sent by status_header().
	 */
	public function set_up() {
		parent::set_up();
		$this->status_codes = array();
		add_filter( 'status_header', array( $this, 'record_status' ), 10, 2 );
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
	 * Create a category with a news item in it.
	 *
	 * @return int Category ID.
	 */
	private function create_category_with_a_news_item() {
		$category_id = self::factory()->category->create( array( 'name' => 'Categoria di prova' ) );
		$news_id     = $this->create_post( NEWS_POST_TYPE, 'Notizia nella categoria' );
		wp_set_object_terms( $news_id, array( $category_id ), 'category' );
		return $category_id;
	}

	/**
	 * Request the archive of a category and render `category.php`, as WordPress does for that request.
	 *
	 * @param int $category_id Category ID.
	 * @return string Rendered HTML.
	 */
	private function render_category_archive( $category_id ) {
		$this->go_to( get_category_link( $category_id ) );
		$this->assertTrue( is_category(), 'The request must be the archive of a category.' );

		return $this->render_theme_template( 'category.php' );
	}

	/**
	 * The archive of a category is a 404, not an empty page with status 200.
	 */
	public function test_category_archive_is_not_found() {
		$html = $this->render_category_archive( $this->create_category_with_a_news_item() );

		$this->assertTrue( is_404(), 'The main query must be a 404.' );
		$this->assertContains( 404, $this->status_codes, 'WordPress must be asked to send the status 404.' );
		$this->assertNotSame( '', trim( $html ), 'The page must not be empty.' );
	}

	/**
	 * The 404 page of the theme is the one that is shown.
	 */
	public function test_category_archive_shows_the_404_page_of_the_theme() {
		$html = $this->render_category_archive( $this->create_category_with_a_news_item() );

		$this->assertStringContainsString( 'dli-hero-404-title', $html );
	}

	/**
	 * Control: a page filtered by category (`?cat[]=ID`, as Notizie, Eventi and Blog are) is still a page, not a 404.
	 *
	 * WordPress picks the template of a page before the one of a category, so `category.php` is not reached.
	 */
	public function test_page_filtered_by_category_is_not_a_404() {
		$category_id = $this->create_category_with_a_news_item();
		$page_id     = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Notizie di prova',
			)
		);
		dli_set_post_language( $page_id, 'it' );

		$this->go_to( add_query_arg( 'cat', array( $category_id ), get_permalink( $page_id ) ) );

		$this->assertTrue( is_page(), 'The request must still be a page.' );
		$this->assertFalse( is_404() );
	}
}
