<?php
/**
 * Servizio template file$start_event_date
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$category         = dli_get_post_main_category( $post, 'category' );
$image_url        = get_the_post_thumbnail_url( 0, 'item-carousel' );
$pg               = dli_get_page_by_post_type( $post->post_type );
$pg_link          = get_permalink( $pg->ID );
$start_date       = get_field( 'data_inizio', $post );
$start_event_date = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $start_date );
$end_date         = get_field( 'data_fine', $post );
$end_event_date   = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $end_date );
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	
	<!-- BANNER NOTIZIA-->
	<section id="banner-news" aria-describedby="Testo introduttivo eventi" class="bg-banner-eventi">
		<div class="section-muted  primary-bg-c1">
			<div class="container">
				<div class="row">
					<div class="col-sm-5 align-middle">
						<div class="hero-title text-left ms-4 pb-3 pt-5 ">
							<p class="card-date">dal <?php echo $start_date; ?> al <?php echo $end_date; ?></p>
							<h2 class="p-0  "><?php echo get_the_title( ); ?></h2>
							<p class="font-weight-normal">
								<?php echo wp_trim_words( get_field( 'descrizione_breve', $last_hero_event ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
							</p>
						</div>
					</div>
					<div class="col-sm-7">
					<img src="<?php echo get_the_post_thumbnail_url( $last_hero_event , 'item-carousel' ); ?>"
							alt="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>" 
							title="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>" 
							alt="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>" 
							class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes"  loading="lazy">
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO EVENTO -->
	<div class="container py-lg-5">
		<div class="row">
			<div class="col-12 col-lg-3">
				<div data-bs-toggle="sticky" data-bs-stackable="true">
					<nav class="navbar it-navscroll-wrapper navbar-expand-lg it-bottom-navscroll it-right-side" data-bs-navscroll>
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
							<span><?php echo __( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo __( 'Dettagli dell\'evento', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
									<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<li class="nav-item">
											<a class="nav-link active" href="#descrizione">
											<span><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#luogo">
											<span><?php echo __( 'Luogo', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#contatti">
											<span><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#allegati">
											<span><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#video">
											<span><?php echo __( 'Video', 'design_laboratori_italia' ); ?></span>
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
			<h3 class="it-page-section h4 visually-hidden" id="descrizione">Descrizione evento</h3>
				<div class="row pb-3">
				<p><?php the_content(); ?></p>
			</div>

			<!-- LUOGO -->
			<h3 class="it-page-section h4 pt-3" id="luogo">Luogo</h3>
			<section id="responsabile">    
			<div class="row pb-3 pt-3">
			<p>{Luogo}</p>
			</div>
			</section>

			<!-- CONTATTI -->
			<h3 class="it-page-section h4 pt-3 pb-3" id="contatti">{Label}</h3>
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
			<h3 class="it-page-section h4 pt-3 pb-3" id="allegati">Allegati</h3>
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
			<h3 id="video" class="it-page-section h4 pt-3">Video</h3>
			<div class="row variable-gutters mb-5">
				<div class="col-lg-9">
					<div class="video-wrapper">
						<iframe title="Intervento del Presidente Draghi alla Firma del Patto per Torino"
							aria-label="Intervento del Presidente Draghi alla Firma del Patto per Torino" width="500"
							height="281" src="https://www.youtube-nocookie.com/embed/s9cYAy-xd6g?feature=oembed"
							frameborder="0"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
							allowfullscreen="">
						</iframe>
					</div>
				</div><!-- /col-lg-9 -->
			</div><!-- /row -->
			</div>
		</div> <!-- END row -->
	</div>   <!-- END container -->

</main>


<?php
get_footer();
