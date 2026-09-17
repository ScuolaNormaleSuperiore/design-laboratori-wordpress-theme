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
$dli_summary   = dli_get_field( 'descrizione_breve' );
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
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
$dli_description = ( '.' === $post->post_content ) ? '' : apply_filters( 'the_content', $post->post_content );
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

	<!-- BANNER RISORSA TECNICA: nessuna foto ampia di copertina, hero a sfondo pieno con
	     breadcrumb integrato non-assoluto (stesso caso di single-spinoff.php). La foto
	     reale della risorsa, quando esiste, non sta qui né in sidebar (troppo stretta per
	     uno sviluppo prevalentemente verticale), ma in una figure a larghezza limitata in
	     testa al corpo: vedi sf-scheda-risorse-tecniche.html. -->
	<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-risorsa-title">
		<div class="container">
			<div class="row align-items-stretch">
				<div class="col-12 col-lg-7">
					<section class="pt-2">
						<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
					</section>
					<div class="it-hero-text-wrapper px-lg-2">
						<h2 id="dli-hero-risorsa-title"><?php echo esc_html( get_the_title() ); ?></h2>
						<?php if ( $dli_summary ) : ?>
							<p class="fs-5"><?php echo esc_html( $dli_summary ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-12 col-lg-5 d-none d-lg-block">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
				</div>
			</div>
		</div>
	</section>


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
								aria-label="<?php echo esc_attr__( 'Toggle navigation', 'design_laboratori_italia' ); ?>"
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
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( 'Dettagli della risorsa tecnica', 'design_laboratori_italia' ); ?></h3>
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
			<div class="col-12 col-lg-8 offset-lg-1 it-page-sections-container">

				<?php if ( $dli_has_photo ) : ?>
					<!-- Foto della risorsa: non in sidebar (troppo stretta per uno sviluppo
					     prevalentemente verticale) né nell'hero (che resta a sfondo pieno).
					     Figure a larghezza limitata in testa al corpo, aspect ratio naturale
					     (nessun crop forzato). -->
					<figure class="figure mb-4" style="max-width: 280px">
						<img src="<?php echo esc_url( $dli_photo_url ); ?>" class="img-fluid rounded shadow-sm" title="<?php echo esc_attr( $dli_photo_title ); ?>" alt="<?php echo esc_attr( $dli_photo_title ); ?>">
						<figcaption class="figure-caption mt-2"><?php echo esc_html( get_the_title() ); ?></figcaption>
					</figure>
				<?php endif; ?>

				<?php if ( $dli_description ) : ?>
					<h3 class="it-page-section h4 visually-hidden" id="description"><?php echo esc_html__( 'Descrizione Risorsa Tecnica', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_description ); ?>
				<?php endif; ?>

				<?php if ( $dli_code ) : ?>
					<h3 class="it-page-section h4 pt-3" id="codiceinterno"><?php echo esc_html__( 'Codice interno', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_code ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_year ) : ?>
					<h3 class="it-page-section h4 pt-3" id="acquisizione"><?php echo esc_html__( 'Anno di acquisizione', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_year ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_tipo_risorsa ) : ?>
					<h3 class="it-page-section h4 pt-3" id="tiporisorsa"><?php echo esc_html__( 'Tipo risorsa', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_tipo_risorsa['title'] ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_cost ) : ?>
					<h3 class="it-page-section h4 pt-3" id="costo"><?php echo esc_html__( 'Costo', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_cost ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_location_post ) : ?>
					<h3 class="it-page-section h4 pt-3" id="localizzazione"><?php echo esc_html__( 'Localizzazione', 'design_laboratori_italia' ); ?></h3>
					<p>
						<a href="<?php echo esc_url( get_permalink( $dli_location_post->ID ) ); ?>">
							<?php echo esc_html( $dli_location_post->post_title ); ?>
						</a>
					</p>
				<?php endif; ?>

				<?php if ( $dli_position ) : ?>
					<h3 class="it-page-section h4 pt-3" id="posizione"><?php echo esc_html__( 'Posizione', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_position ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_status ) : ?>
					<h3 class="it-page-section h4 pt-3" id="status"><?php echo esc_html__( 'Stato', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_status ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_brand || $dli_model ) : ?>
					<h3 class="it-page-section h4 pt-3" id="marcamodello"><?php echo esc_html__( 'Marca e modello', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( join( ' - ', array_filter( array( $dli_brand, $dli_model ) ) ) ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_dimension ) : ?>
					<h3 class="it-page-section h4 pt-3" id="dimensioni"><?php echo esc_html__( 'Dimensioni e peso', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_dimension ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_allegati ) : ?>
					<h3 class="it-page-section h4 pt-3" id="allegati"><?php echo esc_html__( 'Allegati', 'design_laboratori_italia' ); ?></h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-allegati',
						null,
						array(
							'section_id' => 'allegati',
							'items'      => $dli_allegati,
						)
					);
					?>
				<?php endif; ?>

				<?php if ( $dli_responsabili ) : ?>
					<h3 class="it-page-section h4 pt-3" id="responsabili"><?php echo esc_html__( 'Responsabili', 'design_laboratori_italia' ); ?></h3>
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
				<?php endif; ?>

				<?php if ( $dli_progetti ) : ?>
					<h3 class="it-page-section h4 pt-3" id="progetti"><?php echo esc_html__( 'Progetti', 'design_laboratori_italia' ); ?></h3>
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
				<?php endif; ?>

			</div>

		</div> <!-- END row -->
	</div> <!-- END BODY container -->

</main>


<?php
get_footer();
