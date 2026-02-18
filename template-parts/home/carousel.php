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
					echo '<em>' . esc_html__( 'Indicare in Admin->Configurazione gli articoli da mostrare nel carousel', 'design_laboratori_italia' ) . '&nbsp;.</em>';
				}
				foreach ( $dli_items as $dli_item ) {
					?>
					<!-- Single slide -->
					<li class="splide__slide">
						<div class="it-single-slide-wrapper">
							<div class="img-responsive-wrapper">
								<div class="img-responsive">
									<div class="img-wrapper">
										<a href="<?php echo esc_url( $dli_item['link'] ); ?>">
											<img src="<?php echo esc_url( $dli_item['image_url'] ); ?>" 
												title="<?php echo esc_attr( $dli_item['image_title'] ); ?>"
												alt="<?php echo esc_attr( $dli_item['image_alt'] ); ?>" />
										</a>
									</div>
								</div>
							</div>
							<div class="it-text-slider-wrapper-outside">
								<div class="card-wrapper">
									<div class="card">
										<div class="card-body">
											<div class="category-top">
												<a class="category" href="<?php echo esc_url( $dli_item['category_link'] ); ?>"><?php echo esc_attr( $dli_item['category'] ); ?></a>
												<span class="data"><?php echo esc_attr( $dli_item['date'] ); ?></span>
											<?php
											if ( array_key_exists( 'orario_inizio', $dli_item ) && $dli_item['orario_inizio'] ) {
												?>
												<span class="data"><?php echo esc_attr( $dli_item['orario_inizio'] ); ?></span>
												<?php
											}
											?>
											</div>
											<h3 class="h5 card-title big-heading"><?php echo esc_html( $dli_item['title'] ); ?></h3>
											<p class="card-text"><?php echo esc_html( $dli_item['description'] ); ?></p>
											<?php
											if ( $dli_item['link'] ) {
												?>
											<a class="read-more" href="<?php echo esc_url( $dli_item['link'] ); ?>">
												<span class="text"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
												<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo esc_attr__( 'Leggi di più', 'design_laboratori_italia' ); ?>">
													<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
												</svg>
											</a>
												<?php
											}
											?>
										</div>
									</div>
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
