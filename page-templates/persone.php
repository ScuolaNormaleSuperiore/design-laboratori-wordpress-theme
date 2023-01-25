<?php
/* Template Name: Persone
 *
 * didattica template file
 *
 * @package Design_Scuole_Italia
 */
global $post;
get_header();

?>
		<main id="main-container" class="main-container bluelectric">
				<?php get_template_part("template-parts/common/breadcrumb"); ?>
				<?php
					while ( have_posts() ) {
						the_post();
						get_template_part("template-parts/hero/persone");

						//recupero i termini della tassonomia struttura
						$strutture = get_terms([
							'taxonomy' => 'struttura',
							'hide_empty' => false,
						]);
						//visualizzo i filtri sulle strutture solo se ne esistono almeno 2
						if(count($strutture) >= 1) {
						?>
							<aside class="badges-wrapper badges-main text-center">
								<div class="badges">
									<?php
									//TODO: aggiungere logica filtro
									foreach ( $strutture as $struttura ) { ?>
										<a href="<?php echo $struttura->name ?>" title="<?php _e("Filtra per", "design_laboratori_italia"); ?>: <?php echo $struttura->name; ?>" class="badge badge-sm badge-pill badge-outline-bluelectric"><?php echo $struttura->name; ?></a>
									<?php } ?>
									<a href="#" title="<?php _e("Disattiva filtri", "design_laboratori_italia"); ?>" class="badge badge-sm badge-pill badge-outline-bluelectric"><?php _e("Disattiva filtri", "design_laboratori_italia"); ?></a>
								</div><!-- /badges -->
              </aside>
							<section class="section bg-white py-5">
								<?php
								}

								//recupero tutte le categorie
								$categorie_persone= new WP_Query(array(
									'posts_per_page' => -1,
									'post_type' => 'tipologia-persona',
									'meta_key' => 'priorita',
									'orderby' => 'meta_value_num',
									'order' => 'ASC'
								));

								while($categorie_persone->have_posts()) {
									$categorie_persone->the_post();
									print_r($categorie_persone->get_the_post());
									$nome_categoria = get_field('nome');
									

									$categoria_id = get_the_ID();

									// recupero la lista delle persone
									$persone= new WP_Query(array(
										'posts_per_page' => -1,
										'post_type' => 'persona',
										'orderby' => 'cognome',
										'order' => 'ASC',
										'meta_query' => array(
													array(
														'key' => 'categoria_appartenenza', 
														'compare' => 'LIKE',
														'value' => '"' . $categoria_id . '"',
													)
										)
									));

									if($persone) {
										?>
											<div class="container">
												<?php if ($persone->have_posts()) { ?>
													<div class="row variable-gutters mb-4">
														<div class="col-lg-3">
															<h3 class="text-lg-right mb-3"><?php _e($nome_categoria, "design_laboratori_italia"); ?></h3>
														</div><!-- /col-lg-3 -->
														<div class="col-lg-9">
															<div class="row variable-gutters">
																<?php
																	while($persone->have_posts()) {
																		$persone->the_post();
																		$escludi_da_elenco = get_field('escludi_da_elenco');
																		if(!$escludi_da_elenco) {
																			$nome = get_field('nome');
																			$cognome = get_field('cognome');
																			$foto = get_field('foto');
																			$disattiva_pagina_dettaglio = get_field('disattiva_pagina_dettaglio');
																			$ID = get_the_ID();?> 
																			<div class="col-lg-4">
																				<div class="card card-bg bg-white card-avatar rounded mb-3">
																					<div class="card-body">
																						<?php get_template_part("template-parts/autore/card", "insegnante"); ?>
																					</div><!-- /card-body -->
																				</div><!-- /card card-bg card-avatar rounded -->
																			</div><!-- /col-lg-4 -->
																		<?php
																		}
																	}
																	?>
															</div><!-- /row -->
														</div><!-- /col-lg-9 -->
													</div><!-- /row -->
												<?php } ?>
											</div><!-- /container -->
										<?php
									}
									wp_reset_postdata();
								}
					} // End of the loop.
				?>
				</section><!-- /section -->
		</main>

<?php
get_footer();



