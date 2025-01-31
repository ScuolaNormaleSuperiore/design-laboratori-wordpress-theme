<?php
/**
 * Detail page for the post-type: evento.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$categories         = dli_get_post_categories( $post, 'category' );
$current_lang       = dli_current_language();
$cat_page           = DLI_PAGE_PER_CT[EVENT_POST_TYPE][$current_lang];
$image_metadata     = dli_get_image_metadata( $post );
$pg                 = dli_get_page_by_post_type( $post->post_type );
$pg_link            = get_permalink( $pg->ID );
$start_date         = dli_get_field( 'data_inizio', $post );
$tmstp_start        = strtotime( str_replace('/', '-', $start_date ) );
$start_day          = $tmstp_start ? date( 'd', $tmstp_start ) : '';
$start_month_number = $tmstp_start ? date( 'm', $tmstp_start ) : '';
$start_month_name   = $start_month_number ? dli_get_monthname_short( $start_month_number ) : '';
$start_event_date   = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $start_date );
$orario_inizio      = dli_get_field( 'orario_inizio', $post );
$orario_inizio      = $orario_inizio ? $orario_inizio : '';
$end_date           = dli_get_field( 'data_fine', $post );
$tmstp_end          = strtotime( str_replace('/', '-', $end_date  ) );
$end_day            = $tmstp_start ? date( 'd', $tmstp_end ) : '';
$end_month_number   = $tmstp_start ? date( 'm', $tmstp_end ) : '';
$end_month_name     = $start_month_number ? dli_get_monthname_short( $end_month_number ) : '';
$end_event_date     = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $end_date );
$orario_fine        = dli_get_field( 'orario_fine', $post );
$orario_fine        = $orario_fine ? $orario_fine : '';
$same_date          = ( $start_date === $end_date ) ? true : false;
$luogo              = dli_get_field( 'luogo' );
$label_contatti     = dli_get_field( 'label_contatti' );
$telefono           = dli_get_field( 'telefono' );
$email              = dli_get_field( 'email' );
$sito_web           = dli_get_field( 'sitoweb' );
$video              = dli_get_field( 'video' );
$allegato           = dli_get_field( 'allegato' );
$short_descr        = dli_get_field( 'descrizione_breve' );
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
					<img src="<?php echo $image_metadata['image_url']; ?>"
						alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>" 
						title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
						class="d-block mx-lg-auto img-fluid figure-img" loading="lazy">
					>
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
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<!-- Data -->
						<span class="it-Categoria">
							<?php
								if ( $start_date === $end_date ){
								?>
								<p class="card-date">
									<?php 
										echo $start_date; 
										if ( $orario_inizio ) {
											echo ', ' . $orario_inizio;
										}
									?>
								</p>
								<?php
								} else {
								?>
								<p class="card-date">
									<?php
										echo __( 'dal', 'design_laboratori_italia' );
										echo ' ' . $start_date;
										echo ' ' . __( 'al', 'design_laboratori_italia' );
										echo ' ' . $end_date;
										if ( $orario_inizio ) {
											echo ', ' . $orario_inizio;
										}
									?>
									</p>
									<?php
								}
								?>
						</span>
						<!-- Titolo -->
						<h2><?php echo get_the_title(); ?></h2>
						<!-- Testo -->
						<p class="d-none d-lg-block">
							<?php echo wp_trim_words( $short_descr, DLI_ACF_SHORT_DESC_LENGTH ); ?>
						</p>
						<!-- Categorie -->
						<?php
								foreach( $categories as $category ) {
									$cat_url = add_query_arg( 'cat', array( $category['id'] ), get_site_url() . '/' . $cat_page );
							?>
							<div class="chip chip-primary chip-lg chip-simple border-light mt-3">
								<a class="text-decoration-none" href="<?php echo $cat_url ?>">
									<span class="chip-label"><?php echo esc_attr( $category['title'] ); ?></span>
								</a>
							</div>
							<?php
								}
							?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE BANNER HERO-->



	<!-- BODY EVENTO -->
	<div class="container py-lg-5">
		<div class="row">

			<!-- Dettagli dell'evento -->
			<div class="col-12 col-lg-3">
				<div data-bs-toggle="sticky" data-bs-stackable="true">
					<nav class="navbar it-navscroll-wrapper navbar-expand-lg it-bottom-navscroll it-right-side" data-bs-navscroll>
						<div class="progress custom-navbar-progressbar">
							<div	div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
							<svg class="icon icon-sm icon-primary align-top" role="img" aria-labelledby="Chevron left">
								<title><?php echo __( 'Indietro', 'design_laboratori_italia' ); ?></title>
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
										<?php
											if ( $short_descr ) {
										?>
										<li class="nav-item">
											<a class="nav-link active" href="#descrizione">
											<span><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<li class="nav-item"> <a class="nav-link" href="#date_e_orari"> <span><?php echo __( 'Date e orari', 'design_laboratori_italia' ); ?></span> </a> </li>
										<?php
											}
											if ( $luogo ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#luogo">
											<span><?php echo __( 'Luogo', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										</li>
										<?php
											}
											if ( $telefono || $email || $sito_web ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#contatti">
											<span><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
											}
											if ( $allegato ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#allegati">
											<span><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></span>
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
					<!-- Condividi -->
					<?php get_template_part( 'template-parts/common/social_sharing'); ?>
				</div>
			</div>

			<!-- Colonna destra --> 
			<div class="col-12 col-lg-9 it-page-sections-container">


				<!-- Descrizione -->
				<article id="descrizione" class="it-page-section mb-4 anchor-offset clearfix">
					<p><?php the_content(); ?></p>
				</article>

				<!-- DATE E ORARI -->
				<article id="date_e_orari" class="it-page-section mb-4 anchor-offset clearfix">
					<h4><?php echo __( 'Date e orari', 'design_laboratori_italia' ); ?></h4>
					<div class="point-list-wrapper my-4">
						<!-- data inizio -->
						<div class="point-list">
							<div class="point-list-aside point-list-warning">
								<div class="point-date text-monospace">
									<?php echo $start_day; ?>
								</div>
								<div class="point-month text-monospace">
									<?php echo $start_month_name; ?>
								</div>
							</div>
							<div class="point-list-content">
								<?php
									if ( ( $orario_inizio ) || ( $orario_fine && $same_date ) ) {
								?>
								<div class="card card-teaser shadow p-4 rounded border">
									<div class="card-body">
										<h5 class="card-title">
											<?php
											if ( $orario_inizio ) {
											?>
											<?php echo __( 'Inizio evento', 'design_laboratori_italia' ); ?> <?php echo $orario_inizio; ?>
											<?php
											}
											if ( $orario_inizio && $orario_fine && $same_date ) {
												echo '&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;';
											}
											if ( $orario_fine && $same_date ) {
												echo __( 'Fine evento', 'design_laboratori_italia' );
												echo $orario_fine;
											}
											?>
										</h5>
									</div>
								</div>
								<?php
									}
								?>
							</div>
						</div>
						<?php
						if ( ! $same_date ){
						?>
						<!-- data fine -->
						<div class="point-list">
							<div class="point-list-aside point-list-warning">
								<div class="point-date text-monospace">
									<?php echo $end_day; ?>
								</div>
								<div class="point-month text-monospace">
									<?php echo $end_month_name; ?>
								</div>
							</div>
							<div class="point-list-content">
								<?php
									if ( $orario_fine ) {
								?>
								<div class="card card-teaser shadow p-4 rounded border">
									<div class="card-body">
										<h5 class="card-title">
											<?php
											if ( $orario_fine ) {
											?>
											<?php echo __( 'Fine evento', 'design_laboratori_italia' ); ?> <?php echo $orario_fine; ?>
											<?php
											}
											?>
										</h5>
									</div>
								</div>
								<?php
									}
								?>
							</div>
						</div>
						<?php
						}
						?>
					</div>
				</article>
	
				<!-- LUOGO -->
				<?php
				if ( $luogo ) {
				?>
					<article id="luogo" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="it-page-section h4 pt-3" id="luogo-title"><?php echo __( 'Luogo', 'design_laboratori_italia' ); ?></h3>
						<div class="row pb-3 pt-3">
						<p><?php echo esc_attr( $luogo ); ?></p>
						</div>
					</article>
				<?php
				}
				?>

				<!-- CONTATTI -->
				<article id="contatti" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="it-page-section h4 pt-3 pb-3" id="contatti-title">
						<?php
						if ( $label_contatti ) {
						?>
						<span><?php echo esc_attr( $label_contatti ) ?></span>
						<?php
						} else {
						?>
						<span><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></span>
						<?php
						}
						?>
					</h3>
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

				<!-- ALLEGATI -->
				<?php
				if ( $allegato ){
				?>
					<article id="allegati" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="it-page-section h4 pt-3 pb-3" id="allegati-title"><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></h3>
							<div class="row ">
								<div class="card-wrapper card-teaser-wrapper">
									<!--start card-->
									<div class="card card-teaser rounded shadow border">
										<div class="card-body">
											<h3 class="card-title h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf'; ?>"></use>
												</svg>
												<a target="_blank" href="<?php echo esc_attr( $allegato['url'] ); ?>"><?php echo esc_attr( $allegato['title'] ); ?></a>
											</h3>
										</div>
									</div>
									<!--end card-->
								</div>  
							</div>
					</article>
				<?php
					}
				?>

				<!-- VIDEO -->
				<?php
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
			<!-- END Colonna destra -->

		</div> <!-- END row -->
	</div>   <!-- END container -->

</main>


<?php
get_footer();
