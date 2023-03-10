<?php

if( ! function_exists( 'dli_main_search_query' ) ) {
		function dli_main_search_query( $selected_contents, $searchstring, $pagesize ) {
			$the_query = new WP_Query(
				array(
					'paged'          => get_query_var( 'paged', 1 ),
					'post_type'      => $selected_contents,
					'posts_per_page' => $pagesize,
				)
			);
			return $the_query;
		}
}
