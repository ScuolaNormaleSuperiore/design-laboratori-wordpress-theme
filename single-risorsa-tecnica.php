<?php
/**
 * Detail page for the post-type: risorsa-tecnica.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();

// Campi personalizzati.
$code      = dli_get_field( 'codice_interno' );
$cost      = dli_get_field( 'costo' );
$position  = dli_get_field( 'posizione' );
$location  = dli_get_field( 'localizzazione' );
$brand     = dli_get_field( 'marca' );
$model     = dli_get_field( 'modello' );
$dimension = dli_get_field( 'dimensioni_e_peso' );
$year      = dli_get_field( 'anno_acquisizione' );
$status    = dli_get_field( 'stato' );
// Allegati.
$allegati = array();
$att_fields = array( 'scheda_tecnica', 'manuale_uso', 'allegato_1', 'allegato_2' );
foreach ( $att_fields as $af ) {
	$item = dli_get_field( $af );
	if  ( is_array( $item ) && count( $item ) > 0 ) {
		array_push( $allegati, $item );
	}
}
// Tassonomie
$tipo_risorsa = dli_get_post_main_category( $post, TECHNICAL_RESOURCE_POST_TYPE );
// Immagini.
$photo          = dli_get_field( 'foto' );
$image_metadata = dli_get_image_metadata( $post, 'full' );
// Contenuto.
$description = ( $post->post_content === '.' ) ? '' : apply_filters( 'the_content', $post->post_content );
// Relazioni.
$responsabili = dli_get_field( 'responsabile' );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- INIZIO BANNER HERO -->
	<section class="it-hero-wrapper it-dark it-overlay it-hero-small-size"> 
		<!-- - img-->
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<?php
					if ( $image_metadata['image_url'] ) {
					?>
					<img src="<?php echo $image_metadata['image_url']; ?>"
						alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>" 
						title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
						class="d-block mx-lg-auto img-fluid figure-img" loading="lazy">
					>
					<?php
					}
					?>
					<?php
							if( $image_metadata['image_caption'] ) {
						?>
							<figcaption class="figure-caption"><?php echo esc_attr( $image_metadata['image_caption'] ); ?></figcaption>
						<?php
							}
					?>
				</div>
			</div>
		</div>
		<!-- - texts-->
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<span class="it-Categoria"></span>
						<h2><?php echo get_the_title(); ?></h2>
						<p class="d-none d-lg-block">
						</p>
						<!-- categorie -->
						<div class="chip chip-primary chip-lg chip-simple border-light mt-3">
							<?php
								if ( ! empty( $settore_attivita ) ) {
							?>
							<a class="text-white text-decoration-none"
								href="<?php echo esc_url( site_url() . '/spinoff?business_sector[]=' . $settore_attivita['id'] );?>" 
							>
								<span class="chip-label text-light">
									<?php echo esc_attr( $settore_attivita['title'] ); ?>
								</span>
							</a>
							<?php
								}
							?>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE BANNER HERO -->


		<!-- BODY SPINOFF -->
		<div class="container py-lg-5">
		<div class="row">

			<!-- Dettagli dell'spinoff -->
			<div class="col-12 col-lg-3">
				<div data-bs-toggle="sticky" data-bs-stackable="true">
					<nav class="navbar it-navscroll-wrapper navbar-expand-lg it-bottom-navscroll it-right-side" data-bs-navscroll>
						<button
								class="custom-navbar-toggler"
								type="button"
								aria-controls="navbarNav"
								aria-expanded="false"
								aria-label="Toggle navigation"
								data-bs-toggle="navbarcollapsible"
								data-bs-target="#navbarNav"
							>
								<span class="it-list"></span>
						</button>

						<div class="progress custom-navbar-progressbar">
							<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>

						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
								<svg class="icon icon-sm icon-primary align-top">
									<title><?php echo __( 'Indietro', 'design_laboratori_italia' ); ?></title>
									<use href="bootstrap-italia/svg/sprites.svg#it-chevron-left" xlink:href="bootstrap-italia/svg/sprites.svg#it-chevron-left"></use>
								</svg>
								<span><?php echo __( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo __( 'La Spin-off', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
										</div>
									</div>
									<ul class="link-list">
										<?php
										if ( $description ) {
										?>
											<li class="nav-item">
												<a class="nav-link active" href="#description">
													<span><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $code ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#codiceinterno">
													<span><?php echo __( 'Codice interno', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $year ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#acquisizione">
													<span><?php echo __( 'Anno acquisizione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $tipo_risorsa  ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#tiporisorsa">
													<span><?php echo __( 'Tipo risorsa', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $cost ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#costo">
													<span><?php echo __( 'Costo', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $location ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#localizzazione">
													<span><?php echo __( 'Localizzazione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $position ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#posizione">
													<span><?php echo __( 'Posizione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $status ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#status">
													<span><?php echo __( 'Stato', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $brand || $model ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#marcamodello">
													<span><?php echo __( 'Marca e modello', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $dimension ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#dimensioni">
													<span><?php echo __( 'Dimensioni e peso', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( count( $allegati ) > 0 ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#allegati">
													<span><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										if ( $responsabili ) {
										?>
											<li class="nav-item">
												<a class="nav-link" href="#responsabili">
													<span><?php echo __( 'Responsabili', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
										<?php
										}
										?>
									</ul>
								</div>
							</div>
						</div>

					</nav>
				</div>
			</div>

			<!-- DESCRIZIONE -->
			<div class="col-12 col-lg-9 it-page-sections-container">

				<?php
				if ( $description ) {
				?>
					<article id="description" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="it-page-section h4 visually-hidden"><?php echo __( 'Descrizione Spin-off', 'design_laboratori_italia' ); ?></h3>
						<p>
							<?php echo $description; ?>
						</p>
					
					</article>
				<?php
				}
				if ( $code ) {
				?>
					<!-- Codice interno -->
					<article id="codiceinterno" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Codice interno', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $code ) ; ?></p>
					</article>
				<?php
				}
				?>
				<?php
				if ( $year ) {
				?>
					<!-- Anno di acquisizione -->
					<article id="costituzione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Anno di acquisizione', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $year ) ; ?></p>
					</article>
				<?php
				}
				if ( $tipo_risorsa ) {
				?>
					<!-- Tipo risorsa -->
					<article id="tiporisorsa" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Tipo risorsa', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $tipo_risorsa['title'] ) ; ?></p>
					</article>
				<?php
				}
				if ( $cost ) {
				?>
					<!-- Costo -->
					<article id="costo" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Costo', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $cost ) ; ?></p>
					</article>
				<?php
				}
				if ( $location ) {
				?>
					<!-- Localizzazione -->
					<article id="localizzazione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Localizzazione', 'design_laboratori_italia' ); ?></h3>
						<p>
							<a href="<?php echo esc_url( get_permalink( $location[0] ) ); ?>">
								<?php echo esc_attr( $location[0]->post_title ); ?>
							</a>
						</p>
					</article>
				<?php
				}
				if ( $position ) {
				?>
					<!-- Posizione -->
					<article id="posizione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Posizione', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $position ) ; ?></p>
					</article>
				<?php
				}
				if ( $status ) {
				?>
					<!-- Stato -->
					<article id="status" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Stato', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $status ) ; ?></p>
					</article>
				<?php
				}
				if ( $brand || $model ) {
					$brand_model = join( ' - ' , array( $brand, $model ) );
				?>
					<!-- Marca e modello -->
					<article id="marcamodello" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Marca e modello', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $brand_model ) ; ?></p>
					</article>
				<?php
				}
				if ( $dimension ) {
				?>
					<!-- Dimensioni -->
					<article id="dimensioni" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Dimensioni e peso', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dimension ) ; ?></p>
					</article>
				<?php
				}
				if ( $allegati ) {
				?>
					<!-- Allegati -->
					<article id="allegati" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></h3>
						<section id="ulteriori-info">
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								if ( count( $allegati ) > 0 ) {
									foreach ( $allegati as $allegato ) {
								?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title><?php echo __( 'File PDF', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf';?>"></use>
												</svg>
												<a href="<?php echo esc_url( $allegato['url'] ); ?>">
													<?php echo esc_attr( $allegato['title'] ); ?>&nbsp;
												</a>
											</h3>
										</div>
									</div>
									<!--end card-->
								<?php
									}
								}
								?>
								</div> <!--end card wrapper-->
							</div> <!--end row-->
						</section>
					</article>
				<?php
				}
				if ( $responsabili ) {
				?>
					<!-- Responsabili -->
					<article id="responsabili" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Responsabili', 'design_laboratori_italia' ); ?></h3>
						<?php
							get_template_part(
								'template-parts/common/sezione-persone',
								null,
								array(
									'section_id' => 'responsabile',
									'items'      => $responsabili,
								)
							);
						?>
					</article>
				<?php
				}
				?>



			</div>

		</div> <!-- END row -->
	</div> <!-- END container -->
	
</main>


<?php
get_footer();
