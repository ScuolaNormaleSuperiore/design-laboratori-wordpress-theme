<?php
/**
 * Homepage carousel section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title      = $args['show_title'] ?? false;

if ( 'true' === $dli_section_enabled ) {
		$dli_items = DLI_ContentsManager::get_carousel_items();
	?>
	<section class="section pt-5 pb-5">
		<div class="it-carousel-wrapper it-carousel-landscape-abstract splide" data-bs-carousel-splide>
			<div class="splide__track">
				<!-- SLIDES -->
				<ul class="splide__list">
				<?php
				if ( count( $dli_items ) === 0 ) {
					?>
					<li class="splide__slide">
						<em><?php echo esc_html__( 'Indicare in Admin->Configurazione gli articoli da mostrare nel carousel', 'design_laboratori_italia' ); ?>.</em>
					</li>
					<?php
				}
				foreach ( $dli_items as $dli_item ) {
					?>
					<!-- Single slide -->
					<li class="splide__slide">
						<div class="it-single-slide-wrapper">
							<div class="img-responsive-wrapper">
								<div class="img-responsive">
									<div class="img-wrapper">
										<img src="<?php echo esc_url( $dli_item['image_url'] ); ?>"
											title="<?php echo esc_attr( $dli_item['image_title'] ); ?>"
											alt="<?php echo esc_attr( $dli_item['image_alt'] ); ?>" />
									</div>
								</div>
							</div>
							<div class="it-text-slider-wrapper-outside">
								<div class="card-wrapper">
									<article class="it-card it-card-height-full rounded shadow-sm border">
										<h3 class="h5 it-card-title big-heading">
											<a href="<?php echo esc_url( $dli_item['link'] ); ?>"><?php echo esc_html( $dli_item['title'] ); ?></a>
										</h3>
										<div class="it-card-body">
											<p class="it-card-text"><?php echo esc_html( $dli_item['description'] ); ?></p>
										</div>
										<?php if ( $dli_item['category'] || $dli_item['date'] ) : ?>
											<footer class="it-card-footer">
												<?php if ( $dli_item['category'] ) : ?>
													<div class="it-card-taxonomy">
														<a class="it-card-category it-card-link" href="<?php echo esc_url( $dli_item['category_link'] ); ?>">
															<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
															<?php echo esc_html( $dli_item['category'] ); ?>
														</a>
													</div>
												<?php endif; ?>
												<?php if ( $dli_item['date'] ) : ?>
													<time class="it-card-date">
														<?php
														echo esc_html( $dli_item['date'] );
														if ( ! empty( $dli_item['orario_inizio'] ) ) {
															echo ' ' . esc_html( $dli_item['orario_inizio'] );
														}
														?>
													</time>
												<?php endif; ?>
											</footer>
										<?php endif; ?>
									</article>
								</div>
							</div>
						</div>
					</li>
					<?php
				}
				?>
				</ul>
			</div>
		</div>
	</section>
	<?php
}
?>
