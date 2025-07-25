<?php
/**
 * Detail page for the post-type: persona.
 *
 * @package Design_Laboratori_Italia
 */

get_header();

while ( have_posts() ) {
	the_post();
	$ID                     = get_the_ID();
	$title                  = get_the_title( $ID );
	$image_url              = dli_get_persona_avatar( $post, $ID );
	$bio                    = get_the_content();
	$categoria_appartenenza = dli_get_field( 'categoria_appartenenza' )[0]->nome;
	$allegato_cv            = dli_get_field( 'allegato_cv' );
	$allegato1              = dli_get_field( 'allegato1' );
	$allegato2              = dli_get_field( 'allegato2' );
	$allegato3              = dli_get_field( 'allegato3' );
	$telefono               = dli_get_field( 'telefono' );
	$email                  = dli_get_field( 'email' );
	$sito_web               = dli_get_field( 'sito_web' );
	$terms                  = get_the_terms( $ID, 'struttura' );
	$terms                  = $terms ? $terms : array();
	$nome_struttura         = count( $terms ) ? $terms[0]->name : '';
}

// recupero la lista dei progetti.
$progetti = new WP_Query(
	array(
	'posts_per_page' => -1,
	'post_type'      => 'progetto',
	'orderby'        => 'data_inizio', //I’m afraid the ordering post by subfield is not possible, https://support.advancedcustomfields.com/forums/topic/wp_query-and-sub-fields/
	'order'          => 'DESC',
	'meta_query'     => array(
		'relation'  => 'OR',
		array(
			'key'     => 'responsabile_del_progetto',
			'compare' => 'LIKE',
			'value'   => '"' . $ID . '"',
		),
		array(
			'key'       => 'persone',
			'compare'   => 'LIKE',
			'value'     => '"' . $ID . '"',
		),
	),
	)
);

$pubblicazioni = new WP_Query(
	array(
		'posts_per_page' => -1,
		'post_type'      => 'pubblicazione',
		'orderby'        => 'anno',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key' => 'autori',
				'compare' => 'LIKE',
				'value' => $ID,
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
			<section id="banner-persone" aria-describedby="Scheda persona">
				<div class="p-3 primary-bg-c1">
					<div class="container">
						<div class="row">
							<div class="col-12 col-lg-3 d-flex align-items-center justify-content-center">
								<div class="avatar size-xxl">
								<?php
								if ( $image_url ) {
									echo "<img src='" . esc_url($image_url) . "' alt='" . esc_attr( dli_get_persona_display_name( dli_get_field( 'nome' ), dli_get_field( 'cognome' ), $title ) )
									. "' title='" . esc_attr( dli_get_persona_display_name( dli_get_field( 'nome' ), dli_get_field( 'cognome' ), $title ) );
									if ( $nome_struttura ) {
										echo '- ' . esc_attr($nome_struttura);
									}
									echo "' aria-hidden='true'/>";
								}
								?>
								</div><!-- /avatar -->
							</div><!-- /col-lg-3 -->
							<div class="col-12 col-lg-9">
								<div class="section-title">
									<h2 class="mb-3 mt-3"><?php echo esc_attr( dli_get_persona_display_name( dli_get_field( 'nome' ), dli_get_field( 'cognome' ), $title ) ); ?></h2>
									<p>
										<?php
										echo $categoria_appartenenza;
										if ( $nome_struttura ) {
											echo ', ' . esc_attr( $nome_struttura );
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
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left';?>" xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left'?>"></use>
										</svg>
										<span>Indietro</span>
									</a>
									<div class="menu-wrapper">
										<div class="link-list-wrapper">
										<?php
											$show_label = dli_get_configuration_field_by_lang( 'label_person_details_is_visible', 'persone' );
											if ( $show_label != 'false' ){
										?>
											<h3>
												<?php echo __( 'Dettagli della persona', 'design_laboratori_italia' );?>
											</h3>
										<?php
											}
										?>
											<div class="progress">
												<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
											<ul class="link-list">
												<?php if ( $bio != '' ) { ?>
												<li class="nav-item">
													<a class="nav-link active" href="#p1">
														<span><?php _e( 'Biografia', "design_laboratori_italia" ); ?> </span>
													</a>
												</li>
												<?php
													}
												if ( $progetti && $progetti->have_posts() ) {
													?>
												<li class="nav-item">
													<a class="nav-link" href="#p2">
														<span><?php _e( 'Progetti', "design_laboratori_italia" ); ?></span>
													</a>
												</li>
												<?php
												}
												// controllo che ci siano indirizzi di ricerca da mostrare.
												$indirizzi_di_ricerca_ids = array();
												while ( $progetti->have_posts() ) {
													$progetti->the_post();
													$ID        = get_the_ID();
													$indirizzi = dli_get_field( 'elenco_indirizzi_di_ricerca_correlati' );
													$indirizzi = $indirizzi ? $indirizzi : array();
													foreach ( $indirizzi as $indirizzo ) {
														array_push( $indirizzi_di_ricerca_ids, $indirizzo->ID );
													}
												}
												if ( is_array( $indirizzi_di_ricerca_ids ) && count( $indirizzi_di_ricerca_ids ) > 0 ) {
												?>
												<li class="nav-item">
													<a class="nav-link" href="#p3">
														<span><?php
															echo __( 'Attività di ricerca', "design_laboratori_italia" );
														?></span>
													</a>
												</li>
												<?php
												}
												if ( $pubblicazioni && $pubblicazioni->have_posts() ) {
												?>
												<li class="nav-item">
													<a class="nav-link" href="#p4">
														<span><?php _e( 'Pubblicazioni', "design_laboratori_italia" ); ?></span>
													</a>
												</li>
												<?php
												}
												wp_reset_postdata();
												if ( ( is_array( $allegato_cv ) && count( $allegato_cv ) > 0 ) || ( is_array( $allegato1 ) && count( $allegato1 ) > 0 )
												|| ( is_array( $allegato2 ) && count( $allegato2 ) > 0 ) || ( is_array( $allegato3 ) && count( $allegato3 ) > 0 ) ) {
												?>
												<li class="nav-item">
													<a class="nav-link" href="#p5">
														<span><?php _e( 'Ulteriori informazioni', "design_laboratori_italia" ); ?></span>
													</a>
												</li>
												<?php
												}
												if ( ( $telefono != '' ) || ( $email != '' ) || ( $sito_web != '' ) ) {
													?>
												<li class="nav-item">
													<a class="nav-link" href="#p6">
														<span><?php _e( 'Contatti', "design_laboratori_italia" ); ?></span>
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
							if ( $bio != '' ) {
							?>
						<h3 class="it-page-section h4" id="p1"><?php _e( 'Biografia', "design_laboratori_italia" ); ?></h3>
						<div class="row pb-3">
							<?php the_content(); ?>
						</div>
							<?php
						}
						if ( $progetti && $progetti->have_posts() ) {
							?>
						<h3 class="it-page-section h4" id="p2"><?php _e( 'Progetti', "design_laboratori_italia" ); ?></h3>
						<!-- PROGETTI -->
						<section id="progetti">
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								while ( $progetti->have_posts() ) {
									$progetti->the_post();
									$ID        = get_the_ID();
									$title     = get_the_title( $ID );
									?>
									<!--start card-->
									<div class="card card-teaser rounded shadow">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5">
												<svg class="icon" role="img" aria-labelledby="Folder">
													<title>Folder</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder';?>"></use>
												</svg>
												<a href="<?php echo get_permalink(); ?>"><?php echo esc_attr( $title ); ?></a>
											</h3>
											<div class="card-text">
												<p><?php echo esc_attr( dli_get_field( 'descrizione_breve' ) ); ?></p>
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
						if ( is_array( $indirizzi_di_ricerca_ids ) && count( $indirizzi_di_ricerca_ids ) > 0 ) {
							// recupero la lista degli indirizzi di ricerca.
							$indirizzi_di_ricerca = new WP_Query(
								array(
									'posts_per_page' => -1,
									'post_type'      => 'indirizzo-di-ricerca',
									'orderby'        => 'title',
									'order'          => 'ASC',
									'post__in'       => $indirizzi_di_ricerca_ids,
								)
							);
							?>
						<h3 class="it-page-section h4 pt-3" id="p3"><?php echo __( 'Attività di ricerca', "design_laboratori_italia" );?></h3>
						<!-- INDIRIZZI DI RICERCA -->
						<section id="indirizzi-ricerca">
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								while ( $indirizzi_di_ricerca->have_posts() ) {
									$indirizzi_di_ricerca->the_post();
									$ID    = get_the_ID();
									$indirizzo_ricerca_title = get_the_title( $ID );
									?>
											<!--start card-->
											<div class="card card-teaser rounded shadow">
												<div class="card-body">
													<h3 class="card-title cardTitlecustomSpacing h5 ">
														<svg class="icon" role="img" aria-labelledby="Folder">
															<title>Folder</title>
															<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder';?>"></use>
														</svg>
														<a href="<?php echo get_permalink(); ?>"><?php echo esc_attr( $indirizzo_ricerca_title ); ?></a>
													</h3>
													<div class="card-text">
														<p><?php echo esc_attr( dli_get_field( 'descrizione_breve' ) ); ?></p>
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
						if ( $pubblicazioni && $pubblicazioni->have_posts() ) {
							?>
						<h3 class="it-page-section h4 pt-3" id="p4"><?php _e( 'Pubblicazioni', "design_laboratori_italia" ); ?></h3>
						<!-- PUBBLICAZIONI -->
						<section id="pubblicazioni">    
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
								while ( $pubblicazioni->have_posts() ) {
									$pubblicazioni->the_post();
									$ID    = get_the_ID();
									$title = get_the_title( $ID );
									$url      = dli_get_field( 'url' );
									?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="Note">
													<title>Note</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note';?>"></use>
												</svg>
												<?php
												if($url) {
													?>
												<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_attr( $title ); ?></a>
												<?php }
												else {
													echo esc_attr( $title );
												}
												?>
											</h3>
											<div class="card-text">
												<p><?php echo esc_attr( the_content() ); ?></p>
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

						if ( ( is_array( $allegato_cv ) && count( $allegato_cv ) > 0 ) || ( is_array( $allegato1 ) && count( $allegato1 ) > 0 )
						|| ( is_array( $allegato2 ) && count( $allegato2 ) > 0 ) || ( is_array( $allegato3 ) && count( $allegato3 ) > 0 ) ) {
							?>
						<h3 class="it-page-section h4 pt-3" id="p5"><?php _e( 'Ulteriori informazioni', "design_laboratori_italia" ); ?></h3>
						<section id="ulteriori-info">
							<div class="row pb-3">
								<div class="card-wrapper card-teaser-wrapper">
								<?php
									if ( ( is_array( $allegato_cv ) && count( $allegato_cv ) > 0 ) ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf';?>"></use>
												</svg>
												<a href="<?php echo esc_url($allegato_cv['url']); ?>"><?php echo esc_attr( $allegato_cv['title'] ); ?>&nbsp;</a>
											</h3>
										</div>
									</div><!--end card-->
									<?php } ?>
									<?php
									if ( ( is_array( $allegato1 ) && count( $allegato1 ) > 0 ) ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf';?>"></use>
												</svg>
												<a href="<?php echo esc_url($allegato1['url']); ?>"><?php echo esc_attr( $allegato1['title'] ); ?></a>
											</h3>
										</div>
									</div><!--end card-->
									<?php }
									if ( ( is_array( $allegato2 ) && count( $allegato2 ) > 0 ) ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf';?>"></use>
												</svg>
												<a href="<?php echo esc_url($allegato2['url']); ?>"><?php echo esc_attr( $allegato2['title'] ); ?></a>
											</h3>
										</div>
									</div><!--end card-->
									<?php }
									if ( ( is_array( $allegato3 ) && count( $allegato3 ) > 0 ) ) {
										?>
									<!--start card-->
									<div class="card card-teaser rounded shadow ">
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5 ">
												<svg class="icon" role="img" aria-labelledby="File PDF">
													<title>File PDF</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf';?>"></use>
												</svg>
												<a href="<?php echo esc_url($allegato3['url']); ?>"><?php echo esc_attr( $allegato3['title'] ); ?></a>
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
						<?php if ( ( $telefono != "" ) || ( $email != "" ) || ( $sito_web != "" ) ) { ?>
							<h3 class="it-page-section h4 pt-3" id="p6"><?php _e( 'Contatti', "design_laboratori_italia" ); ?></h3>
							<div class="it-list-wrapper">
								<ul class="it-list">
								<?php if ( $telefono != '' ) { ?>
									<li>
										<div class="list-item">
											<div class="it-rounded-icon">
												<svg class="icon" role="img" aria-labelledby="Telephone">
													<title>Telephone</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone';?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo esc_attr( $telefono ); ?></span></div>
										</div>
									</li>
									<?php
								}
								if ( $email != '' ) {
									?>
									<li>
										<a href="mailto:<?php echo esc_attr( $email ); ?>" class="list-item">
											<div class="it-rounded-icon">
												<svg class="icon" role="img" aria-labelledby="Mail">
													<title>Mail</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail';?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo esc_attr( $email ); ?></span></div>
										</a>
									</li>
									<?php
								}
								if ( $sito_web != '' ) {
									?>
									<li>
										<a class="list-item" target="_blank" href="<?php echo esc_attr( $sito_web ); ?>">
											<div class="it-rounded-icon">
												<svg class="icon" role="img" aria-labelledby="Link">
													<title>Website Link</title>
													<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link';?>"></use>
												</svg>
											</div>
											<div class="it-right-zone"><span class="text"><?php echo __( 'Sito web', 'design_laboratori_italia' ); ?></span></div>
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
