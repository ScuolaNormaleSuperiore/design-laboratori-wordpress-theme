<?php
/**
 * Detail page for the post-type: indirizzo-di-ricerca.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$dli_image_metadata = dli_get_image_metadata( $post );
$dli_description    = trim( get_the_content() );
$dli_responsabili   = dli_get_field( 'responsabile_attivita_di_ricerca' );
$dli_website        = dli_get_field( 'sitioweb' ) ? dli_get_field( 'sitioweb' ) : '';
$dli_phone          = dli_get_field( 'telefono' ) ? dli_get_field( 'telefono' ) : '';
$dli_email          = dli_get_field( 'email' ) ? dli_get_field( 'email' ) : '';
$dli_cont_pres      = $dli_website || $dli_phone || $dli_email;
$dli_contatti       = array(
	'email'   => $dli_email,
	'pec'     => '',
	'address' => '',
	'mobile'  => '',
	'phone'   => $dli_phone,
	'website' => $dli_website,
);

$dli_levels = wp_get_post_terms( $post->ID, 'post_tag' );
$dli_levels = ( is_wp_error( $dli_levels ) || ! is_array( $dli_levels ) ) ? array() : $dli_levels;

// Recupero la lista dei progetti correlati.
$dli_progetti = DLI_ContentsManager::get_related_items( $post, 'elenco_indirizzi_di_ricerca_correlati', array( PROGETTO_POST_TYPE ) );

// Recupero la lista degli eventi e delle notizie correlate ad un progetto.
$dli_eventi = DLI_ContentsManager::get_related_items( $post, 'indirizzo_di_ricerca', array( EVENT_POST_TYPE, NEWS_POST_TYPE ) );

?>

<main id="main-container" role="main">

	<!-- BANNER INDIRIZZI DI RICERCA: foto di copertina con overlay + breadcrumb
	     in overlay assoluto sopra la foto, stesso pattern di single-progetto.php. -->
	<section class="it-hero-wrapper it-hero-small-size it-dark it-overlay">
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>" title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>" alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>">
				</div>
			</div>
		</div>
		<div class="container it-hero-breadcrumb">
			<div class="row">
				<div class="col-12">
					<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper">
						<h2><?php echo esc_html( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block"><?php echo wp_kses_post( dli_get_field( 'descrizione_breve' ) ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO INDIRIZZO DI RICERCA -->
	<div class="container p-5" id="scheda_indirizzo_ricerca">
		<div class="row">
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
								<svg class="icon icon-sm icon-primary align-top" role="img">
									<title>Chevron Left</title>
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>" 
										xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>">
									</use>
								</svg>
								<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div id="menu_laterale" class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( "Dettagli dell'attività", 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<?php
										if ( $dli_description ) {
											?>
										<li class="nav-item">
											<a class="nav-link active" href="#sezione-descrizione"><span><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></span></a>
										</li>
											<?php
										}
										if ( $dli_responsabili ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-responsabile"><span><?php echo esc_html( dli_translate( 'Responsabile', 'design_laboratori_italia' ) ); ?></span></a>
										</li>
											<?php
										}
										if ( $dli_progetti ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-progetti"><span><?php echo esc_html__( 'Progetti', 'design_laboratori_italia' ); ?></span></a>
										</li>
											<?php
										}
										if ( $dli_cont_pres ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-contatti"><span><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></span></a>
										</li>
											<?php
										}
										if ( $dli_eventi ) {
											?>
										<li class="nav-item">
											<a class="nav-link link-100" href="#sezione-eventi"><span><?php echo esc_html__( 'Eventi e notizie', 'design_laboratori_italia' ); ?></span></a>
										</li>
											<?php
										}
										?>
									</ul>
								</div>
							</div> <!-- menu_laterale -->
						</div>
					</nav>
				</div>
			</div> <!-- col-12 col-lg-3 -->
			<div class="col-12 col-lg-8 offset-lg-1 it-page-sections-container">
				<?php if ( ! empty( $dli_levels ) ) : ?>
					<!-- Argomenti correlati: stesso pattern taxonomy/chip della card
						 nell'elenco (it-card-taxonomy/it-card-chips), qui fuori da una
						 card ma i suoi stessi stili non richiedono quel contesto.
						 Nessun filtro reale da azionare su questa scheda, quindi chip
						 statiche (span), non link. -->
					<div class="it-card-taxonomy mb-4">
						<ul class="it-card-chips" aria-label="<?php echo esc_attr__( 'Argomenti correlati:', 'design_laboratori_italia' ); ?>">
							<?php foreach ( $dli_levels as $dli_level ) : ?>
								<li class="list-item">
									<span class="chip chip-secondary">
										<span class="chip-label">
											<span class="visually-hidden"><?php echo esc_html__( 'Argomento:', 'design_laboratori_italia' ); ?></span>
											<?php echo esc_html( $dli_level->name ); ?>
										</span>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<?php
				if ( $dli_description ) {
					?>
				<h3 class="it-page-section h4" id="sezione-descrizione"><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></h3>
				<?php
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
				echo wp_kses_post( apply_filters( 'the_content', get_the_content() ) );
				?>
					<?php
				}
				?>

				<!-- RESPONSABILE -->
				<?php
				if ( $dli_responsabili ) {
					?>
				<h3 class="it-page-section h4 pt-3" id="sezione-label-responsabile"><?php echo esc_html( dli_translate( 'Responsabile', 'design_laboratori_italia' ) ); ?></h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-persone',
						null,
						array(
							'section_id' => 'responsabile',
							'items'      => $dli_responsabili,
						)
					);
				}
				?>

				<!-- PROGETTI -->
				<?php
				if ( $dli_progetti ) {
					?>
				<h3 class="it-page-section h4 pt-3" id="sezione-label-progetti"><?php echo esc_html__( 'Progetti', 'design_laboratori_italia' ); ?></h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-progetti',
						null,
						array(
							'section_id' => 'progetti',
							'items'      => $dli_progetti,
						)
					);
				}
				?>

				<!-- CONTATTI -->
				<?php
				if ( $dli_cont_pres ) {
					?>
				<h3 class="it-page-section h4 pt-3" id="sezione-label-contatti"><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></h3>
					<?php
						get_template_part(
							'template-parts/common/sezione-contatti',
							null,
							array(
								'section_id' => 'contatti',
								'items'      => $dli_contatti,
							)
						);
				}
				?>

				<!-- EVENTI -->
				<?php
				if ( $dli_eventi ) {
					?>
					<?php
					get_template_part(
						'template-parts/common/sezione-related-items',
						null,
						array(
							'items' => $dli_eventi,
						)
					);
				}
				?>

			</div>
		</div>
	</div> <!-- scheda_indirizzo_ricerca -->

</main>

<?php
get_footer();
