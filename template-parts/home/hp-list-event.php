<?php
/**
 * Homepage event list section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title      = $args['show_title'] ?? false;

if ( 'true' === $dli_section_enabled ) {

	$dli_query = new WP_Query(
		array(
			'post_type'      => array( EVENT_POST_TYPE ),
			'post_status'    => 'publish',
			'meta_key'       => 'data_inizio',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'posts_per_page' => 6,
			'meta_query'     => array(
				array(
					'key'     => 'promuovi_in_home',
					'compare' => '=',
					'value'   => 1,
				),
			),
		)
	);

	$dli_num_items = $dli_query->post_count;
	if ( $dli_num_items > 0 ) {
		?>

	<!-- INIZIO ELENCO EVENTI HP -->
	<section id="blocco-eventi-slide" class="section pt-5" >
		<div class="section-content">
			<div class="container">
				<?php
				if ( 'true' === $dli_show_title ) {
					?>
					<h2 class="h3 pb-2 ">
						<?php echo esc_html__( 'Eventi', 'design_laboratori_italia' ); ?>
					</h2>
					<?php
				}
				?>
				<div class="it-carousel-wrapper splide it-carousel-landscape-abstract-three-cols-arrow-visible" data-bs-carousel-splide>
					<div class="splide__track">
						<ul class="splide__list">

						<?php
						foreach ( $dli_query->posts as $dli_post ) {
							$dli_postitem      = dli_get_post_wrapper( $dli_post );
							$dli_date          = $dli_postitem['date'];
							$dli_item_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
							$dli_item_day      = $dli_item_date ? intval( $dli_item_date->format( 'd' ) ) : '';
							$dli_item_month    = $dli_item_date ? dli_get_monthname( $dli_item_date->format( 'm' ) ) : '';
							$dli_orario_inizio = $dli_postitem['orario_inizio'];
							?>
							<!-- SINGOLO EVENTO -->
							<li class="splide__slide lined_slide">
								<div class="it-single-slide-wrapper h-100">
									<div class="card-wrapper h-100">
										<div class="card card-img">
											<div class="img-responsive-wrapper">
												<div class="img-responsive img-responsive-panoramic">
													<figure class="img-wrapper">
													<img src="<?php echo esc_url( $dli_postitem['image_url'] ); ?>"
														alt="<?php echo esc_attr( $dli_postitem['image_alt'] ); ?>"
														title="<?php echo esc_attr( $dli_postitem['image_title'] ); ?>"
													>
													</figure>
												<?php if ( $dli_item_date ) { ?>
													<div class="card-calendar d-flex flex-column justify-content-center">
														<span class="card-date"><?php echo esc_html( $dli_item_day ); ?></span>
														<span class="card-day"><?php echo esc_html( $dli_item_month ); ?></span>
													</div>
													<?php } ?>
												</div>
											</div>
											<div class="card-body p-4">
												<h3 class="card-title h4"><?php echo esc_html( $dli_postitem['title'] ); ?></h3>
												<p class="card-text font-serif">
												<?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
												</p>
												<a class="read-more" href="<?php echo esc_url( $dli_postitem['link'] ); ?>">
													<span class="text"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
													<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo esc_attr__( 'Leggi di più', 'design_laboratori_italia' ); ?>">
														<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
														<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>">
														</use>
													</svg>
												</a>
											</div>
										</div>
									</div>
								</div>
							</li>
							<!-- FINE SINGOLO EVENTO -->
							<?php
						}
						?>

						</ul>
					</div>
				</div>

				<div class="text-center pt-5">
					<?php
						$dli_page_url = dli_get_translated_page_url_by_slug( SLUG_EVENTI_IT );
					?>
					<a href="<?php echo esc_url( $dli_page_url ); ?>" class="btn btn-secondary">
						<?php echo esc_html__( 'Tutti gli eventi', 'design_laboratori_italia' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE ELENCO EVENTI HP -->
		<?php
	}
}
?>
