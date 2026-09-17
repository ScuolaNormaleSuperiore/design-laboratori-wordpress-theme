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
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
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
	<section id="blocco-eventi-slide" class="section pt-5 pb-3">
		<div class="section-content">
			<div class="container">
				<?php if ( 'true' === $dli_show_title ) : ?>
					<h2 id="blocco-eventi-slide-title" class="h3 pb-2"><?php echo esc_html__( 'Eventi', 'design_laboratori_italia' ); ?></h2>
				<?php endif; ?>
				<div class="it-carousel-wrapper splide it-carousel-landscape-abstract-three-cols-arrow-visible" data-bs-carousel-splide role="region" <?php echo ( 'true' === $dli_show_title ) ? 'aria-labelledby="blocco-eventi-slide-title"' : 'aria-label="' . esc_attr__( 'Eventi', 'design_laboratori_italia' ) . '"'; ?>>
					<div class="splide__track">
						<ul class="splide__list">

						<?php
						foreach ( $dli_query->posts as $dli_post ) {
							$dli_postitem  = dli_get_post_wrapper( $dli_post );
							$dli_item_date = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_postitem['date'] );
							$dli_footer_date = dli_get_event_datetime_display( $dli_postitem['id'] );
							?>
							<!-- SINGOLO EVENTO -->
							<li class="splide__slide lined_slide">
								<div class="it-single-slide-wrapper h-100">
									<div class="card-wrapper h-100">
										<article class="it-card it-card-image it-card-height-full rounded shadow-sm border">
											<h3 class="it-card-title h4">
												<a href="<?php echo esc_url( $dli_postitem['link'] ); ?>"><?php echo esc_html( $dli_postitem['title'] ); ?></a>
											</h3>
											<div class="it-card-image-wrapper">
												<div class="ratio ratio-16x9">
													<figure class="figure img-full">
														<img src="<?php echo esc_url( $dli_postitem['image_url'] ); ?>"
															alt="<?php echo esc_attr( $dli_postitem['image_alt'] ); ?>"
															title="<?php echo esc_attr( $dli_postitem['image_title'] ); ?>"
														>
													</figure>
												</div>
											</div>
											<div class="it-card-body p-4">
												<p class="it-card-text font-serif">
												<?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
												</p>
											</div>
											<?php if ( $dli_item_date && $dli_footer_date ) : ?>
												<footer class="it-card-footer">
													<time class="it-card-date" datetime="<?php echo esc_attr( $dli_item_date->format( 'Y-m-d' ) ); ?>">
														<?php echo esc_html( $dli_footer_date ); ?>
													</time>
												</footer>
											<?php endif; ?>
										</article>
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
