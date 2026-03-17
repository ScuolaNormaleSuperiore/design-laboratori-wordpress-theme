<?php
/**
 * Template Name: Brevetti
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

define( 'DLI_PATENT_CELLS_PER_ROW', 2 );

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

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_per_page = sanitize_text_field( wp_unslash( $_GET['per_page'] ) );
}

if ( isset( $_GET['thematic_area'] ) && is_array( $_GET['thematic_area'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_selected_areas = array_values(
		array_filter(
			array_map(
				'sanitize_text_field',
				wp_unslash( $_GET['thematic_area'] )
			)
		)
	);
}

if ( isset( $_GET['search_string'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_search_string = sanitize_text_field( wp_unslash( $_GET['search_string'] ) );
}

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

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER BREVETTI -->
	<?php get_template_part( 'template-parts/hero/brevetti' ); ?>

	<!-- ELENCO BREVETTI -->
	<section id="brevetti">
		<div class="container p-5">
			<!-- inizio row principale -->
			<div class="row">
				<div class="col-12 col-lg-3 border-end pb-3">
					<form action="<?php echo esc_url( get_permalink() ); ?>" id="brevettiform" method="get">
						<!-- COLONNA FILTRI -->

						<!-- FILTRO PER ANNO -->
						<div class="row pt-3">
							<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Anno di deposito', 'design_laboratori_italia' ); ?></h3>
							<div class="select-wrapper">
								<label for="deposit_year" class="visually-hidden"><?php echo esc_html__( 'Anno di deposito', 'design_laboratori_italia' ); ?></label>
								<select id="deposit_year" name="deposit_year">
									<option selected="" value=""><?php echo esc_html__( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
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
						<div class="row mt-3 pt-4">
							<h3 class="h6 text-uppercase border-bottom vjs-hidden"><?php echo esc_html__( 'Ricerca libera', 'design_laboratori_italia' ); ?></h3>
							<div>
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-text">
											<svg class="icon icon-sm">
												<title><?php echo esc_html__( 'Cerca contenuto', 'design_laboratori_italia' ); ?></title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-search' ); ?>"></use>
											</svg>
										</span>
										<label for="search_string"><?php echo esc_html__( 'Cerca contenuto', 'design_laboratori_italia' ); ?></label>
										<input type="text" class="form-control" id="search_string" name="search_string" value="<?php echo esc_attr( $dli_search_string ); ?>">
									</div>
								</div>
							</div>
						</div>

						<!-- Pulsante filtro -->
						<div class="row text-center w-100">
							<button type="submit" style="margin: auto;" class="w-50 btn btn-primary">
								<?php echo esc_html__( 'Filtra', 'design_laboratori_italia' ); ?>
							</button>
						</div>
					</form>
				</div>

				<!-- ELENCO BREVETTI 6 per pagina -->
				<?php
				// The main loop of the page.
				$dli_patent_index = 0;
				?>
				<?php if ( $dli_num_results > 0 ) : ?>
					<!-- inizio contenitore brevetti -->
					<div class="col-12 col-lg-9 pt-3">
						<?php while ( $dli_query->have_posts() ) : ?>
							<?php
							$dli_query->the_post();

							if ( 0 === ( $dli_patent_index % DLI_PATENT_CELLS_PER_ROW ) ) :
								?>
								<div class="row pb-5">
									<!-- row -->
							<?php endif; ?>

							<?php
							$dli_summary        = dli_get_field( 'sommario_elenco' );
							$dli_status         = dli_get_field( 'stato_legale_custom' );
							$dli_owners         = dli_get_field( 'titolari' );
							$dli_thematic_area  = dli_get_post_main_category( $post, THEMATIC_AREA_TAXONOMY );
							$dli_image_metadata = dli_get_image_metadata( $post, 'item-card-list' );
							?>
							<div class="col-12 col-lg-6">
								<!-- start card -->
								<div class="card-wrapper shadow">
									<div class="card card-img no-after">
										<?php if ( $dli_image_metadata['image_url'] ) : ?>
											<div class="img-responsive-wrapper">
												<div class="img-responsive img-responsive-panoramic">
													<figure class="img-wrapper">
														<img
															src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
															title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
															alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
														>
													</figure>
												</div>
											</div>
										<?php endif; ?>

										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5"><?php echo esc_html( get_the_title() ); ?></h3>
											<p class="card-text font-serif"><?php echo wp_kses_post( $dli_summary ); ?></p>
											<p class="card-text font-serif titolari">
												<em><?php echo esc_html( $dli_owners ); ?></em>
											</p>
											<p class="card-text font-serif area">
												<?php if ( $dli_thematic_area && array_key_exists( 'title', $dli_thematic_area ) ) : ?>
													<strong><?php echo esc_html( $dli_thematic_area['title'] ); ?></strong> -
												<?php endif; ?>
												<?php echo esc_html( $dli_status ); ?>
											</p>
											<div class="pt-5">
												<a class="read-more" href="<?php echo esc_url( get_permalink() ); ?>">
													<span class="text"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
													<svg class="icon">
														<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
														<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
													</svg>
												</a>
											</div>
										</div>
									</div>
								</div>
								<!-- end card -->
							</div>

							<?php
							if (
								( DLI_PATENT_CELLS_PER_ROW - 1 ) === ( $dli_patent_index % DLI_PATENT_CELLS_PER_ROW ) ||
								( $dli_query->current_post + 1 ) === $dli_query->post_count
							) :
								?>
								</div>
								<!-- end row -->
							<?php endif; ?>

							<?php ++$dli_patent_index; ?>
						<?php endwhile; ?>
					</div>
					<!-- end contenitore brevetti -->
				<?php else : ?>
					<div class="col-12 col-lg-8">
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
