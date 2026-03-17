<?php
/**
 * Template Name: Spinoff
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

define( 'DLI_SPINOFF_CELLS_PER_ROW', 2 );

$dli_selected_year   = '';
$dli_selected_sectors = array();
$dli_search_string   = '';
$dli_all_sectors     = dli_get_all_categories_by_ct( BUSINESS_SECTOR_TAXONOMY, SPINOFF_POST_TYPE );
$dli_all_sector_ids  = $dli_all_sectors
	? array_map(
		static function ( $dli_item ) {
			return $dli_item['id'];
		},
		$dli_all_sectors
	)
	: array();
$dli_all_years       = DLI_ContentsManager::dli_get_all_spinoff_years();
$dli_per_page        = strval( DLI_PER_PAGE );
$dli_per_page_values = DLI_PER_PAGE_VALUES;

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_per_page = sanitize_text_field( wp_unslash( $_GET['per_page'] ) );
}

if ( isset( $_GET['business_sector'] ) && is_array( $_GET['business_sector'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_selected_sectors = array_values(
		array_filter(
			array_map(
				'sanitize_text_field',
				wp_unslash( $_GET['business_sector'] )
			)
		)
	);
}

if ( isset( $_GET['search_string'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_search_string = sanitize_text_field( wp_unslash( $_GET['search_string'] ) );
}

if ( isset( $_GET['foundation_year'] ) && is_numeric( $_GET['foundation_year'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter.
	$dli_selected_year = sanitize_text_field( wp_unslash( $_GET['foundation_year'] ) );
}

$dli_paged = absint( get_query_var( 'paged' ) );

if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}

if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_params = array(
	'search_string'   => $dli_search_string,
	'business_sector' => $dli_selected_sectors ? $dli_selected_sectors : $dli_all_sector_ids,
	'foundation_year' => $dli_selected_year ? array( $dli_selected_year ) : $dli_all_years,
	'per_page'        => $dli_per_page,
	'paged'           => $dli_paged,
);

$dli_query       = DLI_ContentsManager::get_spinoff_data_query( $dli_params );
$dli_num_results = $dli_query->found_posts;
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER SPINOFF -->
	<?php get_template_part( 'template-parts/hero/spinoff' ); ?>

	<!-- ELENCO SPINOFF -->
	<section id="spinoff">
		<div class="container p-5">
			<div class="row">
				<div class="col-12 col-lg-3 border-end pb-3">
					<form action="<?php echo esc_url( get_permalink() ); ?>" id="spinoffform" method="get">
						<div class="row pt-3">
							<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?></h3>
							<div class="select-wrapper">
								<label for="foundation_year" class="visually-hidden"><?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?></label>
								<select id="foundation_year" name="foundation_year">
									<option selected="" value=""><?php echo esc_html__( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
									<?php foreach ( $dli_all_years as $dli_foundation_year ) : ?>
										<option value="<?php echo esc_attr( $dli_foundation_year ); ?>" <?php selected( $dli_foundation_year, $dli_selected_year ); ?>>
											<?php echo esc_html( $dli_foundation_year ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="row pt-5">
							<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Settore di attività', 'design_laboratori_italia' ); ?></h3>
							<div>
								<?php foreach ( $dli_all_sectors as $dli_business_sector ) : ?>
									<div class="form-check">
										<input
											type="checkbox"
											name="business_sector[]"
											id="<?php echo esc_attr( $dli_business_sector['slug'] ); ?>"
											value="<?php echo esc_attr( $dli_business_sector['id'] ); ?>"
											<?php checked( in_array( $dli_business_sector['id'], $dli_selected_sectors, true ) ); ?>
										>
										<label for="<?php echo esc_attr( $dli_business_sector['slug'] ); ?>"><?php echo esc_html( $dli_business_sector['name'] ); ?></label>
									</div>
								<?php endforeach; ?>
							</div>
						</div>

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

						<div class="row text-center w-100">
							<button type="submit" style="margin: auto;" class="w-50 btn btn-primary">
								<?php echo esc_html__( 'Filtra', 'design_laboratori_italia' ); ?>
							</button>
						</div>
					</form>
				</div>

				<?php
				// The main loop of the page.
				$dli_spinoff_index = 0;
				?>
				<?php if ( $dli_num_results > 0 ) : ?>
					<div class="col-12 col-lg-9 pt-3">
						<?php while ( $dli_query->have_posts() ) : ?>
							<?php
							$dli_query->the_post();

							if ( 0 === ( $dli_spinoff_index % DLI_SPINOFF_CELLS_PER_ROW ) ) :
								?>
								<div class="row pb-5">
							<?php endif; ?>

							<?php
							$dli_spinoff_status       = dli_get_field( 'stato' );
							$dli_spinoff_year         = dli_get_field( 'anno_costituzione' );
							$dli_business_sector_main = dli_get_post_main_category( $post, BUSINESS_SECTOR_TAXONOMY );
							$dli_image_metadata       = dli_get_image_metadata( $post, 'item-card-list' );
							$dli_logo                 = dli_get_field( 'logo' );
							?>

							<div class="col-12 col-lg-6">
								<div class="card-wrapper shadow">
									<div class="card card-img no-after">
										<?php if ( $dli_image_metadata['image_url'] || $dli_logo ) : ?>
											<div class="img-responsive-wrapper">
												<div class="img-responsive img-responsive-panoramic">
													<figure class="img-wrapper">
														<?php if ( $dli_logo ) : ?>
															<img
																src="<?php echo esc_url( $dli_logo['url'] ); ?>"
																title="<?php echo esc_attr( $dli_logo['title'] ); ?>"
																alt="<?php echo esc_attr( $dli_logo['title'] ); ?>"
															>
														<?php else : ?>
															<img
																src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
																title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
																alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
															>
														<?php endif; ?>
													</figure>
												</div>
											</div>
										<?php endif; ?>

										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5"><?php echo esc_html( get_the_title() ); ?></h3>
											<p class="card-text font-serif">
												<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
											</p>
											<p class="card-text font-serif titolari">
												<?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?>:
												<em><?php echo esc_html( $dli_spinoff_year ); ?></em>
											</p>
											<p class="card-text font-serif area">
												<?php if ( $dli_business_sector_main && array_key_exists( 'title', $dli_business_sector_main ) ) : ?>
													<strong><?php echo esc_html( $dli_business_sector_main['title'] ); ?></strong> -
												<?php endif; ?>
												<?php echo esc_html( $dli_spinoff_status ); ?>
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
							</div>

							<?php
							if (
								( DLI_SPINOFF_CELLS_PER_ROW - 1 ) === ( $dli_spinoff_index % DLI_SPINOFF_CELLS_PER_ROW ) ||
								( $dli_query->current_post + 1 ) === $dli_query->post_count
							) :
								?>
								</div>
							<?php endif; ?>

							<?php ++$dli_spinoff_index; ?>
						<?php endwhile; ?>
					</div>
				<?php else : ?>
					<div class="col-12 col-lg-8">
						<div class="row pt-2">
							<?php echo esc_html__( 'Non è stata trovata alcuna spinoff', 'design_laboratori_italia' ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php wp_reset_postdata(); ?>

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
