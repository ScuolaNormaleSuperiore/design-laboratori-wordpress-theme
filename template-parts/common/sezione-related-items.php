<?php
	define( 'DLI_RELATED_ITEMS_NUMBER', 3 );
	$related_items = $args['items'];
	$num_items     = dli_get_option("numero_pagine_collegate", "notizie");
	$num_items     = 	$num_items ? 	$num_items : 5;
	$related_items = array_slice( $related_items, 0, $num_items, true);
?>

<section id="sezione-eventi">
	<div class="it-carousel-wrapper it-carousel-landscape-abstract-three-cols splide pt-4" data-bs-carousel-splide>
		<div class="it-header-block">
			<div class="it-header-block-title">
			<h2 class="it-page-section h4 pb-2"><?php echo __( 'Eventi e notizie', 'design_laboratori_italia' ); ?></h2>
			</div>
		</div>
		<div class="splide__track ps-lg-3 pe-lg-3">
			<ul class="splide__list it-carousel-all">
				<?php
				foreach ( $related_items as $item ) {
				?>

					<li class="splide__slide">
						<div class="it-single-slide-wrapper">
							<div class="card-wrapper card-space">
							<?php
							if ( $item->post_type === 'evento' ) {
								get_template_part(
									'template-parts/common/sezione-box-evento',
									null,
									array(
										'item' => $item,
									)
									);
							} else {
								get_template_part(
									'template-parts/common/sezione-box-notizia',
									null,
									array(
										'item' => $item,
									)
									);
							}
							?>
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
