<?php
/**
 * Gestione dei contenuti del sito.
 *
 * @package Design_Laboratori_Italia
 */



class DLI_OG_Wrapper {
	public string $id    = '';
	public string $title = '';
	public string $shared_title  = '';
	public string $type  = '';
	public string $description  = '';
	public string $url  = '';
	public string $locale  = '';
	public string $site_title  = '';
	public string $site_tagline  = '';
	public string $image  = '';
	public string $img_width  = '';
	public string $img_height  = '';
	public string $img_type  = '';
	public string $site_url  = '';
	public string $domain  = '';
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

		if ( $item_id && in_array( $item_type, DLI_POST_TYPES_TO_TRANSLATE ) ) {
			// Get data to fill OG structure.
			$site_title   = dli_get_option( 'nome_laboratorio' );
			$site_tagline = dli_get_option( 'tagline_laboratorio' );	
			$item_title   = is_home() ? $site_title : $post->post_title;
			$item_desc    = is_home() ? $site_tagline: clean_and_truncate_text( $post->post_content, 256 );
			$item_url     = get_permalink();
			$img_id       = is_home() ? null : get_post_thumbnail_id( $item_id );
			$img_array    = wp_get_attachment_image_src( $img_id, 'large' );
			$file_path    = $img_id ? get_attached_file( $img_id ) : '';
			$file_info    = $img_id ? wp_check_filetype( $file_path ) : '';
			$img_type     = $img_id ? $file_info['type'] : '';
			$item_image   = $img_id && count( $img_array ) ? $img_array[0] : '';
			$site_url     = site_url();
			$parsed_url   = parse_url( $site_url );
			$domain       = $parsed_url['host'];
			$shared_title = is_home() ? $site_title : $site_title . ' - ' . $post->post_title;

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

	public static function get_patent_data_query( $args ) {
		// Pulisci parametri di ricerca.
		if ( isset( $args['thematic_area'] ) && is_array( $args['thematic_area'] ) && ! empty( $args['thematic_area'] ) ) {
			$areas = array_map( 'intval', $args['thematic_area'] );
		} else {
			$areas = [];
		}
		if ( isset( $args['deposit_year'] ) && is_array( $args['deposit_year'] ) && ! empty( $args['deposit_year'] ) ) {
			$deposit_years = array_map( 'strval', $args['deposit_year'] );
		} else {
			$deposit_years = [];
		}
		if ( isset( $args['search_string'] ) && is_string( $args['search_string'] ) ) {
			$search_string = sanitize_text_field( trim( $args['search_string'] ) );
		} else {
			$search_string = '';
		}


		$params = array(
			'paged'          => get_query_var( 'paged', 1 ),
			'post_type'      => PATENT_POST_TYPE,
			'posts_per_page' => DLI_POSTS_PER_PAGE,
			'orderby'        => 'title',
			'order'          => 'ASC',
			's'              => $search_string,
			// 'tax_query'      => array(),
			// 'meta_query'     => array(),
			'meta_query'     =>  array(
				array(
						'key'     => 'anno_deposito',
						'value'   => $deposit_years,
						'compare' => 'IN',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => THEMATIC_AREA_TAXONOMY,
					'field'    => 'term_id',
					'operator' => 'IN',
					'terms'    => $areas,
				)
			)
		);
		return new WP_Query( $params );
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

}
