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


	public static function get_patent_data_query( $params ) {
		$the_query = new WP_Query(
			array(
				'paged'          => get_query_var( 'paged', 1 ),
				'post_type'      => PATENT_POST_TYPE,
				'posts_per_page' => 2,
				// 'category__in'   => $selected_categories,
			)
		);
		return $the_query;
	}


}
