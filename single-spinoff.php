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
$dli_image_metadata = dli_get_image_metadata( $post, 'full' );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
$dli_description      = ( '.' === $post->post_content ) ? '' : apply_filters( 'the_content', $post->post_content );
$dli_telefono         = dli_get_field( 'telefono' );
$dli_email            = dli_get_field( 'email' );
$dli_sito_web         = dli_get_field( 'sito_web' );
$dli_note             = dli_get_field( 'note' );
$dli_stato            = dli_get_field( 'stato' );
$dli_year             = dli_get_field( 'anno_costituzione' );
$dli_video            = dli_get_field( 'video' );
$dli_settore_attivita = dli_get_post_main_category( $post, BUSINESS_SECTOR_TAXONOMY );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- INIZIO BANNER HERO -->
	<section class="it-hero-wrapper it-dark it-overlay it-hero-small-size"> 
		<!-- - img-->
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<figure class="img-wrapper">
					<?php
					if ( $dli_image_metadata['image_url'] ) {
						?>
					<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
						alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>" 
						title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>" 
						class="d-block mx-lg-auto img-fluid figure-img" loading="lazy">
						<?php
					}
					?>
					<?php
					if ( $dli_image_metadata['image_caption'] ) {
						?>
							<figcaption class="figure-caption"><?php echo esc_html( $dli_image_metadata['image_caption'] ); ?></figcaption>
						<?php
					}
					?>
				</figure>
			</div>
		</div>
		<!-- - texts-->
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<span class="it-Categoria"></span>
						<h2><?php echo esc_html( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block">
						</p>
						<!-- categorie -->
						<div class="chip chip-primary chip-lg chip-simple border-light mt-3">
							<?php
							if ( ! empty( $dli_settore_attivita ) ) {
								?>
							<a class="text-white text-decoration-none"
								href="<?php echo esc_url( site_url() . '/spinoff?business_sector[]=' . $dli_settore_attivita['id'] ); ?>" 
							>
								<span class="chip-label text-light">
								<?php echo esc_html( $dli_settore_attivita['title'] ); ?>
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
									<title><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></title>
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
								</svg>
								<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( 'La Spin-off', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
										</div>
									</div>
									<ul class="link-list">
										<?php
										if ( $dli_description ) {
											?>
											<li class="nav-item">
												<a class="nav-link active" href="#description">
													<span><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_year ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#costituzione">
													<span><?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_settore_attivita ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#settoreattivita">
													<span><?php echo esc_html__( 'Settore di attività', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_stato ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#status">
												<span><?php echo esc_html__( 'Stato', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_telefono || $dli_sito_web || $dli_email ) {
											?>
												<li class="nav-item">
													<a class="nav-link" href="#contatti">
														<span><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></span>
													</a>
												</li>
												<?php
										}
										if ( $dli_note ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#altreinformazioni">
												<span><?php echo esc_html__( 'Altre informazioni', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_video ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#video">
											<span><?php echo esc_html__( 'Video', 'design_laboratori_italia' ); ?></span>
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
				if ( $dli_description ) {
					?>
					<article id="description" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="it-page-section h4 visually-hidden"><?php echo esc_html__( 'Descrizione Spin-off', 'design_laboratori_italia' ); ?></h3>
						<p>
							<?php echo wp_kses_post( $dli_description ); ?>
						</p>
					
					</article>
					<?php
				}
				?>
				<?php
				if ( $dli_year ) {
					?>
					<!-- Anno di costituzione -->
					<article id="costituzione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_html( $dli_year ); ?></p>
					</article>
					<?php
				}
				if ( $dli_settore_attivita ) {
					?>
					<!-- Settore di attività -->
					<article id="settoreattivita" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Settore di attività', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_html( $dli_settore_attivita['title'] ); ?></p>
					</article>
					<?php
				}
				if ( $dli_stato ) {
					?>
					<!-- Stato -->
				<article id="status" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo esc_html__( 'Stato', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_stato ); ?></p>
				</article>
					<?php
				}
				if ( $dli_telefono || $dli_sito_web || $dli_email ) {
					?>
					<!-- CONTATTI -->
					<article id="contatti" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></h3>

						<div class="it-list-wrapper">
						<ul class="it-list">
							<?php
							if ( $dli_telefono ) {
								?>
							<li>
								<div class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Telephone">
											<title>Telephone</title>
											<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_telefono ); ?></span></div>
								</div>
							</li>
								<?php
							}
							if ( $dli_email ) {
								?>
							<li>
								<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( 'mailto:' . sanitize_email( $dli_email ) ); ?>" class="list-item">
								<div class="it-rounded-icon">
									<svg class="icon" role="img" aria-labelledby="Mail">
										<title>Mail</title>
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
									</svg>
								</div>
								<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_email ); ?></span></div>
								</a>
							</li>
								<?php
							}
							if ( $dli_sito_web ) {
								?>
							<li>
								<a class="list-item" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dli_sito_web ); ?>">
								<div class="it-rounded-icon">
									<svg class="icon" role="img" aria-labelledby="Link">
										<title>Link</title>
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link' ); ?>"></use>
									</svg>
								</div>
								<div class="it-right-zone"><span class="text"><?php echo esc_html__( 'Sito web', 'design_laboratori_italia' ); ?></span></div>
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
				if ( $dli_note ) {
					?>
				<!-- ALTRE INFORMAZIONI -->
				<article id="altreinformazioni" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo esc_html__( 'Altre informazioni', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo wp_kses_post( $dli_note ); ?></p>
				</article>
					<?php
				}
				if ( $dli_video ) {
					?>
					<article id="video" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 id="p5" class="it-page-section h4 pt-3"><?php echo esc_html__( 'Video', 'design_laboratori_italia' ); ?></h3>

						<?php
							$dli_video_text = null;
							get_template_part(
								'template-parts/common/sezione-video',
								null,
								array(
									'video'       => $dli_video,
									'video_text'  => $dli_video_text,
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
