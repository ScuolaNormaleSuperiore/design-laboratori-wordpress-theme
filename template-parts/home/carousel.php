<?php
	$items = dli_get_carousel_items();
?>

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
					<a href="#">
						<div class="img-responsive-wrapper">
							<div class="img-responsive">
								<div class="img-wrapper">
									<img src="<?php echo $item['image_url']; ?>" title="titolo immagine" alt="descrizione immagine" />
								</div>
							</div>
						</div>
					</a>
					<div class="it-text-slider-wrapper-outside">
						<div class="card-wrapper">
							<div class="card">
								<div class="card-body">
									<div class="category-top">
										<a class="category" href="#"><?php echo $item['category']; ?></a>
										<span class="data"><?php echo $item['date']; ?></span>
									</div>
									<h5 class="card-title big-heading"><?php echo $item['title']; ?></h5>
									<p class="card-text"><?php echo $item['description']; ?></p>
									<a class="read-more" href="<?php echo $item['link']; ?>">
										<span class="text"><?php echo __( 'Leggi di piu', 'design_laboratori_italia' ); ?></span>
										<svg class="icon">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ?>"></use>
										</svg>
									</a>
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