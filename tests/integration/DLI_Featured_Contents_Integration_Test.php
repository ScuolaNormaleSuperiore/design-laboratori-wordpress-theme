<?php
/**
 * The homepage section "Contenuti in evidenza": its boxes, the items they list and how they are printed.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `inc/featured-contents.php` and `template-parts/home/featured-contents.php` with its renderers.
 */
class DLI_Featured_Contents_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards: it is a property of the
	 * Polylang object, which is shared by all the tests, and is not rolled back with the database.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * Remember the current language of Polylang and make Italian the current one.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;
		if ( ! PLL()->model->get_language( 'en' ) ) {
			PLL()->model->languages->add(
				array(
					'name'   => 'English',
					'slug'   => 'en',
					'locale' => 'en_US',
					'rtl'    => false,
					'flag'   => 'gb',
				)
			);
		}
		PLL()->curlang = PLL()->model->get_language( 'it' );
	}

	/**
	 * Put back the current language of Polylang.
	 */
	public function tear_down() {
		PLL()->curlang = $this->previous_language;
		parent::tear_down();
	}

	/**
	 * Create a content promoted to the homepage.
	 *
	 * @param string               $type   Post type.
	 * @param string               $title  Title.
	 * @param array<string, mixed> $fields ACF fields, by name.
	 * @param array<string, mixed> $post   Extra arguments of wp_update_post().
	 * @param bool                 $promoted Whether the field "Promuovi in Home" is on.
	 * @return int Post ID.
	 */
	private function create_content( $type, $title, array $fields = array(), array $post = array(), $promoted = true ) {
		$post_id = $this->create_post( $type, $title );
		if ( ! empty( $post ) ) {
			wp_update_post( array_merge( array( 'ID' => $post_id ), $post ) );
		}
		$this->set_acf_fields( $post_id, array_merge( $promoted ? array( 'promuovi_in_home' => 1 ) : array(), $fields ) );
		return $post_id;
	}

	/**
	 * Create the page that lists a content type, in Italian.
	 *
	 * @param string $slug   Slug of the page.
	 * @param string $status Status of the page.
	 * @return int Page ID.
	 */
	private function create_listing_page( $slug, $status = 'publish' ) {
		$page_id = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => ucfirst( $slug ),
				'post_name'   => $slug,
				'post_status' => $status,
			)
		);
		dli_set_post_language( $page_id, 'it' );
		return $page_id;
	}

	/**
	 * Save the boxes in Layout HP, as the three groups do. A box not given is emptied on purpose: the options of
	 * CMB2 are kept in memory and an array that omits a key does not clear it.
	 *
	 * @param array<int, array<string, string>> $boxes Boxes by number (1 to 3): type, title, title_eng.
	 */
	private function set_boxes( array $boxes ) {
		$values = array();
		foreach ( array( 1, 2, 3 ) as $number ) {
			if ( empty( $boxes[ $number ] ) ) {
				$values[ 'featured_contents_' . $number ] = array();
				continue;
			}
			$box = $boxes[ $number ];

			$values[ 'featured_contents_' . $number ] = array(
				0 => array(
					'featured_contents_type_box_' . $number  => $box['type'],
					'featured_contents_label_box_' . $number => $box['title'] ?? '',
					'featured_contents_label_box_' . $number . DLI_ENG_SUFFIX_LANGUAGE => $box['title_eng'] ?? '',
				),
			);
		}
		$this->set_theme_options( 'homepage', $values );
	}

	/**
	 * Print the section of the featured contents.
	 *
	 * @param array<string, mixed> $row Properties of the row, on top of the defaults.
	 * @return string
	 */
	private function render_section( array $row = array() ) {
		ob_start();
		get_template_part(
			'template-parts/home/featured-contents',
			null,
			array_merge(
				array(
					'id'       => 'featured_contents',
					'enabled'  => 'true',
					'instance' => 1,
				),
				$row
			)
		);
		return (string) ob_get_clean();
	}

	/**
	 * Only news, events, publications and the types that can show a promoted content are offered.
	 */
	public function test_content_types_are_the_ones_that_can_be_promoted() {
		$types = dli_get_featured_content_types();

		foreach ( array( NEWS_POST_TYPE, EVENT_POST_TYPE, PUBLICATION_POST_TYPE ) as $type ) {
			$this->assertArrayHasKey( $type, $types );
			$this->assertNotSame( '', $types[ $type ] );
		}
		$this->assertArrayNotHasKey( RESEARCH_ACTIVITY_POST_TYPE, $types, 'A type without the field "Promuovi in Home" cannot fill a box.' );
	}

	/**
	 * The link of a content follows the choice of the editor; a protected content leads to its own page.
	 */
	public function test_content_link_follows_the_choice_of_the_editor() {
		$plain    = $this->create_content( NEWS_POST_TYPE, 'Notizia scheda' );
		$external = $this->create_content(
			NEWS_POST_TYPE,
			'Notizia esterna',
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'sitoweb'        => 'https://example.org/notizia',
			)
		);
		$locked   = $this->create_content(
			NEWS_POST_TYPE,
			'Notizia protetta',
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'sitoweb'        => 'https://example.org/segreto',
			),
			array( 'post_password' => 'segreto' )
		);
		$no_link  = $this->create_content( NEWS_POST_TYPE, 'Notizia senza link', array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'] ) );

		$this->assertSame(
			array(
				'url'      => get_permalink( $plain ),
				'external' => false,
			),
			dli_get_content_link( $plain )
		);
		$this->assertSame(
			array(
				'url'      => 'https://example.org/notizia',
				'external' => true,
			),
			dli_get_content_link( $external )
		);
		$this->assertSame(
			array(
				'url'      => get_permalink( $locked ),
				'external' => false,
			),
			dli_get_content_link( $locked ),
			'A protected content never leads outside: the address is what the password protects.'
		);
		$this->assertSame(
			array(
				'url'      => '',
				'external' => false,
			),
			dli_get_content_link( $no_link ),
			'A choice without its address gives no link at all.'
		);
	}

	/**
	 * The button leads to the listing page in the current language, and only when that page can be reached.
	 */
	public function test_archive_url_is_the_published_listing_page_in_the_current_language() {
		$this->assertSame( '', dli_get_featured_archive_url( NEWS_POST_TYPE ), 'Without the page there is no address, never a relative one.' );

		$draft = $this->create_listing_page( SLUG_NOTIZIE_IT, 'draft' );
		$this->assertSame( '', dli_get_featured_archive_url( NEWS_POST_TYPE ), 'A draft page is not an address.' );

		wp_update_post(
			array(
				'ID'          => $draft,
				'post_status' => 'publish',
			)
		);
		$this->assertSame( get_permalink( $draft ), dli_get_featured_archive_url( NEWS_POST_TYPE ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertSame( '', dli_get_featured_archive_url( NEWS_POST_TYPE ), 'A page without a translation is not an address either.' );

		$english = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => 'News',
				'post_status' => 'draft',
			)
		);
		dli_set_post_language( $english, 'en' );
		dli_save_post_translations(
			array(
				'it' => $draft,
				'en' => $english,
			)
		);
		$this->assertSame( '', dli_get_featured_archive_url( NEWS_POST_TYPE ), 'A translation that is a draft does not count.' );
	}

	/**
	 * Three contents at most, the newest first, only the promoted ones and the ones in the current language.
	 */
	public function test_posts_are_the_three_newest_promoted_contents_of_the_language() {
		foreach ( array( 'Uno', 'Due', 'Tre', 'Quattro' ) as $index => $title ) {
			$this->create_content( NEWS_POST_TYPE, 'Notizia ' . $title, array(), array( 'post_date' => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( 4 - $index ) . ' days' ) ) ) );
		}
		$this->create_content( NEWS_POST_TYPE, 'Notizia non promossa', array(), array(), false );
		$english = $this->create_content( NEWS_POST_TYPE, 'English news' );
		dli_set_post_language( $english, 'en' );

		$posts = dli_get_featured_posts( NEWS_POST_TYPE );

		$this->assertSame( array( 'Notizia Quattro', 'Notizia Tre', 'Notizia Due' ), wp_list_pluck( $posts, 'post_title' ) );
		$this->assertSame( array(), dli_get_featured_posts( 'tipo-che-non-esiste' ), 'A type that does not exist has no content.' );
		$this->assertSame( array(), dli_get_featured_posts( '' ) );
	}

	/**
	 * The boxes are read from Layout HP: a box without a type, with an unknown type or without contents is left out.
	 */
	public function test_boxes_are_the_configured_ones_that_have_something_to_show() {
		$news = $this->create_content( NEWS_POST_TYPE, 'Una notizia' );
		$this->create_content( PUBLICATION_POST_TYPE, 'Una pubblicazione' );
		$this->set_boxes(
			array(
				1 => array(
					'type'  => NEWS_POST_TYPE,
					'title' => 'Notizie',
				),
				2 => array(
					'type'  => EVENT_POST_TYPE,
					'title' => 'Eventi senza contenuti',
				),
				3 => array(
					'type'  => PUBLICATION_POST_TYPE,
					'title' => '',
				),
			)
		);

		$boxes = dli_get_featured_boxes();

		$this->assertSame( array( NEWS_POST_TYPE, PUBLICATION_POST_TYPE ), wp_list_pluck( $boxes, 'type' ), 'The box with no contents is left out.' );
		$this->assertSame( 'Notizie', $boxes[0]['title'] );
		$this->assertNotSame( '', $boxes[1]['title'], 'An empty title is the default of the type.' );
		$this->assertSame( 'card-notizia', $boxes[0]['renderer'] );
		$this->assertSame( 'card-pubblicazione', $boxes[1]['renderer'] );
		$this->assertSame( array( $news ), wp_list_pluck( $boxes[0]['posts'], 'ID' ) );

		$this->set_boxes(
			array(
				1 => array(
					'type'  => 'tipo-che-non-esiste',
					'title' => 'Sconosciuto',
				),
			)
		);
		$this->assertSame( array(), dli_get_featured_boxes(), 'A type that does not exist is not a box.' );
	}

	/**
	 * The title of the box is the one of the language of the page.
	 */
	public function test_box_title_follows_the_language() {
		$this->create_content( NEWS_POST_TYPE, 'Una notizia' );
		$english = $this->create_content( NEWS_POST_TYPE, 'An English news' );
		dli_set_post_language( $english, 'en' );
		$this->set_boxes(
			array(
				1 => array(
					'type'      => NEWS_POST_TYPE,
					'title'     => 'Notizie',
					'title_eng' => 'News',
				),
			)
		);

		$this->assertSame( 'Notizie', dli_get_featured_boxes()[0]['title'] );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertSame( 'News', dli_get_featured_boxes()[0]['title'] );
	}

	/**
	 * Two boxes of the same type show the same contents: they are read once for the type.
	 */
	public function test_two_boxes_of_the_same_type_are_both_shown() {
		$this->create_content( NEWS_POST_TYPE, 'Una notizia' );
		$this->set_boxes(
			array(
				1 => array(
					'type'  => NEWS_POST_TYPE,
					'title' => 'Prima',
				),
				2 => array(
					'type'  => NEWS_POST_TYPE,
					'title' => 'Seconda',
				),
			)
		);

		$this->assertSame( array( 'Prima', 'Seconda' ), wp_list_pluck( dli_get_featured_boxes(), 'title' ) );
	}

	/**
	 * A section that is off prints nothing, and does not look for contents.
	 */
	public function test_disabled_section_prints_nothing_and_does_not_query() {
		global $wpdb;
		$this->create_content( NEWS_POST_TYPE, 'Una notizia' );
		$this->set_boxes(
			array(
				1 => array(
					'type'  => NEWS_POST_TYPE,
					'title' => 'Notizie',
				),
			)
		);

		$queries = $wpdb->num_queries;
		$this->assertSame( '', trim( $this->render_section( array( 'enabled' => 'false' ) ) ) );
		$this->assertSame( $queries, $wpdb->num_queries, 'No query is made for a section that is off.' );

		$this->set_boxes( array() );
		$this->assertSame( '', trim( $this->render_section() ), 'Without a box with contents there is no empty section.' );
	}

	/**
	 * The boxes share the row, with no empty column, and the headings are in order.
	 */
	public function test_columns_follow_the_number_of_boxes_and_headings_are_in_order() {
		$this->create_content( NEWS_POST_TYPE, 'Una notizia' );
		$this->create_content( EVENT_POST_TYPE, 'Un evento', array( 'data_inizio' => '20260115' ) );
		$this->create_content( PUBLICATION_POST_TYPE, 'Una pubblicazione' );
		$box = static function ( $type, $title ) {
			return array(
				'type'  => $type,
				'title' => $title,
			);
		};

		$this->set_boxes( array( 1 => $box( NEWS_POST_TYPE, 'Notizie' ) ) );
		$one = $this->render_section();
		$this->assertSame( 1, substr_count( $one, 'class="col-12 pb-5"' ), 'One box takes the whole row.' );
		$this->assertStringNotContainsString( 'col-lg-', $one );

		$this->set_boxes(
			array(
				1 => $box( NEWS_POST_TYPE, 'Notizie' ),
				3 => $box( PUBLICATION_POST_TYPE, 'Pubblicazioni' ),
			)
		);
		$two = $this->render_section();
		$this->assertSame( 2, substr_count( $two, 'col-lg-6' ), 'Two boxes take half the row each, also when the box 2 is not configured.' );
		$this->assertStringNotContainsString( 'col-lg-4', $two );

		$this->set_boxes(
			array(
				1 => $box( NEWS_POST_TYPE, 'Notizie' ),
				2 => $box( EVENT_POST_TYPE, 'Eventi' ),
				3 => $box( PUBLICATION_POST_TYPE, 'Pubblicazioni' ),
			)
		);
		$three = $this->render_section();
		$this->assertSame( 3, substr_count( $three, 'col-lg-4' ) );

		// The section has a hidden h2, each box an h3 and each item an h4 at most: no level is skipped.
		$this->assertSame( 1, substr_count( $three, '<h2' ) );
		$this->assertSame( 3, substr_count( $three, '<h3' ) );
		$this->assertMatchesRegularExpression( '#<h3 id="[^"]+"[^>]*>Notizie</h3>#', $three );
		$this->assertSame( 3, substr_count( $three, '<h4' ), 'One item in each box, with an h4 title.' );
		preg_match_all( '#aria-labelledby="([^"]+)"#', $three, $labels );
		foreach ( $labels[1] as $label ) {
			$this->assertSame( 1, substr_count( $three, 'id="' . $label . '"' ), 'Every list is named by a heading that exists once.' );
		}
	}

	/**
	 * Each content type has its own layout: news with the date, events with date and time, publications with authors and year.
	 */
	public function test_each_type_has_its_own_renderer() {
		$this->create_content( NEWS_POST_TYPE, 'Notizia di prova', array(), array( 'post_date' => '2026-01-15 10:00:00' ) );
		$this->create_content( EVENT_POST_TYPE, 'Evento di prova', array( 'data_inizio' => '20260220' ) );
		$this->create_content( PUBLICATION_POST_TYPE, 'Pubblicazione di prova', array( 'anno' => '2024' ) );
		$this->set_boxes(
			array(
				1 => array(
					'type'  => NEWS_POST_TYPE,
					'title' => 'Notizie',
				),
				2 => array(
					'type'  => EVENT_POST_TYPE,
					'title' => 'Eventi',
				),
				3 => array(
					'type'  => PUBLICATION_POST_TYPE,
					'title' => 'Pubblicazioni',
				),
			)
		);

		$html = $this->render_section();

		$this->assertStringContainsString( 'Notizia di prova', $html );
		$this->assertStringContainsString( '15/01/2026', $html, 'The date of a news is day, month and year with slashes, like the playground.' );
		$this->assertStringContainsString( 'Evento di prova', $html );
		$this->assertStringContainsString( '20 ' . dli_get_monthname( '02' ) . ' 2026', $html, 'An event shows its own date, not the date of publication.' );
		$this->assertStringContainsString( 'Pubblicazione di prova', $html );
		$this->assertStringNotContainsString( '2024', $html, 'A publication in the box shows no year.' );
		$this->assertSame( 3, substr_count( $html, '<article' ) );
		$this->assertStringNotContainsString( '<img', $html, 'News and events are a list without images.' );
	}

	/**
	 * A type without a renderer of its own has the essential layout.
	 */
	public function test_other_types_have_the_essential_layout() {
		$this->create_content( PATENT_POST_TYPE, 'Un brevetto', array(), array( 'post_excerpt' => 'Riassunto del brevetto.' ) );
		$this->set_boxes(
			array(
				1 => array(
					'type'  => PATENT_POST_TYPE,
					'title' => 'Brevetti',
				),
			)
		);

		$html = $this->render_section();

		$this->assertStringContainsString( 'Un brevetto', $html );
		$this->assertStringContainsString( 'Riassunto del brevetto.', $html );
		$this->assertMatchesRegularExpression( '#<a href="[^"]+">\s*Un brevetto#', $html );
	}

	/**
	 * The links chosen by the editor are kept, a missing address is plain text, never an empty href.
	 */
	public function test_titles_follow_the_link_of_the_content() {
		$this->create_content( NEWS_POST_TYPE, 'Notizia esterna', array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'], 'sitoweb' => 'https://example.org/notizia' ), array( 'post_date' => '2026-01-03 10:00:00' ) );
		$this->create_content( NEWS_POST_TYPE, 'Notizia senza link', array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'] ), array( 'post_date' => '2026-01-02 10:00:00' ) );
		$this->set_boxes(
			array(
				1 => array(
					'type'  => NEWS_POST_TYPE,
					'title' => 'Notizie',
				),
			)
		);

		$html = $this->render_section();

		$this->assertMatchesRegularExpression( '#<a href="https://example.org/notizia" target="_blank" rel="noopener noreferrer">\s*Notizia esterna#', $html );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*>\s*Notizia senza link#', $html, 'Without an address the title is plain text.' );
		$this->assertStringContainsString( 'Notizia senza link', $html );
		$this->assertDoesNotMatchRegularExpression( '#href=""#', $html );
	}

	/**
	 * The button leads to the listing page of the type, with the label of the type; without the page there is none.
	 */
	public function test_button_leads_to_the_listing_page_of_the_type() {
		$this->create_content( NEWS_POST_TYPE, 'Una notizia' );
		$this->create_content( EVENT_POST_TYPE, 'Un evento' );
		$this->set_boxes(
			array(
				1 => array(
					'type'  => NEWS_POST_TYPE,
					'title' => 'Notizie',
				),
				2 => array(
					'type'  => EVENT_POST_TYPE,
					'title' => 'Eventi',
				),
			)
		);
		$news_page = $this->create_listing_page( SLUG_NOTIZIE_IT );

		$html = $this->render_section();

		$this->assertMatchesRegularExpression( '#<a href="' . preg_quote( esc_url( get_permalink( $news_page ) ), '#' ) . '" class="btn btn-secondary">\s*Tutte le news\s*</a>#', $html );
		$this->assertStringNotContainsString( 'Tutti gli eventi', $html, 'The events page does not exist: no button, never an empty address.' );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*href=""#', $html );
	}

	/**
	 * Every type a box can show has the label of its own button: none falls back to the generic one, and they differ.
	 */
	public function test_every_offered_type_has_its_own_button_label() {
		$fallback = dli_get_featured_button_label( 'tipo-che-non-esiste' );

		$labels = array();
		foreach ( array_keys( dli_get_featured_content_types() ) as $type ) {
			$label = dli_get_featured_button_label( $type );
			$this->assertNotSame( $fallback, $label, "The type $type needs a label of its own." );
			$labels[ $type ] = $label;
		}

		$this->assertSame( $labels, array_unique( $labels ), 'No two types share a label.' );
		$this->assertSame( 'Tutti i brevetti', $labels[ PATENT_POST_TYPE ] );
	}

	/**
	 * Print the box of events, with the first event of the test.
	 *
	 * @return string
	 */
	private function render_events_box() {
		$this->set_boxes(
			array(
				1 => array(
					'type'  => EVENT_POST_TYPE,
					'title' => 'Eventi',
				),
			)
		);
		return $this->render_section();
	}

	/**
	 * An event card is laid out like the one of the playground: title, then date and place in the body, then the
	 * category in the footer; the date is not in the footer.
	 */
	public function test_event_card_has_date_and_place_in_the_body_and_the_category_in_the_footer() {
		$event = $this->create_content(
			EVENT_POST_TYPE,
			'Seminario di prova',
			array(
				'data_inizio'   => '20260220',
				'orario_inizio' => '15:00',
				'luogo'         => 'Altana Conference Hall',
			)
		);
		$term  = wp_insert_term( 'Seminari', 'category', array( 'slug' => 'seminari-home' ) );
		wp_set_object_terms( $event, array( (int) $term['term_id'] ), 'category' );
		$events_page = $this->create_listing_page( SLUG_EVENTI_IT );

		$html = $this->render_events_box();

		// Title, then the body with the date and the place, then the footer with the category.
		$title    = strpos( $html, 'Seminario di prova' );
		$subtitle = strpos( $html, 'it-card-subtitle' );
		$place    = strpos( $html, 'Altana Conference Hall' );
		$footer   = strpos( $html, '<footer class="it-card-footer">' );
		$this->assertNotFalse( $subtitle, 'The date is a subtitle in the body.' );
		$this->assertTrue( $title < $subtitle && $subtitle < $place && $place < $footer, 'Order: title, date, place, footer.' );

		$this->assertMatchesRegularExpression( '#<p class="it-card-subtitle">.*?20 ' . preg_quote( dli_get_monthname( '02' ), '#' ) . ' 2026.*?</p>#s', $html );
		$this->assertMatchesRegularExpression( '#<p class="it-card-text mb-0"[^>]*>.*?Altana Conference Hall.*?</p>#s', $html );
		$this->assertMatchesRegularExpression(
			'#<footer class="it-card-footer">\s*<div class="it-card-taxonomy">\s*<a class="it-card-category it-card-link" href="' . preg_quote( esc_url( add_query_arg( 'cat', array( (int) $term['term_id'] ), get_permalink( $events_page ) ) ), '#' ) . '">.*?Seminari.*?</a>#s',
			$html,
			'The category leads to the events page filtered by it.'
		);
		$footer_html = substr( $html, $footer );
		$this->assertStringNotContainsString( '<time', $footer_html, 'The date is not in the footer.' );
	}

	/**
	 * Without the events page the category is plain text; without a place or a category the lines are not printed.
	 */
	public function test_event_card_leaves_out_what_the_event_does_not_have() {
		$event = $this->create_content( EVENT_POST_TYPE, 'Evento essenziale', array( 'data_inizio' => '20260220' ) );
		$html  = $this->render_events_box();

		$this->assertStringContainsString( 'Evento essenziale', $html );
		$this->assertStringNotContainsString( 'it-card-text', $html, 'No place, no line of the place.' );
		$this->assertStringNotContainsString( 'it-card-category', $html, 'No category, no category.' );

		$term = wp_insert_term( 'Convegni', 'category', array( 'slug' => 'convegni-home' ) );
		wp_set_object_terms( $event, array( (int) $term['term_id'] ), 'category' );
		$html = $this->render_events_box();

		$this->assertMatchesRegularExpression( '#<span class="it-card-category">.*?Convegni.*?</span>#s', $html );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*class="it-card-category#', $html, 'Without the events page the category is not a link.' );
	}

	/**
	 * Print the box of a type with the first content of the test.
	 *
	 * @param string $type Post type.
	 * @return string
	 */
	private function render_box_of( $type ) {
		$this->set_boxes(
			array(
				1 => array(
					'type'  => $type,
					'title' => 'Box di prova',
				),
			)
		);
		return $this->render_section();
	}

	/**
	 * A news card is laid out like the one of the playground: title, then the footer with the category and the date.
	 */
	public function test_news_card_has_the_category_linked_in_the_footer_before_the_date() {
		$news = $this->create_content( NEWS_POST_TYPE, 'Notizia con categoria', array(), array( 'post_date' => '2026-01-15 10:00:00' ) );
		$term = wp_insert_term( 'Ricerca', 'category', array( 'slug' => 'ricerca-home' ) );
		wp_set_object_terms( $news, array( (int) $term['term_id'] ), 'category' );
		$news_page = $this->create_listing_page( SLUG_NOTIZIE_IT );

		$html = $this->render_box_of( NEWS_POST_TYPE );

		$this->assertStringContainsString( '<article class="it-card border-bottom">', $html );
		$this->assertMatchesRegularExpression(
			'#<footer class="it-card-footer">\s*<div class="it-card-taxonomy">\s*<a class="it-card-category it-card-link" href="' . preg_quote( esc_url( add_query_arg( 'cat', array( (int) $term['term_id'] ), get_permalink( $news_page ) ) ), '#' ) . '">.*?Ricerca.*?</a>\s*</div>\s*<time class="it-card-date"#s',
			$html,
			'The category leads to the news page filtered by it, and comes before the date.'
		);
	}

	/**
	 * Without the news page the category of a news is plain text.
	 */
	public function test_news_category_is_text_without_the_news_page() {
		$news = $this->create_content( NEWS_POST_TYPE, 'Notizia senza pagina' );
		$term = wp_insert_term( 'Ricerca', 'category', array( 'slug' => 'ricerca-home' ) );
		wp_set_object_terms( $news, array( (int) $term['term_id'] ), 'category' );

		$html = $this->render_box_of( NEWS_POST_TYPE );

		$this->assertMatchesRegularExpression( '#<span class="it-card-category">.*?Ricerca.*?</span>#s', $html );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*class="it-card-category#', $html );
	}

	/**
	 * In the box a publication has the same card as in the playground: a line under the card, authors without margin.
	 */
	public function test_publication_card_in_the_box_follows_the_playground_layout() {
		$person      = $this->create_post( PEOPLE_POST_TYPE, 'Persona del box' );
		$this->set_acf_fields(
			$person,
			array(
				'nome'    => 'Grace',
				'cognome' => 'Hopper',
			)
		);
		$this->create_content( PUBLICATION_POST_TYPE, 'Pubblicazione del box', array( 'autori' => array( $person ) ) );

		$html = $this->render_box_of( PUBLICATION_POST_TYPE );

		$this->assertStringContainsString( '<article class="it-card border-bottom">', $html );
		$this->assertStringNotContainsString( 'shadow-sm', $html, 'The box has no shadow: the list section of the home keeps its own card.' );
		$this->assertMatchesRegularExpression( '#<p class="it-card-text mb-0[^"]*"[^>]*>[^<]*Hopper#', $html );
	}
}
