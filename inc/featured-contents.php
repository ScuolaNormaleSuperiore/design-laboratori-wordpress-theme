<?php
/**
 * Data of the homepage section "Contenuti in evidenza".
 *
 * The section shows up to three boxes, configured in Configurazione → Layout HP: each box has a content type and a
 * title in Italian and in English. The layout is decided by the type (`dli_get_featured_renderer()`), not chosen by
 * the editor. The pure rules (renderer of a type, columns, reading of a saved box) are in `inc/home-sections.php`;
 * here the contents are read with WordPress and Polylang.
 *
 * Provides:
 *  - dli_get_featured_content_types()  The content types a box can show.
 *  - dli_get_featured_button_label()   The label of the button that leads to the listing of a type.
 *  - dli_get_featured_archive_url()    The address of the listing page of a type, in the current language.
 *  - dli_get_content_link()            Where the title of a content leads, and whether it leaves the site.
 *  - dli_get_featured_posts()          The promoted contents of a type for a box.
 *  - dli_get_featured_boxes()          The boxes of the section that have something to show.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_featured_content_types' ) ) {
	/**
	 * The content types that a box of "Contenuti in evidenza" can show, with their label.
	 *
	 * They are the types that have the field "Promuovi in Home": a type without it could never fill a box. News,
	 * events and publications have a layout of their own; the others have the essential one
	 * (`dli_get_featured_renderer()`).
	 *
	 * @return array<string, string> The labels of the types, by post type.
	 */
	function dli_get_featured_content_types() {
		return array(
			NEWS_POST_TYPE        => __( 'Notizie', 'design_laboratori_italia' ),
			EVENT_POST_TYPE       => __( 'Eventi', 'design_laboratori_italia' ),
			PUBLICATION_POST_TYPE => __( 'Pubblicazioni', 'design_laboratori_italia' ),
			PROGETTO_POST_TYPE    => __( 'Progetti', 'design_laboratori_italia' ),
			PATENT_POST_TYPE      => __( 'Brevetti', 'design_laboratori_italia' ),
			PEOPLE_POST_TYPE      => __( 'Persone', 'design_laboratori_italia' ),
			PLACE_POST_TYPE       => __( 'Luoghi', 'design_laboratori_italia' ),
			WP_DEFAULT_POST       => __( 'Post', 'design_laboratori_italia' ),
		);
	}
}

if ( ! function_exists( 'dli_get_featured_button_label' ) ) {
	/**
	 * The label of the button under a box, which leads to the listing page of its content type.
	 *
	 * Every type that `dli_get_featured_content_types()` offers has a label of its own; any other type has the
	 * generic one. The test `test_every_offered_type_has_its_own_button_label` fails when a type is added without it.
	 *
	 * @param string $post_type The content type of the box.
	 * @return string The label.
	 */
	function dli_get_featured_button_label( $post_type ) {
		$labels = array(
			NEWS_POST_TYPE        => __( 'Tutte le news', 'design_laboratori_italia' ),
			EVENT_POST_TYPE       => __( 'Tutti gli eventi', 'design_laboratori_italia' ),
			PUBLICATION_POST_TYPE => __( 'Tutte le pubblicazioni', 'design_laboratori_italia' ),
			PROGETTO_POST_TYPE    => __( 'Tutti i progetti', 'design_laboratori_italia' ),
			PATENT_POST_TYPE      => __( 'Tutti i brevetti', 'design_laboratori_italia' ),
			PEOPLE_POST_TYPE      => __( 'Tutte le persone', 'design_laboratori_italia' ),
			PLACE_POST_TYPE       => __( 'Tutti i luoghi', 'design_laboratori_italia' ),
			WP_DEFAULT_POST       => __( 'Tutti i post', 'design_laboratori_italia' ),
		);

		return $labels[ $post_type ] ?? __( 'Vedi tutti', 'design_laboratori_italia' );
	}
}

if ( ! function_exists( 'dli_get_featured_archive_url' ) ) {
	/**
	 * The address of the page that lists a content type, in the current language.
	 *
	 * The page is the one mapped to the type in `DLI_PAGE_PER_CT`, found by its Italian slug and then taken in the
	 * current language through its translation. Only a published page counts, and so does its translation: without
	 * it there is no address, so that no button ever leads to the current page (an empty address) or to a draft.
	 *
	 * @param string $post_type The content type.
	 * @return string The address, or an empty string.
	 */
	function dli_get_featured_archive_url( $post_type ) {
		$slug = DLI_PAGE_PER_CT[ $post_type ][ DLI_IT_SLUG ] ?? '';
		if ( '' === $slug ) {
			return '';
		}

		$pages = get_posts(
			array(
				'post_type'      => WP_DEFAULT_PAGE,
				'post_status'    => 'publish',
				'name'           => $slug,
				'posts_per_page' => 1,
			)
		);
		if ( empty( $pages ) ) {
			return '';
		}

		$page         = $pages[0];
		$translations = dli_get_post_translations( $page->ID );
		$current      = dli_current_language();
		$page_id      = empty( $translations ) ? $page->ID : (int) ( $translations[ $current ] ?? 0 );
		if ( ! $page_id || 'publish' !== get_post_status( $page_id ) ) {
			return '';
		}

		return (string) get_permalink( $page_id );
	}
}

if ( ! function_exists( 'dli_get_content_link' ) ) {
	/**
	 * Where the title of a content leads in the lists of the home, and whether it leaves the site.
	 *
	 * The editor chooses it with the field `link_dettaglio`, like for events, news and publications: the detail page
	 * (default, also for a content without the field), the external link, or the attached file. A choice without its
	 * address gives no link at all: the address is an empty string and the template prints the title as plain text.
	 * A password-protected content always leads to its detail page: the external link and the file are what the
	 * password protects.
	 *
	 * @param WP_Post|int $content The content.
	 * @return array{url: string, external: bool} The address, and whether it leaves the site (another site or a file:
	 *                                            it opens in a new window).
	 */
	function dli_get_content_link( $content ) {
		$content = get_post( $content );
		if ( ! $content instanceof WP_Post ) {
			return array(
				'url'      => '',
				'external' => false,
			);
		}

		$permalink = (string) get_permalink( $content );
		$url       = post_password_required( $content ) ? $permalink : (string) dli_manage_item_link( $content );

		return array(
			'url'      => $url,
			'external' => '' !== $url && $url !== $permalink,
		);
	}
}

if ( ! function_exists( 'dli_get_featured_posts' ) ) {
	/**
	 * The contents of a type that a box shows: the newest promoted ones, in the current language.
	 *
	 * At most `DLI_HP_FEATURED_ITEMS`, ordered by publication date, newest first. The total is not counted, as no
	 * pagination needs it. A type that a box cannot show gives nothing.
	 *
	 * @param string $post_type The content type.
	 * @return WP_Post[] The contents.
	 */
	function dli_get_featured_posts( $post_type ) {
		if ( ! isset( dli_get_featured_content_types()[ $post_type ] ) ) {
			return array();
		}

		$query = new WP_Query(
			array(
				'post_type'      => array( $post_type ),
				'post_status'    => 'publish',
				'orderby'        => 'post_date',
				'order'          => 'DESC',
				'posts_per_page' => DLI_HP_FEATURED_ITEMS,
				'no_found_rows'  => true,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Few contents are promoted.
					array(
						'key'     => 'promuovi_in_home',
						'compare' => '=',
						'value'   => 1,
					),
				),
			)
		);

		return $query->posts;
	}
}

if ( ! function_exists( 'dli_get_featured_boxes' ) ) {
	/**
	 * The boxes of the section "Contenuti in evidenza" that have something to show, ready to print.
	 *
	 * A box that is not configured, has a type that cannot be shown, or has no content is left out, so that the
	 * columns are shared only among boxes that have something in them. The contents of a type are read once, also
	 * when two boxes have the same type. An empty title is the name of the type.
	 *
	 * @return array<int, array{number: int, type: string, title: string, renderer: string, posts: WP_Post[], archive_url: string, button_label: string}> The boxes, in order.
	 */
	function dli_get_featured_boxes() {
		$is_italian = ( dli_current_language() === DLI_IT_SLUG );
		$types      = dli_get_featured_content_types();

		$posts_by_type = array();
		$boxes         = array();
		foreach ( array( 1, 2, 3 ) as $number ) {
			$box = dli_normalize_featured_box( dli_get_option( 'featured_contents_' . $number, 'homepage' ), $number, $is_italian );
			if ( null === $box || ! isset( $types[ $box['type'] ] ) ) {
				continue;
			}

			if ( ! isset( $posts_by_type[ $box['type'] ] ) ) {
				$posts_by_type[ $box['type'] ] = dli_get_featured_posts( $box['type'] );
			}
			if ( empty( $posts_by_type[ $box['type'] ] ) ) {
				continue;
			}

			$boxes[] = array(
				'number'       => $number,
				'type'         => $box['type'],
				'title'        => '' !== $box['title'] ? $box['title'] : $types[ $box['type'] ],
				'renderer'     => dli_get_featured_renderer( $box['type'] ),
				'posts'        => $posts_by_type[ $box['type'] ],
				'archive_url'  => dli_get_featured_archive_url( $box['type'] ),
				'button_label' => dli_get_featured_button_label( $box['type'] ),
			);
		}

		return $boxes;
	}
}
