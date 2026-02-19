<?php
/**
 * Template Name: I progetti
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_prog_cells_per_row = 3;
$dli_pagination_mode    = dli_get_option( 'pagination_mode', 'progetti' );
$dli_custom_per_page    = dli_get_option( 'pagination_number', 'progetti' );
$dli_per_page           = ( 'show_all' === $dli_pagination_mode ) ? 999 : DLI_POSTS_PER_PAGE;
$dli_per_page_values    = DLI_POST_PER_PAGE_VALUES;
$dli_today              = gmdate( 'Ymd' );

$dli_per_page_input = filter_input( INPUT_GET, 'per_page', FILTER_VALIDATE_INT );
if ( false !== $dli_per_page_input && null !== $dli_per_page_input && $dli_per_page_input > 0 ) {
	$dli_per_page = absint( $dli_per_page_input );
}

$dli_selected_level_raw = filter_input( INPUT_GET, 'level', FILTER_DEFAULT );
$dli_selected_level     = is_string( $dli_selected_level_raw ) ? sanitize_text_field( wp_unslash( $dli_selected_level_raw ) ) : '';

$dli_paged = absint( get_query_var( 'paged' ) );
if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}
if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_projects_params = array(
	'today'     => $dli_today,
	'per_page'  => $dli_per_page,
	'paged'     => $dli_paged,
	'tag_level' => $dli_selected_level,
);

$dli_projects_query   = DLI_ContentsManager::get_projects_data_query( $dli_projects_params );
$dli_num_results      = $dli_projects_query->found_posts;
$dli_tags             = DLI_ContentsManager::get_tags_by_post_type( PROGETTO_POST_TYPE );
$dli_label_all_levels = dli_get_configuration_field_by_lang( 'tutti_i_livelli_progetti', 'progetti' );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PROGETTI -->
	<?php get_template_part( 'template-parts/hero/progetti' ); ?>

	<section id="progetti">
		<div class="container p-5">

			<!-- Filtro per TAG -->
			<?php if ( count( $dli_tags ) > 0 ) : ?>
				<div class="row text-center pb-5">
					<div class="col-12 col-lg-12">
						<div class="title-section">
							<?php foreach ( $dli_tags as $dli_tag ) : ?>
								<div class="chip chip-primary chip-lg chip-simple <?php echo ( $dli_selected_level === $dli_tag->slug ) ? 'chip-selected' : ''; ?>">
									<span class="chip-label customSpacing">
										<a class="hover-text-white"
											href="#"
											onclick="addParameterAndReloadPage('level', '<?php echo esc_attr( $dli_tag->slug ); ?>'); return false;"
											title="<?php echo esc_attr__( 'Filtra per', 'design_laboratori_italia' ) . ': ' . esc_attr( $dli_tag->name ); ?>"
											data-focus-mouse="false">
											<?php echo esc_html( $dli_tag->name ); ?>
										</a>
									</span>
								</div>
							<?php endforeach; ?>
							<div class="chip chip-primary chip-lg chip-simple <?php echo ( '' === $dli_selected_level ) ? 'chip-selected' : ''; ?>">
								<span class="chip-label customSpacing">
									<a class="hover-text-white"
										href="#"
										onclick="addParameterAndReloadPage('level', ''); return false;"
										title="<?php echo esc_attr( $dli_label_all_levels ); ?>">
										<?php echo esc_html( $dli_label_all_levels ); ?>
									</a>
								</span>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<!-- FINE FILTRO PER TAG -->

			<?php
			$dli_pindex = 0;
			if ( $dli_num_results ) :
				while ( $dli_projects_query->have_posts() ) :
					$dli_projects_query->the_post();
					if ( 0 === ( $dli_pindex % $dli_prog_cells_per_row ) ) :
						?>
						<!-- begin row -->
						<div class="row">
					<?php endif; ?>

					<?php
					$dli_post_id            = get_the_ID();
					$dli_project_post       = get_post( $dli_post_id );
					$dli_image_metadata     = dli_get_image_metadata( $dli_project_post, 'item-card-list' );
					$dli_responsabili       = dli_get_field( 'responsabile_del_progetto', $dli_post_id );
					$dli_levels             = wp_get_post_terms( $dli_post_id, 'post_tag' );
					$dli_responsabili_names = array();

					if ( is_array( $dli_responsabili ) && count( $dli_responsabili ) > 0 ) {
						foreach ( $dli_responsabili as $dli_persona ) {
							if ( isset( $dli_persona->post_title ) ) {
								$dli_responsabili_names[] = $dli_persona->post_title;
							}
						}
					}

					$dli_nomi_resp = implode( ', ', $dli_responsabili_names );
					?>

					<!--start card-->
					<div class="col-12 col-lg-4">
						<div class="card-space pb-5">
							<div class="card card-bg card-big no-after dli_card_progetti">
								<?php if ( ! empty( $dli_image_metadata['image_url'] ) ) : ?>
									<div class="img-responsive-wrapper">
										<div class="img-responsive img-responsive-panoramic">
											<figure class="img-wrapper">
												<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
													title="<?php echo esc_attr( $dli_image_metadata['image_title'] ?? '' ); ?>"
													alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ?? '' ); ?>">
											</figure>
										</div>
									</div>
								<?php endif; ?>

								<div class="card-body">
									<h3 class="card-title h5"><?php echo esc_html( get_the_title() ); ?></h3>
									<p class="card-text font-serif">
										<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
									</p>
									<span class="card-signature"><?php echo esc_html( $dli_nomi_resp ); ?></span>

									<div class="it-card-footer">
										<div class="head-tags">
											<?php foreach ( $dli_levels as $dli_level ) : ?>
												<a class="card-tag text-decoration-none" href="#"
													onclick="addParameterAndReloadPage('level', '<?php echo esc_attr( $dli_level->slug ); ?>'); return false;">
													<?php echo esc_html( $dli_level->name ); ?>
												</a>
											<?php endforeach; ?>
										</div>
										<a class="read-more" href="<?php echo esc_url( get_permalink() ); ?>">
											<span class="text customSpacing"><?php echo esc_html__( 'Vai al progetto', 'design_laboratori_italia' ); ?></span>
											<svg class="icon" role="img" aria-label="<?php echo esc_attr__( 'Vai al progetto', 'design_laboratori_italia' ); ?>">
												<title><?php echo esc_html__( 'Vai al progetto', 'design_laboratori_italia' ); ?></title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
											</svg>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--end card-->

					<?php
					if ( ( ( $dli_pindex % $dli_prog_cells_per_row ) === $dli_prog_cells_per_row - 1 ) || ( $dli_projects_query->current_post + 1 === $dli_projects_query->post_count ) ) :
						?>
						</div>
						<!-- end row -->
					<?php endif; ?>
					<?php ++$dli_pindex; ?>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="row pt-2">
					<?php echo esc_html__( 'Non è stato trovato nessun progetto', 'design_laboratori_italia' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- RESTORE ORIGINAL Post Data -->
	<?php wp_reset_postdata(); ?>

	<!-- PAGINAZIONE -->
	<?php
	get_template_part(
		'template-parts/common/paginazione',
		null,
		array(
			'mode'            => $dli_pagination_mode,
			'query'           => $dli_projects_query,
			'per_page'        => $dli_per_page,
			'per_page_values' => $dli_per_page_values,
		)
	);
	?>

</main>

<?php
get_footer();
