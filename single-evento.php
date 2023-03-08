<?php
/**
 * Servizio template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$category   = dli_get_post_main_category( $post, 'category' );
$image_url  = get_the_post_thumbnail_url( 0, 'item-carousel' );
$pg         = dli_get_page_by_post_type( $post->post_type );
$pg_link    = get_permalink( $pg->ID );
$date       = get_field( 'data_inizio', $post );
$event_date = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- DETTAGLIO EVENTO -->
	<div class="container py-lg-5">
		<div class="row">
			<div class="col-12 col-lg-3">
				<div data-bs-toggle="sticky" data-bs-stackable="true">
					<nav class="navbar it-navscroll-wrapper navbar-expand-lg it-bottom-navscroll it-right-side" data-bs-navscroll>
						<button class="custom-navbar-toggler" type="button" aria-controls="navbarNav" aria-expanded="false"
							aria-label="Toggle navigation" data-bs-toggle="navbarcollapsible" data-bs-target="#navbarNav">
							<span class="it-list"></span>1. Introduzione
						</button>
						<div class="progress custom-navbar-progressbar">
							<div	div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
							<svg class="icon icon-sm icon-primary align-top">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left'; ?>" 
								xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left'; ?>"></use>
							</svg>
							<span>Indietro</span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3>DETTAGLI dell'evento</h3>
									<div class="progress">
									<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<li class="nav-item">
											<a class="nav-link active" href="#p1">
											<span>Descrizione&nbsp;&nbsp; </span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p2">
											<span>Luogo</span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p3">
											<span>Contatti</span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p4">
											<span>Allegati&nbsp;</span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p5">
											<span>Video</span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</nav>
				</div>
			</div>
			<div class="col-12 col-lg-9 it-page-sections-container">

			<!-- DESCRIZIONE --> 
			<h3 class="it-page-section h4 visually-hidden" id="p1">Descrizione evento</h3>
				<div class="row pb-3">
				<p>
				Proin placerat ipsum massa, ac commodo velit tempor quis. In ante augue, sodales ac rhoncus in, ultricies a neque. Morbi non semper felis, at lacinia
				nibh. Nam quis elit massa. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam laoreet, diam quis blandit porttitor, leo erat semper
				sem, vel sagittis dolor quam eu magna. Nunc feugiat pretium tempor. Nam eget augue quis tellus viverra malesuada vel ut quam. Cras vehicula rutrum
				vehicula. Suspendisse efficitur eget purus vitae convallis. Integer euismod pharetra lorem, non ullamcorper lorem euismod vel. Orci varius natoque
				penatibus et magnis dis parturient montes, nascetur ridiculus mus.
				</p>
			</div>

			<!-- LUOGO -->
			<h3 class="it-page-section h4 pt-3" id="p2">Luogo</h3>
			<section id="responsabile">    
			<div class="row pb-3 pt-3">
			<p>{Luogo}</p>
			</div>
			</section>

			<!-- CONTATTI -->
			<h3 class="it-page-section h4 pt-3 pb-3" id="p3">{Label}</h3>
			<div class="it-list-wrapper">
			<ul class="it-list">
			<li>
			<div class="list-item">
			<div class="it-rounded-icon">
			<svg class="icon">
				<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone'; ?>"></use>
			</svg>
			</div>
			<div class="it-right-zone"><span class="text">050 509662</span>
			</div>
			</div>
			</li>   
			<li>
			<a href="#" class="list-item">
			<div class="it-rounded-icon">
			<svg class="icon">
				<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail'; ?>"></use>
			</svg>
			</div>
			<div class="it-right-zone"><span class="text">mail@sns.it</span>
			</div>
			</a>
			</li>
			<li>
			<a class="list-item" href="#">
			<div class="it-rounded-icon">
			<svg class="icon">
				<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link'; ?>"></use>
			</svg>
			</div>
			<div class="it-right-zone"><span class="text">www.sns.it</span>
			</div>
			</a>
			</li>
			</ul>
			</div>	

			<!-- ALLEGATI -->
			<h3 class="it-page-section h4 pt-3 pb-3" id="p4">Allegati</h3>
			<section id="allegati">    
			<div class="row ">
				<div class="card-wrapper card-teaser-wrapper">
					<!--start card-->
					<div class="card card-teaser rounded shadow ">
						<div class="card-body">
							<h3 class="card-title h5 ">
								<svg class="icon"><use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf'; ?>"></use></svg>
								<a href="#">Locandina</a>
							</h3>
						</div>
					</div>
					<!--end card-->
				</div>  
			</div>
			</section>

			<!-- VIDEO -->	
			<h3 id="p5" class="it-page-section h4 pt-3">Video</h3>
			<div class="row variable-gutters mb-5">
				<div class="col-lg-9">
					<div class="video-wrapper">
						<iframe title="Intervento del Presidente Draghi alla Firma del Patto per Torino"
						aria-label="Intervento del Presidente Draghi alla Firma del Patto per Torino" width="500"
						height="281" src="https://www.youtube-nocookie.com/embed/s9cYAy-xd6g?feature=oembed"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen=""></iframe>
					</div><!-- /video-wrapper -->
					<div id="accordionDiv1" class="collapse-div transcription-accordion">
						<div class="collapse-header" id="headingA2">
							<button data-toggle="collapse" data-target="#accordion2" aria-expanded="false"
							aria-controls="accordion2">
							Trascrizione del video
							</button>
						</div>
						<div id="accordion2" class="collapse" role="region" aria-labelledby="headingA2" data-parent="#accordionDiv1">
							<div class="collapse-body">
								Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw
								denim
								aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable
								VHS.
							</div>
						</div>
					</div>
				</div><!-- /col-lg-9 -->
			</div><!-- /row -->
			</div>
		</div> <!-- END row -->
	</div>   <!-- END container -->

</main>


<?php
get_footer();
