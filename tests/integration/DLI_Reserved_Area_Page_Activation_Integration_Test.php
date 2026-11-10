<?php
/**
 * The page "Area riservata" made by the activation data of the theme (Strumenti DLI → Utilità → Ricarica i dati di
 * attivazione): the page in both languages, its link in the footer menus and its choice in the configuration.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_create_the_pages()` and `dli_select_reserved_area_page()`.
 */
class DLI_Reserved_Area_Page_Activation_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Add the English language and start with no page chosen.
	 */
	public function set_up() {
		parent::set_up();
		$language = PLL()->model->languages->add(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => false,
				'flag'   => 'gb',
			)
		);
		if ( is_wp_error( $language ) ) {
			$this->fail( 'Cannot create the English language: ' . $language->get_error_message() );
		}
		// Explicit values: CMB2 does not empty its in-memory copy of a group when given an empty array.
		$this->set_theme_options(
			DLI_RESERVED_AREA_OPTION_KEY,
			array(
				'reserved_area_enabled'   => 'false',
				'reserved_area_page_id'   => '',
				'reserved_area_saml_only' => 'false',
			)
		);
	}

	/**
	 * The page chosen in the configuration, as saved (also with the reserved area switched off).
	 *
	 * @return int
	 */
	private function chosen_page_id() {
		return absint( dli_get_option( 'reserved_area_page_id', DLI_RESERVED_AREA_OPTION_KEY ) );
	}

	/**
	 * The pages of the theme include "Area riservata", a page whose text the editors write.
	 */
	public function test_the_pages_of_the_theme_include_the_reserved_area_page() {
		$found = null;
		foreach ( DLI_STATIC_PAGE_CATS as $page ) {
			if ( SLUG_AREA_RISERVATA_IT === $page['content_slug_it'] ) {
				$found = $page;
			}
		}
		$this->assertNotNull( $found, 'The page is in the list of the pages of the theme.' );
		$this->assertSame( SLUG_AREA_RISERVATA_EN, $found['content_slug_en'] );
		$this->assertSame( 'Area riservata', $found['content_title_it'] );
		$this->assertSame( 'Reserved area', $found['content_title_en'] );
		$this->assertSame( 'publish', $found['content_status'] );
		$this->assertSame( '', $found['content_template'], 'The default template, which shows the access box.' );
		$this->assertSame( DLI_STATIC_PAGE_CAT, $found['content_category'] );
	}

	/**
	 * The footer menus of a new site link the page, in each language.
	 */
	public function test_the_footer_menus_link_the_reserved_area_page() {
		$slugs_it = wp_list_pluck( DLI_FOOTER_MENU_IT['items'], 'slug' );
		$slugs_en = wp_list_pluck( DLI_FOOTER_MENU_EN['items'], 'slug' );
		$this->assertContains( SLUG_AREA_RISERVATA_IT, $slugs_it );
		$this->assertContains( SLUG_AREA_RISERVATA_EN, $slugs_en );
	}

	/**
	 * The reload makes the page in both languages, linked as translations, and chooses it when no page is chosen.
	 */
	public function test_reload_creates_the_page_in_both_languages_and_chooses_it() {
		dli_create_the_pages();

		$page_it = dli_get_content( SLUG_AREA_RISERVATA_IT, 'page' );
		$page_en = dli_get_content( SLUG_AREA_RISERVATA_EN, 'page' );
		$this->assertInstanceOf( WP_Post::class, $page_it );
		$this->assertInstanceOf( WP_Post::class, $page_en );
		$this->assertSame( 'publish', $page_it->post_status );
		$this->assertSame( 'it', pll_get_post_language( $page_it->ID ) );
		$this->assertSame( 'en', pll_get_post_language( $page_en->ID ) );
		$this->assertSame( $page_en->ID, pll_get_post( $page_it->ID, 'en' ), 'The two pages are translations.' );

		$this->assertSame( $page_it->ID, $this->chosen_page_id(), 'The Italian page is chosen in the configuration.' );
	}

	/**
	 * A second reload makes no copy of the page.
	 */
	public function test_a_second_reload_makes_no_copy() {
		dli_create_the_pages();
		dli_create_the_pages();

		$pages = get_posts(
			array(
				'post_type'   => 'page',
				'name'        => SLUG_AREA_RISERVATA_IT,
				'post_status' => 'any',
				'numberposts' => -1,
			)
		);
		$this->assertCount( 1, $pages );
	}

	/**
	 * A page already chosen in the configuration is kept.
	 */
	public function test_a_page_already_chosen_is_kept() {
		$own_page = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Intranet',
			)
		);
		cmb2_update_option( DLI_RESERVED_AREA_OPTION_KEY, 'reserved_area_page_id', (string) $own_page );

		dli_create_the_pages();

		$this->assertSame( $own_page, $this->chosen_page_id() );
	}

	/**
	 * "Nessuna" chosen after the page was made survives a later reload: the field is filled only when the reload
	 * makes the page.
	 */
	public function test_no_page_chosen_on_purpose_survives_a_later_reload() {
		dli_create_the_pages();
		cmb2_update_option( DLI_RESERVED_AREA_OPTION_KEY, 'reserved_area_page_id', '' );

		dli_create_the_pages();

		$this->assertSame( 0, $this->chosen_page_id() );
	}

	/**
	 * A page with the same address made before is kept and not chosen.
	 */
	public function test_an_existing_page_is_kept_and_not_chosen() {
		$existing = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Area riservata',
				'post_name'  => SLUG_AREA_RISERVATA_IT,
			)
		);

		dli_create_the_pages();

		$this->assertSame( $existing, dli_get_content( SLUG_AREA_RISERVATA_IT, 'page' )->ID, 'No new page.' );
		$this->assertSame( 0, $this->chosen_page_id() );
	}

	/**
	 * Only a published page is chosen.
	 */
	public function test_a_page_not_published_is_not_chosen() {
		$draft = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'draft',
			)
		);

		dli_select_reserved_area_page( $draft );

		$this->assertSame( 0, $this->chosen_page_id() );
	}
}
