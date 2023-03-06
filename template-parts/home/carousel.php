<?php
	$items = dli_get_carousel_items();
?>
<section class="section pt-5 pb-5">
	<div class="it-carousel-wrapper it-carousel-landscape-abstract splide " data-bs-carousel-splide>
		<div class="splide__track">
			<!-- SLIDES -->
			<ul class="splide__list">
			<?php
				if ( count( $items ) === 0 ) {
					echo '<em>' . __( 'Indicare in Admin->Configurazione gli articoli da mostrare nel carousel.', 'design_laboratori_italia' ) . '</em>';
				}
				foreach ( $items as $item ) {
			?>
				<!-- Single slide -->
				<li class="splide__slide">
					<div class="it-single-slide-wrapper">
						<a href="<?php echo esc_attr( $item['link'] ); ?>">
							<div class="img-responsive-wrapper">
								<div class="img-responsive">
									<div>
										<img src="<?php echo esc_url( $item['image_url'] ); ?>" title="<?php echo esc_attr( $item['image_title'] ); ?>" alt="<?php echo esc_attr( $item['image_alt'] ); ?>" />
									</div>
								</div>
							</div>
						</a>
						<div class="it-text-slider-wrapper-outside">
							<div class="card-wrapper">
								<div class="card">
									<div class="card-body">
										<div class="category-top">
											<a class="category" href="<?php echo esc_url( $item['category_link'] ); ?>"><?php echo esc_attr( $item['category'] ); ?></a>
											<span class="data"><?php echo esc_attr( $item['date'] ); ?></span>
										</div>
										<h5 class="card-title big-heading"><?php echo $item['title']; ?></h5>
										<p class="card-text"><?php echo esc_attr( $item['description'] ); ?></p>
										<?php
										if ( $item['link'] ) {
										?>
										<a class="read-more" href="<?php echo esc_attr( $item['link'] ); ?>">
											<span class="text"><?php echo __( 'Leggi di piu', 'design_laboratori_italia' ); ?></span>
											<svg class="icon">
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
