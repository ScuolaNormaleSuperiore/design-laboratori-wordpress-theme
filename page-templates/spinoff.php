<?php
/**
 * Template Name: Spinoff
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

// Permalink della pagina stessa, catturato prima che il loop sui risultati sotto sposti $post: usato per i link di filtro sul settore della card.
$dli_page_permalink = get_permalink();

$dli_selected_year    = '';
$dli_selected_sectors = array();
$dli_search_string    = '';
$dli_all_sectors      = dli_get_all_categories_by_ct( BUSINESS_SECTOR_TAXONOMY, SPINOFF_POST_TYPE );
$dli_all_sector_ids   = $dli_all_sectors
	? array_map(
		static function ( $dli_item ) {
			return $dli_item['id'];
		},
		$dli_all_sectors
	)
	: array();
$dli_all_years        = DLI_ContentsManager::dli_get_all_spinoff_years();
$dli_per_page         = strval( DLI_PER_PAGE );
$dli_per_page_values  = DLI_PER_PAGE_VALUES;

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

	<!-- BANNER SPINOFF -->
	<?php get_template_part( 'template-parts/hero/spinoff' ); ?>

	<!-- ELENCO SPINOFF -->
	<section id="spinoff">
		<div class="container p-5">
			<div class="row">
				<div class="col-12 col-lg-3 border-bottom pb-3 mb-4 mb-lg-0">
					<form action="<?php echo esc_url( get_permalink() ); ?>" id="spinoffform" method="get">
						<div class="sticky-top pt-3" style="top: 1rem;">
							<div class="row">
								<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?></h3>
								<div class="select-wrapper">
									<label for="foundation_year" class="visually-hidden"><?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?></label>
									<select id="foundation_year" name="foundation_year">
										<option selected="" value=""><?php echo esc_html__( 'Tutti gli anni', 'design_laboratori_italia' ); ?></option>
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

				<?php if ( $dli_num_results > 0 ) : ?>
					<div class="col-12 col-lg-8 offset-lg-1 pt-3">
						<div class="row">
							<?php while ( $dli_query->have_posts() ) : ?>
								<?php
								$dli_query->the_post();
								$dli_spinoff_year         = dli_get_field( 'anno_costituzione' );
								$dli_business_sector_main = dli_get_post_main_category( $post, BUSINESS_SECTOR_TAXONOMY );
								$dli_image_metadata       = dli_get_image_metadata( $post, 'item-card-list' );
								$dli_logo                 = dli_get_field( 'logo' );
								$dli_card_image_url       = $dli_logo ? $dli_logo['url'] : $dli_image_metadata['image_url'];
								$dli_card_image_title     = $dli_logo ? $dli_logo['title'] : $dli_image_metadata['image_title'];
								$dli_card_image_alt       = $dli_logo ? $dli_logo['title'] : $dli_image_metadata['image_alt'];
								?>
								<div class="col-12 mb-4">
									<article class="it-card<?php echo $dli_card_image_url ? ' it-card-inline it-card-image' : ''; ?> rounded shadow-sm border">
										<?php if ( $dli_card_image_url ) : ?>
											<div class="it-card-inline-content">
										<?php endif; ?>
											<h3 class="it-card-title h5">
												<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
											</h3>
											<div class="it-card-body">
												<p class="it-card-text"><?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
											</div>
											<?php if ( ( $dli_business_sector_main && array_key_exists( 'title', $dli_business_sector_main ) ) || $dli_spinoff_year ) : ?>
												<footer class="it-card-footer">
													<?php if ( $dli_business_sector_main && array_key_exists( 'title', $dli_business_sector_main ) ) : ?>
														<div class="it-card-taxonomy">
															<?php if ( $dli_business_sector_main['id'] ) : ?>
																<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'business_sector', array( $dli_business_sector_main['id'] ), $dli_page_permalink ) ); ?>">
																	<span class="visually-hidden"><?php esc_html_e( 'Settore:', 'design_laboratori_italia' ); ?></span>
																	<?php echo esc_html( $dli_business_sector_main['title'] ); ?>
																</a>
															<?php else : ?>
																<span class="it-card-category">
																	<span class="visually-hidden"><?php esc_html_e( 'Settore:', 'design_laboratori_italia' ); ?></span>
																	<?php echo esc_html( $dli_business_sector_main['title'] ); ?>
																</span>
															<?php endif; ?>
														</div>
													<?php endif; ?>
													<?php if ( $dli_spinoff_year ) : ?>
														<time class="it-card-date" datetime="<?php echo esc_attr( $dli_spinoff_year ); ?>"><?php echo esc_html( $dli_spinoff_year ); ?></time>
													<?php endif; ?>
												</footer>
											<?php endif; ?>
										<?php if ( $dli_card_image_url ) : ?>
											</div>
											<div class="it-card-image-wrapper">
												<div class="ratio ratio-16x9">
													<figure class="figure img-full">
														<img src="<?php echo esc_url( $dli_card_image_url ); ?>"
															title="<?php echo esc_attr( $dli_card_image_title ); ?>"
															alt="<?php echo esc_attr( $dli_card_image_alt ); ?>">
													</figure>
												</div>
											</div>
										<?php endif; ?>
									</article>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
				<?php else : ?>
					<div class="col-12 col-lg-8 offset-lg-1">
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
