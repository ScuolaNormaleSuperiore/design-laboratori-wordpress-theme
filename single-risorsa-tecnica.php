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
$dli_code      = dli_get_field( 'codice_interno' );
$dli_cost      = dli_get_field( 'costo' );
$dli_position  = dli_get_field( 'posizione' );
$dli_location  = dli_get_field( 'localizzazione' );
$dli_brand     = dli_get_field( 'marca' );
$dli_model     = dli_get_field( 'modello' );
$dli_dimension = dli_get_field( 'dimensioni_e_peso' );
$dli_year      = dli_get_field( 'anno_acquisizione' );
$dli_status    = dli_get_field( 'stato' );
// Progetti.
$dli_progetti = DLI_ContentsManager::get_related_items( $post, 'risorse_tecniche', array( PROGETTO_POST_TYPE ) );
// Allegati.
$dli_allegati   = array();
$dli_att_fields = array( 'scheda_tecnica', 'manuale_uso', 'allegato_1', 'allegato_2' );
foreach ( $dli_att_fields as $dli_attachment_field ) {
	$dli_item = dli_get_field( $dli_attachment_field );
	if ( is_array( $dli_item ) && count( $dli_item ) > 0 ) {
		array_push( $dli_allegati, $dli_item );
	}
}
// Tassonomie.
$dli_tipo_risorsa     = dli_get_post_main_category( $post, RT_TYPE_TAXONOMY );
$dli_archive_page_obj = dli_get_page_by_post_type( TECHNICAL_RESOURCE_POST_TYPE );
$dli_archive_page     = $dli_archive_page_obj ? get_permalink( $dli_archive_page_obj->ID ) : '';
// Immagini.
$dli_photo          = dli_get_field( 'foto' );
$dli_image_metadata = dli_get_image_metadata( $post, 'full' );
// Contenuto.
$dli_description = ( '.' === $post->post_content ) ? '' : wpautop( do_shortcode( $post->post_content ) );
// Relazioni.
$dli_responsabili  = dli_get_field( 'responsabile' );
$dli_location_post = null;
if ( is_array( $dli_location ) && ! empty( $dli_location ) ) {
	$dli_location_post = is_object( $dli_location[0] ) ? $dli_location[0] : get_post( $dli_location[0] );
} elseif ( $dli_location ) {
	$dli_location_post = is_object( $dli_location ) ? $dli_location : get_post( $dli_location );
}
$dli_photo_url   = ( is_array( $dli_photo ) && ! empty( $dli_photo['url'] ) ) ? $dli_photo['url'] : $dli_image_metadata['image_url'];
$dli_photo_title = ( is_array( $dli_photo ) && ! empty( $dli_photo['title'] ) ) ? $dli_photo['title'] : get_the_title();
$dli_has_photo   = ! empty( $dli_photo_url );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- INIZIO BANNER HERO -->
		<section id="banner-progetto">
		<div class="p-0 primary-bg-c1">
			<div class="container">
				<div class="row pt-0 pb-0">
					<div class="col-12 col-lg-7">
						<div class="section-title">
							<h2 class="mb-3 mt-3">
								<?php echo esc_html( get_the_title() ); ?>
							</h2>
							<p>&nbsp;</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE BANNER HERO -->


	<!-- BODY -->
	<div class="container p-5">
		<div class="row">

			<!-- Dettagli della risorsa -->
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
							<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>


						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
								<svg class="icon icon-sm icon-primary align-top">
									<title><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></title>
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>" xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
								</svg>
								<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="card-body text-center">
									<?php if ( $dli_has_photo ) { ?>
										<figure class="img-wrapper">
											<img
												style="max-width: 200px; height: auto;"
												src="<?php echo esc_url( $dli_photo_url ); ?>" 
												title="<?php echo esc_attr( $dli_photo_title ); ?>"
												alt="<?php echo esc_attr( $dli_photo_title ); ?>"
											>
											<figcaption class="figure-caption pt-2">
												<?php echo esc_html( get_the_title() ); ?>
											</figcaption>
										</figure>
									<?php } ?>
								</div>
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( 'Risorsa Tecnica', 'design_laboratori_italia' ); ?></h3>
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
										if ( $dli_code ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#codiceinterno">
													<span><?php echo esc_html__( 'Codice interno', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_year ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#acquisizione">
													<span><?php echo esc_html__( 'Anno acquisizione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_tipo_risorsa ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#tiporisorsa">
													<span><?php echo esc_html__( 'Tipo risorsa', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_cost ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#costo">
													<span><?php echo esc_html__( 'Costo', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_location_post ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#localizzazione">
													<span><?php echo esc_html__( 'Localizzazione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_position ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#posizione">
													<span><?php echo esc_html__( 'Posizione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_status ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#status">
													<span><?php echo esc_html__( 'Stato', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_brand || $dli_model ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#marcamodello">
													<span><?php echo esc_html__( 'Marca e modello', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_dimension ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#dimensioni">
													<span><?php echo esc_html__( 'Dimensioni e peso', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( count( $dli_allegati ) > 0 ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#allegati">
													<span><?php echo esc_html__( 'Allegati', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_responsabili ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#responsabili">
													<span><?php echo esc_html__( 'Responsabili', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $dli_progetti ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#progetti">
													<span><?php echo esc_html__( 'Progetti', 'design_laboratori_italia' ); ?></span>
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
						<h3 class="it-page-section h4 visually-hidden"><?php echo esc_html__( 'Descrizione Risorsa Tecnica', 'design_laboratori_italia' ); ?></h3>
						<p>
							<?php echo wp_kses_post( $dli_description ); ?>
						</p>
					
					</article>
					<?php
				}
				if ( $dli_code ) {
					?>
					<!-- Codice interno -->
					<article id="codiceinterno" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Codice interno', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_code ); ?></p>
					</article>
					<?php
				}
				?>
				<?php
				if ( $dli_year ) {
					?>
					<!-- Anno di acquisizione -->
					<article id="acquisizione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Anno di acquisizione', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_year ); ?></p>
					</article>
					<?php
				}
				if ( $dli_tipo_risorsa ) {
					?>
					<!-- Tipo risorsa -->
					<article id="tiporisorsa" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Tipo risorsa', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_tipo_risorsa['title'] ); ?></p>
					</article>
					<?php
				}
				if ( $dli_cost ) {
					?>
					<!-- Costo -->
					<article id="costo" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Costo', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_cost ); ?></p>
					</article>
					<?php
				}
				if ( $dli_location_post ) {
					?>
					<!-- Localizzazione -->
					<article id="localizzazione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Localizzazione', 'design_laboratori_italia' ); ?></h3>
						<p>
							<a href="<?php echo esc_url( get_permalink( $dli_location_post->ID ) ); ?>">
								<?php echo esc_html( $dli_location_post->post_title ); ?>
							</a>
						</p>
					</article>
					<?php
				}
				if ( $dli_position ) {
					?>
					<!-- Posizione -->
					<article id="posizione" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Posizione', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_position ); ?></p>
					</article>
					<?php
				}
				if ( $dli_status ) {
					?>
					<!-- Stato -->
					<article id="status" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Stato', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_status ); ?></p>
					</article>
					<?php
				}
				if ( $dli_brand || $dli_model ) {
					$dli_brand_model = join( ' - ', array( $dli_brand, $dli_model ) );
					?>
					<!-- Marca e modello -->
					<article id="marcamodello" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Marca e modello', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_brand_model ); ?></p>
					</article>
					<?php
				}
				if ( $dli_dimension ) {
					?>
					<!-- Dimensioni -->
					<article id="dimensioni" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Dimensioni e peso', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $dli_dimension ); ?></p>
					</article>
					<?php
				}
				if ( $dli_allegati ) {
					?>
					<!-- Allegati -->
					<article id="allegati" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Allegati', 'design_laboratori_italia' ); ?></h3>
						<section id="ulteriori-info">
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								if ( count( $dli_allegati ) > 0 ) {
									foreach ( $dli_allegati as $dli_allegato ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title><?php echo esc_html__( 'File PDF', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
												</svg>
												<a href="<?php echo esc_url( $dli_allegato['url'] ); ?>">
													<?php echo esc_attr( $dli_allegato['title'] ); ?>&nbsp;
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
				if ( $dli_responsabili ) {
					?>
					<!-- Responsabili -->
					<article id="responsabili" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Responsabili', 'design_laboratori_italia' ); ?></h3>
						<?php
							get_template_part(
								'template-parts/common/sezione-persone',
								null,
								array(
									'section_id' => 'responsabile',
									'items'      => $dli_responsabili,
								)
							);
						?>
					</article>
					<?php
				}
				if ( $dli_progetti ) {
					?>
					<!-- Progetti -->
					<article id="progetti" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo esc_html__( 'Progetti', 'design_laboratori_italia' ); ?></h3>
						<?php
							get_template_part(
								'template-parts/common/sezione-progetti',
								null,
								array(
									'section_id' => 'progetti',
									'items'      => $dli_progetti,
								)
							);
						?>
					</article>
					<?php
				}
				?>



			</div>

		</div> <!-- END row -->
	</div> <!-- END BODY container -->
	
</main>


<?php
get_footer();
