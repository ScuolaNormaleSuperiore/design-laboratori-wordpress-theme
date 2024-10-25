<?php
 $the_query       = $args['query'];
 $per_page        = isset( $args['per_page'] ) && $args['per_page'] ? $args['per_page'] : strval( DLI_POSTS_PER_PAGE );
 $per_page_values = isset( $args['per_page_values'] ) && $args['per_page_values'] ? $args['per_page_values'] : DLI_POST_PER_PAGE_VALUES;
 $num_results     = $the_query->found_posts;
 $pagination_on   = ( $num_results > intval ( $per_page) ) ? true : false;
?>

<!-- PAGINAZIONE condivisa -->
<nav class="pagination-wrapper justify-content-center mt-3" aria-label="Navigazione centrata">
	<div class="row w-100" id='pagination_links'>

		<!-- Filters column -->
		<div class="col-md-4 pt-2"></div>

		<!-- Navigazione pagine -->
		<div class="col-md-3 pt-2">
			<?php
			if ( $the_query && $pagination_on ) {
				$prev_label = '<svg class="icon icon-primary" role="img" aria-labelledby="Chevron Left"><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left"></use></svg>';
				$next_label = '<svg class="icon icon-primary" role="img" aria-labelledby="Chevron Right"><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right"></use></svg>';
				echo paginate_links(
					array(
						'total'     => $the_query->max_num_pages,
						'prev_text' => $prev_label,
						'next_text' => $next_label,
						'type'      => 'list',
						'add_args'  => array( 'per_page' => $per_page ),
					)
				);
			}
			?>
		</div>

		<!-- Scelta numero elementi per pagina -->
		<div class="col-md-3 dli-dropdown-container">
			<?php
			if ( $pagination_on || isset( $_GET['per_page'] ) ) {
			?>
				<div class="dropdown">
					<button class="btn btn-dropdown dropdown-toggle" type="button" id="pagerChanger"
						data-bs-toggle="dropdown" aria-haspopup="true"
						aria-expanded="false" aria-label="Salta alla pagina">
						<?php echo $per_page; ?>/<?php echo __( 'pagina', 'design_laboratori_italia' ); ?>
						<svg class="icon icon-primary icon-sm">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
						</svg>
					</button>
					<div class="dropdown-menu" aria-labelledby="pagerChanger">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<?php foreach( $per_page_values as $pvalue ) {
									$is_active = ( $pvalue === $per_page );
								?>
								<li>
									<a class="dropdown-item list-item <?php if( $is_active ) { echo 'active'; } ?>"
										href="#" data-perpage="<?php echo $pvalue; ?>"><span><?php echo $pvalue; ?>/<?php echo __( 'pagina', 'design_laboratori_italia' ); ?></span>
									</a>
									</li>
								<?php
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</div>

		<!-- Right empty column -->
		<div class="col-md-2 pt-2"></div>

	</div>
</nav>
