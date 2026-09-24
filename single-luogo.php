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
	$dli_id             = get_the_ID();
	$dli_title          = get_the_title( $dli_id );
	$dli_image_metadata = dli_get_image_metadata( $post );
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
	$dli_descrizione       = ( '.' === $post->post_content ) ? '' : apply_filters( 'the_content', $post->post_content );
	$dli_posizione         = dli_get_field( 'posizione_gps' );
	$dli_come_raggiungerci = dli_get_field( 'come_raggiungerci' );
	$dli_indirizzo         = dli_get_field( 'indirizzo' );
	$dli_cap               = dli_get_field( 'cap' );
	$dli_orari             = dli_get_field( 'orario_per_il_pubblico' );
	$dli_telefono          = dli_get_field( 'riferimento_telefonico' );
	$dli_mail              = dli_get_field( 'riferimento_mail' );
	$dli_pec               = dli_get_field( 'pec' );
}
?>

	<!-- START CONTENT -->
	<main id="main-container" role="main">

	<!-- BANNER LUOGO: hero a due colonne (testo + immagine in evidenza), stesso
		pattern di single-notizia.php: l'immagine di un luogo, quando esiste, resta
		al proprio formato naturale (nessun object-fit forzato) in una colonna
		dedicata invece di un crop forzato a piena pagina; il post type "luogo" non
		ha un campo immagine ACF dedicato, solo l'eventuale featured image. -->
	<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-luogo-title">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-7">
					<section class="pt-2">
						<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
					</section>
					<div class="it-hero-text-wrapper px-lg-2">
						<h2 id="dli-hero-luogo-title"><?php echo esc_html( $dli_title ); ?></h2>
						<?php if ( dli_get_field( 'descrizione_breve' ) ) : ?>
							<p class="fs-5"><?php echo wp_kses_post( dli_get_field( 'descrizione_breve' ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-12 col-lg-5 d-none d-lg-block">
					<?php if ( $dli_image_metadata['image_url'] ) : ?>
						<figure class="figure mb-0">
							<img class="figure-img img-fluid rounded mb-0"
								src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
								alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>">
							<?php if ( $dli_image_metadata['image_caption'] ) : ?>
								<figcaption class="figure-caption mt-2 text-light"><?php echo esc_html( $dli_image_metadata['image_caption'] ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php elseif ( dli_show_hero_decorative_image() ) : ?>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
					<?php endif; ?>
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
							aria-label="<?php echo esc_attr__( 'Toggle navigation', 'design_laboratori_italia' ); ?>"
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
								<svg class="icon icon-sm icon-primary align-top" role="img">
									<title><?php echo esc_html__( 'Chevron Left', 'design_laboratori_italia' ); ?></title>
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>" xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
								</svg>
								<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( 'Dettagli del luogo', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
									<?php
									if ( '' !== $dli_descrizione ) {
										?>
										<li class="nav-item">
											<a class="nav-link active" href="#descrizione">
												<span><?php esc_html_e( 'Descrizione', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
									}
									if ( is_string( $dli_posizione ) && ! str_contains( $dli_posizione, 'data-map-markers="[]">' ) ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#posizione">
												<span><?php esc_html_e( 'Posizione', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
									}
									if ( '' !== $dli_come_raggiungerci ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#come-raggiungerci">
												<span><?php esc_html_e( 'Come raggiungerci', 'design_laboratori_italia' ); ?></span>
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
			<div class="col-12 col-lg-8 offset-lg-1 it-page-sections-container">
				<?php if ( '' !== $dli_descrizione ) : ?>
					<h3 class="it-page-section h4 visually-hidden" id="descrizione"><?php esc_html_e( 'Descrizione luogo', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_descrizione ); ?>
				<?php endif; ?>

				<!-- POSIZIONE: mappa reale (campo ACF "posizione_gps", tipo OpenStreetMap/Leaflet,
					già pronta come markup/JS) + lista indirizzo/orari/contatti, stessa card
					unica del prototipo (sf-scheda-luogo.html). -->
				<h3 class="it-page-section h4 pt-3" id="posizione"><?php esc_html_e( 'Posizione', 'design_laboratori_italia' ); ?></h3>
				<div class="it-card rounded shadow overflow-hidden">
					<?php if ( is_string( $dli_posizione ) && ! str_contains( $dli_posizione, 'data-map-markers="[]">' ) ) : ?>
						<div class="img-responsive-wrapper">
							<?php echo wp_kses_post( $dli_posizione ); ?>
						</div>
					<?php endif; ?>
					<div class="it-list-wrapper">
						<ul class="it-list">
							<?php if ( '' !== $dli_indirizzo ) : ?>
								<li>
									<div class="list-item">
										<div class="visually-hidden"><?php esc_html_e( 'Indirizzo', 'design_laboratori_italia' ); ?></div>
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-map-marker' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html( '' !== $dli_cap ? $dli_indirizzo . ', ' . $dli_cap : $dli_indirizzo ); ?></span></div>
									</div>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $dli_orari ) : ?>
								<li>
									<div class="list-item">
										<div class="visually-hidden"><?php esc_html_e( 'Orari', 'design_laboratori_italia' ); ?></div>
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-clock' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_orari ); ?></span></div>
									</div>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $dli_telefono ) : ?>
								<li>
									<a class="list-item" href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', $dli_telefono ) ); ?>">
										<div class="visually-hidden"><?php esc_html_e( 'Telefono', 'design_laboratori_italia' ); ?></div>
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_telefono ); ?></span></div>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $dli_mail ) : ?>
								<li>
									<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $dli_mail ) ); ?>" class="list-item">
										<div class="visually-hidden"><?php esc_html_e( 'Email', 'design_laboratori_italia' ); ?></div>
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_mail ); ?></span></div>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $dli_pec ) : ?>
								<li>
									<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $dli_pec ) ); ?>" class="list-item">
										<div class="visually-hidden"><?php esc_html_e( 'PEC', 'design_laboratori_italia' ); ?></div>
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_pec ); ?></span></div>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>

				<?php if ( '' !== $dli_come_raggiungerci ) : ?>
					<h3 class="it-page-section h4 pt-3" id="come-raggiungerci"><?php esc_html_e( 'Come raggiungerci', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_come_raggiungerci ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div> <!-- END container -->
	</main>
<!-- END CONTENT -->

<?php
get_footer();
