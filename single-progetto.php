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

$ID                   = get_the_ID();
$image_metadata       = dli_get_image_metadata( $post, 'item-gallery' );
$descrizione          = apply_filters( 'the_content', $post->post_content );
$responsabili         = dli_get_field( 'responsabile_del_progetto' );
$partecipanti         = dli_get_field( 'persone' );
$indirizzi_di_ricerca = dli_get_field( 'elenco_indirizzi_di_ricerca_correlati' );
$pubblicazioni        = dli_get_field( 'pubblicazioni' );
$levels               = wp_get_post_terms( $ID, 'post_tag' );
$fields_allegati      = array( 'allegato1', 'allegato2', 'allegato3' );
$allegati             = array();
foreach ( $fields_allegati as $field ) {
	$item = dli_get_field( $field );
	if ( $item ) {
		array_push( $allegati, $item );
	}
}
$web_site_url = dli_get_field( 'url' );
$current_lang = dli_current_language();
$tag_page     = DLI_PAGE_PER_CT[PROGETTO_POST_TYPE][$current_lang];

// Recupero la lista degli eventi e delle notizie correlate ad un progetto.
$eventi = DLI_ContentsManager::get_related_items( $post, 'progetto', array( EVENT_POST_TYPE, NEWS_POST_TYPE ) );

?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>


	<!-- BANNER PROGETTI -->
	<section class="it-hero-wrapper it-hero-small-size it-dark it-overlay it-primary">
		<div class="img-responsive-wrapper">
		<div class="img-responsive">
		<?php
			if ( isset( $image_metadata['image_url'] ) && $image_metadata['image_url'] ) {
		?>
		<div class="img-wrapper">
			<img src="<?php echo $image_metadata['image_url']; ?>" title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
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
						<h2><?php echo esc_attr( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block">
							<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
							<?php
								if ( $web_site_url ) {
							?>
							<a class="btn btn-sm btn-secondary" href="<?php echo esc_url( $web_site_url ) ; ?>">
								<?php _e( 'Sito web', "design_laboratori_italia" ); ?></a>
							</a>
							<?php
								}
							?>
						</p>
						<div>
							<!-- tag -->
							<?php
							foreach( $levels as $level ){
							?>
								<div class="chip chip-primary chip-lg chip-simple border-light mt-3">
									<a href="<?php echo site_url(); ?>/<?php echo $tag_page?>?level=<?php echo esc_attr(  $level->slug ); ?>">
										<span class="chip-label text-light"><?php echo esc_attr(  $level->name ); ?></span>
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
									<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left'; ?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ?>"></use>
								</svg>
								<span>Indietro</span>
							</a>
							<div id="menu_laterale" class="menu-wrapper">
								<div class="link-list-wrapper">
									<?php
										$show_label = dli_get_configuration_field_by_lang( 'label_project_details_is_visible', 'progetti' );
										if ( $show_label != 'false' ){
									?>
									<h3>
										<?php echo __( 'Dettagli del progetto', 'design_laboratori_italia' ); ?>
									</h3>
									<?php
										}
									?>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<?php
										if ( $descrizione ) {
											?>
											<li class="nav-item">
												<a class="nav-link active" href="#sezione-descrizione">
													<span><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></span>
												</a>
											</li>
											<?php
										}
										if ( $responsabili ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#sezione-responsabile"><span><?php echo dli_translate( 'Responsabile', 'design_laboratori_italia' ); ?></span></a>
											</li>
											<?php
										}
										if ( $partecipanti ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#sezione-partecipanti"><span><?php echo dli_translate( 'Partecipanti', 'design_laboratori_italia' ); ?></span></a>
											</li>
											<?php
										}
										if ( $indirizzi_di_ricerca ) {
											?>
											<li class="nav-item">
												<a class="nav-link" href="#sezione-indirizzi-di-ricerca">
													<span>
														<?php echo dli_translate( 'Indirizzi di ricerca', 'design_laboratori_italia' ); ?>
													</span>
												</a>
											</li>
											<?php
										}
										if ( $pubblicazioni ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-pubblicazioni"><span><?php echo __( 'Pubblicazioni', 'design_laboratori_italia' ); ?></span></a>
										</li>
											<?php
										}
										if ( count( $allegati ) > 0 ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#sezione-allegati"><span><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></span></a>
										</li>
											<?php
										}
										if ( $eventi->posts ) {
											?>
										<li class="nav-item">
											<a class="nav-link link-100" href="#sezione-eventi"><span><?php echo __( 'Eventi e notizie', 'design_laboratori_italia' ); ?></span></a>
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
				if ( $descrizione ) {
				?>
				<h3 class="it-page-section h4" id="sezione-descrizione">
					<?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?>
				</h3>
				<div class="row pb-3">
					<p>
						<?php
							echo $descrizione;
						?>
					</p>
				</div>
					<?php
				}
				if ( $responsabili ) {
				?>
					<!-- RESPONSABILE -->
					<h3 class="it-page-section h4 pt-3" id="p2">
						<?php echo dli_translate( 'Responsabile', 'design_laboratori_italia' ); ?>
					</h3>
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
				if ( $partecipanti ) {
				?>
					<!-- PARTECIPANTI -->
					<h3 class="it-page-section h4 pt-3" id="p3"><?php echo dli_translate( 'Partecipanti', 'design_laboratori_italia' ); ?></h3>
					<?php
						get_template_part(
							'template-parts/common/sezione-persone',
							null,
							array(
								'section_id' => 'partecipanti',
								'items'      => $partecipanti,
							)
						);
				}
				if ( $indirizzi_di_ricerca ) {
				?>
				<!-- INDIRIZZI DI RICERCA -->
				<h3 class="it-page-section h4 pt-3" id="p4">
					<?php echo dli_translate( 'Indirizzi di ricerca', 'design_laboratori_italia' ); ?>
				</h3>
					<?php
						get_template_part(
							'template-parts/common/sezione-indirizzidiricerca',
							null,
							array(
								'section_id' => 'indirizzi-di-ricerca',
								'items'      => $indirizzi_di_ricerca,
							)
						);
				}
				if ( $pubblicazioni ) {
				?>
				<!-- PUBBLICAZIONI -->
				<h3 class="it-page-section pt-3 h4" id="p5">
					<?php echo __( 'Pubblicazioni', 'design_laboratori_italia' ); ?>
				</h3>
					<?php
						get_template_part(
							'template-parts/common/sezione-pubblicazioni',
							null,
							array(
								'section_id' => 'pubblicazioni',
								'items'      => $pubblicazioni,
							)
						);
				}
				if ( count( $allegati ) > 0 ) {
				?>
				<!-- ALLEGATI -->
				<h3 class="it-page-section h4 pt-3" id="p6">
					<?php echo __( 'Allegati', 'design_laboratori_italia' ); ?>
				</h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-allegati',
						null,
						array(
							'section_id' => 'allegati',
							'items'      => $allegati,
						)
					);
				}
				if ( $eventi->posts ) {
				?>
				<!-- EVENTI -->
				<?php
					if ( $eventi->posts ) {
						get_template_part(
							'template-parts/common/sezione-related-items',
							null,
							array(
								'related_items' => $eventi->posts,
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
