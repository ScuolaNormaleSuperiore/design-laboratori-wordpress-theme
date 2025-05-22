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
