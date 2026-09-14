<?php
/**
 * Template part: paginazione condivisa.
 *
 * Markup allineato al componente "pagination" di Bootstrap Italia 3 (ul.pagination/
 * li.page-item/a.page-link), come nel prototipo statico bs-playground
 * (sf-elenco-progetti.html), al posto dell'output nativo di paginate_links()
 * (ul.page-numbers) usato in precedenza e mai tematizzato da v3. Stessa logica/stesse
 * variabili di prima: $args['query']/['mode']/['per_page']/['per_page_values'], stesso
 * dropdown "N/pagina" (invariato, la logica JS che lo aziona è in functions.php).
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_the_query       = ( isset( $args['query'] ) && ( $args['query'] instanceof WP_Query ) ) ? $args['query'] : null;
$dli_mode            = isset( $args['mode'] ) ? $args['mode'] : 'show_paged';
$dli_per_page        = isset( $args['per_page'] ) ? absint( $args['per_page'] ) : DLI_POSTS_PER_PAGE;
$dli_per_page        = $dli_per_page > 0 ? $dli_per_page : DLI_POSTS_PER_PAGE;
$dli_per_page_values = isset( $args['per_page_values'] ) && $args['per_page_values'] ? $args['per_page_values'] : DLI_POST_PER_PAGE_VALUES;
$dli_per_page_values = array_map( 'absint', (array) $dli_per_page_values );
$dli_per_page_values = array_filter( $dli_per_page_values );
$dli_per_page_raw    = filter_input( INPUT_GET, 'per_page' );
$dli_num_results     = $dli_the_query ? $dli_the_query->found_posts : 0;
$dli_pagination_on   = ( ( 'show_paged' === $dli_mode ) && ( $dli_num_results > intval( $dli_per_page ) ) );

$dli_total_pages  = $dli_the_query ? (int) $dli_the_query->max_num_pages : 0;
$dli_current_page = (int) get_query_var( 'paged' );
$dli_current_page = $dli_current_page ? $dli_current_page : 1;

/*
 * URL della pagina N, con lo stesso parametro per_page preservato che prima
 * veniva passato a paginate_links() via 'add_args'. Chiusura locale (non una
 * funzione globale) perché questo template-part può in teoria essere
 * incluso più volte nella stessa richiesta.
 */
$dli_paginazione_page_url = static function ( $dli_page_number ) use ( $dli_per_page ) {
	return add_query_arg( 'per_page', $dli_per_page, get_pagenum_link( $dli_page_number ) );
};
?>

<!-- PAGINAZIONE condivisa -->
<nav class="pagination-wrapper justify-content-center mt-3" aria-label="<?php echo esc_attr__( 'Navigazione pagine', 'design_laboratori_italia' ); ?>">
	<?php if ( $dli_the_query && $dli_pagination_on && $dli_total_pages > 1 ) : ?>
		<ul class="pagination">
			<li class="page-item<?php echo ( $dli_current_page <= 1 ) ? ' disabled' : ''; ?>">
				<a
					class="page-link"
					href="<?php echo esc_url( $dli_paginazione_page_url( max( 1, $dli_current_page - 1 ) ) ); ?>"
					<?php echo ( $dli_current_page <= 1 ) ? ' tabindex="-1" aria-hidden="true"' : ''; ?>
				>
					<svg class="icon icon-primary" aria-hidden="true" focusable="false">
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
					</svg>
					<span class="visually-hidden"><?php echo esc_html__( 'Pagina precedente', 'design_laboratori_italia' ); ?></span>
				</a>
			</li>
			<?php
			// Pagine mostrate: sempre la prima e l'ultima, più le due precedenti/successive alla corrente; salti coperti da un'ellissi.
			$dli_pages_to_show = array( 1, $dli_total_pages );
			for ( $dli_p = $dli_current_page - 2; $dli_p <= $dli_current_page + 2; $dli_p++ ) {
				if ( $dli_p >= 1 && $dli_p <= $dli_total_pages ) {
					$dli_pages_to_show[] = $dli_p;
				}
			}
			$dli_pages_to_show = array_unique( $dli_pages_to_show );
			sort( $dli_pages_to_show );

			$dli_prev_shown = 0;
			foreach ( $dli_pages_to_show as $dli_p ) :
				if ( $dli_prev_shown && ( $dli_p - $dli_prev_shown > 1 ) ) :
					?>
					<li class="page-item disabled"><span class="page-link">&hellip;</span></li>
					<?php
				endif;
				$dli_prev_shown  = $dli_p;
				$dli_is_current = ( $dli_p === $dli_current_page );
				?>
				<li class="page-item<?php echo $dli_is_current ? ' active' : ''; ?>">
					<a
						class="page-link"
						href="<?php echo esc_url( $dli_paginazione_page_url( $dli_p ) ); ?>"
						<?php echo $dli_is_current ? ' aria-current="page"' : ''; ?>
					>
						<span class="d-inline-block d-sm-none"><?php echo esc_html__( 'Pagina', 'design_laboratori_italia' ); ?></span>
						<?php echo esc_html( (string) $dli_p ); ?>
					</a>
				</li>
			<?php endforeach; ?>
			<li class="page-item<?php echo ( $dli_current_page >= $dli_total_pages ) ? ' disabled' : ''; ?>">
				<a
					class="page-link"
					href="<?php echo esc_url( $dli_paginazione_page_url( min( $dli_total_pages, $dli_current_page + 1 ) ) ); ?>"
					<?php echo ( $dli_current_page >= $dli_total_pages ) ? ' tabindex="-1" aria-hidden="true"' : ''; ?>
				>
					<span class="visually-hidden"><?php echo esc_html__( 'Pagina successiva', 'design_laboratori_italia' ); ?></span>
					<svg class="icon icon-primary" aria-hidden="true" focusable="false">
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
					</svg>
				</a>
			</li>
		</ul>
	<?php endif; ?>

	<?php if ( $dli_pagination_on || null !== $dli_per_page_raw ) : ?>
		<div class="dropdown">
			<button class="btn btn-dropdown dropdown-toggle" type="button" id="pagerChanger"
				data-bs-toggle="dropdown" aria-haspopup="true"
				aria-expanded="false" aria-label="<?php echo esc_attr__( 'Salta alla pagina', 'design_laboratori_italia' ); ?>">
				<?php echo esc_html( $dli_per_page ); ?>/<?php echo esc_html__( 'pagina', 'design_laboratori_italia' ); ?>
				<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
					<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand' ); ?>"></use>
				</svg>
			</button>
			<div class="dropdown-menu dli-pagination-dropdown" aria-labelledby="pagerChanger">
				<div class="link-list-wrapper">
					<ul class="link-list">
						<?php foreach ( $dli_per_page_values as $dli_pvalue ) : ?>
							<?php $dli_is_active = ( $dli_pvalue === $dli_per_page ); ?>
							<li>
								<a class="dropdown-item list-item<?php echo $dli_is_active ? ' active' : ''; ?>"
									href="#" data-perpage="<?php echo esc_attr( $dli_pvalue ); ?>">
									<span><?php echo esc_html( $dli_pvalue ); ?>/<?php echo esc_html__( 'pagina', 'design_laboratori_italia' ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	<?php endif; ?>
</nav>
