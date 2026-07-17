<?php
/**
 * Gestione dei contenuti del sito.
 *
 * @package Design_Laboratori_Italia
 */



class DLI_OG_Wrapper {
	public string $id           = '';
	public string $title        = '';
	public string $shared_title = '';
	public string $type         = '';
	public string $description  = '';
	public string $url          = '';
	public string $locale       = '';
	public string $site_title   = '';
	public string $site_tagline = '';
	public string $image        = '';
	public string $img_width    = '';
	public string $img_height   = '';
	public string $img_type     = '';
	public string $site_url     = '';
	public string $domain       = '';
}

/**
 * The Activation manager.
 */
class DLI_ContentsManager {


	public static function get_og_data() {
		global $post;
		$og_data     = new DLI_OG_Wrapper();
		$item_id     = $post && $post->ID ? $post->ID : '';
		$item_type   = $item_id && $post->post_type ? $post->post_type : '';
		$is_homepage = is_home() || is_front_page();

		if ( $item_id && in_array( $item_type, DLI_POST_TYPES_TO_TRANSLATE ) ) {
			// Get data to fill OG structure.
			$site_title   = dli_get_option_by_lang( 'nome_laboratorio' );
			$site_tagline = dli_get_option_by_lang( 'tagline_laboratorio' );
			$item_title   = $is_homepage ? $site_title : $post->post_title;
			$item_desc    = $is_homepage ? $site_tagline : dli_clean_and_truncate_text( $post->post_content, 256 );
			$item_url     = $is_homepage ? dli_homepage_url() : get_permalink( $item_id );
			$img_id       = $is_homepage ? null : get_post_thumbnail_id( $item_id );
			$img_array    = wp_get_attachment_image_src( $img_id, 'large' );
			$has_img_data = is_array( $img_array );
			$file_path    = $img_id ? get_attached_file( $img_id ) : '';
			$file_info    = $img_id ? wp_check_filetype( $file_path ) : '';
			$img_type     = $img_id ? $file_info['type'] : '';
			$item_image   = $img_id && $has_img_data && ! empty( $img_array[0] ) ? $img_array[0] : '';
			$site_url     = site_url();
			$parsed_url   = parse_url( $site_url );
			$domain       = isset( $parsed_url['host'] ) ? $parsed_url['host'] : wp_parse_url( home_url(), PHP_URL_HOST );
			$shared_title = $is_homepage ? $site_title : $site_title . ' - ' . $post->post_title;

			// Fill OG data:
			$og_data->id           = $item_id;
			$og_data->title        = $item_title;
			$og_data->type         = $item_type;
			$og_data->description  = $item_desc;
			$og_data->site_url     = $site_url;
			$og_data->url          = $item_url;
			$og_data->locale       = dli_current_language();
			$og_data->site_title   = $site_title;
			$og_data->site_tagline = $site_tagline;
			$og_data->image        = $item_image;
			$og_data->img_width    = $img_id && $has_img_data && isset( $img_array[1] ) ? (string) $img_array[1] : '0';
			$og_data->img_height   = $img_id && $has_img_data && isset( $img_array[2] ) ? (string) $img_array[2] : '0';
			$og_data->img_type     = $img_type;
			$og_data->domain       = $domain;
			$og_data->shared_title = $shared_title;
		}
		return $og_data;
	}

	public static function dli_get_all_patent_years() {
		global $wpdb;
		$results = $wpdb->get_col(
			$wpdb->prepare(
				"
			SELECT DISTINCT pm.meta_value AS anno_deposito
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE p.post_type = %s
			AND p.post_status = 'publish'
				AND pm.meta_key = %s
			AND pm.meta_value != ''
			ORDER BY pm.meta_value DESC
		",
				PATENT_POST_TYPE,
				'anno_deposito'
			)
		);
		return $results;
	}

	public static function dli_get_all_spinoff_years() {
		global $wpdb;
		$results = $wpdb->get_col(
			$wpdb->prepare(
				"
			SELECT DISTINCT pm.meta_value AS anno_costituzione
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE p.post_type = %s
			AND p.post_status = 'publish'
				AND pm.meta_key = %s
			AND pm.meta_value != ''
			ORDER BY pm.meta_value DESC
		",
				SPINOFF_POST_TYPE,
				'anno_costituzione'
			)
		);
		return $results;
	}

	public static function dli_get_all_technical_res_years() {
		global $wpdb;
		$results = $wpdb->get_col(
			$wpdb->prepare(
				"
			SELECT DISTINCT pm.meta_value AS anno_acquisizione
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE p.post_type = %s
			AND p.post_status = 'publish'
				AND pm.meta_key = %s
			AND pm.meta_value != ''
			ORDER BY pm.meta_value DESC
		",
				TECHNICAL_RESOURCE_POST_TYPE,
				'anno_acquisizione'
			)
		);
		return $results;
	}

	public static function build_content_path( $post ) {
		$steps = array(
			array(
				'label' => 'Home',
				'url'   => dli_homepage_url(),
				'class' => 'breadcrumb-item',
			),
		);
		if ( $post ) {
			switch ( $post->post_type ) {
				case 'page':
					$post_parent  = $post->post_parent;
					$post_parents = array();
					while ( $post_parent !== 0 ) {
						$post_tmp = get_post( $post_parent );
						if ( ! $post_tmp ) {
							break;
						}
						$post_parents[] = array(
							'label' => $post_tmp->post_title,
							'url'   => get_permalink( $post_tmp->ID ),
							'class' => 'breadcrumb-item',
						);
						$post_parent    = $post_tmp->post_parent;
					}

					// reverse array
					$post_parents = count( $post_parents ) > 1 ? array_reverse( $post_parents ) : $post_parents;

					foreach ( $post_parents as $parent ) {
						array_push(
							$steps,
							$parent,
						);
					}

					array_push(
						$steps,
						array(
							'label' => $post->post_title,
							'url'   => get_permalink( $post->ID ),
							'class' => 'breadcrumb-item active',
						),
					);
					break;
				case 'post':
					array_push(
						$steps,
						array(
							'label' => 'Blog',
							'url'   => get_site_url() . '/blog',
							'class' => 'breadcrumb-item active',
						),
					);
					break;
				default:
					$ct = dli_get_page_by_post_type( $post->post_type );
					if ( $ct ) {
						array_push(
							$steps,
							array(
								'label' => get_the_title( $ct->ID ),
								'url'   => get_permalink( $ct->ID ),
								'class' => 'breadcrumb-item',
							),
						);
					}
					array_push(
						$steps,
						array(
							'label' => $post->post_title,
							'url'   => get_permalink( $post->ID ),
							'class' => 'breadcrumb-item active',
						),
					);
					break;
			}
		}
		return $steps;
	}


	public static function get_patent_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => PATENT_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'orderby'        => 'title',
			'order'          => 'ASC',
			's'              => $params['search_string'],
			'meta_query'     => array(
				array(
					'key'     => 'anno_deposito',
					'value'   => $params['deposit_year'],
					'compare' => 'IN',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => THEMATIC_AREA_TAXONOMY,
					'field'    => 'term_id',
					'operator' => 'IN',
					'terms'    => $params['thematic_area'],
				),
			),
		);
		return new WP_Query( $args );
	}

	public static function get_spinoff_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => SPINOFF_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'orderby'        => 'title',
			'order'          => 'ASC',
			's'              => $params['search_string'],
		);
		// Filter by foundation year.
		if ( $params['foundation_year'] ) {
			$args['meta_query'] = array(
				array(
					'key'     => 'anno_costituzione',
					'value'   => $params['foundation_year'],
					'compare' => 'IN',
				),
			);
		}
		// Filter by business sector.
		if ( $params['business_sector'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => BUSINESS_SECTOR_TAXONOMY,
					'field'    => 'term_id',
					'operator' => 'IN',
					'terms'    => $params['business_sector'],
				),
			);
		}
		return new WP_Query( $args );
	}

	public static function get_technical_resource_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => TECHNICAL_RESOURCE_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'orderby'        => 'title',
			'order'          => 'ASC',
			's'              => $params['search_string'],
		);
		// Aggiungi la meta_query solo se 'acquisition_year' è presente e non vuoto
		if ( ( ! empty( $params['acquisition_year'] ) ) && ( $params['acquisition_year'] !== null ) ) {
			$args['meta_query'][] = array(
				'key'     => 'anno_acquisizione',
				'value'   => $params['acquisition_year'],
				'compare' => 'IN',
			);
		}
		// Aggiungi la tax_query solo se 'type_technical_resource' è presente e non vuoto
		if ( ( ! empty( $params['type_technical_resource'] ) ) && ( $params['type_technical_resource'] !== null ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => RT_TYPE_TAXONOMY,
				'field'    => 'term_id',
				'operator' => 'IN',
				'terms'    => $params['type_technical_resource'],
			);
		}
		return new WP_Query( $args );
	}

	public static function get_event_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => EVENT_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'category__in'   => $params['selected_categories'],
			'meta_key'       => 'data_inizio',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
		);
		return new WP_Query( $args );
	}

	public static function get_news_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => NEWS_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'category__in'   => $params['selected_categories'],
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		return new WP_Query( $args );
	}

	public static function get_projects_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => PROGETTO_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'meta_key'       => 'priorita',
			'orderby'        => array(
				'meta_value_num' => 'ASC',
				'title'          => 'ASC',
			),
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'data_inizio',
					'compare' => '<=',
					'value'   => $params['today'],
				),
				// array(
				// 'key'      => 'data_fine',
				// 'compare'  => '>=',
				// 'value'    => $params['today'],
				// ),
				array(
					'key'     => 'archiviato',
					'compare' => '=',
					'value'   => 0,
				),
			),
		);
		// Aggiungi la condizione per il filtro tag solo se il parametro 'tag' è presente e non vuoto.
		if ( ! empty( $params['tag_level'] ) && $params['tag_level'] !== '' ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'slug', // 'slug', 'name' o 'term_id'.
					'terms'    => $params['tag_level'],
				),
			);
		}
		return new WP_Query( $args );
	}

	public static function get_archived_projects_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => PROGETTO_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'meta_key'       => 'priorita',
			'orderby'        => array(
				'meta_value_num' => 'ASC',
				'title'          => 'ASC',
			),
			'meta_query'     => array(
				array(
					'key'     => 'archiviato',
					'value'   => true,
					'compare' => '=',
					'type'    => 'BOOLEAN',
				),
			),
		);
		return new WP_Query( $args );
	}

	public static function get_research_area_data_query( $params ) {
		$args = array(
			'post_status'    => 'publish',
			'paged'          => $params['paged'],
			'post_type'      => RESEARCH_ACTIVITY_POST_TYPE,
			'posts_per_page' => $params['per_page'],
			'meta_key'       => 'priorita',
			'orderby'        => array(
				'meta_value_num' => 'ASC',
				'title'          => 'ASC',
			),
		);
		return new WP_Query( $args );
	}

	public static function get_tags_by_post_type( $post_type, $taxonomy = WP_DEFAULT_TAGS ) {
		$tags = get_tags(
			array(
				'taxonomy'    => $taxonomy,
				'orderby'     => 'name',
				'hide_empty'  => true,
				'object_type' => array( $post_type ),
			)
		);
		return $tags ? $tags : array();
	}

	// PROGETTI
	public static function get_people_query( $params ) {
		$args = array(
			'paged'          => $params['paged'],
			'post_type'      => PEOPLE_POST_TYPE,
			'posts_per_page' => $params['per_page'],
		);
		// Aggiungi la condizione per il filtro tag solo se il parametro 'tag' è presente e non vuoto.
		if ( ! empty( $params['tag_level'] ) && $params['tag_level'] !== '' ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'slug', // 'slug', 'name' o 'term_id'.
					'terms'    => $params['tag_level'],
				),
			);
		}
		return new WP_Query( $args );
	}

	/**
	 * Restituisce tutti i dati necessari alla pagina Persone.
	 *
	 * @param array $args {
	 *     Argomenti opzionali.
	 *     @type string $selected_structure Slug struttura selezionata ('' = nessuna).
	 *     @type string $selected_level     Slug tag selezionato ('' = nessuno).
	 *     @type int    $posts_per_page     Persone per pagina (-1 = tutte).
	 *     @type int    $paged              Pagina corrente.
	 * }
	 * @return array {
	 *     @type array     $people_by_category  Persone indicizzate per ID categoria (usato dalla vista chip).
	 *     @type array     $people_rows         Lista flat di righe {person: WP_Post, category_id: int}, una per coppia (persona, tipologia) (usato dalla vista tabella).
	 *     @type WP_Post[] $categories          Categorie ordinate per priorità.
	 *     @type WP_Term[] $structures          Strutture disponibili.
	 *     @type WP_Term[] $tags                Tag disponibili.
	 *     @type string    $selected_structure  Slug struttura filtrata.
	 *     @type string    $selected_level      Slug tag filtrato.
	 *     @type int       $result_count        Numero persone uniche renderizzabili.
	 *     @type int       $current_page        Pagina corrente.
	 *     @type int       $total_pages         Numero totale di pagine.
	 * }
	 */
	public static function get_people_page_data( $args = array() ) {
		$selected_structure = isset( $args['selected_structure'] ) ? (string) $args['selected_structure'] : '';
		$selected_level     = isset( $args['selected_level'] ) ? (string) $args['selected_level'] : '';
		$posts_per_page     = isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : -1;
		$paged              = isset( $args['paged'] ) ? (int) $args['paged'] : 1;

		$categories_query = new WP_Query(
			array(
				'posts_per_page' => -1,
				'post_type'      => PEOPLE_TYPE_POST_TYPE,
				'meta_key'       => 'priorita',
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
			)
		);
		$categories       = $categories_query->posts;

		$people_args = array(
			'posts_per_page' => $posts_per_page,
			'paged'          => $paged,
			'post_type'      => PEOPLE_POST_TYPE,
			'meta_key'       => 'cognome',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
		);

		$tax_query = array();

		if ( '' !== $selected_structure ) {
			$tax_query[] = array(
				'taxonomy' => STRUCTURE_TAXONOMY,
				'field'    => 'slug',
				'terms'    => $selected_structure,
			);
		}

		if ( '' !== $selected_level ) {
			$tax_query[] = array(
				'taxonomy' => WP_DEFAULT_TAGS,
				'field'    => 'slug',
				'terms'    => $selected_level,
			);
		}

		if ( ! empty( $tax_query ) ) {
			$people_args['tax_query'] = $tax_query;
		}

		$people_query       = new WP_Query( $people_args );
		$people_by_category = array();
		$people_rows        = array();
		$unique_person_ids  = array();

		if ( $people_query->have_posts() ) {
			while ( $people_query->have_posts() ) {
				$people_query->the_post();
				$person_id = get_the_ID();

				if ( dli_get_field( 'escludi_da_elenco', $person_id ) ) {
					continue;
				}

				$person_categories = dli_get_field( 'categoria_appartenenza', $person_id );
				if ( empty( $person_categories ) ) {
					continue;
				}

				$category_ids = self::extract_category_ids( $person_categories );
				if ( empty( $category_ids ) ) {
					continue;
				}

				$person_post = get_post( $person_id );
				if ( ! ( $person_post instanceof WP_Post ) ) {
					continue;
				}

				$unique_person_ids[] = $person_id;

				foreach ( array_unique( $category_ids ) as $cat_id ) {
					if ( ! isset( $people_by_category[ $cat_id ] ) ) {
						$people_by_category[ $cat_id ] = array();
					}
					$people_by_category[ $cat_id ][] = $person_post;
					$people_rows[]                   = array(
						'person'      => $person_post,
						'category_id' => $cat_id,
					);
				}
			}
		}

		$total_pages = $posts_per_page > 0 ? (int) $people_query->max_num_pages : 1;
		wp_reset_postdata();

		$structures = get_terms(
			array(
				'taxonomy'   => STRUCTURE_TAXONOMY,
				'hide_empty' => false,
			)
		);
		$structures = ( is_wp_error( $structures ) || ! is_array( $structures ) ) ? array() : $structures;

		$tags = self::get_tags_by_post_type( PEOPLE_POST_TYPE );

		return array(
			'people_by_category' => $people_by_category,
			'people_rows'        => $people_rows,
			'categories'         => $categories,
			'structures'         => $structures,
			'tags'               => $tags,
			'selected_structure' => $selected_structure,
			'selected_level'     => $selected_level,
			'result_count'       => count( array_unique( $unique_person_ids ) ),
			'current_page'       => $paged,
			'total_pages'        => $total_pages,
		);
	}

	/**
	 * Estrae un array di ID categoria dal valore del campo ACF categoria_appartenenza.
	 *
	 * @param mixed $person_categories Valore del campo ACF (WP_Post|WP_Post[]|int|int[]).
	 * @return int[] Array di ID categoria.
	 */
	private static function extract_category_ids( $person_categories ) {
		$ids = array();
		if ( is_array( $person_categories ) ) {
			foreach ( $person_categories as $cat ) {
				if ( $cat instanceof WP_Post && ! empty( $cat->ID ) ) {
					$ids[] = (int) $cat->ID;
				} elseif ( is_array( $cat ) && ! empty( $cat['ID'] ) ) {
					$ids[] = (int) $cat['ID'];
				} elseif ( is_numeric( $cat ) ) {
					$ids[] = (int) $cat;
				}
			}
		} elseif ( $person_categories instanceof WP_Post && ! empty( $person_categories->ID ) ) {
			$ids[] = (int) $person_categories->ID;
		} elseif ( is_numeric( $person_categories ) ) {
			$ids[] = (int) $person_categories;
		}
		return $ids;
	}

	public static function get_related_items( $post, $field_name, $related_ct ) {
		$item = new WP_Query(
			array(
				'posts_per_page' => -1,
				'post_type'      => $related_ct,
				'orderby'        => 'data_inizio',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'     => $field_name,
						'compare' => 'LIKE',
						'value'   => '"' . $post->ID . '"',
					),
				),
			)
		);
		return $item->posts;
	}

	public static function get_carousel_items() {
		$items           = array();
		$results         = array();
		$mode_auto       = dli_get_option( 'home_carousel_is_selezione_automatica', 'homepage' );
		$order_raw       = dli_get_option( 'home_carousel_order', 'homepage' );
		$order_date_type = in_array( $order_raw, array( 'post_date', 'post_modified', 'event_date' ), true ) ? $order_raw : 'post_date';
		$query_orderby   = 'event_date' === $order_date_type ? 'post_date' : $order_date_type;
		if ( $mode_auto === 'true' ) {
			$query   = new WP_Query(
				array(
					'posts_per_page' => -1,
					'post_type'      => DLI_CAROUSEL_POST_TYPES,
					'orderby'        => $query_orderby,
					'order'          => 'DESC',
					'meta_query'     => array(
						array(
							'key'     => 'promuovi_in_carousel',
							'compare' => '=',
							'value'   => 1,
						),
					),
				)
			);
			$results = $query->posts;
		} else {
			$result_ids       = dli_get_option( 'articoli_presentazione', 'homepage' );
			$result_ids       = $result_ids ? $result_ids : array();
			$is_multilanguage = 'true' === dli_get_option( 'selettore_lingua_visible', 'setup' );
			$current_language = dli_current_language( 'slug' );
			foreach ( $result_ids as $id ) {
				// On multilingual sites resolve the selected post to its current-language translation.
				// If the translation does not exist, skip the item instead of showing the wrong language.
				if ( $is_multilanguage ) {
					$translations = dli_get_post_translations( $id );
					if ( ! array_key_exists( $current_language, $translations ) ) {
						continue;
					}
					$id = $translations[ $current_language ];
				}
				$post = get_post( $id );
				if ( $post && 'publish' === $post->post_status ) {
					array_push( $results, $post );
				}
			}
		}
		foreach ( $results as $result ) {
			$item = dli_get_post_wrapper( $result, 'item-carousel' );
			array_push( $items, $item );
		}
		// Apply the selected ordering consistently in both automatic and manual modes.
		self::sort_carousel_items_by_order_date_desc( $items, $order_date_type );
		return $items;
	}

	private static function sort_carousel_items_by_order_date_desc( &$items, $order_date_type ) {
		usort(
			$items,
			function ( $a, $b ) use ( $order_date_type ) {
				$a_ts = self::get_carousel_item_order_timestamp( $a, $order_date_type );
				$b_ts = self::get_carousel_item_order_timestamp( $b, $order_date_type );
				// Descending order: most recent date first.
				return $b_ts <=> $a_ts;
			}
		);
	}

	private static function get_carousel_item_order_timestamp( $item, $order_date_type ) {
		if ( 'post_modified' === $order_date_type ) {
			$post_id       = isset( $item['id'] ) ? absint( $item['id'] ) : 0;
			$modified_date = $post_id ? get_post_modified_time( 'U', false, $post_id ) : false;

			return false !== $modified_date ? (int) $modified_date : PHP_INT_MIN;
		}

		$date = trim( (string) ( $item['order_date'] ?? '' ) );
		$dt   = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $date );

		return $dt ? (int) $dt->format( 'U' ) : PHP_INT_MIN;
	}

	public static function get_projects_by_event_id( $event_id ) {
		$query = new WP_Query(
			array(
				'posts_per_page' => -1,
				'post_type'      => 'progetto',
				'orderby'        => 'post_date',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'     => 'elenco_indirizzi_di_ricerca_correlati',
						'compare' => 'LIKE',
						'value'   => '"' . $event_id . '"',
					),
				),
			)
		);
		return $query->posts;
	}

	public static function get_post_types_with_results( $post_types, $post_status = 'publish' ) {
		global $wpdb;

		if ( ! is_array( $post_types ) || empty( $post_types ) ) {
			return array();
		}

		$post_types = array_values(
			array_unique(
				array_map( 'sanitize_key', $post_types )
			)
		);

		if ( empty( $post_types ) ) {
			return array();
		}

		$post_types_placeholders = implode( ', ', array_fill( 0, count( $post_types ), '%s' ) );
		$prepare_args            = array_merge(
			array(
				sanitize_key( $post_status ),
			),
			$post_types
		);
		$sql                     = "SELECT post_type
			FROM {$wpdb->posts}
			WHERE post_status = %s
				AND post_type IN (" . $post_types_placeholders . ')
			GROUP BY post_type';

		$query = $wpdb->prepare( $sql, ...$prepare_args );
		$rows  = $wpdb->get_col( $query );
		if ( ! is_array( $rows ) || empty( $rows ) ) {
			return array();
		}

		return array_values(
			array_filter(
				$post_types,
				function ( $post_type ) use ( $rows ) {
					return in_array( $post_type, $rows, true );
				}
			)
		);
	}

	public static function get_all_contenttypes_with_results() {
		$content_types = array_values(
			array_filter(
				DLI_POST_TYPES_TO_SEARCH,
				function ( $content_type ) {
					return PEOPLE_TYPE_POST_TYPE !== $content_type;
				}
			)
		);

		if ( empty( $content_types ) ) {
			return array();
		}

		return self::get_post_types_with_results( $content_types, 'publish' );
	}

	// SITE SEARCH
	public static function main_search_query( $selected_contents, $search_string, $page_size, $paged = null ) {
		$has_search_string = '' !== trim( $search_string );
		if ( null === $paged ) {
			// On a Page template the paginated segment is exposed as `page`, not `paged`.
			$paged = absint( get_query_var( 'paged' ) );
			if ( 0 === $paged ) {
				$paged = absint( get_query_var( 'page' ) );
			}
			if ( 0 === $paged ) {
				$paged = 1;
			}
		}
		$params = array(
			'paged'          => $paged,
			'post_status'    => 'publish',
			'posts_per_page' => $page_size,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		if ( $has_search_string ) {
			$params['s'] = $search_string;
		}

		if ( count( $selected_contents ) > 0 ) {
			$params['post_type'] = $selected_contents;
		} else {
			$params['post_type'] = self::get_all_contenttypes_with_results();
		}

		$the_query = new WP_Query( $params );
		return $the_query;
	}

	public static function get_hp_sections() {
		return DLI_HP_SECTIONS;
	}

	public static function get_hp_section_list() {
		$result = array();
		foreach ( DLI_HP_SECTIONS as $key => $item ) {
				$result[ $key ] = $item['name'];
		}
		return $result;
	}

	public static function get_hp_section_options( $only_active = false ) {
		$sections = dli_get_option( 'site_sections', 'homepage_sections' );
		$results  = array();
		if ( $sections ) {
			foreach ( $sections as $section ) {
				if ( ( $only_active === 'false' ) || $section['enabled'] === 'true' ) {
					array_push( $results, $section );
				}
			}
		}
		return $results;
	}
}
