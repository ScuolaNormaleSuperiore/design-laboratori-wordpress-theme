<?php
/**
 * Example pages: four main pages, one of them the section "Research Data" with subpages, in Italian and in English.
 *
 * Used by the tool of Strumenti DLI → Utilità, to try the navigation cards of the subpages, the
 * sidebar of the related pages and the homepage sections that list pages. Every page has a lorem ipsum
 * text of a different length. The pages are marked with a meta, so that the tool can remove them without
 * touching the pages written by editors.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Creates and removes the example pages.
 */
class DLI_Example_Content {

	/**
	 * Meta that marks the pages created by this class.
	 */
	const META = '_dli_example_content';

	/**
	 * Languages of the example pages, in the order they are created.
	 */
	const LANGUAGES = array( 'it', 'en' );

	/**
	 * ACF key of the field `mostra_sottopagine_a_card` of the base page.
	 */
	const CARDS_FIELD_KEY = 'field_670e717b3bedd';

	/**
	 * ACF key of the field `promuovi_in_home` of the base page.
	 */
	const PROMOTE_FIELD_KEY = 'field_670e717b3bedc';

	/**
	 * The languages of the example pages that the site has.
	 *
	 * @return string[] Slugs, a subset of `it` and `en`.
	 */
	public static function languages() {
		return array_values( array_intersect( self::LANGUAGES, (array) dli_languages_list( array( 'fields' => 'slug' ) ) ) );
	}

	/**
	 * Create the example pages in the languages asked for.
	 *
	 * Nothing is written when an address is already used by a page of another language. A page that
	 * already exists (same address and language) is left as it is, so the call can be repeated. The
	 * versions of a page in different languages are linked as translations of each other, also when the
	 * languages are created in two separate calls.
	 *
	 * @param string[] $languages Slugs of the languages to create.
	 * @param int      $author    The author of the pages.
	 * @return array{created: int, skipped: int, top: array<string, array<int, array{title: string, url: string}>>}|WP_Error Counts, and the main pages (title and address, in the order of the menu) by language.
	 */
	public static function create( array $languages, $author ) {
		$languages = array_values( array_intersect( self::LANGUAGES, $languages ) );
		if ( empty( $languages ) || array_diff( $languages, self::languages() ) ) {
			return new WP_Error( 'dli_example_language', __( 'La lingua richiesta non esiste in questo sito.', 'design_laboratori_italia' ) );
		}
		if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'pll_set_post_language' ) ) {
			return new WP_Error( 'dli_example_plugins', __( 'ACF e Polylang devono essere attivi.', 'design_laboratori_italia' ) );
		}

		$context = array(
			'author'    => (int) $author,
			'languages' => $languages,
			'lorem'     => self::lorem(),
			'counts'    => array(
				'created' => 0,
				'skipped' => 0,
			),
			'top'       => array(),
		);

		$tree = self::tree();

		// First pass, writing nothing: an address used by another language stops everything.
		foreach ( $tree as $node ) {
			$conflict = self::find_conflict( $node, array() );
			if ( is_wp_error( $conflict ) ) {
				return $conflict;
			}
		}

		foreach ( $tree as $position => $node ) {
			$result = self::create_page( $node, array(), array(), $position, true, $context );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return array(
			'created' => $context['counts']['created'],
			'skipped' => $context['counts']['skipped'],
			'top'     => $context['top'],
		);
	}

	/**
	 * Remove the pages created by this class, and only those.
	 *
	 * @return int Number of pages removed.
	 */
	public static function remove() {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- A rarely used tool.
				'lang'           => '',
				'fields'         => 'ids',
			)
		);
		foreach ( $pages as $page_id ) {
			wp_delete_post( $page_id, true );
		}
		return count( $pages );
	}

	/**
	 * The language slug of a page.
	 *
	 * @param int $page_id The page.
	 * @return string Empty when the page has no language.
	 */
	private static function language_of( $page_id ) {
		$language = array_search( $page_id, dli_get_post_translations( $page_id ), true );
		return false === $language ? '' : (string) $language;
	}

	/**
	 * Look for a page that has the address of an example page but another language.
	 *
	 * @param array<string, mixed>  $node         The page and its subpages.
	 * @param array<string, string> $parent_paths Path of the parent, by language.
	 * @return true|WP_Error
	 */
	private static function find_conflict( array $node, array $parent_paths ) {
		$paths = array();
		foreach ( self::LANGUAGES as $language ) {
			$paths[ $language ] = self::path( $node, $language, $parent_paths );
			$existing           = get_page_by_path( $paths[ $language ], OBJECT, 'page' );
			if ( $existing instanceof WP_Post && self::language_of( $existing->ID ) !== $language ) {
				return new WP_Error(
					'dli_example_conflict',
					sprintf(
						/* translators: %s: address of the page */
						__( 'L\'indirizzo «%s» è già usato da una pagina in un\'altra lingua: non è stato creato nulla. Rimuovi le pagine di esempio di un\'esecuzione precedente e riprova.', 'design_laboratori_italia' ),
						$paths[ $language ]
					)
				);
			}
		}
		foreach ( $node['children'] ?? array() as $child ) {
			$conflict = self::find_conflict( $child, $paths );
			if ( is_wp_error( $conflict ) ) {
				return $conflict;
			}
		}
		return true;
	}

	/**
	 * The address of a page in a language, without its parents: the one of its title, unless the page names another.
	 *
	 * A page whose title is the same in two languages names an address for one of them, because two pages
	 * cannot have the same address.
	 *
	 * @param array<string, mixed> $node     The page.
	 * @param string               $language Language slug.
	 * @return string
	 */
	private static function slug( array $node, $language ) {
		return sanitize_title( $node['slug'][ $language ] ?? $node[ $language ][0] );
	}

	/**
	 * The path of a page in a language.
	 *
	 * @param array<string, mixed>  $node         The page.
	 * @param string                $language     Language slug.
	 * @param array<string, string> $parent_paths Path of the parent, by language.
	 * @return string
	 */
	private static function path( array $node, $language, array $parent_paths ) {
		$prefix = empty( $parent_paths[ $language ] ) ? '' : $parent_paths[ $language ] . '/';
		return $prefix . self::slug( $node, $language );
	}

	/**
	 * Create a page in every language asked for, then its subpages.
	 *
	 * @param array<string, mixed>  $node         The page: it and en (title, summary), lorem, children.
	 * @param array<string, int>    $parent_ids   Parent page by language, empty for a main page.
	 * @param array<string, string> $parent_paths Path of the parent by language, empty for a main page.
	 * @param int                   $order        Position among the siblings.
	 * @param bool                  $is_top       Whether it is a main page, that is a page without a parent.
	 * @param array<string, mixed>  $context      author, languages, lorem, counts and top, by reference through the array copy.
	 * @return true|WP_Error
	 */
	private static function create_page( array $node, array $parent_ids, array $parent_paths, $order, $is_top, array &$context ) {
		$ids   = array();
		$paths = array();

		$content = '';
		foreach ( $node['lorem'] as $lorem_index ) {
			$content .= '<p>' . $context['lorem'][ $lorem_index ] . '</p>';
		}

		foreach ( self::LANGUAGES as $language ) {
			list( $title, $summary ) = $node[ $language ];
			$paths[ $language ]      = self::path( $node, $language, $parent_paths );
			$asked                   = in_array( $language, $context['languages'], true );

			$existing = get_page_by_path( $paths[ $language ], OBJECT, 'page' );
			if ( $existing instanceof WP_Post ) {
				$ids[ $language ] = $existing->ID;
				if ( $asked ) {
					++$context['counts']['skipped'];
					// A page that an earlier run of the tool created gets the promotion it did not have yet; nothing else of it changes.
					if ( ! empty( $node['promote'] ) && '' !== (string) get_post_meta( $existing->ID, self::META, true ) && '1' !== (string) get_post_meta( $existing->ID, 'promuovi_in_home', true ) ) {
						update_field( self::PROMOTE_FIELD_KEY, 1, $existing->ID );
					}
				}
			} elseif ( $asked ) {
				$id = wp_insert_post(
					array(
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_author'  => $context['author'],
						'post_title'   => $title,
						'post_name'    => self::slug( $node, $language ),
						'post_parent'  => $parent_ids[ $language ] ?? 0,
						'menu_order'   => $order,
						'post_excerpt' => $summary,
						'post_content' => $content,
					),
					true
				);
				if ( is_wp_error( $id ) ) {
					return $id;
				}
				dli_set_post_language( $id, $language );
				update_post_meta( $id, self::META, '1' );
				if ( ! empty( $node['cards'] ) ) {
					update_field( self::CARDS_FIELD_KEY, 1, $id );
				}
				if ( ! empty( $node['promote'] ) ) {
					update_field( self::PROMOTE_FIELD_KEY, 1, $id );
				}
				++$context['counts']['created'];
				$ids[ $language ] = $id;
			}

			if ( $is_top && $asked && isset( $ids[ $language ] ) ) {
				$context['top'][ $language ][] = array(
					'title' => $title,
					'url'   => (string) get_permalink( $ids[ $language ] ),
				);
			}
		}

		// The versions of a page are translations of each other.
		if ( count( $ids ) > 1 ) {
			$current = dli_get_post_translations( reset( $ids ) );
			$group   = array_merge( $current, $ids );
			if ( $current !== $group ) {
				dli_save_post_translations( $group );
			}
		}

		foreach ( $node['children'] ?? array() as $position => $child ) {
			$result = self::create_page( $child, $ids, $paths, $position, false, $context );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return true;
	}

	/**
	 * Lorem ipsum, in paragraphs of different length.
	 *
	 * Each page takes the paragraphs listed in `lorem` (positions in this pool), so that the pages have
	 * texts of different length and the two versions of a page have the same text.
	 *
	 * @return string[]
	 */
	private static function lorem() {
		return array(
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus tincidunt justo a placerat congue. Pellentesque mattis sollicitudin lectus, non tristique lectus convallis a. Aenean vitae libero in neque viverra pulvinar. Proin vestibulum nunc in mollis dapibus. Etiam finibus sem eget nisl ullamcorper, accumsan viverra magna eleifend. Sed bibendum elit libero, eget tincidunt neque sodales nec. Integer sagittis felis vel ipsum imperdiet ultrices. In mauris ante, tempor ornare sodales in, eleifend et magna. Sed ut tincidunt nibh. Vestibulum et mauris molestie, suscipit tortor vel, interdum odio. Cras bibendum lacus porttitor, tempus leo id, consequat dui. Aliquam sit amet leo fringilla, rutrum leo vel, accumsan leo. Sed eros lacus, pulvinar ut dapibus quis, tristique vitae erat. Suspendisse vulputate nisi ut elit ultricies cursus at nec augue. Pellentesque hendrerit, augue tempor scelerisque mattis, sapien massa bibendum mauris, vitae gravida ex diam et purus. Nullam ipsum lacus, laoreet non venenatis non, rutrum at tellus.',
			'Nunc eget elit urna. Duis luctus, ante eu ultrices sollicitudin, ex turpis maximus enim, vitae porttitor erat dolor at risus. Maecenas vel elit turpis. Aenean pretium erat ex, sit amet tempus lectus convallis sed. Aliquam quis est et dolor elementum dignissim. Maecenas nibh nisl, porta eu porta ut, suscipit vel ex. Morbi pretium sit amet ante fringilla consectetur. Duis pretium nisl a nibh ultrices, ut hendrerit sapien elementum. Morbi mauris ante, pulvinar placerat tristique vitae, pulvinar et dui. Fusce convallis, elit sed dignissim mollis, elit libero dapibus ante, a fermentum tellus justo et justo. Donec placerat convallis mattis. In mollis ac erat a sodales. Praesent vestibulum orci risus, et varius leo ornare a.',
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus tincidunt justo a placerat congue. Pellentesque mattis sollicitudin lectus, non tristique lectus convallis a. Aenean vitae libero in neque viverra pulvinar.',
			'Curabitur at justo non mauris luctus tempor. Vivamus ac ligula vel nunc consequat pharetra. Quisque non dolor id arcu feugiat interdum. Donec vitae nulla sit amet lorem aliquam posuere. Praesent eget ipsum a erat pulvinar tincidunt. Nam sed lacus at nisl dictum sollicitudin, vel porta augue ornare.',
			'Sed bibendum elit libero, eget tincidunt neque sodales nec. Integer sagittis felis vel ipsum imperdiet ultrices. In mauris ante, tempor ornare sodales in, eleifend et magna. Sed ut tincidunt nibh. Vestibulum et mauris molestie, suscipit tortor vel, interdum odio. Cras bibendum lacus porttitor, tempus leo id, consequat dui.',
			'Morbi pretium sit amet ante fringilla consectetur. Duis pretium nisl a nibh ultrices, ut hendrerit sapien elementum.',
		);
	}

	/**
	 * The tree of the pages: the main pages, that is the pages without a parent, in the order of the menu.
	 *
	 * `it` and `en` are the title and the summary of each version; the order of the entries is the order
	 * of the menu. `slug` names the address of a version, only when the title would give the same address
	 * as the other language. The page with `cards` shows its subpages as cards (field `mostra_sottopagine_a_card`).
	 * The pages with `promote` are promoted to the home (field `promuovi_in_home`): the four main pages, so that
	 * the section "Card di navigazione" has four cards, and the three subpages that have subpages of their own,
	 * so that the section "Navigazione per pagine" has four groups (the main page with subpages and these three).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function tree() {
		return array(
			array(
				'it'      => array( 'Open Access', 'Il movimento per l\'accesso libero e gratuito ai risultati della ricerca.' ),
				'en'      => array( 'Open Access', 'The movement for free and open access to research results.' ),
				'slug'    => array( 'en' => 'open-access-en' ),
				'lorem'   => array( 2, 3 ),
				'promote' => true,
			),
			self::research_data(),
			array(
				'it'      => array( 'Valutazione della ricerca', 'Come si valuta la ricerca: criteri, indicatori e buone pratiche.' ),
				'en'      => array( 'Research Assessment', 'How research is assessed: criteria, indicators and good practice.' ),
				'lorem'   => array( 4, 5 ),
				'promote' => true,
			),
			array(
				'it'      => array( 'Monitoraggio', 'Il monitoraggio delle attività e dei risultati della ricerca.' ),
				'en'      => array( 'Monitoring', 'The monitoring of the activities and of the results of the research.' ),
				'lorem'   => array( 1, 2 ),
				'promote' => true,
			),
		);
	}

	/**
	 * The main page «Research Data» with its four subpages and their own subpages.
	 *
	 * It is the only page that shows its subpages as cards, and the only one with subpages.
	 *
	 * @return array<string, mixed>
	 */
	private static function research_data() {
		return array(
			'it'       => array( 'Dati della ricerca', 'Contenuto di esempio (albero di navigazione a più livelli)' ),
			'en'       => array( 'Research Data', 'Example content (navigation tree with several levels)' ),
			'lorem'    => array( 0, 1 ),
			'promote'  => true,
			'cards'    => true,
			'children' => array(
				array(
					'it'    => array( 'Perché la gestione dei dati della ricerca è importante', 'Perché la gestione dei dati della ricerca è importante: riproducibilità, requisiti dei finanziatori, riuso e conservazione nel tempo.' ),
					'en'    => array( 'Why does Research Data Management matter?', 'Why research data management matters: reproducibility, funders requirements, reuse and preservation over time.' ),
					'lorem' => array( 2, 3 ),
				),
				array(
					'it'       => array( 'Pianificare', 'Le attività di pianificazione: capire quali sono i dati prodotti dalla ricerca e preparare un piano di gestione dei dati.' ),
					'en'       => array( 'Plan', 'Planning activities: understand which data the research produces and prepare a data management plan.' ),
					'lorem'    => array( 0 ),
					'promote'  => true,
					'children' => array(
						array(
							'it'    => array( 'Cosa sono i dati della ricerca?', '' ),
							'en'    => array( 'What are research data?', '' ),
							'lorem' => array( 4, 5 ),
						),
						array(
							'it'    => array( 'Piano di gestione dei dati', '' ),
							'en'    => array( 'Data Management plan', '' ),
							'lorem' => array( 1, 2 ),
						),
					),
				),
				array(
					'it'       => array( 'Gestire', 'La gestione dei dati durante il progetto: accesso e riuso, strutturazione e archiviazione dei dati di lavoro.' ),
					'en'       => array( 'Manage', 'Data management during the project: access and reuse, structure and storage of the working data.' ),
					'lorem'    => array( 1, 3, 5 ),
					'promote'  => true,
					'children' => array(
						array(
							'it'    => array( 'Accesso e riuso dei dati della ricerca', '' ),
							'en'    => array( 'Access and grant access to Research Data (Re-use)', '' ),
							'lorem' => array( 3 ),
						),
						array(
							'it'    => array( 'Strutturare i dati', '' ),
							'en'    => array( 'Data structuring', '' ),
							'lorem' => array( 0, 4 ),
						),
						array(
							'it'    => array( 'Archiviare i dati di lavoro', '' ),
							'en'    => array( 'Storage research data', '' ),
							'lorem' => array( 5 ),
						),
					),
				),
				array(
					'it'       => array( 'Condividere', 'Come condividere i dati al termine del progetto: archiviazione a lungo termine e pubblicazione.' ),
					'en'       => array( 'Sharing', 'How to share the data at the end of the project: long-term archiving and publication.' ),
					'lorem'    => array( 4 ),
					'promote'  => true,
					'children' => array(
						array(
							'it'    => array( 'Archiviazione a lungo termine', '' ),
							'en'    => array( 'Archiving research data', '' ),
							'lorem' => array( 2, 1 ),
						),
						array(
							'it'    => array( 'Pubblicare i dati della ricerca', '' ),
							'en'    => array( 'Publishing research data', '' ),
							'lorem' => array( 0, 1, 3 ),
						),
					),
				),
			),
		);
	}
}
