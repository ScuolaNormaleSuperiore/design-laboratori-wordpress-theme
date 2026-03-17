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

			<!-- BREADCRUMB -->
			<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

			<!-- BANNER PERSONE -->
			<section id="banner-persone">
				<div class="p-3 primary-bg-c1">
					<div class="container">
						<div class="row">
							<div class="col-12 col-lg-3 d-flex align-items-center justify-content-center">
								<div class="avatar size-xxl">
								<?php
								if ( $dli_image_url ) {
									echo "<img src='" . esc_url( $dli_image_url ) . "' alt='" . esc_attr( dli_get_persona_display_name( dli_get_field( 'nome' ), dli_get_field( 'cognome' ), $dli_title ) )
									. "' title='" . esc_attr( dli_get_persona_display_name( dli_get_field( 'nome' ), dli_get_field( 'cognome' ), $dli_title ) );
									if ( $dli_nome_struttura ) {
										echo '- ' . esc_html( $dli_nome_struttura );
									}
									echo "' aria-hidden='true'/>";
								}
								?>
								</div><!-- /avatar -->
							</div><!-- /col-lg-3 -->
							<div class="col-12 col-lg-9">
								<div class="section-title">
									<h2 class="mb-3 mt-3"><?php echo esc_html( dli_get_persona_display_name( dli_get_field( 'nome' ), dli_get_field( 'cognome' ), $dli_title ) ); ?></h2>
									<p>
										<?php
											echo esc_html( $dli_categoria_appartenenza );
										if ( $dli_nome_struttura ) {
											echo ', ' . esc_html( $dli_nome_struttura );
										}
										?>
									</p>
								</div><!-- /title-section -->
							</div><!-- /col-12 col-lg-9 -->
						</div><!-- /row -->
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
								aria-label="Toggle navigation"
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
										<svg class="icon icon-sm icon-primary align-top" role="img" aria-labelledby="Chevron Left">
											<title>Chevron Left</title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>" xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
										</svg>
										<span>Indietro</span>
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
					<div class="col-12 col-lg-9 it-page-sections-container">
						<?php
						if ( '' !== $dli_bio ) {
							?>
						<h3 class="it-page-section h4" id="p1"><?php esc_html_e( 'Biografia', 'design_laboratori_italia' ); ?></h3>
						<div class="row pb-3">
							<?php the_content(); ?>
						</div>
							<?php
						}
						if ( $dli_progetti && $dli_progetti->have_posts() ) {
							?>
						<h3 class="it-page-section h4" id="p2"><?php esc_html_e( 'Progetti', 'design_laboratori_italia' ); ?></h3>
						<!-- PROGETTI -->
						<section id="progetti">
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								while ( $dli_progetti->have_posts() ) {
									$dli_progetti->the_post();
									$dli_project_id    = get_the_ID();
									$dli_project_title = get_the_title( $dli_project_id );
									?>
									<!--start card-->
									<div class="card card-teaser rounded shadow">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5">
												<svg class="icon" role="img" aria-labelledby="Folder">
													<title>Folder</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder' ); ?>"></use>
												</svg>
												<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( $dli_project_title ); ?></a>
											</h3>
											<div class="card-text">
												<p><?php echo wp_kses_post( dli_get_field( 'descrizione_breve' ) ); ?></p>
											</div>
										</div>
									</div>
									<!--end card-->
									<?php
								}
								?>
								</div> <!--end card wrapper-->
							</div> <!--end row-->
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
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								while ( $dli_indirizzi_di_ricerca->have_posts() ) {
									$dli_indirizzi_di_ricerca->the_post();
									$dli_indirizzo_ricerca_id    = get_the_ID();
									$dli_indirizzo_ricerca_title = get_the_title( $dli_indirizzo_ricerca_id );
									?>
											<!--start card-->
											<div class="card card-teaser rounded shadow">
												<div class="card-body">
													<h3 class="card-title cardTitlecustomSpacing h5 ">
														<svg class="icon" role="img" aria-labelledby="Folder">
															<title>Folder</title>
															<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder' ); ?>"></use>
														</svg>
														<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( $dli_indirizzo_ricerca_title ); ?></a>
													</h3>
													<div class="card-text">
														<p><?php echo wp_kses_post( dli_get_field( 'descrizione_breve' ) ); ?></p>
													</div>
												</div>
											</div>
											<!--end card-->
									<?php
								}
								?>
								</div> <!--end card wrapper-->
							</div> <!--end row-->
						</section>
							<?php
						}
						if ( $dli_pubblicazioni && $dli_pubblicazioni->have_posts() ) {
							?>
						<h3 class="it-page-section h4 pt-3" id="p4"><?php esc_html_e( 'Pubblicazioni', 'design_laboratori_italia' ); ?></h3>
						<!-- PUBBLICAZIONI -->
						<section id="pubblicazioni">    
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								while ( $dli_pubblicazioni->have_posts() ) {
									$dli_pubblicazioni->the_post();
									$dli_publication_id    = get_the_ID();
									$dli_publication_title = get_the_title( $dli_publication_id );
									$dli_url               = dli_get_field( 'url' );
									?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="Note">
													<title>Note</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note' ); ?>"></use>
												</svg>
												<?php
												if ( $dli_url ) {
													?>
												<a href="<?php echo esc_url( $dli_url ); ?>"><?php echo esc_html( $dli_publication_title ); ?></a>
													<?php
												} else {
													echo esc_html( $dli_publication_title );
												}
												?>
											</h3>
											<div class="card-text">
													<p><?php echo wp_kses_post( wpautop( do_shortcode( get_the_content() ) ) ); ?></p>
											</div>
										</div>
									</div><!--end card-->
									<?php
								}
								?>
								</div> <!--end card wrapper-->
							</div> <!--end row-->
						</section>
							<?php
						}
						wp_reset_postdata(); // pubblicazioni.

						if ( ( is_array( $dli_allegato_cv ) && count( $dli_allegato_cv ) > 0 ) || ( is_array( $dli_allegato1 ) && count( $dli_allegato1 ) > 0 )
						|| ( is_array( $dli_allegato2 ) && count( $dli_allegato2 ) > 0 ) || ( is_array( $dli_allegato3 ) && count( $dli_allegato3 ) > 0 ) ) {
							?>
						<h3 class="it-page-section h4 pt-3" id="p5"><?php esc_html_e( 'Ulteriori informazioni', 'design_laboratori_italia' ); ?></h3>
						<section id="ulteriori-info">
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								if ( ( is_array( $dli_allegato_cv ) && count( $dli_allegato_cv ) > 0 ) ) {
									?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
												</svg>
												<a href="<?php echo esc_url( $dli_allegato_cv['url'] ); ?>"><?php echo esc_attr( $dli_allegato_cv['title'] ); ?>&nbsp;</a>
											</h3>
										</div>
									</div><!--end card-->
									<?php } ?>
									<?php
									if ( ( is_array( $dli_allegato1 ) && count( $dli_allegato1 ) > 0 ) ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
												</svg>
												<a href="<?php echo esc_url( $dli_allegato1['url'] ); ?>"><?php echo esc_attr( $dli_allegato1['title'] ); ?></a>
											</h3>
										</div>
									</div><!--end card-->
										<?php
									}
									if ( ( is_array( $allegato2 ) && count( $allegato2 ) > 0 ) ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
												</svg>
												<a href="<?php echo esc_url( $allegato2['url'] ); ?>"><?php echo esc_attr( $allegato2['title'] ); ?></a>
											</h3>
										</div>
									</div><!--end card-->
										<?php
									}
									if ( ( is_array( $allegato3 ) && count( $allegato3 ) > 0 ) ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
												</svg>
												<a href="<?php echo esc_url( $allegato3['url'] ); ?>"><?php echo esc_attr( $allegato3['title'] ); ?></a>
											</h3>
										</div>
									</div><!--end card-->
									<?php } ?>
									</div> <!--end card wrapper-->
							</div> <!--end row-->
						</section>
							<?php
						}
						?>
							<?php if ( ( '' !== $telefono ) || ( '' !== $email ) || ( '' !== $sito_web ) ) { ?>
							<h3 class="it-page-section h4 pt-3" id="p6"><?php esc_html_e( 'Contatti', 'design_laboratori_italia' ); ?></h3>
							<div class="it-list-wrapper">
								<ul class="it-list">
									<?php if ( '' !== $telefono ) { ?>
									<li>
										<div class="list-item">
											<div class="it-rounded-icon">
												<svg class="icon" role="img" aria-labelledby="Telephone">
													<title>Telephone</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo esc_html( $telefono ); ?></span></div>
										</div>
									</li>
										<?php
									}
									if ( '' !== $email ) {
										?>
									<li>
											<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>" class="list-item">
											<div class="it-rounded-icon">
												<svg class="icon" role="img" aria-labelledby="Mail">
													<title>Mail</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo esc_html( $email ); ?></span></div>
										</a>
									</li>
										<?php
									}
									if ( '' !== $sito_web ) {
										?>
									<li>
										<a class="list-item" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $sito_web ); ?>">
											<div class="it-rounded-icon">
												<svg class="icon" role="img" aria-labelledby="Link">
													<title>Website Link</title>
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
