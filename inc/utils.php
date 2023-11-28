<?php
/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
if(!function_exists("dli_get_option")) {
	function dli_get_option( $key = '', $type = "dli_options", $default = false ) {
		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option( $type, $key, $default );
		}

		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option( $type, $default );

		$val = $default;

		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}

		return $val;
	}
}

/**
 * Wrapper function for persona avatar
 * @param object $foto
 * @param object $foto
 * @return string url
 */
if( ! function_exists( 'dli_get_persona_avatar' ) ){
	function dli_get_persona_avatar( $persona, $ID, $size=250 ) {
		$thumbnail = get_the_post_thumbnail_url($persona, "item-thumb");
		if( ! $thumbnail ) {
			$thumbnail = get_avatar_url( $ID, array( "size" => $size ) );
		}
		return $thumbnail;
	}
}


/**
 * @param $post_type
 *
 * ritorna il gruppo di appartenenza del post type
 * @return string
 *
 */
function dli_get_post_types_group( $post_type ){
	$group = 'news';
	if( in_array( $post_type, array( 'documento', 'luogo', 'struttura', 'page' ) ) )
		$group = 'laboratory';
	else if( in_array( $post_type, array( 'programma', 'scheda_didattica', 'scheda_progetto' ) ) )
		$group = 'education';
	else if( in_array( $post_type, array( 'indirizzo' ) ) )
		$group = 'service';
	return $group;
}

/**
 * recupera la url del template in base al nome
 * @param $TEMPLATE_NAME
 *
 * @return string|null
 */
function dli_get_template_page_url($TEMPLATE_NAME){
	$pages = get_pages(array(
		'meta_key' => '_wp_page_template',
		'meta_value' => $TEMPLATE_NAME,
				'hierarchical' => 0
	));
		if($pages){
			foreach ($pages as $page){
				if($page->ID)
						return get_page_link($page->ID);
			}
		}
	return null;
}


/**
 * funzione per la gestione del nome persona
 */
if( ! function_exists( 'dli_get_persona_display_name' ) ) {
	function dli_get_persona_display_name( $nome, $cognome, $title ){
		if( ( $nome != '' ) && ( $cognome != '' ) )
			return $nome .' ' . $cognome;
		else
			return $title;
	}
}

/**
 *  Funzione per la ricerca di un valore in un array multiplo
 *  * @since  0.1.0
 * @param  string $search_for  Value to search
 * @param  array  $search_in Array where to search
 * @param  mixed  $okey Previous value
 * @return mixed
 */
if(!function_exists("dli_multi_array_search")) {
		function dli_multi_array_search($search_for, $search_in, $okey = false) {
				foreach ($search_in as $key => $element) {
						$key = $okey ? $okey : $key;
						if (($element === $search_for) || (is_array($element) && $key = dli_multi_array_search($search_for, $element, $key))) {
								return $key;
						}
				}
				return false;
		}
}

/**
 * get group related to current page
 */
if(!function_exists("dli_get_current_group")) {
		function dli_get_current_group() {
				if (is_front_page()) {
						return null;
				}
				if (is_tax()) {
						$taxonomy = get_queried_object() -> taxonomy;
						$term = get_queried_object() -> slug;

						if ($taxonomy == 'tipologia-documento'){
								$tipo_post ='documento';
						}
						if ($taxonomy == 'tipologia-articolo'){
								$tipo_post = 'post';
						}
						if ($tipo_post == 'documento' && $term == 'albo-online') {
								return 'news';
						}
						return  dli_get_post_types_group($tipo_post);
				}
				if (is_author()) {
						return 'school';
				}
				if ( is_archive()  ) {
						$tipo_post = get_queried_object() -> name;
						return  dli_get_post_types_group($tipo_post);
				}
				if (is_page()) {
						return get_the_title();
				}
				$current_post_type = get_post_type();
				if ($current_post_type == 'documento') {
						$term = wp_get_post_terms(get_the_ID(),'tipologia-documento');
						if ($term[0]->slug == 'albo-online'){
								return 'news';
						}
				}
				if ( ($current_post_type != false)) {
						return dli_get_post_types_group(get_post_type());
				}
				return null;
		}
}

if( ! function_exists( 'dli_get_projects_by_event_id' ) ) {
	function dli_get_projects_by_event_id( $event_id ) {
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
}

if( ! function_exists( 'dli_get_carousel_items' ) ) {
	function dli_get_carousel_items( ) {
		$items   = array();
		$results = array();
		$mode_auto = dli_get_option( 'home_carousel_is_selezione_automatica', 'homepage');
		if ( $mode_auto === 'true' ) {
			$query = new WP_Query(
				array(
					'posts_per_page' => -1,
					'post_type'      => array( EVENT_POST_TYPE, NEWS_POST_TYPE, PUBLICATION_POST_TYPE, PROGETTO_POST_TYPE, WP_DEFAULT_POST ),
					'orderby'        => 'post_date',
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
			$item = dli_get_post_wrapper( $result );
			array_push( $items, $item );
		}
		return $items;
	}
}

if( ! function_exists( 'dli_get_post_wrapper' ) ) {
	function dli_get_post_wrapper( $result ) {
		$item = array();
		if ( $result ){
			switch ( $result->post_type) {
				case EVENT_POST_TYPE:
					$item = dli_from_event_to_carousel_item ( $result );
					break;
				case NEWS_POST_TYPE:
					$item = dli_from_news_to_carousel_item ( $result );
					break;
				case PROGETTO_POST_TYPE:
						$item = dli_from_progetto_to_carousel_item ( $result );
						break;
				case PUBLICATION_POST_TYPE:
						$item = dli_from_publication_to_carousel_item ( $result );
						break;
				case WP_DEFAULT_PAGE:
					$item = dli_from_page_to_carousel_item ( $result );
					break;
				default:
					// Standard post or article.
					$item = dli_from_post_to_carousel_item ( $result );
					break;
			}
		}
		return $item;
	}
}

if ( ! function_exists( 'dli_get_boxes_post_types' ) ) {
	function dli_get_boxes_post_types( $item ) {
		return array(
			RESEARCHACTIVITY_POST_TYPE => __( 'Attività di ricerca', 'design_laboratori_italia' ),
			EVENT_POST_TYPE            => __( 'Eventi', 'design_laboratori_italia' ),
			PLACE_POST_TYPE            => __( 'Luoghi', 'design_laboratori_italia' ),
			NEWS_POST_TYPE             => __( 'Notizie', 'design_laboratori_italia' ),
			PEOPLE_POST_TYPE           => __( 'Persone', 'design_laboratori_italia' ),
			WP_DEFAULT_POST            => __( 'Post', 'design_laboratori_italia' ),
			PROGETTO_POST_TYPE         => __( 'Progetti', 'design_laboratori_italia' ),
			PUBLICATION_POST_TYPE      => __( 'Pubblicazioni', 'design_laboratori_italia' ),
		);
	}
}

if( ! function_exists( 'dli_from_event_to_carousel_item' ) ) {
	function dli_from_event_to_carousel_item( $item ) {
		$result    =  DLI_POST_WRAPPER;
		$post_type = get_post_type( $item );
		$image_url = get_the_post_thumbnail_url( $item, 'item-carousel' );
		if ( ! $image_url ){
			$image_url = get_template_directory_uri() . '/assets/img/yourimage.png';
		}
		$page        = dli_get_page_by_post_type( $post_type );
		$post_title  = get_the_title( $item );
		$image_id    = attachment_url_to_postid( $image_url );
		$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
		
		$image_alt   = $image_alt ? $image_alt : $post_title;
		$image_title = get_the_title( $image_id );
		$image_title = $image_title ? $image_title : $post_title;
		// @TODO: Popolare $result e non ridefinirlo.
		$result = array(
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => get_field( 'data_inizio', $item ),
			'orario_inizio' => get_field( 'orario_inizio', $item ),
			'title'         => $post_title,
			'description'   => wp_trim_words( get_field('descrizione_breve', $item), DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => get_the_permalink( $item ),
			'image_url'     => $image_url,
			'image_alt'     => $image_alt,
			'image_title'   => $image_title,
		);
		return $result;
	}
}

	if( ! function_exists( 'dli_from_news_to_carousel_item' ) ) {
		function dli_from_news_to_carousel_item( $item ) {
			$result    =  DLI_POST_WRAPPER;
			$post_type = get_post_type( $item );
			$image_url = get_the_post_thumbnail_url( $item, 'item-carousel' );
			if ( ! $image_url ){
				$image_url = get_template_directory_uri() . '/assets/img/yourimage.png';
			}
			$page        = dli_get_page_by_post_type( $post_type );
			$post_title  = get_the_title( $item );
			$image_id    = attachment_url_to_postid( $image_url );
			$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
			$image_alt   = $image_alt ? $image_alt : $post_title;
			$image_title = get_the_title( $image_id );
			$image_title = $image_title ? $image_title : $post_title;
			// @TODO: Popolare $result e non ridefinirlo.
			$result      = array(
				'type'          => $post_type,
				'category'      => $page->post_title,
				'category_link' => get_permalink( $page->ID ),
				'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
				'title'         => $post_title,
				'description'   => wp_trim_words( get_field('descrizione_breve', $item), DLI_ACF_SHORT_DESC_LENGTH ),
				'full_content'  => get_the_content( $item ),
				'link'          => get_the_permalink( $item ),
				'image_url'     => $image_url,
				'image_alt'     => $image_alt,
				'image_title'   => $image_title,
			);
			return $result;
		}
	}

if( ! function_exists( 'dli_from_publication_to_carousel_item' ) ) {
	function dli_from_publication_to_carousel_item( $item ) {
		$result    =  DLI_POST_WRAPPER;
		$post_type = get_post_type( $item );
		$image_url = get_the_post_thumbnail_url( $item, 'item-carousel' );
		if ( ! $image_url ){
			$image_url = get_template_directory_uri() . '/assets/img/yourimage.png';
		}
		$page        = dli_get_page_by_post_type( $post_type );
		$post_title  = get_the_title( $item );
		$image_id    = attachment_url_to_postid( $image_url );
		$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
		$image_alt   = $image_alt ? $image_alt : $post_title;
		$image_title = get_the_title( $image_id );
		$image_title = $image_title ? $image_title : $post_title;
		$link_pubbl  = get_field('url', $item);
		$link_pubbl  = $link_pubbl ? $link_pubbl : '';
		// @TODO: Popolare $result e non ridefinirlo.
		$result      = array(
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => get_field('anno', $item),
			'title'         => $post_title,
			'description'   => wp_trim_words( $item->post_content, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => $link_pubbl,
			'image_url'     => $image_url,
			'image_alt'     => $image_alt,
			'image_title'   => $image_title,
		);
		return $result;
	}
}

if( ! function_exists( 'dli_from_progetto_to_carousel_item' ) ) {
	function dli_from_progetto_to_carousel_item( $item ) {
		$result    =  DLI_POST_WRAPPER;
		$post_type = get_post_type( $item );
		$image_url = get_the_post_thumbnail_url( $item, 'item-carousel' );
		if ( ! $image_url ){
			$image_url = get_template_directory_uri() . '/assets/img/yourimage.png';
		}
		$page = dli_get_page_by_post_type( $post_type );
		$post_title  = get_the_title( $item );
		$image_id    = attachment_url_to_postid( $image_url );
		$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
		$image_alt   = $image_alt ? $image_alt : $post_title;
		$image_title = get_the_title( $image_id );
		$image_title = $image_title ? $image_title : $post_title;
		// @TODO: Popolare $result e non ridefinirlo.
		$result      = array(
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
			'title'         => $post_title,
			'description'   => wp_trim_words( $item->post_content, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => get_the_permalink( $item ),
			'image_url'     => $image_url,
			'image_alt'     => $image_alt,
			'image_title'   => $image_title,
		);
		return $result;
	}
}

if( ! function_exists( 'dli_from_post_to_carousel_item' ) ) {
	function dli_from_post_to_carousel_item( $item ) {
		$post_type = get_post_type( $item );
		$image_url = get_the_post_thumbnail_url( $item, 'item-carousel' );
		if ( ! $image_url ){
			$image_url = get_template_directory_uri() . '/assets/img/yourimage.png';
		}
		$page   = dli_get_page_by_post_type( $post_type );
		$result = array();
		if ( $page ){
			$post_title  = get_the_title( $item );
			$image_id    = attachment_url_to_postid( $image_url );
			$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
			$image_alt   = $image_alt ? $image_alt : $post_title;
			$image_title = get_the_title( $image_id );
			$image_title = $image_title ? $image_title : $post_title;
			// @TODO: Popolare $result e non ridefinirlo.
			$result      = array(
				'type'          => $post_type,
				'category'      => $page->post_title,
				'category_link' => get_permalink( $page->ID ),
				'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
				'title'         => $post_title,
				'description'   => wp_trim_words( $item->post_content, DLI_ACF_SHORT_DESC_LENGTH ),
				'full_content'  => get_the_content( $item ),
				'link'          => get_the_permalink( $item ),
				'image_url'     => $image_url,
				'image_alt'     => $image_alt,
				'image_title'   => $image_title,
			);
		}
		return $result;
	}
}

if( ! function_exists( 'dli_from_page_to_carousel_item' ) ) {
	function dli_from_page_to_carousel_item( $item ) {
		$post_type = get_post_type( $item );
		$image_url = get_the_post_thumbnail_url( $item, 'item-carousel' );
		if ( ! $image_url ){
			$image_url = get_template_directory_uri() . '/assets/img/yourimage.png';
		}
		$page = dli_get_page_by_post_type( $post_type );
		$post_title  = get_the_title( $item );
		$image_id    = attachment_url_to_postid( $image_url );
		$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
		$image_alt   = $image_alt ? $image_alt : $post_title;
		$image_title = get_the_title( $image_id );
		$image_title = $image_title ? $image_title : $post_title;
		$categories  = array( DLI_CUSTOM_PAGE_CAT, DLI_ARCHIVE_PAGE_CAT );
		$pt_slugs    = dli_get_sluglist_by_category( $categories );

		if ( in_array( $item->post_name, $pt_slugs ) ) {
			// PAGINA ELENCO POST TYPE (archivio) in DLI_PAGE_PER_CT.
			$description = '';
			$fullcontent = '';
		} else {
			// PAGINA STATICA.
			$description = $item->post_content;
			$fullcontent = get_the_content( $item );
		}
		// @TODO: Popolare $result e non ridefinirlo.
		$result      = array(
			'type'          => $post_type,
			'category'      => 'Home',
			'category_link' => get_site_url(),
			'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
			'title'         => $post_title,
			'description'   => wp_trim_words( $description, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => $fullcontent,
			'link'          => get_the_permalink( $item ),
			'image_url'     => $image_url,
			'image_alt'     => $image_alt,
			'image_title'   => $image_title,
		);
		return $result;
	}
}

if( ! function_exists( 'dli_get_image_metadata' ) ) {
	function dli_get_image_metadata( $item ) {
		$result    =  DLI_POST_WRAPPER;
		$image_url = get_the_post_thumbnail_url( $item, 'item-carousel' );
		if ( ! $image_url ){
			$image_url = get_template_directory_uri() . '/assets/img/yourimage.png';
		}
		$post_title  = get_the_title( $item );
		$image_id    = attachment_url_to_postid( $image_url );

		$image_title   = get_the_title( $image_id );
		$image_title   = $image_title ? $image_title : $post_title;
		$image_alt     = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
		$image_alt     = $image_alt ? $image_alt : $image_title;
		$image_caption = wp_get_attachment_caption( $image_id );

		$result = array(
			'title'         => $post_title,
			'image_url'     => $image_url,
			'image_alt'     => $image_alt,
			'image_title'   => $image_title,
			'image_caption' => $image_caption,
		);
		return $result;
	}
}

if( ! function_exists( 'dli_get_post_main_category' ) ) {
	function dli_get_post_main_category( $post, $taxonomy ) {
		$terms = get_the_terms( $post, $taxonomy );
		if ( ! is_array( $terms ) || count($terms) ==0 ) {
			$pg        = dli_get_page_by_post_type( $post->post_type );
			$pg_link   = get_permalink( $pg->ID );
			return array(
				'title' => $pg->post_title,
				'url'   => $pg_link,
			);
		} else {
			return array(
				'title' => $terms[0]->name,
				'url'   => get_term_link( $terms[0] ),
			);
		}
	}
}

if( ! function_exists( 'dli_get_all_categories' ) ) {
	function dli_get_all_categories( $taxonomy, $exclude_uncategorized=true ) {
		$terms      = get_terms( $taxonomy );
		$categories = array();
		if ( $terms ) {
			foreach ( $terms as $term ) {
				if ( ! $exclude_uncategorized || $term->name != 'Uncategorized' ) {
					array_push (
						$categories,
						array(
							'id'          => $term->term_id,
							'slug'        => $term->slug,
							'name'        => $term->name,
							'description' => $term->description,
							'url'         => get_term_link( $term ),
						)
					);
				}
			}
		}
		return $categories;
	}
}

if( ! function_exists( 'dli_get_monthname' ) ) {
	function dli_get_monthname( $month ) {
		$index = intval( $month ) - 1;
		$months = array(
			'gennaio',
			'febbraio',
			'marzo',
			'aprile',
			'maggio',
			'giugno',
			'luglio',
			'agosto',
			'settembre',
			'ottobre',
			'novembre',
			'dicembre'
		);
		return $months[ $index ];
	}
}

if( ! function_exists( 'dli_get_content' ) ) {
	function dli_get_content( $slug, $content_type='post' ) {
		$args = array(
			'name'        => $slug,
			'post_type'   => $content_type,
			'numberposts' => 1,
		);
		$posts = get_posts( $args );
		return $posts ? $posts[0] : null;
	}
}

if( ! function_exists( 'dli_get_search_link' ) ) {
	function dli_get_search_link( $current_language ) {
		$search_page = ( 'it' === $current_language ) ? SLUG_RICERCA_SITO_IT : SLUG_RICERCA_SITO_EN;
		return dli_homepage_url() . '/' . $search_page;
	}
}

if( ! function_exists( 'dli_build_content_path' ) ) {
	function dli_build_content_path( $post ) {
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
							'url'   => $post->post_url,
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
					$ct   = dli_get_page_by_post_type( $post->post_type );
					array_push( 
						$steps, 
						array(
							'label' => get_the_title( $ct->ID ),
							'url'   => get_permalink( $ct->ID ),
							'class' => 'breadcrumb-item',
						),
						array(
							'label' => $post->post_title,
							'url'   => $post->post_url,
							'class' => 'breadcrumb-item active',
						),
					);
					break;
			}
		}
		return $steps;
	}
}


if( ! function_exists( 'dli_get_page_slug_by_post_type' ) ) {
	function dli_get_page_slug_by_post_type( $post_type ) {
		$lang = dli_current_language();
		return isset( DLI_PAGE_PER_CT[$post_type] ) ? DLI_PAGE_PER_CT[$post_type][$lang] : '';
	}
}

if( ! function_exists( 'dli_get_page_by_post_type' ) ) {
	function dli_get_page_by_post_type( $post_type ) {
		$page = dli_get_page_slug_by_post_type( $post_type );
		return dli_get_content( $page, 'page' );
	}
}

if( ! function_exists( 'dli_get_all_contenttypes' ) ) {
	function dli_get_all_contenttypes( ) {
		$arr = DLI_POST_TYPES_TO_TRANSLATE;
		if ( ( $key = array_search( PEOPLE_TYPE_POST_TYPE, $arr ) ) !== false) {
			unset( $arr[$key] );
		}
		return $arr;
	}
}

if( ! function_exists( 'dli_get_default_logo' ) ) {
	function dli_get_default_logo( ) {
		$img_link = get_template_directory_uri() . '/assets/img/logo-default.png';
		return $img_link;
	}
}

if( ! function_exists( 'dli_menu_tree_by_items' ) ) {
	function dli_menu_tree_by_items( $menuitems ) {
		$menu_tree = array();
		foreach ( $menuitems As $item ) {
			if ( $item->menu_item_parent === '0' ) {
				$menu_tree[$item->ID] = array(
					'element'  => $item,
					'children' => array(),
				);
			} else {
				if( array_key_exists( $item->menu_item_parent, $menu_tree ) && $menu_tree[$item->menu_item_parent] !== null ) {
					array_push( $menu_tree[$item->menu_item_parent]['children'], $item );
				}
			}
		}
		return $menu_tree;
	}
}

if( ! function_exists( 'dli_get_site_tree' ) ) {
	function dli_get_site_tree( ) {
		$pt              = array(); // Page Tree.
		$lng_slug        = dli_current_language( 'slug' );
		$lng             = ( 'it' === $lng_slug ) ? '' : $lng_slug;
		$site_url        = get_site_url() . $lng;
		$hp              = dli_get_tree_item();
		$hp['name']      = DLI_HOMEPAGE_NAME;
		$hp['slug']      = DLI_HOMEPAGE_SLUG;
		$hp['link']      = $site_url;
		$pt[$hp['slug']] = $hp;

		// Aggiungi link alla pagina del sito padre.
		$ente             = dli_get_tree_item();
		$ente['name']     = dli_get_option( 'nome_ente_appartenza' );
		$ente['slug']     = DLI_ENTE_SLUG;
		$ente['external'] = 'true';
		$ente['link']     = dli_get_option( 'url_ente_appartenenza' );
		$pt[$hp['slug']]['children'][$ente['slug']] = $ente;

		// Recupera elenco dei menu per lingua.
		$menu_items = dli_get_all_menus_by_lang( $lng_slug );
		$slugs      = dli_get_pt_archive_slugs();

		// Aggiungi all'albero delle pagine le voci di menu.
		foreach ( $menu_items as $item ) {
			$mname = key( $item );
			if ( $mname ) {
				$mid     = $item[ $mname ];
				$menu    = wp_get_nav_menu_object( $mid );
				if ( $menu ) {
					$element = dli_get_tree_item();
					$element['name'] = $menu->name;
					$element['slug'] = $menu->slug;
					$element['link'] = '';
					$pt[$hp['slug']]['children'][$menu->slug] = $element;
					$menu_items = wp_get_nav_menu_items( $mid, array( 'order' => 'DESC' ) );
					// Aggiungi all'albero il contenuto di ogni voce di menu.
					foreach( $menu_items as $child ) {
						$object_id        = $child->object_id;
						$object           = get_post( $object_id );
						$child_el         = dli_get_tree_item();
						$child_el['name'] = $object->post_title;
						$child_el['slug'] = $object->post_name;
						$child_el['link'] = get_permalink( $object->ID );
						$pt[$hp['slug']]['children'][$menu->slug]['children'][$object->post_title] = $child_el;
						// Per le pagine archivio (elenco di post), aggiungi all'albero tutti i contenuti di quel tipo.
						$post_tpye = isset( $slugs[ $object->post_name ] ) ? $slugs[ $object->post_name ] : '';
						if ( $post_tpye ) {
							$results = dli_get_sitemap_posts( $post_tpye );
							foreach ( $results as $r ){
								$post_el         = dli_get_tree_item();
								$post_el['name'] = $r->post_title;
								$post_el['slug'] = $r->post_name;
								$post_el['link'] = get_permalink( $r->ID );
								$pt[$hp['slug']]['children'][$menu->slug]['children'][$object->post_title]['children'][$r->post_name] = $post_el;
							}
						}
					}
				}
			}
		}
		return $pt;
	}
}

if( ! function_exists( 'dli_get_tree_item' ) ) {
	function dli_get_tree_item( ) {
		$item = array(
			'name'     => '',
			'slug'     => '',
			'link'     => '',
			'external' => false,
			'children' => array(),
		);
		return $item;
	}
}

if( ! function_exists( 'dli_get_sitemap_posts' ) ) {
	/**
	 * Return the list of the post of a certain type to show in the sitemap.
	 * 
	 * @param array $post_type
	 * @return array of slugs (strings)
	 */
	function dli_get_sitemap_posts( $post_type ) {
		$query = new WP_Query(
			array(
				'posts_per_page' => -1,
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'orderby'        => 'post_date',
				'order'          => 'DESC',
			)
		);
		return $query->posts;
	}
}

if( ! function_exists( 'dli_get_pt_archive_slugs' ) ) {
	/**
	 * Return the slugs of all the page that are archives of posts.
	 * Ritorna un array di coppie: <slug> => <post_type>.
	 * 
	 * @return array.
	 */
	function dli_get_pt_archive_slugs( ) {
		$slugs = array();
		foreach( DLI_PAGE_PER_CT as $pt => $items ){
			if ( $pt !== PEOPLE_TYPE_POST_TYPE ) {
				foreach( $items as $lang => $slug){
					$slugs[$slug] = $pt;
				}
			}
		}
		return $slugs;
	}
}


if( ! function_exists( 'dli_get_sluglist_by_category' ) ) {
	/**
	 * Return the slugs of all the page that are archives of posts.
	 * Ritorna un array di slug
	 * 
	 * @return array.
	 */
	function dli_get_sluglist_by_category( $categories ) {
		$pt_slugs = dli_get_slugs_by_category( $categories );
		$sluglist    = array();
		foreach ( $pt_slugs as $category_items ) {
			foreach ( $category_items as $item ) {
				array_push( $sluglist, $item );
			}
		}
		return $sluglist;
	}
}

if( ! function_exists( 'dli_get_slugs_by_category' ) ) {
	/**
	 *  Return the slugs of the categories passed as parameter.
	 * Ritorna un array di coppie: <categoria> => <array_di_slug>
	 * 
	 * @param array $categories
	 * @return array.
	 */
	function dli_get_slugs_by_category( $categories ) {
		$slugmap = array();
		foreach ( DLI_STATIC_PAGE_CATS as $item ) {
			if ( in_array( $item['content_category'], $categories ) ) {
				if ( ! isset( $slugmap[$item['content_category']] ) ) {
					$slugmap[$item['content_category']] = array();
				}
				array_push( $slugmap[$item['content_category']], $item['content_slug_it'] );
				array_push( $slugmap[$item['content_category']], $item['content_slug_en'] );
			}
		}
		return $slugmap;
	}
}


if( ! function_exists( 'dli_get_page_anchestor_id' ) ) {
	/**
	 *  Return the anchestor id of given page
	 * 
	 * @param array $page
	 * @return id.
	 */
	function dli_get_page_anchestor_id( $page ) {
		if ( $page->post_parent) {
			$ancestors = get_post_ancestors( $page->ID );
			$root      = count( $ancestors ) - 1;
			$parent    = $ancestors[ $root ];
		} else {
			$parent = $page->ID;
		}
		return $parent;
	}
}

if( ! function_exists( 'dli_get_page_slug_anchestors' ) ) {
	/**
	 *  Return the slug anchestors of given page
	 * 
	 * @param array $page
	 * @return array.
	 */
	function dli_get_page_slug_anchestors( $page ) {
		$slugs = array();
		if ( $page->post_parent) {
			$ancestors = get_post_ancestors( $page->ID );
			foreach ( $ancestors as $ancestor ) {
				array_push( $slugs, get_post( $ancestor )->post_name );
			} 
		}
		return $slugs;
	}
}

if ( ! function_exists( 'check_plugin_active' ) ) {
	function check_plugin_active( $plugin ) {
			return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
	}
}

if( ! function_exists( 'check_mandatory_plugins' ) ) {
	function check_mandatory_plugins( ) {
		$mandatory_plugins = MANDATORY_PLUGINS;
		$text_plugins  = implode("','", $mandatory_plugins);
		$all_activated = 1;
		foreach ( $mandatory_plugins as $plugin ) {
			if (! check_plugin_active( $plugin ) ) {
				$all_activated = 0;
			}
		}
		if ( ! $all_activated ) {
			$msg = __( 'ATTENZIONE: I seguenti plugin devono essere installati e attivati obbligatoriamente: ', 'design_laboratori_italia' );
			wp_die( $msg . $text_plugins );
			return false;
		}
		return true;
	}
}
