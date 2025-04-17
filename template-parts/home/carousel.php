<?php
	$home_carousel_enabled = dli_get_option( 'home_carousel_is_visible', 'homepage' );

	if ( 'true' === $home_carousel_enabled ) {
		$items = dli_get_carousel_items();
?>
	<section class="section pt-5 pb-5">
		<div class="it-carousel-wrapper it-carousel-landscape-abstract splide" data-bs-carousel-splide>
			<div class="splide__track">
				<!-- SLIDES -->
				<ul class="splide__list">
				<?php
					if ( count( $items ) === 0 ) {
						echo '<em>' . __( 'Indicare in Admin->Configurazione gli articoli da mostrare nel carousel', 'design_laboratori_italia' ) . '&nbsp;.</em>';
					}
					foreach ( $items as $item ) {
				?>
					<!-- Single slide -->
					<li class="splide__slide">
						<div class="it-single-slide-wrapper">
							<div class="img-responsive-wrapper">
								<div class="img-responsive">
									<div class="img-wrapper">
										<a href="<?php echo esc_attr( $item['link'] ); ?>">
											<img src="<?php echo esc_url( $item['image_url'] ); ?>" 
												title="<?php echo esc_attr( $item['image_title'] ); ?>"
												alt="<?php echo esc_attr( $item['image_alt'] ); ?>" />
										</a>
									</div>
								</div>
							</div>
							<div class="it-text-slider-wrapper-outside">
								<div class="card-wrapper">
									<div class="card">
										<div class="card-body">
											<div class="category-top">
												<a class="category" href="<?php echo esc_url( $item['category_link'] ); ?>"><?php echo esc_attr( $item['category'] ); ?></a>
												<span class="data"><?php echo esc_attr( $item['date'] ); ?></span>
												<?php if (  array_key_exists( 'orario_inizio', $item) && $item['orario_inizio'] ) {
												?>
												<span class="data"><?php echo esc_attr( $item['orario_inizio'] ); ?></span>
											<?php
											}
											?>
											</div>
											<h3 class="h5 card-title big-heading"><?php echo $item['title']; ?></h3>
											<p class="card-text"><?php echo esc_attr( $item['description'] ); ?></p>
											<?php
											if ( $item['link'] ) {
											?>
											<a class="read-more" href="<?php echo esc_attr( $item['link'] ); ?>">
												<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
												<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>">
													<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ) ?>"></use>
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
