<?php
/**
 * Detail page for the post-type: progetto.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_id             = get_the_ID();
$dli_image_metadata = dli_get_image_metadata( $post );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
$dli_descrizione          = apply_filters( 'the_content', $post->post_content );
$dli_responsabili         = dli_get_field( 'responsabile_del_progetto' );
$dli_partecipanti         = dli_get_field( 'persone' );
$dli_indirizzi_di_ricerca = dli_get_field( 'elenco_indirizzi_di_ricerca_correlati' );
$dli_pubblicazioni        = dli_get_field( 'pubblicazioni' );
$dli_levels               = wp_get_post_terms( $dli_id, 'post_tag' );
$dli_levels               = ( is_wp_error( $dli_levels ) || ! is_array( $dli_levels ) ) ? array() : $dli_levels;
$dli_fields_allegati      = array( 'allegato1', 'allegato2', 'allegato3' );
$dli_allegati             = array();
foreach ( $dli_fields_allegati as $dli_field_allegato ) {
	$dli_item = dli_get_field( $dli_field_allegato );
	if ( $dli_item ) {
		array_push( $dli_allegati, $dli_item );
	}
}
$dli_web_site_url = dli_get_field( 'url' );
$dli_current_lang = dli_current_language();
$dli_tag_page     = DLI_PAGE_PER_CT[ PROGETTO_POST_TYPE ][ $dli_current_lang ];

// Recupero la lista degli eventi e delle notizie correlate.
$dli_eventi = DLI_ContentsManager::get_related_items( $post, 'progetto', array( EVENT_POST_TYPE, NEWS_POST_TYPE ) );
// Recupero la lista risorse tecniche correlate.
$dli_risorse = dli_get_field( 'risorse_tecniche' );

?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>


	<!-- BANNER PROGETTI -->
	<section class="it-hero-wrapper it-hero-small-size it-dark it-overlay it-primary">
		<div class="img-responsive-wrapper">
		<div class="img-responsive">
		<?php
		if ( isset( $dli_image_metadata['image_url'] ) && $dli_image_metadata['image_url'] ) {
			?>
		<div class="img-wrapper">
			<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>" title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>" alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>">
		</div>
			<?php
		}
		?>
		</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<h2><?php echo esc_html( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block">
							<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
							<?php
							if ( $dli_web_site_url ) {
								?>
							<a class="btn btn-sm btn-secondary" href="<?php echo esc_url( $dli_web_site_url ); ?>">
								<?php esc_html_e( 'Sito web', 'design_laboratori_italia' ); ?>
							</a>
								<?php
							}
							?>
						</p>
						<div>
							<!-- tag -->
							<?php
							foreach ( $dli_levels as $dli_level ) {
								?>
								<div class="chip chip-primary chip-lg chip-simple border-light mt-3">
									<a href="<?php echo esc_url( site_url() . '/' . $dli_tag_page . '?level=' . $dli_level->slug ); ?>">
										<span class="chip-label text-light"><?php echo esc_html( $dli_level->name ); ?></span>
									</a>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO PROGETTO -->
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
							<span class="it-list"></span>
						</button>

						<div class="progress custom-navbar-progressbar">
							<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>

						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
								<svg class="icon icon-sm icon-primary align-top" role="img" aria-labelledby="Chevron Left">
									<title>Chevron Left</title>
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>" xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
								</svg>
								<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div id="menu_laterale" class="menu-wrapper">
								<div class="link-list-wrapper">
									<?php
										$dli_show_label = dli_get_configuration_field_by_lang( 'label_project_details_is_visible', 'progetti' );
									if ( 'false' !== $dli_show_label ) {
										?>
									<h3>
										<?php echo esc_html__( 'Dettagli del progetto', 'design_laboratori_italia' ); ?>
									</h3>
										<?php
									}
									?>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<?php
										if ( $dli_descrizione ) {
											?>
											<li class="nav-item">
												<a class="nav-link active" href="#sezione-descrizione">
													<span><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></span>
												</a>
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
										if ( $dli_partecipanti ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#sezione-partecipanti"><span><?php echo esc_html( dli_translate( 'Partecipanti', 'design_laboratori_italia' ) ); ?></span></a>
											</li>
											<?php
										}
										if ( $dli_indirizzi_di_ricerca ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#sezione-indirizzi-di-ricerca">
													<span>
														<?php echo esc_html( dli_translate( 'Indirizzi di ricerca', 'design_laboratori_italia' ) ); ?>
													</span>
												</a>
											</li>
											<?php
										}
										if ( $dli_pubblicazioni ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-pubblicazioni"><span><?php echo esc_html__( 'Pubblicazioni', 'design_laboratori_italia' ); ?></span></a>
										</li>
											<?php
										}
										if ( count( $dli_allegati ) > 0 ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-allegati"><span><?php echo esc_html__( 'Allegati', 'design_laboratori_italia' ); ?></span></a>
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
										if ( $dli_risorse ) {
											?>
										<li class="nav-item">
											<a class="nav-link link-100" href="#sezione-risorse-tecniche"><span><?php echo esc_html__( 'Risorse tecniche', 'design_laboratori_italia' ); ?></span></a>
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
				if ( $dli_descrizione ) {
					?>
				<h3 class="it-page-section h4" id="sezione-descrizione">
					<?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?>
				</h3>
				<div class="row pb-3">
					<p>
						<?php
							echo wp_kses_post( $dli_descrizione );
						?>
					</p>
				</div>
					<?php
				}
				if ( $dli_responsabili ) {
					?>
					<!-- RESPONSABILE -->
					<h3 class="it-page-section h4 pt-3" id="p2">
						<?php echo esc_html( dli_translate( 'Responsabile', 'design_laboratori_italia' ) ); ?>
					</h3>
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
				if ( $dli_partecipanti ) {
					?>
					<!-- PARTECIPANTI -->
					<h3 class="it-page-section h4 pt-3" id="p3"><?php echo esc_html( dli_translate( 'Partecipanti', 'design_laboratori_italia' ) ); ?></h3>
					<?php
						get_template_part(
							'template-parts/common/sezione-persone',
							null,
							array(
								'section_id' => 'partecipanti',
								'items'      => $dli_partecipanti,
							)
						);
				}
				if ( $dli_indirizzi_di_ricerca ) {
					?>
				<!-- INDIRIZZI DI RICERCA -->
				<h3 class="it-page-section h4 pt-3" id="p4">
					<?php echo esc_html( dli_translate( 'Indirizzi di ricerca', 'design_laboratori_italia' ) ); ?>
				</h3>
					<?php
						get_template_part(
							'template-parts/common/sezione-indirizzidiricerca',
							null,
							array(
								'section_id' => 'indirizzi-di-ricerca',
								'items'      => $dli_indirizzi_di_ricerca,
							)
						);
				}
				if ( $dli_pubblicazioni ) {
					?>
				<!-- PUBBLICAZIONI -->
				<h3 class="it-page-section pt-3 h4" id="p5">
					<?php echo esc_html__( 'Pubblicazioni', 'design_laboratori_italia' ); ?>
				</h3>
					<?php
						get_template_part(
							'template-parts/common/sezione-pubblicazioni',
							null,
							array(
								'section_id' => 'pubblicazioni',
								'items'      => $dli_pubblicazioni,
							)
						);
				}
				if ( count( $dli_allegati ) > 0 ) {
					?>
				<!-- ALLEGATI -->
				<h3 class="it-page-section h4 pt-3" id="p6">
					<?php echo esc_html__( 'Allegati', 'design_laboratori_italia' ); ?>
				</h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-allegati',
						null,
						array(
							'section_id' => 'allegati',
							'items'      => $dli_allegati,
						)
					);
				}
				if ( $dli_eventi ) {
					?>
				<!-- EVENTI -->
					<?php
					if ( $dli_eventi ) {
						get_template_part(
							'template-parts/common/sezione-related-items',
							null,
							array(
								'section_id' => 'eventi',
								'items'      => $dli_eventi,
							)
						);
					}
				}
				if ( $dli_risorse ) {
					?>
				<!-- RISORSE -->
				<h3 class="it-page-section h4 pt-3" id="p7">
					<?php echo esc_html( dli_translate( 'Risorse tecniche', 'design_laboratori_italia' ) ); ?>
				</h3>
					<?php
					if ( $dli_risorse ) {
						get_template_part(
							'template-parts/common/sezione-related-technical-resources',
							null,
							array(
								'section_id' => 'risorse-tecniche',
								'items'      => $dli_risorse,
							)
						);
					}
				}
				?>
			</div>
		</div>
	</div> <!-- scheda_progetto -->

</main>


<?php
get_footer();
