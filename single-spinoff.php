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
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
$dli_description      = ( '.' === $post->post_content ) ? '' : apply_filters( 'the_content', $post->post_content );
$dli_summary          = dli_get_field( 'descrizione_breve' );
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

	<!-- BANNER SPIN-OFF: nessuna foto di copertina reale (il campo immagine di
		questa scheda è un logo aziendale, non adatto a un fondale a piena
		larghezza), stesso caso già gestito in single-risorsa-tecnica.php:
		hero a sfondo pieno con breadcrumb integrato non-assoluto, pattern
		delle pagine elenco/archivio, non l'hero con foto usato in
		single-progetto.php/single-brevetto.php quando la foto esiste
		davvero. -->
	<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-spinoff-title">
		<div class="container">
			<div class="row align-items-stretch">
				<div class="col-12 col-lg-7">
					<section class="pt-2">
						<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
					</section>
					<div class="it-hero-text-wrapper px-lg-2">
						<h2 id="dli-hero-spinoff-title"><?php echo esc_html( get_the_title() ); ?></h2>
						<?php if ( $dli_summary ) : ?>
							<p class="fs-5"><?php echo esc_html( $dli_summary ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-12 col-lg-5 d-none d-lg-block">
					<?php if ( dli_show_hero_decorative_image() ) : ?>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO SPIN-OFF -->
	<div class="container py-lg-5">
		<div class="row">
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
								<svg class="icon icon-sm icon-primary align-top">
									<title><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></title>
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
								</svg>
								<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( 'Dettagli dello spin-off', 'design_laboratori_italia' ); ?></h3>
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
			<div class="col-12 col-lg-8 offset-lg-1 it-page-sections-container">
				<?php if ( $dli_description ) : ?>
					<h3 class="it-page-section h4 visually-hidden" id="description"><?php echo esc_html__( 'Descrizione Spin-off', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_description ); ?>
				<?php endif; ?>

				<?php if ( $dli_year ) : ?>
					<h3 class="it-page-section h4 pt-3" id="costituzione"><?php echo esc_html__( 'Anno di costituzione', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_year ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_settore_attivita ) : ?>
					<h3 class="it-page-section h4 pt-3" id="settoreattivita"><?php echo esc_html__( 'Settore di attività', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_settore_attivita['title'] ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_stato ) : ?>
					<h3 class="it-page-section h4 pt-3" id="status"><?php echo esc_html__( 'Stato', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_stato ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_telefono || $dli_sito_web || $dli_email ) : ?>
					<h3 class="it-page-section h4 pt-3" id="contatti"><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></h3>
					<div class="it-list-wrapper">
						<ul class="it-list">
							<?php if ( $dli_telefono ) : ?>
								<li>
									<a class="list-item" href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', $dli_telefono ) ); ?>">
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_telefono ); ?></span></div>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( $dli_email ) : ?>
								<li>
									<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $dli_email ) ); ?>" class="list-item">
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_email ); ?></span></div>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( $dli_sito_web ) : ?>
								<li>
									<a class="list-item" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dli_sito_web ); ?>">
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone"><span class="text"><?php echo esc_html__( 'Sito web', 'design_laboratori_italia' ); ?></span></div>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( $dli_note ) : ?>
					<h3 class="it-page-section h4 pt-3" id="altreinformazioni"><?php echo esc_html__( 'Altre informazioni', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_note ); ?>
				<?php endif; ?>

				<?php if ( $dli_video ) : ?>
					<h3 class="it-page-section h4 pt-3" id="video"><?php echo esc_html__( 'Video', 'design_laboratori_italia' ); ?></h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-video',
						null,
						array(
							'video'       => $dli_video,
							'video_text'  => null,
							'video_title' => get_the_title(),
						)
					);
					?>
				<?php endif; ?>

			</div>

		</div> <!-- END row -->
	</div> <!-- END container -->
</main>

<?php
get_footer();
