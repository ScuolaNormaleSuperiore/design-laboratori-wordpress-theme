<?php
/**
 * Detail page for the post-type: persona.
 *
 * @package Design_Laboratori_Italia
 */

get_header();

while ( have_posts() ) {
	the_post();
	$dli_id                     = get_the_ID();
	$dli_title                  = get_the_title( $dli_id );
	$dli_image_url              = dli_get_persona_avatar( $post, $dli_id );
	$dli_bio                    = get_the_content();
	$dli_categoria_appartenenza = '';
	$dli_categoria_items        = dli_get_field( 'categoria_appartenenza' );
	if ( is_array( $dli_categoria_items ) && ! empty( $dli_categoria_items ) && isset( $dli_categoria_items[0]->nome ) ) {
		$dli_categoria_appartenenza = $dli_categoria_items[0]->nome;
	}
	$dli_allegato_cv    = dli_get_field( 'allegato_cv' );
	$dli_allegato1      = dli_get_field( 'allegato1' );
	$dli_allegato2      = dli_get_field( 'allegato2' );
	$dli_allegato3      = dli_get_field( 'allegato3' );
	$dli_telefono       = dli_get_field( 'telefono' );
	$dli_email          = dli_get_field( 'email' );
	$dli_sito_web       = dli_get_field( 'sito_web' );
	$dli_terms          = get_the_terms( $dli_id, 'struttura' );
	$dli_terms          = $dli_terms ? $dli_terms : array();
	$dli_nome_struttura = count( $dli_terms ) ? $dli_terms[0]->name : '';
}

// recupero la lista dei progetti.
$dli_progetti = new WP_Query(
	array(
		'posts_per_page' => -1,
		'post_type'      => 'progetto',
		'orderby'        => 'data_inizio', // Ordering by subfield is not supported: https://support.advancedcustomfields.com/forums/topic/wp_query-and-sub-fields/.
		'order'          => 'DESC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Needed for ACF relationship fields stored as serialized arrays.
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => 'responsabile_del_progetto',
				'compare' => 'LIKE',
				'value'   => '"' . $dli_id . '"',
			),
			array(
				'key'     => 'persone',
				'compare' => 'LIKE',
				'value'   => '"' . $dli_id . '"',
			),
		),
	)
);

$dli_pubblicazioni = new WP_Query(
	array(
		'posts_per_page' => -1,
		'post_type'      => 'pubblicazione',
		'orderby'        => 'anno',
		'order'          => 'ASC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Needed for ACF relationship fields stored as serialized arrays.
		'meta_query'     => array(
			array(
				'key'     => 'autori',
				'compare' => 'LIKE',
				'value'   => $dli_id,
			),
		),
	)
);
?>
	<!-- START CONTENT -->
		<main id="main-container" role="main">

			<!-- BANNER PERSONA: nessuna foto di copertina disponibile per una
				persona, quindi hero a sfondo pieno (default Ottanio) con breadcrumb
				integrato, come nelle pagine di elenco/archivio, invece del pattern
				foto+overlay usato per le altre schede. -->
			<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-persona-title">
				<div class="container">
					<div class="row align-items-stretch">
						<div class="col-12 col-lg-7">
							<section class="pt-2">
								<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
							</section>
							<div class="it-hero-text-wrapper px-lg-2">
								<div class="row align-items-center">
									<?php if ( $dli_image_url ) : ?>
									<div class="col-auto">
										<div class="avatar size-xxl">
											<img src="<?php echo esc_url( $dli_image_url ); ?>" alt="" aria-hidden="true" />
										</div>
									</div>
									<?php endif; ?>
									<div class="col">
										<h2 class="text-white mb-1" id="dli-hero-persona-title"><?php echo esc_html( dli_get_persona_display_name( dli_get_field( 'nome' ), dli_get_field( 'cognome' ), $dli_title ) ); ?></h2>
										<?php if ( $dli_categoria_appartenenza || $dli_nome_struttura ) : ?>
										<p class="text-white mb-0">
											<?php
											echo esc_html( $dli_categoria_appartenenza );
											if ( $dli_nome_struttura ) {
												echo ( $dli_categoria_appartenenza ? ', ' : '' ) . esc_html( $dli_nome_struttura );
											}
											?>
										</p>
										<?php endif; ?>
									</div>
								</div>
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

			<!-- DETTAGLIO PERSONA -->
			<div class="container p-5">
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
								data-bs-target="#navbarNav">
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
										<?php
											$dli_show_label = dli_get_configuration_field_by_lang( 'label_person_details_is_visible', 'persone' );
										if ( 'false' !== $dli_show_label ) {
											?>
											<h3>
											<?php echo esc_html__( 'Dettagli della persona', 'design_laboratori_italia' ); ?>
											</h3>
											<?php
										}
										?>
											<div class="progress">
												<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
											<ul class="link-list">
													<?php if ( '' !== $dli_bio ) { ?>
												<li class="nav-item">
													<a class="nav-link active" href="#p1">
														<span><?php esc_html_e( 'Biografia', 'design_laboratori_italia' ); ?> </span>
													</a>
												</li>
														<?php
													}
													if ( $dli_progetti && $dli_progetti->have_posts() ) {
														?>
												<li class="nav-item">
													<a class="nav-link" href="#p2">
														<span><?php esc_html_e( 'Progetti', 'design_laboratori_italia' ); ?></span>
													</a>
												</li>
														<?php
													}
													// Check whether there are research activities to display.
													$dli_indirizzi_di_ricerca_ids = array();
													while ( $dli_progetti->have_posts() ) {
														$dli_progetti->the_post();
														$dli_project_id        = get_the_ID();
														$dli_indirizzi_ricerca = dli_get_field( 'elenco_indirizzi_di_ricerca_correlati' );
														$dli_indirizzi_ricerca = $dli_indirizzi_ricerca ? $dli_indirizzi_ricerca : array();
														foreach ( $dli_indirizzi_ricerca as $dli_indirizzo_ricerca ) {
															array_push( $dli_indirizzi_di_ricerca_ids, $dli_indirizzo_ricerca->ID );
														}
													}
													$dli_progetti->rewind_posts();
													if ( is_array( $dli_indirizzi_di_ricerca_ids ) && count( $dli_indirizzi_di_ricerca_ids ) > 0 ) {
														?>
												<li class="nav-item">
													<a class="nav-link" href="#p3">
														<span>
														<?php
															echo esc_html__( 'Attività di ricerca', 'design_laboratori_italia' );
														?>
														</span>
													</a>
												</li>
														<?php
													}
													if ( $dli_pubblicazioni && $dli_pubblicazioni->have_posts() ) {
														?>
												<li class="nav-item">
													<a class="nav-link" href="#p4">
														<span><?php esc_html_e( 'Pubblicazioni', 'design_laboratori_italia' ); ?></span>
													</a>
												</li>
														<?php
													}
													wp_reset_postdata();
													if ( ( is_array( $dli_allegato_cv ) && count( $dli_allegato_cv ) > 0 ) || ( is_array( $dli_allegato1 ) && count( $dli_allegato1 ) > 0 )
													|| ( is_array( $dli_allegato2 ) && count( $dli_allegato2 ) > 0 ) || ( is_array( $dli_allegato3 ) && count( $dli_allegato3 ) > 0 ) ) {
														?>
												<li class="nav-item">
													<a class="nav-link" href="#p5">
														<span><?php esc_html_e( 'Ulteriori informazioni', 'design_laboratori_italia' ); ?></span>
													</a>
												</li>
														<?php
													}
													if ( ( '' !== $dli_telefono ) || ( '' !== $dli_email ) || ( '' !== $dli_sito_web ) ) {
														?>
												<li class="nav-item">
													<a class="nav-link" href="#p6">
														<span><?php esc_html_e( 'Contatti', 'design_laboratori_italia' ); ?></span>
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
					<div class="col-12 col-lg-8 offset-lg-1 it-page-sections-container">
						<?php
						if ( '' !== $dli_bio ) {
							?>
						<h3 class="it-page-section h4" id="p1"><?php esc_html_e( 'Biografia', 'design_laboratori_italia' ); ?></h3>
							<?php the_content(); ?>
							<?php
						}
						if ( $dli_progetti && $dli_progetti->have_posts() ) {
							?>
						<h3 class="it-page-section h4" id="p2"><?php esc_html_e( 'Progetti', 'design_laboratori_italia' ); ?></h3>
						<!-- PROGETTI -->
						<section id="progetti">
							<div class="row g-3 pb-3">
							<?php
							while ( $dli_progetti->have_posts() ) {
								$dli_progetti->the_post();
								$dli_project_id    = get_the_ID();
								$dli_project_title = get_the_title( $dli_project_id );
								?>
								<div class="col-md-6">
									<article class="it-card rounded shadow h-100">
										<h3 class="it-card-title cardTitlecustomSpacing h5">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder' ); ?>"></use>
											</svg>
											<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( $dli_project_title ); ?></a>
										</h3>
										<div class="it-card-body">
											<p class="it-card-text"><?php echo wp_kses_post( dli_get_field( 'descrizione_breve' ) ); ?></p>
										</div>
									</article>
								</div>
								<?php
							}
							?>
							</div>
						</section>
							<?php
						}
						if ( is_array( $dli_indirizzi_di_ricerca_ids ) && count( $dli_indirizzi_di_ricerca_ids ) > 0 ) {
							// recupero la lista degli indirizzi di ricerca.
							$dli_indirizzi_di_ricerca = new WP_Query(
								array(
									'posts_per_page' => -1,
									'post_type'      => 'indirizzo-di-ricerca',
									'orderby'        => 'title',
									'order'          => 'ASC',
									'post__in'       => $dli_indirizzi_di_ricerca_ids,
								)
							);
							?>
						<h3 class="it-page-section h4 pt-3" id="p3"><?php echo esc_html__( 'Attività di ricerca', 'design_laboratori_italia' ); ?></h3>
						<!-- INDIRIZZI DI RICERCA -->
						<section id="indirizzi-ricerca">
							<div class="row g-3 pb-3">
							<?php
							while ( $dli_indirizzi_di_ricerca->have_posts() ) {
								$dli_indirizzi_di_ricerca->the_post();
								$dli_indirizzo_ricerca_id    = get_the_ID();
								$dli_indirizzo_ricerca_title = get_the_title( $dli_indirizzo_ricerca_id );
								?>
								<div class="col-md-6">
									<article class="it-card rounded shadow h-100">
										<h3 class="it-card-title cardTitlecustomSpacing h5">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder' ); ?>"></use>
											</svg>
											<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( $dli_indirizzo_ricerca_title ); ?></a>
										</h3>
										<div class="it-card-body">
											<p class="it-card-text"><?php echo wp_kses_post( dli_get_field( 'descrizione_breve' ) ); ?></p>
										</div>
									</article>
								</div>
								<?php
							}
							?>
							</div>
						</section>
							<?php
						}
						if ( $dli_pubblicazioni && $dli_pubblicazioni->have_posts() ) {
							?>
						<h3 class="it-page-section h4 pt-3" id="p4"><?php esc_html_e( 'Pubblicazioni', 'design_laboratori_italia' ); ?></h3>
						<!-- PUBBLICAZIONI -->
						<section id="pubblicazioni">
							<ul class="list-unstyled mb-0">
							<?php
							while ( $dli_pubblicazioni->have_posts() ) {
								$dli_pubblicazioni->the_post();
								$dli_publication_id    = get_the_ID();
								$dli_publication_title = get_the_title( $dli_publication_id );
								$dli_url               = dli_get_field( 'url' );
								?>
								<li class="py-3 border-bottom">
									<h4 class="h6 mb-1">
										<svg class="icon icon-sm icon-primary align-top" aria-hidden="true" focusable="false">
											<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note' ); ?>"></use>
										</svg>
										<?php if ( $dli_url ) : ?>
											<a href="<?php echo esc_url( $dli_url ); ?>"><?php echo esc_html( $dli_publication_title ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $dli_publication_title ); ?>
										<?php endif; ?>
									</h4>
									<div class="mb-0 text-secondary"><?php echo wp_kses_post( wpautop( do_shortcode( get_the_content() ) ) ); ?></div>
								</li>
								<?php
							}
							?>
							</ul>
						</section>
							<?php
						}
						wp_reset_postdata(); // pubblicazioni.

						if ( ( is_array( $dli_allegato_cv ) && count( $dli_allegato_cv ) > 0 ) || ( is_array( $dli_allegato1 ) && count( $dli_allegato1 ) > 0 )
						|| ( is_array( $dli_allegato2 ) && count( $dli_allegato2 ) > 0 ) || ( is_array( $dli_allegato3 ) && count( $dli_allegato3 ) > 0 ) ) {
							?>
						<h3 class="it-page-section h4 pt-3" id="p5"><?php esc_html_e( 'Ulteriori informazioni', 'design_laboratori_italia' ); ?></h3>
						<section id="ulteriori-info">
							<div class="it-list-wrapper">
								<ul class="it-list">
							<?php
							$dli_allegati_utili = array( $dli_allegato_cv, $dli_allegato1, $dli_allegato2, $dli_allegato3 );
							foreach ( $dli_allegati_utili as $dli_allegato ) {
								if ( ! is_array( $dli_allegato ) || empty( $dli_allegato ) ) {
									continue;
								}
								$dli_allegato_metadata = '';
								if ( ! empty( $dli_allegato['mime_type'] ) ) {
									$dli_mime_parts        = explode( '/', $dli_allegato['mime_type'] );
									$dli_allegato_metadata = strtoupper( end( $dli_mime_parts ) );
								}
								if ( ! empty( $dli_allegato['filesize'] ) ) {
									$dli_allegato_metadata .= ( $dli_allegato_metadata ? ', ' : '' ) . size_format( (int) $dli_allegato['filesize'] );
								}
								?>
								<li>
									<a class="list-item" href="<?php echo esc_url( $dli_allegato['url'] ); ?>">
										<div class="it-rounded-icon">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone">
											<span class="text"><?php echo esc_html( $dli_allegato['title'] ); ?></span>
											<?php if ( $dli_allegato_metadata ) : ?>
												<span class="metadata"><?php echo esc_html( $dli_allegato_metadata ); ?></span>
											<?php endif; ?>
										</div>
									</a>
								</li>
								<?php
							}
							?>
								</ul>
							</div>
						</section>
							<?php
						}
						?>
							<?php if ( ( '' !== $dli_telefono ) || ( '' !== $dli_email ) || ( '' !== $dli_sito_web ) ) { ?>
							<h3 class="it-page-section h4 pt-3" id="p6"><?php esc_html_e( 'Contatti', 'design_laboratori_italia' ); ?></h3>
							<div class="it-list-wrapper">
								<ul class="it-list">
									<?php if ( '' !== $dli_telefono ) { ?>
									<li>
										<div class="list-item">
											<div class="it-rounded-icon">
												<svg class="icon" role="img">
													<title><?php echo esc_html__( 'Telephone', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_telefono ); ?></span></div>
										</div>
									</li>
										<?php
									}
									if ( '' !== $dli_email ) {
										?>
									<li>
											<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $dli_email ) ); ?>" class="list-item">
											<div class="it-rounded-icon">
												<svg class="icon" role="img">
													<title><?php echo esc_html__( 'Mail', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo esc_html( $dli_email ); ?></span></div>
										</a>
									</li>
										<?php
									}
									if ( '' !== $dli_sito_web ) {
										?>
									<li>
										<a class="list-item" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dli_sito_web ); ?>">
											<div class="it-rounded-icon">
												<svg class="icon" role="img">
													<title><?php echo esc_html__( 'Website Link', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link' ); ?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo esc_html__( 'Sito web', 'design_laboratori_italia' ); ?></span></div>
										</a>
									</li>
										<?php
									}
									?>
								</ul>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</main>
<!-- END CONTENT -->
<?php
wp_reset_postdata();
get_footer();
