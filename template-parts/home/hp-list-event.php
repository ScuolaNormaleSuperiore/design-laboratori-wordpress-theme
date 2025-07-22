<?php
$section_enabled = dli_get_option( 'home_event_list_is_visible', 'homepage' );

if ( 'true' === $section_enabled ) {

	$query = new WP_Query(
		array(
			'post_type'      => array( EVENT_POST_TYPE ),
			'status'         => 'publish',
			'meta_key'       => 'data_inizio',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'posts_per_page' => 6,
			// 'meta_query' => array(
			// 	'relation' => 'OR',
			// 	array(
			// 			'key' => 'data_inizio',
			// 			'value' => date('Y-m-d'),
			// 			'compare' => '>=',
			// 			'type' => 'DATE'
			// 	),
			// 	array(
			// 			'key' => 'data_fine',
			// 			'value' => date('Y-m-d'),
			// 			'compare' => '>=',
			// 			'type' => 'DATE'
			// 	)
			// ),
			'meta_query' => array(
				array(
					'key'     => 'promuovi_in_home',
					'compare' => '=',
					'value'   => 1,
				),
			),
		)
	);

	$num_items = $query->post_count;
	if ( $num_items > 0 ) {
?>

	<!-- INIZIO ELENCO EVENTI HP -->
	<section id="blocco-eventi-slide" class="section pt-5" >
		<div class="section-content">
			<div class="container">
				<h2 class="h3 pb-2 ">
					<?php echo __('Eventi', 'design_laboratori_italia' ); ?>
				</h2>

				<div class="it-carousel-wrapper splide it-carousel-landscape-abstract-three-cols-arrow-visible" data-bs-carousel-splide>
					<div class="splide__track">
						<ul class="splide__list">

						<?php
							foreach ( $query->posts as $post ){
								$postitem      = dli_get_post_wrapper( $post );
								$date          = $postitem['date'];
								$item_date     = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
								$orario_inizio = $postitem['orario_inizio']
						?>
							<!-- SINGOLO EVENTO -->
							<li class="splide__slide lined_slide">
								<div class="it-single-slide-wrapper h-100">
									<div class="card-wrapper h-100">
										<div class="card card-img">
											<div class="img-responsive-wrapper">
												<div class="img-responsive img-responsive-panoramic">
													<figure class="img-wrapper">
													<img src="<?php echo esc_url( $postitem['image_url'] ); ?>"
														alt="<?php echo esc_attr( $postitem['image_alt'] ); ?>"
														title="<?php echo esc_attr( $postitem['image_title'] ); ?>"
													>
													</figure>
													<div class="card-calendar d-flex flex-column justify-content-center">
														<span class="card-date"><?php echo intval( $item_date->format( 'd' ) ); ?></span>
														<span class="card-day"><?php echo __( dli_get_monthname( $item_date->format( 'm' ), 'design_laboratori_italia' ) ); ?></span>
													</div>
												</div>
											</div>
											<div class="card-body p-4">
												<h3 class="card-title h4"><?php echo $postitem['title']; ?></h3>
												<p class="card-text font-serif">
													<?php echo esc_html( wp_trim_words( $postitem['description'] , DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
												</p>
												<a class="read-more" href="<?php echo $postitem['link']; ?>">
													<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
													<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>">
														<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
														<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ?>">
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
						$page_url = dli_get_translated_page_url_by_slug( SLUG_EVENTI_IT );
					?>
					<a href="<?php echo  $page_url; ?>" class="btn btn-secondary">
						<?php echo __( 'Tutti gli eventi', 'design_laboratori_italia' ); ?>
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
