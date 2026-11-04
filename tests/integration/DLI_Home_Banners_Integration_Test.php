<?php
/**
 * The homepage section "Banner": each row of Sezioni HP chooses between the banners by priority (the way it has
 * always been) and a list of banners chosen by hand, in order, and has a number of banners of its own.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_get_home_banners()` and `template-parts/home/hp-banners-section.php`.
 */
class DLI_Home_Banners_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards: it is a property of the Polylang
	 * object, shared by all the tests, and is not rolled back with the database.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * Remember the current language of Polylang.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;
	}

	/**
	 * Put back the current language of Polylang.
	 */
	public function tear_down() {
		PLL()->curlang = $this->previous_language;
		parent::tear_down();
	}

	/**
	 * Add the English language to Polylang and make Italian the current one.
	 */
	private function add_english() {
		$english = PLL()->model->languages->add(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => false,
				'flag'   => 'gb',
			)
		);
		$this->assertNotWPError( $english );
		PLL()->curlang = PLL()->model->get_language( 'it' );
	}

	/**
	 * Create a banner.
	 *
	 * @param string $title    Title.
	 * @param int    $priority Value of the field `priorita`: the lower, the first.
	 * @param string $status   Post status.
	 * @param string $lang     Language slug.
	 * @return int Banner ID.
	 */
	private function create_banner( $title, $priority = 5, $status = 'publish', $lang = 'it' ) {
		$banner_id = self::factory()->post->create(
			array(
				'post_type'    => BANNER_POST_TYPE,
				'post_title'   => $title,
				'post_content' => 'Testo di ' . $title,
				'post_status'  => $status,
			)
		);
		dli_set_post_language( $banner_id, $lang );
		update_post_meta( $banner_id, 'priorita', $priority );
		return $banner_id;
	}

	/**
	 * The titles of banners.
	 *
	 * @param WP_Post[] $banners The banners.
	 * @return string[]
	 */
	private function titles_of( array $banners ) {
		return wp_list_pluck( $banners, 'post_title' );
	}

	/**
	 * Print the section of a row.
	 *
	 * @param array<string, mixed> $row Values of the row, over the defaults.
	 * @return string The HTML.
	 */
	private function render_section( array $row = array() ) {
		ob_start();
		get_template_part(
			'template-parts/home/hp-banners-section',
			null,
			array_merge(
				array(
					'id'         => 'banners_section',
					'enabled'    => 'true',
					'show_title' => 'true',
					'instance'   => 1,
				),
				$row
			)
		);
		return (string) ob_get_clean();
	}

	/**
	 * Every id of a page.
	 *
	 * @param string $html The HTML.
	 * @return string[]
	 */
	private function ids_of( $html ) {
		preg_match_all( '#\sid="([^"]+)"#', $html, $found );
		return $found[1];
	}

	/**
	 * Without a choice the section is automatic: the first three published banners by priority, as it has always been.
	 */
	public function test_the_automatic_mode_gives_the_first_three_by_priority() {
		$this->create_banner( 'Quinto', 9 );
		$this->create_banner( 'Primo', 1 );
		$this->create_banner( 'Terzo', 3 );
		$this->create_banner( 'Quarto', 4 );
		$this->create_banner( 'Secondo', 2 );
		$this->create_banner( 'Bozza', 0, 'draft' );

		$this->assertSame( array( 'Primo', 'Secondo', 'Terzo' ), $this->titles_of( dli_get_home_banners( array() ) ) );
		$this->assertSame( array( 'Primo', 'Secondo', 'Terzo' ), $this->titles_of( dli_get_home_banners( array( 'banner_mode' => 'automatic' ) ) ) );
	}

	/**
	 * The number of the row is the number of banners of the automatic mode (three when the row does not say); the
	 * manual mode ignores it and gives all the banners chosen, up to the most a section can have.
	 */
	public function test_the_number_of_the_row_counts_in_the_automatic_mode_only() {
		$ids = array();
		for ( $i = 1; $i <= DLI_HP_ITEMS_MAX + 2; $i++ ) {
			$ids[] = $this->create_banner( "B$i", $i );
		}

		$this->assertCount( 3, dli_get_home_banners( array() ), 'Three when the row does not say.' );
		$this->assertCount( 2, dli_get_home_banners( array( 'max_items' => 2 ) ) );
		$this->assertCount( 6, dli_get_home_banners( array( 'max_items' => 6 ) ) );
		$this->assertCount( DLI_HP_ITEMS_MAX, dli_get_home_banners( array( 'max_items' => 99 ) ), 'No more than twelve.' );
		$this->assertCount( 5, dli_get_home_banners( array( 'banner_mode' => 'manual', 'max_items' => 2, 'banner_ids' => array_slice( $ids, 0, 5 ) ) ), 'The manual mode shows all the chosen.' );
		$this->assertCount( DLI_HP_ITEMS_MAX, dli_get_home_banners( array( 'banner_mode' => 'manual', 'banner_ids' => $ids ) ), 'No more than twelve.' );
	}

	/**
	 * The manual mode gives the banners that were chosen, in the order they were chosen, whatever their priority.
	 */
	public function test_the_manual_mode_gives_the_chosen_banners_in_the_chosen_order() {
		$first  = $this->create_banner( 'Primo', 1 );
		$second = $this->create_banner( 'Secondo', 2 );
		$third  = $this->create_banner( 'Terzo', 3 );

		$banners = dli_get_home_banners(
			array(
				'banner_mode' => 'manual',
				'banner_ids'  => array( $third, $first ),
			)
		);

		$this->assertSame( array( 'Terzo', 'Primo' ), $this->titles_of( $banners ) );
		$this->assertNotContains( $second, wp_list_pluck( $banners, 'ID' ) );
	}

	/**
	 * A banner that was chosen and cannot be shown any more is left out: a draft, a trashed one, a missing one, an ID
	 * that is not a banner. The others stay in order.
	 */
	public function test_the_manual_mode_leaves_out_what_cannot_be_shown() {
		$good    = $this->create_banner( 'Buono' );
		$draft   = $this->create_banner( 'Bozza', 5, 'draft' );
		$trashed = $this->create_banner( 'Cestino' );
		wp_trash_post( $trashed );
		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );
		$last = $this->create_banner( 'Ultimo' );

		$banners = dli_get_home_banners(
			array(
				'banner_mode' => 'manual',
				'banner_ids'  => array( $draft, $good, $trashed, 999999, $page, $last ),
			)
		);

		$this->assertSame( array( 'Buono', 'Ultimo' ), $this->titles_of( $banners ) );
	}

	/**
	 * The manual mode with nothing chosen gives nothing: the section is not filled with the automatic ones.
	 */
	public function test_the_manual_mode_without_a_choice_gives_nothing() {
		$this->create_banner( 'Qualcuno' );

		$this->assertSame( array(), dli_get_home_banners( array( 'banner_mode' => 'manual' ) ) );
		$this->assertSame( array(), dli_get_home_banners( array( 'banner_mode' => 'manual', 'banner_ids' => array() ) ) );
	}

	/**
	 * The same banner can be chosen in two sections: it is an editorial choice.
	 */
	public function test_the_same_banner_can_be_chosen_in_two_sections() {
		$banner = $this->create_banner( 'Condiviso' );
		$row    = array(
			'banner_mode' => 'manual',
			'banner_ids'  => array( $banner ),
		);

		$this->assertSame( array( 'Condiviso' ), $this->titles_of( dli_get_home_banners( $row ) ) );
		$this->assertSame( array( 'Condiviso' ), $this->titles_of( dli_get_home_banners( $row ) ) );
	}

	/**
	 * The choices are the banners of the default language; on the English page the translation is shown, and a banner
	 * without it is left out.
	 */
	public function test_the_manual_mode_follows_the_translation() {
		$this->add_english();
		$italian_a = $this->create_banner( 'Banner A' );
		$english_a = $this->create_banner( 'Banner A EN', 5, 'publish', 'en' );
		$italian_b = $this->create_banner( 'Banner B' );
		dli_save_post_translations(
			array(
				'it' => $italian_a,
				'en' => $english_a,
			)
		);
		$row = array(
			'banner_mode' => 'manual',
			'banner_ids'  => array( $italian_b, $italian_a ),
		);

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$this->assertSame( array( 'Banner B', 'Banner A' ), $this->titles_of( dli_get_home_banners( $row ) ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertSame( array( 'Banner A EN' ), $this->titles_of( dli_get_home_banners( $row ) ), 'B has no English version.' );
	}

	/**
	 * The markup of a banner is what it has always been, in the automatic mode.
	 */
	public function test_the_automatic_markup_keeps_the_banner() {
		$banner = $this->create_banner( 'Mostra annuale', 1 );
		update_post_meta( $banner, 'sezione', 'Eventi' );
		update_post_meta( $banner, 'label_pulsante', 'Scopri' );
		update_post_meta( $banner, 'link_pulsante', 'https://example.org/mostra' );
		update_post_meta( $banner, 'apri_in_nuova_finestra', 1 );

		$html = $this->render_section();

		$this->assertStringContainsString( 'container-banner-home', $html );
		$this->assertStringContainsString( 'it-hero-wrapper it-hero-small-size it-primary it-overlay', $html );
		$this->assertStringContainsString( '<span class="it-category">Eventi</span>', $html );
		$this->assertMatchesRegularExpression( '#<h2 id="[^"]*"[^>]*>Mostra annuale</h2>#', $html );
		$this->assertStringContainsString( 'href="https://example.org/mostra"', $html );
		$this->assertStringContainsString( 'target="_blank" rel="noopener noreferrer"', $html );
	}

	/**
	 * Two sections share no identifier, not even for the same banner.
	 */
	public function test_two_sections_share_no_identifier() {
		$banner = $this->create_banner( 'Condiviso' );
		$row    = array(
			'banner_mode' => 'manual',
			'banner_ids'  => array( $banner ),
		);

		$page = $this->render_section( array_merge( $row, array( 'instance' => 1 ) ) )
			. $this->render_section( array_merge( $row, array( 'instance' => 3 ) ) );

		$this->assertSame( array_unique( $this->ids_of( $page ) ), $this->ids_of( $page ), 'No identifier is repeated.' );
		$this->assertStringContainsString( 'id="blocco-banner-1"', $page );
		$this->assertStringContainsString( 'id="blocco-banner-3"', $page );
		// Each banner is named by its own heading.
		preg_match_all( '#aria-labelledby="([^"]+)"#', $page, $labels );
		foreach ( $labels[1] as $label ) {
			$this->assertContains( $label, $this->ids_of( $page ), 'The name points to a heading of the page.' );
		}
	}

	/**
	 * Without a title the section has no heading of its own: the banners look as they always did, whatever the row says
	 * about showing the title.
	 */
	public function test_a_section_without_a_title_has_no_heading_of_its_own() {
		$this->create_banner( 'Uno' );

		foreach ( array( 'true', 'false' ) as $show_title ) {
			$html = $this->render_section( array( 'show_title' => $show_title ) );

			$this->assertStringNotContainsString( 'blocco-banner-1-title', $html );
			$this->assertStringNotContainsString( 'aria-labelledby="blocco-banner-1"', $html );
			$this->assertSame( 1, preg_match_all( '#<h2 #', $html ), 'Only the heading of the banner.' );
		}
	}

	/**
	 * Nothing is printed when the section is off or has no banner.
	 */
	public function test_nothing_is_printed_when_off_or_without_banners() {
		$this->assertSame( '', trim( $this->render_section() ), 'No banner, no empty section.' );

		$this->create_banner( 'Uno' );
		$this->assertSame( '', trim( $this->render_section( array( 'enabled' => 'false' ) ) ) );
		$this->assertSame( '', trim( $this->render_section( array( 'banner_mode' => 'manual' ) ) ), 'Manual with no choice.' );
		$this->assertStringContainsString( 'Uno', $this->render_section() );
	}

	/**
	 * The number of queries does not grow with the number of banners.
	 */
	public function test_the_queries_do_not_grow_with_the_number_of_banners() {
		global $wpdb;
		$ids = array();
		for ( $i = 1; $i <= 3; $i++ ) {
			$ids[] = $this->create_banner( "Gruppo A $i", $i );
		}
		$row    = array( 'banner_mode' => 'manual' );
		$before = $wpdb->num_queries;
		dli_get_home_banners( array_merge( $row, array( 'banner_ids' => $ids ) ) );
		$few = $wpdb->num_queries - $before;

		for ( $i = 4; $i <= 12; $i++ ) {
			$ids[] = $this->create_banner( "Gruppo B $i", $i );
		}
		$before = $wpdb->num_queries;
		dli_get_home_banners( array_merge( $row, array( 'banner_ids' => $ids ) ) );
		$many = $wpdb->num_queries - $before;

		// The translation of each chosen banner is read one by one (at most twelve); the banners and their images are not.
		$this->assertLessThanOrEqual( $few + 3 * 9, $many, "3 banners: $few queries, 12 banners: $many." );
	}

	/**
	 * The choices of the field are the published banners of the default language, and a banner already chosen that
	 * can no longer be shown stays among them, so that saving does not drop it.
	 */
	public function test_the_choices_keep_the_banner_already_chosen() {
		$visible = $this->create_banner( 'Visibile' );
		$draft   = $this->create_banner( 'In bozza', 5, 'draft' );
		$this->set_theme_options(
			'homepage_sections',
			array(
				'site_sections' => array(
					array(
						'id'          => 'banners_section',
						'enabled'     => 'true',
						'banner_mode' => 'manual',
						'banner_ids'  => array( $draft ),
					),
				),
			)
		);

		$choices = dli_get_banners_for_select();

		$this->assertSame( 'Visibile', $choices[ $visible ] );
		$this->assertArrayHasKey( $draft, $choices );
		$this->assertSame( sprintf( __( '%s (non visibile)', 'design_laboratori_italia' ), 'In bozza' ), $choices[ $draft ] );
	}

	/**
	 * The catalog: only the section Banner has the mode and the list, and it has a default number of banners.
	 */
	public function test_the_catalog_gives_the_banner_section_its_fields() {
		$this->assertSame( array( 'banners_section' => true ), DLI_ContentsManager::get_hp_sections_with_banner_mode() );
		$this->assertSame( DLI_HP_BANNERS_DEFAULT_ITEMS, DLI_ContentsManager::get_hp_sections_with_max_items()['banners_section'] ?? null, 'The number of the automatic mode has its default.' );
		$this->assertArrayHasKey( 'banners_section', DLI_ContentsManager::get_hp_sections_with_title() );
		$this->assertSame( 3, DLI_HP_BANNERS_DEFAULT_ITEMS );
	}

	/**
	 * The title is shown only when the row has it in the language of the page and asks to show it: no other language
	 * takes its place, and a title that is not to be shown is not printed at all.
	 */
	public function test_the_title_is_shown_only_in_the_language_it_is_written_in() {
		$this->add_english();
		$italian = $this->create_banner( 'Uno' );
		$english = $this->create_banner( 'One', 5, 'publish', 'en' );
		dli_save_post_translations(
			array(
				'it' => $italian,
				'en' => $english,
			)
		);
		$row = array(
			'title'     => 'In evidenza',
			'title_eng' => 'Highlights',
		);

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$this->assertMatchesRegularExpression( '#<h2 id="blocco-banner-1-title" class="h3 pb-2">\s*In evidenza\s*</h2>#', $this->render_section( $row ) );
		$this->assertStringContainsString( 'aria-labelledby="blocco-banner-1-title"', $this->render_section( $row ) );
		$this->assertStringNotContainsString( 'blocco-banner-1-title', $this->render_section( array_merge( $row, array( 'title' => '' ) ) ), 'An Italian page never takes the English title.' );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertMatchesRegularExpression( '#<h2 id="blocco-banner-1-title" class="h3 pb-2">\s*Highlights\s*</h2>#', $this->render_section( $row ) );
		$this->assertStringNotContainsString( 'blocco-banner-1-title', $this->render_section( array_merge( $row, array( 'title_eng' => ' ' ) ) ), 'No English title, no title.' );

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$hidden = $this->render_section( array_merge( $row, array( 'show_title' => 'false' ) ) );
		$this->assertStringNotContainsString( 'blocco-banner-1-title', $hidden, 'Not to be shown, not printed: not even hidden.' );
		$this->assertStringNotContainsString( 'In evidenza', $hidden );
	}
}
