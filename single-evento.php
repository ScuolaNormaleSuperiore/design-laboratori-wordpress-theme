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
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
$dli_description        = ( '.' === $post->post_content ) ? '' : apply_filters( 'the_content', $post->post_content );
$dli_main_category      = dli_get_post_main_category( $post, 'category' );
$dli_image_metadata     = dli_get_image_metadata( $post );
$dli_start_date         = dli_get_field( 'data_inizio', $post );
$dli_start_event_date   = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_start_date );
$dli_start_day          = $dli_start_event_date ? $dli_start_event_date->format( 'd' ) : '';
$dli_start_month_number = $dli_start_event_date ? $dli_start_event_date->format( 'm' ) : '';
$dli_start_month_name   = $dli_start_month_number ? dli_get_monthname( $dli_start_month_number ) : '';
$dli_start_month_short  = $dli_start_month_number ? dli_get_monthname_short( $dli_start_month_number ) : '';
$dli_orario_inizio      = dli_get_field( 'orario_inizio', $post );
$dli_orario_inizio      = $dli_orario_inizio ? $dli_orario_inizio : '';
$dli_end_date           = dli_get_field( 'data_fine', $post );
$dli_same_date          = ( '' === $dli_end_date || $dli_start_date === $dli_end_date );
$dli_end_event_date     = ( ! $dli_same_date ) ? dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_end_date ) : null;
$dli_end_day            = $dli_end_event_date ? $dli_end_event_date->format( 'd' ) : '';
$dli_end_month_number   = $dli_end_event_date ? $dli_end_event_date->format( 'm' ) : '';
$dli_end_month_name     = $dli_end_month_number ? dli_get_monthname( $dli_end_month_number ) : '';
$dli_end_month_short    = $dli_end_month_number ? dli_get_monthname_short( $dli_end_month_number ) : '';
$dli_orario_fine        = dli_get_field( 'orario_fine', $post );
$dli_orario_fine        = $dli_orario_fine ? $dli_orario_fine : '';
$dli_luogo              = dli_get_field( 'luogo' );
$dli_label_contatti     = dli_get_field( 'label_contatti' );
$dli_telefono           = dli_get_field( 'telefono' );
$dli_email              = dli_get_field( 'email' );
$dli_sito_web           = dli_get_field( 'sitoweb' );
$dli_video              = dli_get_field( 'video' );
$dli_allegato           = dli_get_field( 'allegato' );
$dli_short_descr        = dli_get_field( 'descrizione_breve' );
$dli_datetime_display   = dli_get_event_datetime_display( $post->ID );
?>

<main id="main-container" role="main">

	<!-- BANNER EVENTO: foto di copertina a piena larghezza con overlay scuro e
		breadcrumb sovrapposto (pattern con foto, come Progetti/Brevetti), stesso
		criterio del prototipo (sf-scheda-evento.html), che lascia questa famiglia
		sulla variante "a piena larghezza" invece di quella a due colonne di News. -->
	<section class="it-hero-wrapper it-dark it-overlay it-hero-small-size" aria-labelledby="dli-hero-evento-title">
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
						alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
						title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
					>
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
						<?php if ( $dli_start_event_date ) : ?>
							<span class="it-category"><?php echo esc_html( trim( $dli_start_day . ' ' . $dli_start_month_name . ' ' . $dli_start_event_date->format( 'Y' ) ) ); ?></span>
						<?php endif; ?>
						<h2 id="dli-hero-evento-title"><?php echo esc_html( get_the_title() ); ?></h2>
						<?php if ( $dli_short_descr ) : ?>
							<p class="d-none d-lg-block"><?php echo wp_kses_post( wp_trim_words( $dli_short_descr, DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
						<?php endif; ?>
						<?php if ( $dli_image_metadata['image_caption'] ) : ?>
							<p class="figure-caption mt-3 text-light"><?php echo esc_html( $dli_image_metadata['image_caption'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>



	<!-- BODY EVENTO -->
	<div class="container py-lg-5">
		<div class="row">

			<!-- Dettagli dell'evento -->
			<div class="col-12 col-lg-3">
				<div data-bs-toggle="sticky" data-bs-stackable="true">
					<nav class="navbar it-navscroll-wrapper navbar-expand-lg it-bottom-navscroll it-right-side" data-bs-navscroll>
						<div class="progress custom-navbar-progressbar">
								<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
							<svg class="icon icon-sm icon-primary align-top" role="img">
								<title><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></title>
								<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>" 
								xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
							</svg>
							<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( 'Dettagli dell\'evento', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
									<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<?php
										if ( $dli_description ) {
											?>
										<li class="nav-item">
											<a class="nav-link active" href="#descrizione">
											<span><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_start_event_date ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#date_e_orari">
											<span><?php echo esc_html__( 'Date e orari', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_luogo ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#luogo">
											<span><?php echo esc_html__( 'Luogo', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_telefono || $dli_email || $dli_sito_web ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#contatti">
											<span><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_allegato ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#allegati">
											<span><?php echo esc_html__( 'Allegati', 'design_laboratori_italia' ); ?></span>
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
					<!-- Condividi -->
					<?php get_template_part( 'template-parts/common/social-sharing' ); ?>
				</div>
			</div>

			<!-- Colonna destra -->
			<div class="col-12 col-lg-8 offset-lg-1 it-page-sections-container">

				<?php if ( $dli_description ) : ?>
					<h3 class="it-page-section h4 visually-hidden" id="descrizione"><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_description ); ?>
				<?php endif; ?>

				<?php if ( $dli_start_event_date ) : ?>
					<!-- DATE E ORARI: componente "Timeline point list, calendario" di Bootstrap
						Italia 3 (verificato via MCP Filo), sostituisce il point-list/
						point-list-aside v2. Un solo punto se inizio/fine coincidono
						(o non c'è una data di fine), due punti distinti altrimenti. -->
					<h3 class="it-page-section h4 pt-3" id="date_e_orari"><?php echo esc_html__( 'Date e orari', 'design_laboratori_italia' ); ?></h3>
					<ol class="it-timeline-point-list my-4">
						<li class="timeline-point">
							<div class="point-aside point-aside-primary">
								<time datetime="<?php echo esc_attr( $dli_start_event_date->format( 'Y-m-d' ) ); ?>">
									<span class="visually-hidden"><?php echo esc_html( trim( $dli_start_day . ' ' . $dli_start_month_name ) ); ?></span>
									<div class="point-visual" aria-hidden="true">
										<div class="point-main font-monospace"><?php echo esc_html( $dli_start_day ); ?></div>
										<div class="point-bottom font-monospace"><?php echo esc_html( strtoupper( $dli_start_month_short ) ); ?></div>
									</div>
								</time>
							</div>
							<div class="point-content">
								<?php if ( $dli_same_date ) : ?>
									<?php if ( $dli_orario_inizio && $dli_orario_fine ) : ?>
										<p><?php echo esc_html__( 'Dalle ore', 'design_laboratori_italia' ) . ' ' . esc_html( $dli_orario_inizio ) . ' ' . esc_html__( 'alle', 'design_laboratori_italia' ) . ' ' . esc_html( $dli_orario_fine ); ?></p>
									<?php elseif ( $dli_orario_inizio ) : ?>
										<p><?php echo esc_html__( 'Dalle ore', 'design_laboratori_italia' ) . ' ' . esc_html( $dli_orario_inizio ); ?></p>
									<?php elseif ( $dli_orario_fine ) : ?>
										<p><?php echo esc_html__( 'Fino alle ore', 'design_laboratori_italia' ) . ' ' . esc_html( $dli_orario_fine ); ?></p>
									<?php endif; ?>
								<?php elseif ( $dli_orario_inizio ) : ?>
									<p><?php echo esc_html__( 'Inizio evento, ore', 'design_laboratori_italia' ) . ' ' . esc_html( $dli_orario_inizio ); ?></p>
								<?php else : ?>
									<p><?php echo esc_html__( 'Inizio evento', 'design_laboratori_italia' ); ?></p>
								<?php endif; ?>
							</div>
						</li>
						<?php if ( ! $dli_same_date && $dli_end_event_date ) : ?>
							<li class="timeline-point">
								<div class="point-aside point-aside-primary">
									<time datetime="<?php echo esc_attr( $dli_end_event_date->format( 'Y-m-d' ) ); ?>">
										<span class="visually-hidden"><?php echo esc_html( trim( $dli_end_day . ' ' . $dli_end_month_name ) ); ?></span>
										<div class="point-visual" aria-hidden="true">
											<div class="point-main font-monospace"><?php echo esc_html( $dli_end_day ); ?></div>
											<div class="point-bottom font-monospace"><?php echo esc_html( strtoupper( $dli_end_month_short ) ); ?></div>
										</div>
									</time>
								</div>
								<div class="point-content">
									<?php if ( $dli_orario_fine ) : ?>
										<p><?php echo esc_html__( 'Fine evento, ore', 'design_laboratori_italia' ) . ' ' . esc_html( $dli_orario_fine ); ?></p>
									<?php else : ?>
										<p><?php echo esc_html__( 'Fine evento', 'design_laboratori_italia' ); ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endif; ?>
					</ol>
				<?php endif; ?>

				<?php if ( $dli_luogo ) : ?>
					<!-- LUOGO: campo testo libero (non una relazione a un post "luogo" reale) -->
					<h3 class="it-page-section h4 pt-3" id="luogo"><?php echo esc_html__( 'Luogo', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_luogo ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_telefono || $dli_email || $dli_sito_web ) : ?>
					<h3 class="it-page-section h4 pt-3" id="contatti">
						<?php echo $dli_label_contatti ? esc_html( $dli_label_contatti ) : esc_html__( 'Contatti', 'design_laboratori_italia' ); ?>
					</h3>
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

				<?php if ( $dli_allegato ) : ?>
					<h3 class="it-page-section h4 pt-3" id="allegati"><?php echo esc_html__( 'Allegati', 'design_laboratori_italia' ); ?></h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-allegati',
						null,
						array(
							'section_id' => 'allegati',
							'items'      => array( $dli_allegato ),
						)
					);
					?>
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
			<!-- END Colonna destra -->

		</div> <!-- END row -->
	</div> <!-- END container -->

</main>


<?php
get_footer();
