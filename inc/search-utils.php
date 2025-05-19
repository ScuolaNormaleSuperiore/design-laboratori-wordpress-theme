<?php

if ( ! function_exists( 'dli_main_search_query' ) ) {
		function dli_main_search_query( $selected_contents, $search_string, $page_size ) {
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
}

if ( ! function_exists( 'dli_format_search_result' ) ) {
	function dli_format_search_result( $post ) {
		$wrapper        = dli_get_post_wrapper( $post );
		// Utilizzo l'immagine nel formato item-thumb
		$image_metadata = dli_get_image_metadata( $post, 'item-thumb', '/assets/img/placeholder.png' );
		$image_alt      = ( PEOPLE_POST_TYPE === $post->post_type ) ? $wrapper['title'] : $image_metadata['image_alt'];
		$image_title    = ( PEOPLE_POST_TYPE === $post->post_type ) ? $wrapper['title'] : $image_metadata['image_title'];

		return array(
			'id'            => $post->ID,
			'title'         => $wrapper['title'],
			'description'   => $wrapper['description'],
			'image'         => $image_metadata['image_url'],
			'link'          => $wrapper['link'],
			'date'          => $wrapper['date'],
			'type'          => $wrapper['type'],
			'category'      => $wrapper['category'],
			'link_category' => $wrapper['category_link'],
			'title'         => $wrapper['title'],
			'image_alt'     => $image_alt,
			'image_title'   => $image_title,
		);

	}
}
