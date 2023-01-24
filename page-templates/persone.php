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
						?>
						<div class="card-top badge-container mb-3">
							<?php
							//TODO: aggiungere logica filtro
							foreach ( $strutture as $struttura ) { ?>
								<a href="<?php echo $struttura->name ?>" title="<?php _e("Filtra per", "design_laboratori_italia"); ?>: <?php echo $struttura->name; ?>" class="badge badge-sm badge-pill badge-outline-bluelectric"><?php echo $struttura->name; ?></a>
							<?php } ?>
						</div>
						<?php
						// recupero la lista delle persone
						$persone= new WP_Query(array(
							'posts_per_page' => -1,
							'post_type' => 'persona',
							'orderby' => 'title',
							'order' => 'ASC',
							// 'meta_query' => array(
							// 		array(
							// 				'key' => 'related_programs', 
							// 				'compare' => 'LIKE',
							// 				'value' => '"' . get_the_ID() . '"',
							// 		)
							// )
					));

					if($persone) {
					?>
					<section class="section bg-white py-5">
						<div class="container">
            	<?php if ($persone->have_posts()) { ?>
              	<div class="row variable-gutters mb-4">
									<div class="col-lg-3">
										<h4 class="text-lg-right mb-3"><?php _e("Persone", "design_laboratori_italia"); ?></h4>
									</div><!-- /col-lg-3 -->
									<div class="col-lg-9">
										<div class="row variable-gutters">
											<?php
												while($persone->have_posts()) {
													$persone->the_post();
													$nome = get_field('nome');
													$cognome = get_field('cognome');
													$foto = get_field('foto');
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
												?>
										</div><!-- /row -->
									</div><!-- /col-lg-9 -->
								</div><!-- /row -->
							<?php } ?>
						</div><!-- /container -->
					</section><!-- /section -->
					<?php
					}

				} // End of the loop.
				?>
		</main>

<?php
get_footer();



