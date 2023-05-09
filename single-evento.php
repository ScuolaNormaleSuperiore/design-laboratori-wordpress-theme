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
$luogo            = dli_get_field( 'luogo' );
$label_contatti   = dli_get_field( 'label_contatti' );
$telefono         = dli_get_field( 'telefono' );
$email            = dli_get_field( 'email' );
$sitoweb          = dli_get_field( 'sitoweb' );
$video            = dli_get_field( 'video' );
$allegato         = dli_get_field( 'allegato' );
$short_descr      = get_field( 'descrizione_breve' );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	
	<!-- BANNER NOTIZIA-->
	<section id="banner-news" aria-describedby="Testo introduttivo eventi" class="bg-banner-eventi">
		<div class="section-muted  primary-bg-c1">
			<div class="container">
				<div class="row">
					<div class="col-sm-5 align-middle">
						<div class="hero-title text-left ms-4 pb-3 pt-5 ">
							<?php
							if ( $start_date === $end_date ){
							?>
							<p class="card-date"><?php echo $start_date; ?></p>
							<?php
							} else {
							?>
							<p class="card-date">
								<?php
									echo __( 'dal', 'design_laboratori_italia' );
									echo ' ' . $start_date;
									echo ' ' . __( 'al', 'design_laboratori_italia' );
									echo ' ' . $end_date;
								?>
								</p>
								<?php
							}
							?>
							<h2 class="p-0  "><?php echo get_the_title( ); ?></h2>
							<p class="font-weight-normal">
								<?php echo wp_trim_words( $short_descr, DLI_ACF_SHORT_DESC_LENGTH ); ?>
							</p>
						</div>
					</div>
					<div class="col-sm-7">
					<img src="<?php echo $image_url; ?>"
							alt="<?php echo esc_attr( get_the_title() ); ?>" 
							title="<?php echo esc_attr( get_the_title() ); ?>" 
							alt="<?php echo esc_attr( get_the_title() ); ?>" 
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
							<svg class="icon icon-sm icon-primary align-top" role="img" aria-labelledby="Chevron left">
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
											if ( 1 == 1 ) {
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
				</div>
			</div>
			<div class="col-12 col-lg-9 it-page-sections-container">
				<!-- DESCRIZIONE --> 
				<h3 class="it-page-section h4 visually-hidden" id="descrizione"><?php echo __( 'Descrizione evento', 'design_laboratori_italia' ); ?></h3>
				<div class="row pb-3">
					<p><?php the_content(); ?></p>
				</div>

				<!-- LUOGO -->
				<?php
				if ( $luogo ) {
				?>
				<h3 class="it-page-section h4 pt-3" id="luogo"><?php echo __( 'Luogo', 'design_laboratori_italia' ); ?></h3>
				<section id="responsabile">
				<div class="row pb-3 pt-3">
				<p><?php echo esc_attr( $luogo ); ?></p>
				</div>
				</section>
				<?php
				}
				?>

				<!-- CONTATTI -->
				<h3 class="it-page-section h4 pt-3 pb-3" id="contatti">
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
									<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail'; ?>"></use>
								</svg>
							</div>
							<div class="it-right-zone"><span class="text"><?php echo esc_attr( $email ); ?></span></div>
							</a>
						</li>
						<?php
							}
							if ( $sitoweb ) {
						?>
						<li>
							<a class="list-item" target="_blank" href="<?php echo $sitoweb; ?>">
							<div class="it-rounded-icon">
								<svg class="icon" role="img" aria-labelledby="Link">
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

				<!-- ALLEGATI -->
				<?php
				if ( $allegato ){
				?>
				<h3 class="it-page-section h4 pt-3 pb-3" id="allegati"><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></h3>
					<div class="row ">
						<div class="card-wrapper card-teaser-wrapper">
							<!--start card-->
							<div class="card card-teaser rounded shadow ">
								<div class="card-body">
									<h3 class="card-title h5 ">
										<svg class="icon" role="img" aria-labelledby="File PDF">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf'; ?>"></use>
										</svg>
										<a target="_blank" href="<?php echo esc_attr( $allegato['url'] ); ?>"><?php echo esc_attr( $allegato['title'] ); ?></a>
									</h3>
								</div>
							</div>
							<!--end card-->
						</div>  
					</div>
				<?php
					}
				?>

				<!-- VIDEO -->
				<?php
				if ( $video ){
				?>
				<h3 id="video" class="it-page-section h4 pt-3"><?php echo __( 'Video', 'design_laboratori_italia' ); ?></h3>
				<div class="row variable-gutters mb-5">
					<div class="col-lg-9">
						<div class="video-wrapper">
							<iframe title="<?php echo get_the_title( ); ?> Video'" width="500"
								height="281" src="<?php echo esc_url( $video ); ?>"
								frameborder="0"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								allowfullscreen="">
							</iframe>
						</div>
					</div><!-- /col-lg-9 -->
				</div><!-- /row -->
				<?php
					}
				?>
			</div>


		</div> <!-- END row -->
	</div>   <!-- END container -->

</main>


<?php
get_footer();
