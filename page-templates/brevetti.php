<?php
/**
 * Template Name: Brevetti
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

// Permalink della pagina stessa, catturato prima che il loop sui risultati sotto sposti $post: usato per i link di filtro sull'area tematica della card.
$dli_page_permalink = get_permalink();

$dli_selected_year   = '';
$dli_selected_areas  = array();
$dli_search_string   = '';
$dli_all_areas       = dli_get_all_categories_by_ct( THEMATIC_AREA_TAXONOMY, PATENT_POST_TYPE );
$dli_all_area_ids    = $dli_all_areas
	? array_map(
		static function ( $dli_item ) {
			return $dli_item['id'];
		},
		$dli_all_areas
	)
	: array();
$dli_all_years       = DLI_ContentsManager::dli_get_all_patent_years();
$dli_per_page        = strval( DLI_PER_PAGE );
$dli_per_page_values = DLI_PER_PAGE_VALUES;
$dli_allowed_pages   = array_map( 'strval', (array) $dli_per_page_values );

$dli_raw_per_page = filter_input( INPUT_GET, 'per_page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( is_string( $dli_raw_per_page ) ) {
	$dli_raw_per_page = sanitize_text_field( wp_unslash( $dli_raw_per_page ) );
	if ( in_array( $dli_raw_per_page, $dli_allowed_pages, true ) ) {
		$dli_per_page = $dli_raw_per_page;
	}
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
if ( isset( $_GET['thematic_area'] ) && is_array( $_GET['thematic_area'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_selected_areas = array_values(
		array_filter(
			array_map(
				'sanitize_text_field',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
				wp_unslash( $_GET['thematic_area'] )
			)
		)
	);
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
if ( isset( $_GET['search_string'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_search_string = sanitize_text_field( wp_unslash( $_GET['search_string'] ) );
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
if ( isset( $_GET['deposit_year'] ) && is_numeric( $_GET['deposit_year'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_selected_year = sanitize_text_field( wp_unslash( $_GET['deposit_year'] ) );
}

$dli_paged = absint( get_query_var( 'paged' ) );

if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}

if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_params = array(
	'search_string' => $dli_search_string,
	'thematic_area' => $dli_selected_areas ? $dli_selected_areas : $dli_all_area_ids,
	'deposit_year'  => $dli_selected_year ? array( $dli_selected_year ) : $dli_all_years,
	'per_page'      => $dli_per_page,
	'paged'         => $dli_paged,
);

$dli_query       = DLI_ContentsManager::get_patent_data_query( $dli_params );
$dli_num_results = $dli_query->found_posts;
?>

<main id="main-container" role="main">

	<!-- BANNER BREVETTI -->
	<?php get_template_part( 'template-parts/hero/brevetti' ); ?>

	<!-- ELENCO BREVETTI -->
	<section id="brevetti">
		<div class="container p-5">
			<!-- inizio row principale -->
			<div class="row">
				<div class="col-12 col-lg-3 border-bottom pb-3 mb-4 mb-lg-0">
					<form action="<?php echo esc_url( get_permalink() ); ?>" id="brevettiform" method="get">
						<div class="sticky-top pt-3" style="top: 1rem;">
							<!-- COLONNA FILTRI -->

							<!-- FILTRO PER ANNO -->
							<div class="row">
								<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Anno di deposito', 'design_laboratori_italia' ); ?></h3>
								<div class="select-wrapper">
									<label for="deposit_year" class="visually-hidden"><?php echo esc_html__( 'Anno di deposito', 'design_laboratori_italia' ); ?></label>
									<select id="deposit_year" name="deposit_year">
										<option selected="" value=""><?php echo esc_html__( 'Tutti gli anni', 'design_laboratori_italia' ); ?></option>
										<?php foreach ( $dli_all_years as $dli_deposit_year ) : ?>
											<option value="<?php echo esc_attr( $dli_deposit_year ); ?>" <?php selected( $dli_deposit_year, $dli_selected_year ); ?>>
												<?php echo esc_html( $dli_deposit_year ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<!-- FILTRO PER AREA TEMATICA -->
							<div class="row pt-5">
								<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Area tematica', 'design_laboratori_italia' ); ?></h3>
								<div>
									<?php foreach ( $dli_all_areas as $dli_thematic_area ) : ?>
										<div class="form-check">
											<input
												type="checkbox"
												name="thematic_area[]"
												id="<?php echo esc_attr( $dli_thematic_area['slug'] ); ?>"
												value="<?php echo esc_attr( $dli_thematic_area['id'] ); ?>"
												<?php checked( in_array( $dli_thematic_area['id'], $dli_selected_areas, true ) ); ?>
											>
											<label for="<?php echo esc_attr( $dli_thematic_area['slug'] ); ?>"><?php echo esc_html( $dli_thematic_area['name'] ); ?></label>
										</div>
									<?php endforeach; ?>
								</div>
							</div>

							<!-- FILTRO PER RICERCA LIBERA -->
							<div class="row pt-5">
								<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Ricerca libera', 'design_laboratori_italia' ); ?></h3>
								<div class="form-group">
									<label for="search_string" class="visually-hidden"><?php echo esc_html__( 'Cerca contenuto', 'design_laboratori_italia' ); ?></label>
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
									<?php echo esc_html__( 'Filtra', 'design_laboratori_italia' ); ?>
								</button>
							</div>
						</div>
					</form>
				</div>

				<!-- ELENCO BREVETTI -->
				<?php if ( $dli_num_results > 0 ) : ?>
					<!-- inizio contenitore brevetti -->
					<div class="col-12 col-lg-8 offset-lg-1 pt-3">
						<div class="row">
							<?php while ( $dli_query->have_posts() ) : ?>
								<?php
								$dli_query->the_post();

								$dli_summary        = dli_get_field( 'sommario_elenco' );
								$dli_owners         = dli_get_field( 'titolari' );
								$dli_anno_deposito  = dli_get_field( 'anno_deposito' );
								$dli_thematic_area  = dli_get_post_main_category( $post, THEMATIC_AREA_TAXONOMY );
								$dli_image_metadata = dli_get_image_metadata( $post, 'item-card-list' );
								$dli_has_image      = ! empty( $dli_image_metadata['image_url'] );
								?>
								<div class="col-12 mb-4">
									<!--start card-->
									<article class="it-card<?php echo $dli_has_image ? ' it-card-inline it-card-image' : ''; ?> rounded shadow-sm border">
										<?php if ( $dli_has_image ) : ?>
											<div class="it-card-inline-content">
										<?php endif; ?>
											<h3 class="it-card-title h5">
												<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
											</h3>
											<div class="it-card-body">
												<?php if ( $dli_summary ) : ?>
													<p class="it-card-text font-serif"><?php echo wp_kses_post( $dli_summary ); ?></p>
												<?php endif; ?>
												<?php if ( $dli_owners ) : ?>
													<p class="it-card-text font-serif titolari"><em><?php echo esc_html( $dli_owners ); ?></em></p>
												<?php endif; ?>
											</div>
											<?php if ( ( $dli_thematic_area && array_key_exists( 'title', $dli_thematic_area ) ) || $dli_anno_deposito ) : ?>
												<footer class="it-card-footer">
													<?php if ( $dli_thematic_area && array_key_exists( 'title', $dli_thematic_area ) ) : ?>
														<div class="it-card-taxonomy">
															<?php if ( $dli_thematic_area['id'] ) : ?>
																<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'thematic_area', array( $dli_thematic_area['id'] ), $dli_page_permalink ) ); ?>">
																	<span class="visually-hidden"><?php esc_html_e( 'Area tematica:', 'design_laboratori_italia' ); ?></span>
																	<?php echo esc_html( $dli_thematic_area['title'] ); ?>
																</a>
															<?php else : ?>
																<span class="it-card-category">
																	<span class="visually-hidden"><?php esc_html_e( 'Area tematica:', 'design_laboratori_italia' ); ?></span>
																	<?php echo esc_html( $dli_thematic_area['title'] ); ?>
																</span>
															<?php endif; ?>
														</div>
													<?php endif; ?>
													<?php if ( $dli_anno_deposito ) : ?>
														<time class="it-card-date" datetime="<?php echo esc_attr( $dli_anno_deposito ); ?>"><?php echo esc_html( $dli_anno_deposito ); ?></time>
													<?php endif; ?>
												</footer>
											<?php endif; ?>
										<?php if ( $dli_has_image ) : ?>
											</div>
											<div class="it-card-image-wrapper">
												<div class="ratio ratio-16x9">
													<figure class="figure img-full">
														<img
															src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
															title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
															alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
														>
													</figure>
												</div>
											</div>
										<?php endif; ?>
									</article>
									<!-- end card -->
								</div>
							<?php endwhile; ?>
						</div>
					</div>
					<!-- end contenitore brevetti -->
				<?php else : ?>
					<div class="col-12 col-lg-8 offset-lg-1">
						<div class="row pt-2">
							<?php echo esc_html__( 'Non è stato trovato alcun brevetto', 'design_laboratori_italia' ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<!-- end row principale -->
		</div>
		<!-- end container -->
	</section>

	<!-- RESTORE ORIGINAL POST DATA -->
	<?php wp_reset_postdata(); ?>

	<!-- PAGINAZIONE con selettore -->
	<?php
	get_template_part(
		'template-parts/common/paginazione',
		null,
		array(
			'query'           => $dli_query,
			'per_page'        => $dli_per_page,
			'per_page_values' => $dli_per_page_values,
		)
	);
	?>
</main>

<?php get_footer(); ?>
