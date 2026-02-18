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
class DLI_ContentsManager
{

	public static function get_og_data() {
		global $post;
		$og_data    = new DLI_OG_Wrapper();
		$item_id   = $post && $post->ID ? $post->ID : '';
		$item_type = $item_id && $post->post_type ? $post->post_type : '';
		$is_homepage = is_home() || is_front_page();

		if ( $item_id && in_array( $item_type, DLI_POST_TYPES_TO_TRANSLATE ) ) {
			// Get data to fill OG structure.
			$site_title   = dli_get_option_by_lang( 'nome_laboratorio' );
			$site_tagline = dli_get_option_by_lang( 'tagline_laboratorio' );
			$item_title   = $is_homepage ? $site_title : $post->post_title;
			$item_desc    = $is_homepage ? $site_tagline : clean_and_truncate_text( $post->post_content, 256 );
			$item_url     = $is_homepage ? dli_homepage_url() : get_permalink( $item_id );
			$img_id       = $is_homepage ? null : get_post_thumbnail_id( $item_id );
			$img_array    = wp_get_attachment_image_src( $img_id, 'large' );
			$file_path    = $img_id ? get_attached_file( $img_id ) : '';
			$file_info    = $img_id ? wp_check_filetype( $file_path ) : '';
			$img_type     = $img_id ? $file_info['type'] : '';
			$item_image   = $img_id && count( $img_array ) ? $img_array[0] : '';
			$site_url     = site_url();
			$parsed_url   = parse_url( $site_url );
			$domain       = $parsed_url['host'];
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
			$og_data->img_width    = $img_id && count( $img_array ) > 1 ? $img_array[1] : '0';
			$og_data->img_height   = $img_id && count( $img_array ) > 2 ? $img_array[2] : '0';
			$og_data->img_type     = $img_type;
			$og_data->domain       = $domain;
			$og_data->shared_title = $shared_title;
		}
		return $og_data;
	}

	public static function dli_get_all_patent_years() {
		global $wpdb;
		$results = $wpdb->get_col(
			$wpdb->prepare( "
			SELECT DISTINCT pm.meta_value AS anno_deposito
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE p.post_type = '%s'
			AND p.post_status = 'publish'
			AND pm.meta_key = '%s'
			AND pm.meta_value != ''
			ORDER BY pm.meta_value DESC
		", PATENT_POST_TYPE, 'anno_deposito' ) );
		return $results;
	}

	public static function dli_get_all_spinoff_years() {
		global $wpdb;
		$results = $wpdb->get_col(
			$wpdb->prepare( "
			SELECT DISTINCT pm.meta_value AS anno_costituzione
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE p.post_type = '%s'
			AND p.post_status = 'publish'
			AND pm.meta_key = '%s'
			AND pm.meta_value != ''
			ORDER BY pm.meta_value DESC
		", SPINOFF_POST_TYPE, 'anno_costituzione' ) );
		return $results;
	}

	public static function dli_get_all_technical_res_years() {
		global $wpdb;
		$results = $wpdb->get_col(
			$wpdb->prepare( "
			SELECT DISTINCT pm.meta_value AS anno_acquisizione
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE p.post_type = '%s'
			AND p.post_status = 'publish'
			AND pm.meta_key = '%s'
			AND pm.meta_value != ''
			ORDER BY pm.meta_value DESC
		", TECHNICAL_RESOURCE_POST_TYPE, 'anno_acquisizione' ) );
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
		if ( $post ){
			switch ( $post->post_type ) {
				case 'page':
					$post_parent = $post->post_parent;
					$post_parents = array();
					while ( $post_parent !== 0 ) {
						$post_tmp       = get_post( $post_parent );
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

					//reverse array
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
			'meta_query'     =>  array(
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
				)
			)
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
				)
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
		if ( ( ! empty( $params['type_technical_resource']) ) && ( $params['type_technical_resource'] !== null ) ) {
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

	public static function get_news_data_query( $params){
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

	public static function get_projects_data_query( $params ){
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
			'meta_query' => array(
				'relation'     => 'AND',
				array(
					'key'      => 'data_inizio',
					'compare'  => '<=',
					'value'    => $params['today'],
				),
				// array(
				// 	'key'      => 'data_fine',
				// 	'compare'  => '>=',
				// 	'value'    => $params['today'],
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

	public static function get_archived_projects_data_query( $params ){
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
						'key' => 'archiviato',
						'value' => true,
						'compare' => '=',
						'type' => 'BOOLEAN',
				),
			),
		);
		return new WP_Query( $args );
	}

	public static function get_research_area_data_query( $params ){
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

	public static function get_tags_by_post_type( $post_type, $taxonomy=WP_DEFAULT_TAGS ){
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
	public static function get_people_query( $params ){
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
		$order_date_type = dli_get_option( 'home_carousel_order', 'homepage' ) === 'post_modified' ? 'post_modified' : 'post_date';
		if ( $mode_auto === 'true' ) {
			$query = new WP_Query(
				array(
					'posts_per_page' => -1,
					'post_type'      => DLI_CAROUSEL_POST_TYPES,
					'orderby'        => $order_date_type,
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
			$result_ids = dli_get_option( 'articoli_presentazione', 'homepage');
			$result_ids = $result_ids ? $result_ids : array();
			foreach ( $result_ids As $id) {
				array_push( $results, get_post( $id  ) );
			}
		}
		foreach ( $results as $result ) {
			$item = dli_get_post_wrapper( $result, 'item-carousel' );
			array_push( $items, $item );
		}
		// Change results order if needed.
		if ( $order_date_type === 'event_date' ) {
			self::sort_carousel_items_by_order_date_desc( $items );
		}
		return $items;
	}

	private static function sort_carousel_items_by_order_date_desc( &$items ) {
		usort(
			$items,
			function ( $a, $b ) {
				$a_ts = self::get_carousel_item_order_timestamp( $a );
				$b_ts = self::get_carousel_item_order_timestamp( $b );
				// Descending order: most recent date first.
				return $b_ts <=> $a_ts;
			}
		);
	}

	private static function get_carousel_item_order_timestamp( $item ) {
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

	// SITE SEARCH
	public static function main_search_query( $selected_contents, $search_string, $page_size ) {
		$params = array(
			'paged'          => get_query_var( 'paged', 1 ),
			'post_status'    => 'publish',
			'posts_per_page' => $page_size,
			's'              => $search_string,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);
		if( count( $selected_contents ) > 0 ) {
			$params['post_type'] = $selected_contents;
		}
		$the_query = new WP_Query( $params );
		return $the_query;
	}

	public static function get_hp_sections() {
		return DLI_HP_SECTIONS;
	}

	public static function get_hp_section_list() {
		$result = [];
		foreach ( DLI_HP_SECTIONS as $key => $item ) {
				$result[$key] = $item['name'];
		}
		return $result;
	}

	public static function get_hp_section_options( $only_active= false ) {
		$sections = dli_get_option( 'site_sections', 'homepage_sections' );
		$results  = array();
		if ( $sections )  {
			foreach ( $sections as $section ) {
				if ( ( $only_active === 'false' ) || $section['enabled']==='true' ) {
					array_push( $results, $section );
				}
			}
		}
		return $results;
	}

}
