<?php
/**
 * Template Name: Persone
 *
 * Persone template file
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

?>

<!-- START CONTENT -->
<form action="<?php $_SERVER['PHP_SELF']; ?>" id="personeform" method="GET">
	<main id="main-container">
		<!-- BREADCRUMB -->
		<section id ="breadcrumb">
			<div class="container">
				<div class="row">
					<div class="col-12 ms-4 ">
						<nav class="breadcrumb-container" aria-label="Percorso di navigazione">
							<ol class="breadcrumb pb-0">
								<li class="breadcrumb-item"><a href="sf-index.html">Home</a><span class="separator">&gt;</span></li>
								<li class="breadcrumb-item active" aria-current="Elenco persone"><?php _e( 'Persone', 'design_laboratori_italia' ); ?></li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</section>

		<!-- BANNER PERSONE -->
		<section id="banner-persone" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-persone">
			<div class="section-muted p-3 primary-bg-c1">
				<div class="container">
					<div class="hero-title text-left ms-4 pb-3 pt-3">
						<h2 class="p-0  "><?php _e( 'Le persone', 'design_laboratori_italia' ); ?></h2>
						<p class="font-weight-normal"><?php echo dli_get_option( 'testo_sezione_persone', 'persone' ); ?></p>
					</div>
				</div>
			</div>
		</section>

		<!-- ELENCO PERSONE -->
		<div class="container my-4">
			<section class="section bg-gray-light py-5">
				<div class="container">

					<?php
						// recupero i termini della tassonomia struttura.
						$strutture = get_terms(
							[
								'taxonomy' => 'struttura',
								'hide_empty' => false,
							]
						);
						// visualizzo i filtri sulle strutture solo se ne esistono almeno 2.
						if ( count( $strutture ) >= 1 ) {
							?>
						<!-- FILTRI SU STRUTTURE chips se presenti -->
						<div class="title-section text-center mb-5">
							<?php
							foreach ( $strutture as $struttura ) {
								?>
								<div class="chip chip-simple">
									<span class="chip-label"><a href="?struttura=<?php echo $struttura->slug; ?>" title ="<?php _e( 'Filtra per', "design_laboratori_italia" ); ?>: <?php echo esc_attr($struttura->name); ?>"><?php echo esc_attr($struttura->name); ?></a></span>
								</div>
							<?php } ?>

							<div class="chip chip-simple">
								<span class="chip-label"><a href="<?php the_permalink(); ?>" title="<?php _e( 'Disattiva filtri', "design_laboratori_italia" ); ?>"><?php _e( 'Tutte le strutture', "design_laboratori_italia" ); ?></a></span>
							</div>
						</div>
						<!-- FINE FILTRI -->
							<?php
						}

						// recupero tutte le categorie.
						$categorie_persone = new WP_Query(
							array(
								'posts_per_page' => -1,
								'post_type'      => 'tipologia-persona',
								'meta_key'       => 'priorita',
								'orderby'        => 'meta_value_num',
								'order'          => 'ASC',
							)
						);

						wp_reset_postdata();

						// INIZIO LOOP CATEGORIE.
						while ( $categorie_persone->have_posts() ) {
							$categorie_persone->the_post();
							$nome_categoria = get_field( 'nome' );

							$categoria_id = get_the_ID();
							if ( isset( $_GET['struttura'] ) && $_GET['struttura'] != "" ) {
								$struttura = $_GET['struttura'];
								// recupero la lista delle persone filtrate per struttura.
								$persone = new WP_Query(
									array(
										'posts_per_page' => -1,
										'post_type'      => 'persona',
										'orderby'        => 'cognome',
										'order'          => 'ASC',
										'meta_query'     => array(
											array(
												'key'     => 'categoria_appartenenza',
												'compare' => 'LIKE',
												'value'   => '"' . $categoria_id . '"',
											),
										),
										'tax_query'   => array(
											array(
												'taxonomy' => 'struttura',
												'field'    => 'slug',
												'terms'    => "'" . $struttura . "'",
											),
										),
									)
								);
							}
							else {
								// recupero la lista DI TUTTE persone.
								$persone = new WP_Query(
									array(
										'posts_per_page' => -1,
										'post_type'      => 'persona',
										'orderby'        => 'cognome',
										'order'          => 'ASC',
										'meta_query'     => array(
											array(
												'key' => 'categoria_appartenenza',
												'compare' => 'LIKE',
												'value' => '"' . $categoria_id . '"',
											),
										),
									)
								);
							}

							if ( $persone ) {
								?>

								<!-- ELENCO AVATAR PERSONE -->
								<?php
								if ( $persone->have_posts() ) {
									?>
									<div class="row  mb-4">
										<div class="col-lg-3">
											<h3 class="text-lg-right mb-3 h4"><?php _e( $nome_categoria, "design_laboratori_italia" ); ?></h3>
										</div><!-- /col-lg-3 -->
										<div class="col-lg-9">
											<div class="row ">

										<?php
										while ( $persone->have_posts() ) {
											$persone->the_post();
											$escludi_da_elenco = get_field( 'escludi_da_elenco' );
											if ( ! $escludi_da_elenco ) {
												$nome                       = get_field( 'nome' );
												$cognome                    = get_field( 'cognome' );
												$foto                       = get_field( 'foto' );
												$disattiva_pagina_dettaglio = get_field( 'disattiva_pagina_dettaglio' );
												$ID                         = get_the_ID();
												$link_persona               = get_the_permalink( $ID );
												?>
												<div class="col-lg-4">
													<div class="avatar-wrapper avatar-extra-text">
														<div class="avatar size-xl">
															<img src="<?php echo dli_get_persona_avatar( $foto, $ID ); ?>" alt="<?php echo $foto['alt']; ?>" aria-hidden="true">
														</div>
														<div class="extra-text">
															<?php
															$terms = get_the_terms( $ID, 'struttura' );
															$nome_struttura = $terms[0]->name;
															if ( ! $disattiva_pagina_dettaglio ) {
																?>
																<h4><a href="<?php echo $link_persona; ?>"><?php echo esc_attr($nome) . " " . esc_attr($cognome); ?></a></h4>
																<?php
															}
															else {
																?>
																<h4><?php echo esc_attr($nome) . " " . esc_attr($cognome); ?></h4>
															<?php } ?>
															<time datetime="2023-09-15"><?php echo esc_attr($nome_struttura); ?>&nbsp;</time>
														</div>
													</div>
												</div><!-- /col-lg-4 -->
												<?php
											}
										}
										?>
											</div><!-- /row -->
										</div><!-- /col-lg-9 -->
									</div><!-- /row -->
									<?php
								}
							}
							wp_reset_postdata();
						}
						wp_reset_postdata();
						?>
				</div><!-- /container -->
			</section><!-- /section -->
		</div><!-- /container -->
	</main>
</form>
<!-- END CONTENT -->
<?php
get_footer();
