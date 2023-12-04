<?php

if ( ! function_exists( 'dli_main_search_query' ) ) {
		function dli_main_search_query( $selected_contents, $searchstring, $pagesize ) {
			if( count( $selected_contents ) > 0 ) {
			$the_query = new WP_Query(
				array(
					'paged'          => get_query_var( 'paged', 1 ),
					'post_type'      => $selected_contents,
					'posts_per_page' => $pagesize,
					's'              => $searchstring,
				)
			);
		}
		else {
			$the_query = new WP_Query(
				array(
					'paged'          => get_query_var( 'paged', 1 ),
					'posts_per_page' => $pagesize,
					's'              => $searchstring,
				)
			);
		}

			return $the_query;
		}
}

if ( ! function_exists( 'dli_format_search_result' ) ) {
	function dli_format_search_result( $post ) {


		// Creare un wrapper per ogni tipo di contenuto.
		$descr1 = dli_get_field( $post->ID, 'descrizione_breve' );
		$descr2 = get_the_content( $post->ID );
		$text   = $descr1 ? $descr1 : $descr2;
		$post_title = get_the_title( $post->ID );

		$image_metadata = dli_get_image_metadata( $post, 'item-thumb' );
		$image_alt      = ( PEOPLE_POST_TYPE === $post->post_type ) ? $post_title : $image_metadata['image_alt'];
		$image_title      = ( PEOPLE_POST_TYPE === $post->post_type ) ? $post_title : $image_metadata['image_title'];

		return array(
			'id'            => $post->ID,
			'title'         => $post_title,
			'description'   => $text,
			'image'         => $image_metadata['image_url'],
			'link'          => get_the_permalink( $post->ID ),
			'date'          => null,
			'type'          => $post->post_type,
			'category'      => null,
			'link_category' => null,
			'title'         => $post_title,
			'image_alt'     => $image_alt,
			'image_title'   => $image_title,
		);

	}
}
