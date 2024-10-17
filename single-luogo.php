<?php
/**
 * Detail page for the post-type: luogo.
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

while ( have_posts() ) {
	the_post();
	$ID                = get_the_ID();
	$title             = get_the_title( $ID );
	$image_metadata    = dli_get_image_metadata( $post );
	$descrizione       = get_the_content();
	$posizione         = dli_get_field( 'posizione_gps' );
	$come_raggiungerci = dli_get_field( 'come_raggiungerci' );
	$indirizzo         = dli_get_field( 'indirizzo' );
	$cap               = dli_get_field( 'cap' );
	$orari             = dli_get_field( 'orario_per_il_pubblico' );
	$telefono          = dli_get_field( 'riferimento_telefonico' );
	$mail              = dli_get_field( 'riferimento_mail' );
	$pec               = dli_get_field( 'pec' );

}
?>

	<!-- START CONTENT -->
	<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER  -->
		<section id="banner-luoghi" class="bg-banner-luoghi">
			<div class="section-muted  primary-bg-c1">
				<div class="container">
					<div class="row">
						<div class="col-sm-5 align-middle">
							<div class="hero-title text-left ms-4 pb-3 pt-5 ">
								<h2 class="p-0  "><?php echo esc_attr($title)?>&nbsp;</h2>
								<p class="font-weight-normal"><?php echo esc_attr( dli_get_field( 'descrizione_breve' ) ); ?></p>
							</div>
						</div>
						<div class="col-sm-7">
							<?php
							if ( $image_metadata['image_url'] ) {
							?>
							<figure class="figure">
								<img src="<?php echo $image_metadata['image_url']; ?>" class="d-block mx-lg-auto img-fluid figure-img" 
									alt="<?php echo esc_attr( $image_metadata['image_alt'] )?>"
									title="<?php echo esc_attr( $image_metadata['image_title'] )?>"
									loading="lazy">
								<?php
									if( $image_metadata['image_caption'] ) {
									?>
									<figcaption class="figure-caption"><?php echo esc_attr( $image_metadata['image_caption'] ); ?></figcaption>
									<?php
									}
									?>
							</figure>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<div class="container py-lg-5">
			<div class="row">
				<!--SIDEBAR -->
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
								<span class="it-list"></span>1. Introduzione
							</button>
							<div class="progress custom-navbar-progressbar">
								<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="navbar-collapsable" id="navbarNav">
								<div class="overlay"></div>
								<a class="it-back-button" href="#" role="button">
									<svg class="icon icon-sm icon-primary align-top" role="img" aria-labelledby="Chevron Left">
										<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ?>"></use>
									</svg>
									<span>Indietro</span>
								</a>
								<div class="menu-wrapper">
									<div class="link-list-wrapper">
										<h3><?php echo __( 'Dettagli del luogo', 'design_laboratori_italia' ); ?></h3>
										<div class="progress">
											<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<ul class="link-list">
										<?php if ( $descrizione != '' ) {
											?>
											<li class="nav-item">
												<a class="nav-link active" href="#p1">
													<span><?php _e( 'Descrizione', "design_laboratori_italia" ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( !str_contains( $posizione, 'data-map-markers="[]">' ) ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#p2">
													<span><?php _e( 'Posizione', "design_laboratori_italia" ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $come_raggiungerci != '' ) { 
											?>
											<li class="nav-item">
												<a class="nav-link" href="#p3">
													<span><?php _e( 'Come raggiungerci', "design_laboratori_italia" ); ?></span>
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
				<!-- CORPO CENTRALE -->
				<div class="col-12 col-lg-9 col-md-9 it-page-sections-container">
					<!-- DESCRIZIONE -->
					<?php
					if ( $descrizione != '' ) {
					?>
					<div class="row pb-3">
						<h3 class="it-page-section h4 visually-hidden" id="p1"><?php _e( 'Descrizione luogo', "design_laboratori_italia" ); ?></h3>
						<p><?php echo $descrizione; ?></p>
					</div>
					<?php
					}
					?>
					<!-- DOVE -->
					<?php
					if ( !str_contains( $posizione, 'data-map-markers="[]">' ) ) {
						?>
						<div class="row mb-5">
							<h3 id="p2" class="it-page-section h4"><?php _e( 'Posizione', "design_laboratori_italia" ); ?></h3>  
							<!--start card-->
							<div class="card-wrapper">
								<div class="card card-img no-after">
									<div class="img-responsive-wrapper">
										<?php
										echo $posizione;
										?>
									</div>
									<?php
					}
					?>
					<!-- START LISTA DATI INDIRIZZO -->
					<div class="it-list-wrapper">
						<ul class="it-list">
							<?php if( $indirizzo != '' ) {
								?>
							<li>
								<div class="list-item">
									<div class="visually-hidden">
										Indirizzo
									</div>
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Map Marker">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-map-marker'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-map-marker' ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone"><span class="text"><?php echo esc_html( $indirizzo ) . ', ' . esc_html( $cap ); ?></span></div>
								</div>
							</li>
							<?php }
							if( $orari != '' ) {
								?>
							<li>
								<div class="list-item">
									<div class="visually-hidden">
										Orari
									</div>
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Clock">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-clock'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-clock' ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone"><span class="text"><?php echo esc_html( $orari ); ?></span></div>
								</div>
							</li>
							<?php
							}
							if($telefono != '' ) {
							?>
							<li>
								<div class="visually-hidden">
									Telefono
								</div>
								<div class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Telephone">
										<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ?>"></use>
									</svg>
								</div>
								<div class="it-right-zone"><span class="text"><?php echo esc_html( $telefono ); ?></span></div>
								</div>
							</li>
							<?php
								}
							if($mail != '' ) {
								?>
							<li>
								<div class="visually-hidden">
									Email
								</div>
								<a href="mailto:<?php echo esc_attr( $mail ); ?>" class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Mail">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone"><span class="text"><?php echo esc_attr( $mail ); ?></span></div>
								</a>
							</li>
							<?php
								}
							if($pec != '' ) {
								?>
							<li>
								<div class="visually-hidden">
									PEC
								</div>
								<a href="mailto:<?php echo esc_attr( $pec ); ?>" class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Mail">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone"><span class="text"><?php echo esc_attr( $pec ); ?></span></div>
								</a>
							</li>
							<?php
								}
							?>
						</ul>
					</div>
					<!-- FINE LISTA DATI INDIRIZZO -->
					<!-- COME RAGGIUNGERCI -->
					<?php
					if ( $come_raggiungerci != '' ) {
						?>
					<div class="row pb-3">
						<h3 class="it-page-section h4" id="p3"><?php _e( 'Come raggiungerci', "design_laboratori_italia" ); ?></h3>
						<p><?php echo esc_html( $come_raggiungerci ); ?></p>
					</div>
						<?php
					}
					?>
					</div> <!--end row-->
				</div>
			</div>
			<!--end card-->
			</div>
		</div> <!-- END row -->
	</div>   <!-- END container -->
	</main>
<!-- END CONTENT -->

<?php
get_footer();

