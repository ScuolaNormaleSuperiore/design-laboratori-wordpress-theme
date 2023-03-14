<?php

if ( ! function_exists( 'dli_main_search_query' ) ) {
		function dli_main_search_query( $selected_contents, $searchstring, $pagesize ) {
			$the_query = new WP_Query(
				array(
					'paged'          => get_query_var( 'paged', 1 ),
					'post_type'      => $selected_contents,
					'posts_per_page' => $pagesize,
					's'              => $searchstring,
				)
			);
			return $the_query;
		}
}

if ( ! function_exists( 'dli_format_search_result' ) ) {
	function dli_format_search_result( $post ) {


		// Creare un wrapper per ogni tipo di contenuto.
		$descr1 = dli_get_field( $post->ID, 'descrizione_breve' );
		$descr2 = get_the_content( $post->ID );
		$text   = $descr1 ? $descr1 : $descr2;

		return array(
			'id'            => $post->ID,
			'title'         => get_the_title( $post->ID ),
			'description'   => $text,
			'image'         => get_the_post_thumbnail_url( $post->ID, 'item-thumb' ),
			'link'          => get_the_permalink( $post->ID ),
			'date'          => null,
			'type'          => $post->post_type,
			'category'      => null,
			'link_category' => null,
		);

	}
}
