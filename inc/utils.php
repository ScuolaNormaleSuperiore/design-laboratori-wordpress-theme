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

if( ! function_exists( 'dli_get_option_by_lang' ) ) {
	function dli_get_option_by_lang( $key = '', $type = 'dli_options', $default = false ) {
		$lang      = dli_current_language();
		$text_def  = dli_get_option( $key, $type, $default );
		// $label     = $key . '_' . $lang;
		$label     = $key . DLI_ENG_SUFFIX_LANGUAGE;
		$text_lang = dli_get_option( $label, $type, $default );
		if ( ( $lang != 'it' ) && ( ! empty( $text_lang ) ) ) {
			return $text_lang;
		} else{
			return $text_def;
		}
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
 * recupera la url del template in base al nome
 * @param $TEMPLATE_NAME
 *
 * @return string|null
 */
function dli_get_template_page_url($TEMPLATE_NAME){
	$pages = get_pages(
		array(
		'meta_key'     => '_wp_page_template',
		'meta_value'   => $TEMPLATE_NAME,
		'hierarchical' => 0,
		'sort_column'  => 'menu_order',
		)
	);
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


if( ! function_exists( 'dli_get_post_wrapper' ) ) {
	function dli_get_post_wrapper( $result, $image_format='item-carousel' ) {
		$item = array();
		if ( $result ){
			switch ( $result->post_type) {
				case EVENT_POST_TYPE:
					$item = dli_from_event_to_wrapped_item ( $result, $image_format );
					break;
				case NEWS_POST_TYPE:
					$item = dli_from_news_to_wrapped_item ( $result, $image_format );
					break;
				case PROGETTO_POST_TYPE:
					$item = dli_from_progetto_to_wrapped_item ( $result, $image_format );
					break;
				case PUBLICATION_POST_TYPE:
					$item = dli_from_publication_to_wrapped_item ( $result, $image_format );
					break;
				case PATENT_POST_TYPE:
					$item = dli_from_patent_to_wrapped_item ( $result, $image_format );
					break;
				case SPINOFF_POST_TYPE:
					$item = dli_from_spinoff_to_wrapped_item ( $result, $image_format );
					break;
				case TECHNICAL_RESOURCE_POST_TYPE:
					$item = dli_from_technical_resource_to_wrapped_item ( $result, $image_format );
					break;
				case WP_DEFAULT_PAGE:
					$item = dli_from_page_to_wrapped_item ( $result, $image_format );
					break;
				case PEOPLE_POST_TYPE:
				case RESEARCH_ACTIVITY_POST_TYPE:
				case WP_DEFAULT_POST:
					$item = dli_from_post_to_wrapped_item ( $result, $image_format );
					break;
				default:
					$item = dli_from_post_to_wrapped_item ( $result, $image_format );
					break;
			}
		}
		return $item;
	}
}

if ( ! function_exists( 'dli_get_boxes_post_types' ) ) {
	function dli_get_boxes_post_types( $item ) {
		return array(
			RESEARCH_ACTIVITY_POST_TYPE => __( 'Attività di ricerca', 'design_laboratori_italia' ),
			EVENT_POST_TYPE            => __( 'Eventi', 'design_laboratori_italia' ),
			PLACE_POST_TYPE            => __( 'Luoghi', 'design_laboratori_italia' ),
			NEWS_POST_TYPE             => __( 'Notizie', 'design_laboratori_italia' ),
			PEOPLE_POST_TYPE           => __( 'Persone', 'design_laboratori_italia' ),
			WP_DEFAULT_POST            => __( 'Post', 'design_laboratori_italia' ),
			PROGETTO_POST_TYPE         => __( 'Progetti', 'design_laboratori_italia' ),
			PUBLICATION_POST_TYPE      => __( 'Pubblicazioni', 'design_laboratori_italia' ),
			PATENT_POST_TYPE           => __( 'Brevetti', 'design_laboratori_italia' ),
		);
	}
}

if( ! function_exists( 'dli_manage_item_link' ) ) {
	function dli_manage_item_link( $item ) {
		// Gestione del link al dettaglio dell'oggetto.
		$item_link = '';
		$link_type = dli_get_field( 'link_dettaglio', $item );
		switch ( $link_type ) {
			case DLI_ITEM_LINK['WEBSITE']:
				$website   = dli_get_field( 'sitoweb', $item );
				if ( $website ) {
					$item_link = $website;
				}
				break;
			case DLI_ITEM_LINK['ATTACHMENT']:
				$allegato  = dli_get_field( 'allegato', $item );
				if ( $allegato ) {
					$item_link = $allegato['url'];
				}
				break;
			default:
				$item_link = get_the_permalink( $item );
				break;
		}
		return $item_link;
	}
}


if( ! function_exists( 'dli_from_event_to_wrapped_item' ) ) {
	function dli_from_event_to_wrapped_item( $item, $image_format ) {
		$result         = DLI_POST_WRAPPER;
		$post_type      = get_post_type( $item );
		$placeholder    = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$image_metadata = dli_get_image_metadata( $item, $image_format, $placeholder );
		$page           = dli_get_page_by_post_type( $post_type );
		$post_title     = get_the_title( $item );
		$item_link      = dli_manage_item_link( $item );
		// @TODO: Popolare $result e non ridefinirlo.
		$result = array(
			'id'            => $item->ID,
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => dli_get_field( 'data_inizio', $item ),
			'orario_inizio' => dli_get_field( 'orario_inizio', $item ),
			'title'         => $post_title,
			'description'   => wp_trim_words( dli_get_field( 'descrizione_breve', $item ), DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => $item_link,
			'image_url'     => $image_metadata['image_url'],
			'image_alt'     => $image_metadata['image_alt'],
			'image_title'   => $image_metadata['image_title'],
		);
		return $result;
	}
}

	if( ! function_exists( 'dli_from_news_to_wrapped_item' ) ) {
		function dli_from_news_to_wrapped_item( $item, $image_format ) {
			$result         = DLI_POST_WRAPPER;
			$post_type      = get_post_type( $item );
			$placeholder    = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
			$image_metadata = dli_get_image_metadata( $item, $image_format, $placeholder );
			$page           = dli_get_page_by_post_type( $post_type );
			$post_title     = get_the_title( $item );
			$item_link      = dli_manage_item_link( $item );
			// @TODO: Popolare $result e non ridefinirlo.
			$result = array(
				'id'            => $item->ID,
				'type'          => $post_type,
				'category'      => $page->post_title,
				'category_link' => get_permalink( $page->ID ),
				'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
				'title'         => $post_title,
				'description'   => wp_trim_words( dli_get_field('descrizione_breve', $item), DLI_ACF_SHORT_DESC_LENGTH ),
				'full_content'  => get_the_content( $item ),
				'link'          => $item_link,
				'image_url'     => $image_metadata['image_url'],
				'image_alt'     => $image_metadata['image_alt'],
				'orario_inizio' => null,
				'image_title'   => $image_metadata['image_title'],
			);
			return $result;
		}
	}

if( ! function_exists( 'dli_from_publication_to_wrapped_item' ) ) {
	function dli_from_publication_to_wrapped_item( $item, $image_format ) {
		$result    = DLI_POST_WRAPPER;
		$post_type = get_post_type( $item );
		$placeholder    = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$image_metadata = dli_get_image_metadata( $item, $image_format, $placeholder );
		$page        = dli_get_page_by_post_type( $post_type );
		$post_title  = get_the_title( $item );
		$link_pubbl  = dli_get_field('url', $item);
		$link_pubbl  = $link_pubbl ? $link_pubbl : '';
		// @TODO: Popolare $result e non ridefinirlo.
		$result = array(
			'id'            => $item->ID,
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => dli_get_field('anno', $item),
			'orario_inizio' => null,
			'title'         => $post_title,
			'description'   => wp_trim_words( $item->post_content, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => $link_pubbl,
			'image_url'     => $image_metadata['image_url'],
			'image_alt'     => $image_metadata['image_alt'],
			'image_title'   => $image_metadata['image_title'],
		);
		return $result;
	}
}

if( ! function_exists( 'dli_from_patent_to_wrapped_item' ) ) {
	function dli_from_patent_to_wrapped_item( $item, $image_format ) {
		$result         = DLI_POST_WRAPPER;
		$post_type      = get_post_type( $item );
		$placeholder    = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$image_metadata = dli_get_image_metadata( $item, $image_format, $placeholder );
		$page           = dli_get_page_by_post_type( $post_type );
		$post_title     = get_the_title( $item );
		$link_pubbl     = dli_get_field( 'url', $item );
		$link_pubbl     = $link_pubbl ? $link_pubbl : '';
		$summary        = dli_get_field( 'sommario_elenco', $item );
		// @TODO: Popolare $result e non ridefinirlo.
		$result = array(
			'id'            => $item->ID,
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => dli_get_field('data_deposito', $item),
			'orario_inizio' => null,
			'title'         => $post_title,
			'description'   => wp_trim_words( $summary, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => get_the_permalink( $item ),
			'image_url'     => $image_metadata['image_url'],
			'image_alt'     => $image_metadata['image_alt'],
			'image_title'   => $image_metadata['image_title'],
		);
		return $result;
	}
}

if( ! function_exists( 'dli_from_technical_resource_to_wrapped_item' ) ) {
	function dli_from_technical_resource_to_wrapped_item( $item, $image_format ) {
		$result         = DLI_POST_WRAPPER;
		$post_type      = get_post_type( $item );
		$page           = dli_get_page_by_post_type( $post_type );
		$post_title     = get_the_title( $item );
		$link_pubbl     = dli_get_field( 'url', $item );
		$link_pubbl     = $link_pubbl ? $link_pubbl : '';
		$summary        = dli_get_field( 'descrizione_breve', $item );
		// Image field from custom fields.
		$img_field   = 'foto';
		$placeholder = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$placeholder = get_template_directory_uri() . $placeholder;
		$img         = dli_get_field( $img_field, $item );
		$img_url     = $img ? wp_get_attachment_image_src( $img['ID'], $image_format )[0] : $placeholder;
		$img_title   = ( $img && $img['title'] ) ? $$img['title'] : $post_title;
		$img_alt     = ( $img && $img['alt'] ) ? $img['alt'] : $post_title;
		// @TODO: Popolare $result e non ridefinirlo.
		$result = array(
			'id'            => $item->ID,
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => dli_get_field('anno_acquisizione', $item),
			'orario_inizio' => '',
			'title'         => $post_title,
			'description'   => wp_trim_words( $summary, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => get_the_permalink( $item ),
			'image_url'     => $img_url,
			'image_alt'     => $img_alt,
			'image_title'   => $img_title,
		);
		return $result;
	}
}


if( ! function_exists( 'dli_from_spinoff_to_wrapped_item' ) ) {
	function dli_from_spinoff_to_wrapped_item( $item, $image_format ) {
		$result    = DLI_POST_WRAPPER;
		$post_type = get_post_type( $item );
		$page        = dli_get_page_by_post_type( $post_type );
		$post_title  = get_the_title( $item );
		$link_pubbl  = get_field('url', $item);
		$link_pubbl  = $link_pubbl ? $link_pubbl : '';
		// Image field from custom fields.
		$img_field   = 'logo';
		$placeholder = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$placeholder = get_template_directory_uri() . $placeholder;
		$img         = dli_get_field( $img_field, $item );
		$img_url     = $img ? wp_get_attachment_image_src( $img['ID'], $image_format )[0] : $placeholder;
		$img_title   = ( $img && $img['title'] ) ? $$img['title'] : $post_title;
		$img_alt     = ( $img && $img['alt'] ) ? $img['alt'] : $post_title;
		// @TODO: Popolare $result e non ridefinirlo.
		$result = array(
			'id'            => $item->ID,
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => get_field('anno', $item),
			'orario_inizio' => null,
			'title'         => $post_title,
			'description'   => wp_trim_words( $item->post_content, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => $link_pubbl,
			'image_url'     => $img_url,
			'image_alt'     => $img_alt,
			'image_title'   => $img_title,
		);
		return $result;
	}
}

if( ! function_exists( 'dli_from_progetto_to_wrapped_item' ) ) {
	function dli_from_progetto_to_wrapped_item( $item, $image_format ) {
		$result         = DLI_POST_WRAPPER;
		$post_type      = get_post_type( $item );
		$placeholder    = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$image_metadata = dli_get_image_metadata( $item, $image_format, $placeholder );
		$page           = dli_get_page_by_post_type( $post_type );
		$post_title     = get_the_title( $item );
		// @TODO: Popolare $result e non ridefinirlo.
		$result      = array(
			'id'            => $item->ID,
			'type'          => $post_type,
			'category'      => $page->post_title,
			'category_link' => get_permalink( $page->ID ),
			'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
			'orario_inizio' => null,
			'title'         => $post_title,
			'description'   => wp_trim_words( $item->post_content, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => get_the_content( $item ),
			'link'          => get_the_permalink( $item ),
			'image_url'     => $image_metadata['image_url'],
			'image_alt'     => $image_metadata['image_alt'],
			'image_title'   => $image_metadata['image_title'],
		);
		return $result;
	}
}

if( ! function_exists( 'dli_from_post_to_wrapped_item' ) ) {
	function dli_from_post_to_wrapped_item( $item, $image_format ) {
		$post_type = get_post_type( $item );
		$placeholder    = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$image_metadata = dli_get_image_metadata( $item, $image_format, $placeholder );
		$page   = dli_get_page_by_post_type( $post_type );
		$result = array();
		if ( $page ){
			$post_title  = get_the_title( $item );
			// @TODO: Popolare $result e non ridefinirlo.
			$result = array(
				'id'            => $item->ID,
				'type'          => $post_type,
				'category'      => $page->post_title,
				'category_link' => get_permalink( $page->ID ),
				'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
				'orario_inizio' => null,
				'title'         => $post_title,
				'description'   => wp_trim_words( $item->post_content, DLI_ACF_SHORT_DESC_LENGTH ),
				'full_content'  => get_the_content( $item ),
				'link'          => get_the_permalink( $item ),
				'image_url'     => $image_metadata['image_url'],
				'image_alt'     => $image_metadata['image_alt'],
				'image_title'   => $image_metadata['image_title'],
			);
		}
		return $result;
	}
}

if( ! function_exists( 'dli_from_page_to_wrapped_item' ) ) {
	function dli_from_page_to_wrapped_item( $item, $image_format ) {
		$post_type      = get_post_type( $item );
		$placeholder    = ( $image_format === 'item-thumb' ) ? '/assets/img/placeholder.png' : '/assets/img/yourimage.png';
		$image_metadata = dli_get_image_metadata( $item, $image_format, $placeholder );
		$post_title     = get_the_title( $item );
		$categories     = array( DLI_CUSTOM_PAGE_CAT, DLI_ARCHIVE_PAGE_CAT );
		$pt_slugs       = dli_get_sluglist_by_category( $categories );
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
		$result = array(
			'id'            => $item->ID,
			'type'          => $post_type,
			'category'      => 'Home',
			'category_link' => get_site_url(),
			'date'          => get_the_date( DLI_ACF_DATE_FORMAT, $item ),
			'orario_inizio' => null,
			'title'         => $post_title,
			'description'   => wp_trim_words( $description, DLI_ACF_SHORT_DESC_LENGTH ),
			'full_content'  => $fullcontent,
			'link'          => get_the_permalink( $item ),
			'image_url'     => $image_metadata['image_url'],
			'image_alt'     => $image_metadata['image_alt'],
			'image_title'   => $image_metadata['image_title'],
		);
		return $result;
	}
}

if( ! function_exists( 'dli_get_image_metadata' ) ) {
	function dli_get_image_metadata( $item, $image_size = "item-carousel", $partial_default_img_url = null ) {
		$result        = DLI_POST_WRAPPER;
		$image_url     = get_the_post_thumbnail_url( $item, $image_size );
		$image_caption = '';
		if ( ! $image_url && $partial_default_img_url ) {
			$image_url = get_template_directory_uri() . $partial_default_img_url;
		}
		$post_title = get_the_title( $item );
		$image_id   = get_post_thumbnail_id( $item->ID );

		if( $image_id === 0 ) {
			$image_title = $post_title;
			$image_alt   = $post_title;
		}
		else {
			$image_title   = get_the_title( $image_id );
			$image_title   = $image_title ? $image_title : $post_title;
			$image_alt     = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
			$image_alt     = $image_alt ? $image_alt : $image_title;
			$image_caption = wp_get_attachment_caption( $image_id );
		}
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
		if ( ! is_array( $terms ) || count( $terms ) ==0 ) {
			$pg        = dli_get_page_by_post_type( $post->post_type );
			$pg_link   = get_permalink( $pg->ID );
			return array(
				'title' => $pg->post_title,
				'url'   => $pg_link,
				'id'    => null,
			);
		} else {
			return array(
				'title' => $terms[0]->name,
				'url'   => get_term_link( $terms[0] ),
				'id'    => $terms[0]->term_id,
			);
		}
	}
}

if( ! function_exists( 'dli_get_post_categories' ) ) {
	function dli_get_post_categories( $post, $taxonomy ) {
		$categories = array();
		$terms      = get_the_terms( $post, $taxonomy );

		if ( ! is_array( $terms ) || count( $terms ) ==0 ) {
			$pg        = dli_get_page_by_post_type( $post->post_type );
			$pg_link   = get_permalink( $pg->ID );
			array_push(
				$categories,
				array(
					'title' => $pg->post_title,
					'url'   => $pg_link,
					'id'    => null,
				)
			);
		} else {
			foreach( $terms as $term ) {
				array_push(
					$categories,
					array(
						'title' => $term->name,
						'url'   => get_term_link( $term ),
						'id'    => $term->term_id,
					)
				);
			}
		}
	
		return $categories;
	}
}

if( ! function_exists( 'dli_get_all_categories' ) ) {
	/**
	 * Recupera tutti i termini di una tassonomia ($taxonomy).
	 *
	 * @param string $taxonomy
	 * @param boolean $exclude_uncategorized
	 * @return array
	 */
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

if( ! function_exists( 'dli_get_all_categories_by_ct' ) ) {
	/**
	 * Ritorna tutti i termini di una tassonomia ($taxonomy) associati a dei contenuti di tipo $post_type.
	 *
	 * @param string $taxonomy
	 * @param string $post_type
	 * @param boolean $exclude_uncategorized
	 * @return array
	 */
	function dli_get_all_categories_by_ct( $taxonomy, $post_type, $content_status='publish' ) {
		$exclude_uncategorized=true;
		$categories = array();
		$terms      = get_terms(
			array (
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'object_ids' => get_posts(
					array(
						'post_type'      => $post_type,
						'numberposts'    => -1,
						'fields'         => 'ids',
						'content_status' => $content_status,
				 )
				),
			)
		);
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
			__( 'gennaio', 'design_laboratori_italia' ),
			__( 'febbraio', 'design_laboratori_italia' ),
			__( 'marzo', 'design_laboratori_italia' ),
			__( 'aprile', 'design_laboratori_italia' ),
			__( 'maggio', 'design_laboratori_italia' ),
			__( 'giugno', 'design_laboratori_italia' ),
			__( 'luglio', 'design_laboratori_italia' ),
			__( 'agosto', 'design_laboratori_italia' ),
			__( 'settembre', 'design_laboratori_italia' ),
			__( 'ottobre', 'design_laboratori_italia' ),
			__( 'novembre', 'design_laboratori_italia' ),
			__( 'dicembre', 'design_laboratori_italia' ),
		);
		return $months[ $index ];
	}
}

if( ! function_exists( 'dli_get_monthname_short' ) ) {
	function dli_get_monthname_short( $month ) {
		$index = intval( $month ) - 1;
		$months = array(
			__( 'gen', 'design_laboratori_italia'),
			__( 'feb', 'design_laboratori_italia'),
			__( 'mar', 'design_laboratori_italia'),
			__( 'apr', 'design_laboratori_italia'),
			__( 'mag', 'design_laboratori_italia'),
			__( 'giu', 'design_laboratori_italia'),
			__( 'lug', 'design_laboratori_italia'),
			__( 'ago', 'design_laboratori_italia'),
			__( 'set', 'design_laboratori_italia'),
			__( 'ott', 'design_laboratori_italia'),
			__( 'nov', 'design_laboratori_italia'),
			__( 'dic', 'design_laboratori_italia'),
		);
		return $months[ $index ];
	}
}

if( ! function_exists( 'dli_get_content' ) ) {
	function dli_get_content( $slug, $content_type ) {
		$args = array(
			'name'        => $slug,
			'post_type'   => $content_type,
			'post_status' => array( 'publish', 'draft', 'trash', 'pending', 'private' ),
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

if( ! function_exists( 'dli_get_newsletter_link' ) ) {
	function dli_get_newsletter_link( $current_language ) {
		$newsletter_page = ( 'it' === $current_language ) ? SLUG_NEWSLETTER_IT : SLUG_NEWSLETTER_EN;
		return dli_homepage_url() . '/' . $newsletter_page;
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

if( ! function_exists( 'dli_get_all_contenttypes_with_results' ) ) {
	function dli_get_all_contenttypes_with_results( ) {
		$content_types = DLI_POST_TYPES_TO_SEARCH;
		$content_types_with_results = array();
		foreach ( $content_types as $ct ) {
			if ( PEOPLE_TYPE_POST_TYPE !== $ct ) {
				$the_query = new WP_Query(
					array(
						'post_type'   => $ct,
						'post_status' => 'publish',
					)
				);
				$num_results = $the_query->found_posts;
				if ( $num_results > 0 ) {
					array_push( $content_types_with_results, $ct );
				}
			}
			wp_reset_postdata();
		}
		return $content_types_with_results;
	}
}

if( ! function_exists( 'dli_get_all_place_types_with_results' ) ) {
	function dli_get_all_place_types_with_results( ) {
		// recupero i termini della tassonomia tipologia luogo.
		$tipi_luogo = get_terms(
			[
				'taxonomy'   => PLACE_TYPE_TAXONOMY,
				'hide_empty' => false,
			]
		);

		$place_types_with_results = array();

		foreach ( $tipi_luogo as $tipo_luogo ) {

			$luoghi = new WP_Query(
				array(
					'posts_per_page' => DLI_POSTS_PER_PAGE,
					'paged'          => get_query_var( 'paged', 1 ),
					'post_type'      => PLACE_POST_TYPE,
					'orderby'        => 'title',
					'tax_query'   => array(
						array(
							'taxonomy' => PLACE_TYPE_TAXONOMY,
							'field'    => 'slug',
							'terms'    => "'" . $tipo_luogo->slug . "'",
						),
					),
				)
			);

			$num_results = $luoghi->found_posts;
			if ( $num_results > 0 ) {
				array_push( $place_types_with_results, $tipo_luogo );
			}
			wp_reset_postdata();
		}
		return $place_types_with_results;
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
		$sluglist = array();
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
	 * Return the slugs of the categories passed as parameter.
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
	 * @param arobject ay $page
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

if ( ! function_exists( 'dli_generate_slug' ) ) {
	function dli_generate_slug( $text ) {
		$new_text = sanitize_title( $text );
		$new_text = substr( $new_text, 0, 100 );
		return $new_text;
	}
}


if ( ! function_exists( 'dli_set_post_featured_image_from_url' ) ) {
	function dli_set_post_featured_image_from_url( $post_id, $image_url ) {
		// Controlla che il post ID e l'URL dell'immagine siano validi.
		if ( !$post_id || !$image_url ) {
			return false;
		}
		// Scarica l'immagine dall'URL.
		$image_data = file_get_contents( $image_url );
		if ( !$image_data ) {
			return false;
		}
		// Ottieni il nome del file dall'URL.
		$filename = basename( $image_url );
		// Carica l'immagine nella directory degli upload di WordPress.
		$upload_file = wp_upload_bits( $filename, null, $image_data );
		if ( $upload_file['error'] ) {
			return false;
		}
		// Prepara l'array dei dati per l'inserimento come allegato.
		$wp_filetype = wp_check_filetype( $filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name($filename),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		// Inserisci l'immagine come allegato nel database.
		$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
		if (!$attachment_id) {
			return false;
		}
		// Genera i metadati dell'allegato e aggiorna il database.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );
		// Imposta l'immagine come immagine in evidenza del post.
		set_post_thumbnail( $post_id, $attachment_id );
		return true;
	}
}

if( ! function_exists( 'clean_and_truncate_text' ) ) {
	function clean_and_truncate_text( $text, $size=500, $split=false ) {
		// Remove HTML tags.
		$clean_text = wp_strip_all_tags( $text );
		// Truncate tags.
		if ( strlen( $clean_text ) > $size ) {
			if ( $split ){
				$truncated_text = substr( $clean_text, 0, $size ) . '...';
			} else {
				$truncated_text = mb_substr( $clean_text, 0, $size );
				$last_space     = mb_strrpos( $truncated_text, ' ' );
				if ( $last_space !== false ) {
					$truncated_text = mb_substr( $truncated_text, 0, $last_space ) . '...';
				}
			}
		} else {
			$truncated_text = $clean_text;
		}
		return $truncated_text;
	}
}

if( ! function_exists( 'dli_decode_unicode_string' ) ) {
	function dli_decode_unicode_string( $string ) {
		return preg_replace_callback( '/u([0-9a-fA-F]{4})/', function ( $match ) {
			return mb_convert_encoding( pack('H*', $match[1] ), 'UTF-8', 'UTF-16BE' );
		}, $string );
	}
}

if( ! function_exists( 'dli_translate()' ) ) {
	/**
	 * Ritorna la traduzione personalizzata se esiste, altrimenti quella di default prodotta da gettext.
	 *
	 * @param string $text
	 * @param string $domain
	 * @return void
	 */
	function dli_translate( $text, $domain='design_laboratori_italia' ) {
		global $wpdb;
		$table       = $wpdb->prefix . 'dli_custom_translations';
		$lang        = dli_current_language();
		$translation = $wpdb->get_var( $wpdb->prepare(
			"SELECT translation FROM $table WHERE label = %s AND domain = %s AND lang = %s LIMIT 1",
			$text,
			$domain,
			$lang
		));
		// Se esiste una traduzione personalizzata, restituiscila.
		if ( ! empty( $translation ) ) {
				return $translation;
		} else {
			// Altrimenti, restituisci la traduzione standard fatta con gettext.
			return __( $text, $domain );
		}
	}

}