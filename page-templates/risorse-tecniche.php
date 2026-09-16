<?php
/**
 * Template Name: Risorse-Tecniche
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

// Permalink della pagina stessa, catturato prima che il loop sui risultati sotto sposti $post: usato per il link di filtro sulla tipologia della card.
$dli_page_permalink = get_permalink();

$dli_selected_year  = '';
$dli_selected_types = array();
$dli_search_string  = '';
$dli_all_types      = dli_get_all_categories_by_ct( RT_TYPE_TAXONOMY, TECHNICAL_RESOURCE_POST_TYPE );
$dli_all_years      = DLI_ContentsManager::dli_get_all_technical_res_years();
$dli_per_page       = (string) DLI_PER_PAGE_BIG;
$dli_per_page_vals  = (array) DLI_PER_PAGE_VALUES;
$dli_allowed_pages  = array_map( 'strval', $dli_per_page_vals );

$dli_raw_per_page = filter_input( INPUT_GET, 'per_page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( is_string( $dli_raw_per_page ) ) {
	$dli_raw_per_page = sanitize_text_field( wp_unslash( $dli_raw_per_page ) );
	if ( in_array( $dli_raw_per_page, $dli_allowed_pages, true ) ) {
		$dli_per_page = $dli_raw_per_page;
	}
}

$dli_raw_types = filter_input( INPUT_GET, 'type_technical_resource', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
if ( is_array( $dli_raw_types ) ) {
	$dli_selected_types = array_map( 'absint', $dli_raw_types );
	$dli_selected_types = array_values( array_filter( $dli_selected_types ) );
}

$dli_raw_search = filter_input( INPUT_GET, 'search_string', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( is_string( $dli_raw_search ) ) {
	$dli_search_string = sanitize_text_field( wp_unslash( $dli_raw_search ) );
}

$dli_raw_year = filter_input( INPUT_GET, 'acquisition_year', FILTER_SANITIZE_NUMBER_INT );
if ( is_scalar( $dli_raw_year ) ) {
	$dli_selected_year_num = absint( $dli_raw_year );
	if ( $dli_selected_year_num > 0 ) {
		$dli_selected_year = (string) $dli_selected_year_num;
	}
}

$dli_paged = absint( get_query_var( 'paged' ) );
if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}
if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_params = array(
	'search_string'           => $dli_search_string,
	'type_technical_resource' => ! empty( $dli_selected_types ) ? $dli_selected_types : null,
	'acquisition_year'        => '' !== $dli_selected_year ? array( $dli_selected_year ) : null,
	'per_page'                => $dli_per_page,
	'paged'                   => $dli_paged,
);

$dli_query       = DLI_ContentsManager::get_technical_resource_data_query( $dli_params );
$dli_num_results = $dli_query->found_posts;

?>

<main id="main-container" role="main">

	<!-- BANNER RISORSE TECNICHE -->
	<?php get_template_part( 'template-parts/hero/risorse-tecniche' ); ?>

	<!-- ELENCO RISORSE TECNICHE -->
	<section id="technical-resources">
		<div class="container p-5">
			<div class="row">
				<div class="col-12 col-lg-3 border-bottom pb-3 mb-4 mb-lg-0">
					<form action="<?php echo esc_url( get_permalink() ); ?>" id="techresourcesform" method="GET">
						<div class="sticky-top pt-3" style="top: 1rem;">
							<!-- FILTRO PER ANNO -->
							<?php if ( is_array( $dli_all_years ) && count( $dli_all_years ) > 0 ) { ?>
								<div class="row">
									<h3 class="h6 text-uppercase border-bottom"><?php esc_html_e( 'Anno di acquisizione', 'design_laboratori_italia' ); ?></h3>
									<div class="select-wrapper">
										<label for="acquisition_year" class="visually-hidden"><?php esc_html_e( 'Anno di acquisizione', 'design_laboratori_italia' ); ?></label>
										<select id="acquisition_year" name="acquisition_year">
											<option value="" <?php selected( '', $dli_selected_year ); ?>><?php esc_html_e( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
											<?php foreach ( $dli_all_years as $dli_acquisition_year ) { ?>
												<option value="<?php echo esc_attr( $dli_acquisition_year ); ?>" <?php selected( (string) $dli_acquisition_year, $dli_selected_year ); ?>>
													<?php echo esc_html( $dli_acquisition_year ); ?>
												</option>
											<?php } ?>
										</select>
									</div>
								</div>
							<?php } ?>

							<!-- FILTRO PER TIPO DI RISORSA -->
							<?php if ( is_array( $dli_all_types ) && count( $dli_all_types ) > 0 ) { ?>
								<div class="row pt-5">
									<h3 class="h6 text-uppercase border-bottom"><?php esc_html_e( 'Tipo di risorsa', 'design_laboratori_italia' ); ?></h3>
									<div>
										<?php foreach ( $dli_all_types as $dli_type_resource ) { ?>
											<?php
											$dli_type_id    = isset( $dli_type_resource['id'] ) ? absint( $dli_type_resource['id'] ) : 0;
											$dli_type_slug  = isset( $dli_type_resource['slug'] ) ? sanitize_html_class( $dli_type_resource['slug'] ) : '';
											$dli_type_name  = isset( $dli_type_resource['name'] ) ? $dli_type_resource['name'] : '';
											$dli_type_check = in_array( $dli_type_id, $dli_selected_types, true );
											?>
											<?php if ( $dli_type_id > 0 && '' !== $dli_type_slug ) { ?>
												<div class="form-check">
													<input type="checkbox" name="type_technical_resource[]" id="<?php echo esc_attr( $dli_type_slug ); ?>" value="<?php echo esc_attr( $dli_type_id ); ?>" <?php checked( true, $dli_type_check ); ?>>
													<label for="<?php echo esc_attr( $dli_type_slug ); ?>"><?php echo esc_html( $dli_type_name ); ?></label>
												</div>
											<?php } ?>
										<?php } ?>
									</div>
								</div>
							<?php } ?>

							<!-- FILTRO PER RICERCA LIBERA -->
							<div class="row pt-5">
								<h3 class="h6 text-uppercase border-bottom"><?php esc_html_e( 'Ricerca libera', 'design_laboratori_italia' ); ?></h3>
								<div class="form-group">
									<label for="search_string" class="visually-hidden"><?php esc_html_e( 'Cerca contenuto', 'design_laboratori_italia' ); ?></label>
									<div class="input-group">
										<span class="input-group-text">
											<svg class="icon icon-sm" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-search' ); ?>"></use>
											</svg>
										</span>
										<input type="text" class="form-control" id="search_string" name="search_string" placeholder="<?php echo esc_attr__( 'Cerca contenuto', 'design_laboratori_italia' ); ?>" value="<?php echo esc_attr( $dli_search_string ); ?>">
									</div>
								</div>
								<button type="submit" class="btn btn-primary w-50 mx-auto d-block mt-3">
									<?php esc_html_e( 'Filtra', 'design_laboratori_italia' ); ?>
								</button>
							</div>
						</div>
					</form>
				</div>
				<!-- ELENCO RISORSE TECNICHE -->
				<?php if ( $dli_num_results > 0 ) { ?>
					<div class="col-12 col-lg-8 offset-lg-1 pt-3">
						<div class="row">
							<?php
							while ( $dli_query->have_posts() ) {
								$dli_query->the_post();
								$dli_result       = dli_get_post_wrapper( $post, 'medium' );
								$dli_tipo_risorsa = dli_get_post_main_category( $post, RT_TYPE_TAXONOMY );
								?>
								<div class="col-12 mb-4">
									<article class="it-card it-card-inline it-card-inline-mini<?php echo $dli_result['image_url'] ? ' it-card-image' : ''; ?> rounded shadow-sm border">
										<div class="it-card-inline-content">
											<h3 class="it-card-title h5">
												<a href="<?php echo esc_url( $dli_result['link'] ); ?>">
													<?php echo esc_html( $dli_result['title'] ); ?>
												</a>
											</h3>
											<div class="it-card-body">
												<p class="it-card-text"><?php echo esc_html( wp_trim_words( $dli_result['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
											</div>
											<?php if ( $dli_tipo_risorsa && array_key_exists( 'title', $dli_tipo_risorsa ) ) : ?>
												<footer class="it-card-footer">
													<div class="it-card-taxonomy">
														<?php if ( $dli_tipo_risorsa['id'] ) : ?>
															<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'type_technical_resource', array( $dli_tipo_risorsa['id'] ), $dli_page_permalink ) ); ?>">
																<span class="visually-hidden"><?php esc_html_e( 'Tipo di risorsa:', 'design_laboratori_italia' ); ?></span>
																<?php echo esc_html( $dli_tipo_risorsa['title'] ); ?>
															</a>
														<?php else : ?>
															<span class="it-card-category">
																<span class="visually-hidden"><?php esc_html_e( 'Tipo di risorsa:', 'design_laboratori_italia' ); ?></span>
																<?php echo esc_html( $dli_tipo_risorsa['title'] ); ?>
															</span>
														<?php endif; ?>
													</div>
												</footer>
											<?php endif; ?>
										</div>
										<?php if ( $dli_result['image_url'] ) : ?>
											<div class="it-card-image-wrapper">
												<div class="ratio ratio-1x1">
													<figure class="figure img-full">
														<img src="<?php echo esc_url( $dli_result['image_url'] ); ?>"
															title="<?php echo esc_attr( $dli_result['image_title'] ); ?>"
															alt="<?php echo esc_attr( $dli_result['image_alt'] ); ?>">
													</figure>
												</div>
											</div>
										<?php endif; ?>
									</article>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				<?php } else { ?>
					<div class="col-12 col-lg-8 offset-lg-1">
						<div class="row pt-2">
							<?php esc_html_e( 'Non e stata trovata alcuna risorsa tecnica', 'design_laboratori_italia' ); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>

	<?php wp_reset_postdata(); ?>

	<!-- PAGINAZIONE con selettore -->
	<?php
	get_template_part(
		'template-parts/common/paginazione',
		null,
		array(
			'query'           => $dli_query,
			'per_page'        => $dli_per_page,
			'per_page_values' => $dli_per_page_vals,
		)
	);
	?>

</main>

<?php
get_footer();
