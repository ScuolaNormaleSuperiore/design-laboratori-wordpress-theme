<?php
 $the_query = $args['query'];
?>
<nav class="pagination-wrapper justify-content-center" aria-label="Navigazione centrata">
		<div class="row pt-5" id='pagination_links'>
		<?php
		if ( $the_query ) {
			$prev_label = '<svg class="icon icon-primary" role="img" aria-labelledby="Chevron Left"><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left"></use></svg>';
			$next_label = '<svg class="icon icon-primary" role="img" aria-labelledby="Chevron Right"><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right"></use></svg>';
			echo paginate_links(
				array(
					'total'     => $the_query->max_num_pages,
					'prev_text' => $prev_label,
					'next_text' => $next_label,
					'type'      => 'list',
				)
			);
		}
		?>
		</div>
	</nav>
