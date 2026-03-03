<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_the_query       = $args['query'];
$dli_mode            = isset( $args['mode'] ) ? $args['mode'] : 'show_paged';
$dli_per_page        = isset( $args['per_page'] ) && $args['per_page'] ? $args['per_page'] : strval( DLI_POSTS_PER_PAGE );
$dli_per_page_values = isset( $args['per_page_values'] ) && $args['per_page_values'] ? $args['per_page_values'] : DLI_POST_PER_PAGE_VALUES;
$dli_num_results     = $dli_the_query ? $dli_the_query->found_posts : 0;
$dli_pagination_on   = ( ( 'show_paged' === $dli_mode ) && ( $dli_num_results > intval( $dli_per_page ) ) );
?>

<!-- PAGINAZIONE condivisa -->
<nav class="pagination-wrapper justify-content-center mt-3" aria-label="Navigazione centrata">
	<div class="row w-100" id='pagination_links'>

		<!-- Filters column -->
		<div class="col-md-4 pt-2"></div>

		<!-- Navigazione pagine -->
		<div class="col-md-3 pt-2">
			<?php
			if ( $dli_the_query && $dli_pagination_on ) {
				$dli_prev_label = '<svg class="icon icon-primary" role="img" aria-labelledby="Chevron Left"><title>Chevron Left</title><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left"></use></svg>';
				$dli_next_label = '<svg class="icon icon-primary" role="img" aria-labelledby="Chevron Right"><title>Chevron Right</title><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right"></use></svg>';
					echo wp_kses_post(
						paginate_links(
							array(
								'total'     => $dli_the_query->max_num_pages,
								'prev_text' => $dli_prev_label,
								'next_text' => $dli_next_label,
								'type'      => 'list',
								'add_args'  => array( 'per_page' => $dli_per_page ),
							)
						)
					);
			}
			?>
		</div>

		<!-- Scelta numero elementi per pagina -->
		<div class="col-md-3 dli-dropdown-container">
			<?php
			if ( $dli_pagination_on || isset( $_GET['per_page'] ) ) {
				?>
				<div class="dropdown">
					<button class="btn btn-dropdown dropdown-toggle" type="button" id="pagerChanger"
						data-bs-toggle="dropdown" aria-haspopup="true"
						aria-expanded="false" aria-label="Salta alla pagina">
							<?php echo esc_html( $dli_per_page ); ?>/<?php echo esc_html__( 'pagina', 'design_laboratori_italia' ); ?>
							<svg class="icon icon-primary icon-sm">
								<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand' ); ?>"></use>
							</svg>
					</button>
					<div class="dropdown-menu dli-pagination-dropdown" aria-labelledby="pagerChanger">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<?php
								foreach ( $dli_per_page_values as $dli_pvalue ) {
									$dli_is_active = ( $dli_pvalue === $dli_per_page );
									?>
								<li>
									<a class="dropdown-item list-item 
									<?php
									if ( $dli_is_active ) {
										echo 'active'; }
									?>
									"
											href="#" data-perpage="<?php echo esc_attr( $dli_pvalue ); ?>"><span><?php echo esc_html( $dli_pvalue ); ?>/<?php echo esc_html__( 'pagina', 'design_laboratori_italia' ); ?></span>
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
