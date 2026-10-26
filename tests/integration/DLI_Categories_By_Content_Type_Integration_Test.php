<?php
/**
 * Integration tests of dli_get_all_categories_by_ct(), which feeds the filters of the archive pages.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The function lists the terms used by published content of a type, without WordPress's default category.
 */
class DLI_Categories_By_Content_Type_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Create a published post filed in the given categories.
	 *
	 * @param string $title        Title.
	 * @param int[]  $category_ids Category term IDs; empty keeps WordPress's default category.
	 * @return int Post ID.
	 */
	private function create_categorized_post( $title, array $category_ids = array() ) {
		$post_id = $this->create_post( 'post', $title );
		if ( $category_ids ) {
			wp_set_post_categories( $post_id, $category_ids );
		}
		return $post_id;
	}

	/**
	 * Names of the categories returned for the standard posts.
	 *
	 * @return string[]
	 */
	private function listed_names() {
		return wp_list_pluck( dli_get_all_categories_by_ct( 'category', 'post' ), 'name' );
	}

	/**
	 * A category used by a published post is listed, with the data the archive filters need.
	 */
	public function test_a_used_category_is_listed_with_its_data() {
		$category_id = self::factory()->category->create( array( 'name' => 'Ricerca' ) );
		$this->create_categorized_post( 'Un articolo', array( $category_id ) );

		$categories = dli_get_all_categories_by_ct( 'category', 'post' );

		$this->assertSame( array( 'Ricerca' ), wp_list_pluck( $categories, 'name' ) );
		$this->assertSame( array( 'id', 'slug', 'name', 'description', 'url' ), array_keys( $categories[0] ) );
		$this->assertSame( $category_id, $categories[0]['id'] );
	}

	/**
	 * WordPress's default category is never listed, even when a published post is filed in it.
	 */
	public function test_the_uncategorized_category_is_never_listed() {
		$category_id = self::factory()->category->create( array( 'name' => 'Ricerca' ) );
		$this->create_categorized_post( 'Senza categoria' );
		$this->create_categorized_post( 'Con categoria', array( $category_id ) );

		$names = $this->listed_names();

		$this->assertContains( 'Ricerca', $names );
		$this->assertNotContains( 'Uncategorized', $names );
	}

	/**
	 * A category that no published post uses is not listed.
	 */
	public function test_a_category_without_published_posts_is_not_listed() {
		$used   = self::factory()->category->create( array( 'name' => 'Usata' ) );
		$unused = self::factory()->category->create( array( 'name' => 'Inutilizzata' ) );
		$this->create_categorized_post( 'Pubblicato', array( $used ) );
		$draft = $this->create_post( 'post', 'Bozza', 'draft' );
		wp_set_post_categories( $draft, array( $unused ) );

		$names = $this->listed_names();

		$this->assertContains( 'Usata', $names );
		$this->assertNotContains( 'Inutilizzata', $names );
	}
}
