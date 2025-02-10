<?php
/**
 * Detail page for the post-type: spinoff.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
$image_metadata   = dli_get_image_metadata( $post, 'full' );
$description      = ( $post->post_content === '.' ) ? '' : apply_filters( 'the_content', $post->post_content );
$telefono         = dli_get_field( 'telefono' );
$email            = dli_get_field( 'email' );
$sito_web         = dli_get_field( 'sito_web' );
$note             = dli_get_field( 'note' );
$stato            = dli_get_field( 'stato' );
$year             = dli_get_field( 'anno_costituzione' );
$video            = dli_get_field( 'video' );
$settore_attivita = dli_get_post_main_category( $post, BUSINESS_SECTOR_TAXONOMY );
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
										if ( $year ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#costituzione">
													<span><?php echo __( 'Anno di costituzione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
											}
										if ( $settore_attivita  ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#settoreattivita">
													<span><?php echo __( 'Settore di attività', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
											}
										if ( $stato ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#status">
												<span><?php echo __( 'Stato', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $telefono || $sito_web || $email ) {
												?>
												<li class="nav-item">
													<a class="nav-link" href="#contatti">
														<span><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></span>
													</a>
												</li>
												<?php
												}
										if ( $note ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#altreinformazioni">
												<span><?php echo __( 'Altre informazioni', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $video ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#video">
											<span><?php echo __( 'Video', 'design_laboratori_italia' ); ?></span>
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
				?>
				<?php
				if ( $year ) {
					?>
					<!-- Anno di costituzione -->
					<article id="costituzione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Anno di costituzione', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $year ) ; ?></p>
					</article>
					<?php
				}
				if ( $settore_attivita ) {
					?>
					<!-- Settore di attività -->
					<article id="settoreattivita" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Settore di attività', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $settore_attivita['title'] ) ; ?></p>
					</article>
					<?php
				}
				if ( $stato ) {
				?>
					<!-- Stato -->
				<article id="status" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Stato', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_attr( $stato ) ; ?></p>
				</article>
				<?php
				}
				if ( $contatti || $sito_web ) {
					?>
					<!-- CONTATTI -->
					<article id="contatti" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></h3>

						<div class="it-list-wrapper">
						<ul class="it-list">
							<?php
								if ( $telefono ) {
							?>
							<li>
								<div class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Telephone">
											<title>Telephone</title>
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone'; ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone"><span class="text"><?php echo esc_attr( $telefono ); ?></span></div>
								</div>
							</li>
							<?php
								}
								if ( $email ) {
							?>
							<li>
								<a target="_blank" href="mailto:<?php echo $email; ?>" class="list-item">
								<div class="it-rounded-icon">
									<svg class="icon" role="img" aria-labelledby="Mail">
										<title>Mail</title>
										<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail'; ?>"></use>
									</svg>
								</div>
								<div class="it-right-zone"><span class="text"><?php echo esc_attr( $email ); ?></span></div>
								</a>
							</li>
							<?php
								}
								if ( $sito_web ) {
							?>
							<li>
								<a class="list-item" target="_blank" href="<?php echo $sito_web; ?>">
								<div class="it-rounded-icon">
									<svg class="icon" role="img" aria-labelledby="Link">
										<title>Link</title>
										<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link'; ?>"></use>
									</svg>
								</div>
								<div class="it-right-zone"><span class="text"><?php echo __( 'Sito web', 'design_laboratori_italia' ); ?></span></div>
								</a>
							</li>
							<?php
								}
							?>
						</ul>
					</div>

					</article>
					<?php
					}
				if ( $note ) {
				?>
				<!-- ALTRE INFORMAZIONI -->
				<article id="altreinformazioni" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Altre informazioni', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo $note; ?></p>
				</article>
				<?php
				}
				if ( $video ){
				?>
					<article id="video" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 id="p5" class="it-page-section h4 pt-3"><?php echo __( 'Video', 'design_laboratori_italia' ); ?></h3>

						<?php
							$video_text = null;
							get_template_part(
								'template-parts/common/sezione-video',
								null,
								array(
									'video'       => $video,
									'video_text'  => $video_text,
									'video_title' => get_the_title(),
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
