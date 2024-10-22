<?php
/**
 * Servizio template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$ID             = get_the_ID();
$image_metadata = dli_get_image_metadata( $post, 'item-gallery' );
$description    = trim( get_the_content() );
$responsabili   = dli_get_field( 'responsabile_attivita_di_ricerca' );
$progetti       = dli_get_projects_by_event_id( get_the_ID() );
$website        = dli_get_field( 'sitioweb' ) ? dli_get_field( 'sitioweb' ) : '';
$phone          = dli_get_field( 'telefono' )? dli_get_field( 'telefono' ) : '';
$email          = dli_get_field( 'email' )? dli_get_field( 'email' ) : '';
$cont_pres      = $website || $phone || $email;
$contatti       = array(
	'email'   => $email,
	'pec'     => '',
	'address' => '',
	'mobile'  => '',
	'phone'   => $phone,
	'website' => $website,
);

// recupero la lista degli eventi correlati ad un indirizzo di ricerca.
$eventi = new WP_Query(
	array(
	'posts_per_page' => -1,
	'post_type'      => 'evento',
	'orderby'        => 'data_inizio', //I’m afraid the ordering post by subfield is not possible, https://support.advancedcustomfields.com/forums/topic/wp_query-and-sub-fields/
	'order'          => 'DESC',
	'meta_query'     => array(
		array(
			'key'     => 'indirizzo_di_ricerca',
			'compare' => 'LIKE',
			'value'   => '"' . $ID . '"',
		),
	),
	)
);
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER INDIRIZZI DI RICERCA -->
	<section class="it-hero-wrapper it-hero-small-size it-dark it-overlay it-primary">
		<div class="img-responsive-wrapper">
		<div class="img-responsive">
		<div class="img-wrapper">
			<img src="<?php echo $image_metadata['image_url']; ?>" title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
		</div>
		</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<h2><?php echo esc_attr( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block"><?php echo dli_get_field( 'descrizione_breve' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO INDIRIZZO DI RICERCA -->
	<div class="container p-5" id="scheda_progetto">
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
						<span class="it-list"></span>1. Introduzione
						</button>
						<div class="progress custom-navbar-progressbar">
							<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
								<svg class="icon icon-sm icon-primary align-top" role="img" aria-labelledby="Chevron Left">
									<title>Chevron Left</title>
									<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ?>"></use>
								</svg>
								<span>Indietro</span>
							</a>
							<div id="menu_laterale" class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo __( 'Dettagli del progetto', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<?php
										if ( $description ) {
										?>
										<li class="nav-item">
											<a class="nav-link active" href="#sezione-descrizione"><span><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<?php
										}
										if ( $responsabili ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-responsabile"><span><?php echo __( 'Responsabile', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<?php
										}
										if ( $progetti ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-progetti"><span><?php echo __( 'Progetti', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<?php
										}
										if ( $cont_pres ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-contatti"><span><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<?php
										}
										if ( $eventi->posts ) {
										?>
										<li class="nav-item">
											<a class="nav-link link-100" href="#sezione-eventi"><span><?php echo __( 'Eventi', 'design_laboratori_italia' ); ?></span></a>
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
			</div> <!-- row -->
			<div class="col-12 col-lg-9 it-page-sections-container">

				<?php
				if ( $description ) {
				?>
				<h3 class="it-page-section h4" id="sezione-descrizione"><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></h3>
				<div class="row pb-3">
					<p>
						<?php echo the_content(); ?>
					</p>
				</div>
				<?php
				}
				?>

				<!-- RESPONSABILE -->
				<?php
				if ( $responsabili ) {
				?>
				<h3 class="it-page-section h4 pt-3" id="sezione-label-responsabile"><?php echo __( 'Responsabile', 'design_laboratori_italia' ); ?></h3>
				<?php
					get_template_part(
						'template-parts/common/sezione-persone',
						null,
						array(
							'section_id' => 'responsabile',
							'items'      => $responsabili,
						)
					);
				}
				?>

				<!-- PROGETTI -->
				<?php
				if ( $progetti ) {
				?>
				<h3 class="it-page-section h4 pt-3" id="sezione-label-progetti"><?php echo __( 'Progetti', 'design_laboratori_italia' ); ?></h3>
				<?php
					get_template_part(
						'template-parts/common/sezione-progetti',
						null,
						array(
							'section_id' => 'progetti',
							'items'      => $progetti,
						)
					);
				}
				?>

				<!-- CONTATTI -->
				<?php
				if ( $cont_pres ) {
				?>
				<h3 class="it-page-section pt-3 h4" id="sezione-label-contatti"><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></h3>
				<?php
						get_template_part(
							'template-parts/common/sezione-contatti',
							null,
							array(
								'section_id' => 'contatti',
								'items'      => $contatti,
							)
						);
					}
				?>

				<!-- EVENTI -->
				<?php
				if ( $eventi->posts ) {
				?>
				<h3 class="it-page-section h4 pt-3" id="sezione-label-eventi"><?php echo __( 'Eventi', 'design_laboratori_italia' ); ?></h3>
				<?php
					get_template_part(
						'template-parts/common/sezione-eventi',
						null,
						array(
							'section_id' => 'eventi',
							'items'      => $eventi->posts,
						)
					);
				}
				?>

			</div>
		</div>
	</div> <!-- scheda_progetto -->

</main>


<?php
get_footer();
