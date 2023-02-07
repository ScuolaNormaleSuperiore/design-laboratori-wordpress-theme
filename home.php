<?php
/**
 * The template for displaying home
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();
?>
<main id="main-container" class="main-container redbrown">

<!-- CAROUSEL -->
<section class="section pt-5 pb-5">
<div class="it-carousel-wrapper it-carousel-landscape-abstract splide " data-bs-carousel-splide>
	<div class="splide__track">
		<!-- SLIDES -->
		<ul class="splide__list">
			<!-- Single slide -->
			<li class="splide__slide">
				<div class="it-single-slide-wrapper">
					<a href="#">
						<div class="img-responsive-wrapper">
							<div class="img-responsive">
								<div class="img-wrapper">
									<img src="<?php echo get_template_directory_uri() . '/assets/img/yourimage.png' ?>" title="titolo immagine" alt="descrizione immagine" />
								</div>
							</div>
						</div>
					</a>
					<div class="it-text-slider-wrapper-outside">
						<div class="card-wrapper">
							<div class="card">
								<div class="card-body">
									<div class="category-top">
										<a class="category" href="#">Categoria</a>
										<span class="data">10/12/2023</span>
									</div>
									<h5 class="card-title big-heading">Lorem ipsum dolor sit amet, consectetur adipiscing elit…</h5>
									<p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
									<span class="card-signature">di Mario Rossi</span>
									<a class="read-more" href="#">
										<span class="text">Leggi di più <span class="visually-hidden">Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span></span>
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
		</ul>
	</div>
</div>
</section>

</main>
<?php
get_footer();
