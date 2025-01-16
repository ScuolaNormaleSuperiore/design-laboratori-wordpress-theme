<?php
/* Template Name: Le persone
*
* @package Design_Laboratori_Italia
*/

global $post;
get_header();

// Gestione del parametro struttura.
$selected_structure = isset( $_GET['struttura'] ) ? sanitize_text_field( $_GET['struttura'] ) : '';
// Gestione del parametro: TAG livello.
$selected_level = isset( $_GET['level'] ) ? sanitize_text_field( $_GET['level'] ) : '';

// Recupero dati da visualizzare.
$params = array(
	'paged'     => get_query_var( 'paged', 1 ),
	'post_type' => PEOPLE_POST_TYPE,
	'per_page'  => -1,
	'tag_level' => $selected_level,
);
$the_query                = DLI_ContentsManager::get_people_query( $params );
$num_results              = $the_query->found_posts;
$filter_mode              = dli_get_option('pagination_mode', 'persone' );
$select_structure_enabled = $filter_mode === 'disabled' ? false :  true;
$filter_level_enabled     = dli_get_option('level_filter_enabled', 'persone' ) !== 'true' ? false : true ;
// Get the tag used into published people.
$tags = DLI_ContentsManager::get_tags_by_post_type( PEOPLE_POST_TYPE );
// Etichette per la gestione dei tag.
$label_select_level = dli_get_configuration_field_by_lang( 'seleziona_livello_persone', 'persone' );
$label_all_levels   = dli_get_configuration_field_by_lang( 'tutti_i_livelli_persone', 'persone' );
?>
<script>
	function redirectToPage(baseUrl, parname, selectedValue ) {
		if (selectedValue) {
			// Aggiungi il parametro con il valore selezionato.
			window.location.href = baseUrl + "?" + parname + "=" + encodeURIComponent(selectedValue);
		} else {
			window.location.href = baseUrl;
		}
	}
</script>

<!-- START CONTENT -->

<main id="main-container" role="main">
	<form action="<?php $_SERVER['PHP_SELF']; ?>" id="personeform" method="GET">

		<!-- BREADCRUMB -->
		<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

		<!-- BANNER PERSONE -->
		<?php get_template_part( 'template-parts/hero/persone' ); ?>

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
						// visualizzo il filtro con CHIP sulle strutture solo se ne esistono almeno 2 e se il filtro è abilitato.
						if ( ( count( $strutture ) >= 1 ) && ( $num_results ) && $select_structure_enabled && ( $filter_mode !== 'combobox' ) ) {
						?>

						<!-- FILTRO STRUTTURE con chips -->
						<div class="row mb-5">
							<div class="col-lg-3"></div>
							<div class="col-lg-9">
								<div class="title-section">
									<?php
									foreach ( $strutture as $struttura ) {
									?>
										<div class="chip chip-primary chip-lg chip-simple <?php if ( $selected_structure === $struttura->slug ) echo " chip-selected" ?>">
											<span class="chip-label customSpacing">
												<a class="hover-text-white" 
													href="#"
													onclick="addParameterAndReloadPage('struttura', '<?php echo esc_attr( $struttura->slug ); ?>'); return false;"
													title ="<?php _e( 'Filtra per', "design_laboratori_italia" ); ?>: <?php echo esc_attr( $struttura->name ); ?>"><?php echo esc_attr( $struttura->name ); ?></a>
											</span>
										</div>
									<?php
									}
									?>
									<div class="chip chip-primary chip-lg chip-simple <?php if ( $selected_structure === '' ) echo " chip-selected" ?>">
										<span class="chip-label customSpacing">
											<a class="hover-text-white"
												href="#"
												onclick="addParameterAndReloadPage('struttura', ''); return false;"
												title="<?php _e( 'Tutte le strutture', "design_laboratori_italia" ); ?>">
												<?php _e( 'Tutte le strutture', "design_laboratori_italia" ); ?>
											</a>
										</span>
									</div>
								</div>
							</div>
						</div>
							<?php
						}

							// visualizzo il filtro con CHIP sulle strutture solo se ne esistono almeno 2 e se il filtro è abilitato.
							if ( $select_structure_enabled || $filter_level_enabled ) {
						?>

							
							<div class="row mb-5">
								<div class="col-lg-3"></div>
								<div class="col-lg-4">
									<!-- FILTRO STRUTTURE con combo -->
									<?php
										if ( ( count( $strutture ) >= 1 ) && ( $num_results ) && $select_structure_enabled & ( $filter_mode === 'combobox' ) ) {
									?>
									<div class="select-wrapper">
										<label for="selectPeopleStructure"><?php _e( 'Seleziona la struttura', "design_laboratori_italia" ); ?></label>
										<select id="selectPeopleStructure" onchange="reloadWithSelectedItem('selectPeopleStructure', 'struttura')">
											<option value=" " <?php if ( $selected_structure === '' ) echo "selected" ?> >
												<?php _e( 'Tutte le strutture', "design_laboratori_italia" ); ?>
											</option>
											<?php
											foreach ( $strutture as $struttura ) {
											?>
											<option value="<?php echo $struttura->slug; ?>" <?php if ( $selected_structure === $struttura->slug ) echo "selected" ?> >
												<?php echo esc_attr( $struttura->name ); ?>
											</option>
											<?php
												}
											?>
										</select>
									</div>
									<?php
										}
									?>
								</div>
								<div class="col-lg-4">
									<!-- Filtro per TAG livello -->
									<?php
										if ( ( $filter_level_enabled ) && count( $tags )> 0 ) {
									?>

									<div class="select-wrapper">
										<label for="selectPeopleLevel"><?php echo esc_attr( $label_select_level ); ?></label>
										<select id="selectPeopleLevel" onchange="reloadWithSelectedItem('selectPeopleLevel', 'level')">
											<option value=" " <?php if ( $selected_level === '' ) echo "selected" ?> >
												<?php echo esc_attr( $label_all_levels ); ?>
											</option>
											<?php
											foreach( $tags as $tag ){
											?>
												<option value="<?php echo esc_attr( $tag->slug ); ?>" <?php if ( $selected_level === $tag->slug ) echo "selected" ?> >
													<?php echo esc_attr( $tag->name ); ?>
												</option>
											<?php
											}
											?>
										</select>
									</div>
									<?php
									}
									?>
								</div>
							</div>


						<?php
						}
						if( $num_results ) {
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
								$nome_categoria = dli_get_field( 'nome' );
								$categoria_id   = get_the_ID();

								$args = array(
									'posts_per_page' => -1,
									'post_type'      => 'persona',
									'meta_key'       => 'cognome',
									'orderby'        => 'meta_value',
									'order'          => 'ASC',
									'meta_query'     => array(
										array(
											'key'     => 'categoria_appartenenza',
											'compare' => 'LIKE',
											'value'   => '"' . $categoria_id . '"',
										),
									),
								);

								// Aggiungo il filtro su struttura, se richiesto.
								if ( $selected_structure !== '' ) {
									// recupero la lista delle persone filtrate per struttura.
									$args['tax_query'] = array(
										array(
											'taxonomy' => 'struttura',
											'field'    => 'slug',
											'terms'    => "'" . $selected_structure . "'",
										),
									);
								}

								// Aggiungo il filtro su persona, se richiesto.
								if ( $selected_level ) {
									if (! isset( $args['tax_query'] ) ) {
										$args['tax_query'] = array(
											array(
												'taxonomy' => 'post_tag',
												'field'    => 'slug',
												'terms'    => $params['tag_level'],
											),
										);
									} else {
										array_push(
											$args['tax_query'],
											array(
												'taxonomy' => 'post_tag',
												'field'    => 'slug',
												'terms'    => $params['tag_level'],
											)
										);
									}
								}

								$persone = new WP_Query( $args );

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
												$escludi_da_elenco = dli_get_field( 'escludi_da_elenco' );
												if ( ! $escludi_da_elenco ) {
													$nome                                = dli_get_field( 'nome' );
													$cognome                             = dli_get_field( 'cognome' );
													$disattiva_pagina_dettaglio          = dli_get_field( 'disattiva_pagina_dettaglio' );
													$abilita_link_diretto_pagina_persona = dli_get_field( 'abilita_link_diretto_pagina_persona' );
													$ID                                  = get_the_ID();
													$link_persona                        = get_the_permalink( $ID );
													$sitoweb                             = dli_get_field( 'sito_web' );
													$title                               = get_the_title( $ID );
													$levels                              = wp_get_post_terms( $ID, 'post_tag' );
													?>
													<div class="col-lg-4">
														<div class="avatar-wrapper avatar-extra-text">
															<div class="avatar size-xl">
																<img src="<?php echo dli_get_persona_avatar( $post, $ID ); ?>" 
																	alt="<?php echo esc_attr( dli_get_persona_display_name( $nome, $cognome, $title ) ); ?>"
																	title="<?php echo esc_attr( dli_get_persona_display_name( $nome, $cognome, $title ) ); ?>"
																	aria-hidden="true">
															</div>
															<div class="extra-text">
																<?php
																	if ( ! $disattiva_pagina_dettaglio ) {
																		if ( ! $abilita_link_diretto_pagina_persona ) {
																			?>
																			<h4>
																				<a class="text-decoration-none" href="<?php echo $link_persona; ?>">
																					<?php echo esc_attr( $nome ) . ' ' . esc_attr( $cognome ); ?>
																				</a>
																			</h4>
																			<?php
																		}
																		else {
																			?>
																				<h4>
																					<a class="text-decoration-none" href="<?php echo esc_attr( $sitoweb ); ?>" target="_blank">
																						<?php echo esc_attr( $nome ) . ' ' . esc_attr( $cognome ); ?>
																					</a>
																				</h4>
																			<?php
																		}
																	}
																	else {
																		?>
																		<h4><?php echo esc_attr( $nome ) . " " . esc_attr( $cognome ); ?></h4>
																	<?php }
																	$terms = get_the_terms( $ID, 'struttura' );
																	if ( $terms ) {
																		$nome_struttura = $terms[0]->name;
																	?>
																		<p><?php echo esc_attr( $nome_struttura ); ?>&nbsp;</p>
																	<?php
																		if ( count($levels) ) {
																	?>
																		<p><?php echo esc_attr($levels[0]->name); ?></p>
																	<?php
																		}
																	}
																?>
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
						}
						else {
							?>
								<div class="col-12 col-lg-8">
									<div class="row pt-2">
										<?php echo __( 'Non è stata trovata nessuna persona', 'design_laboratori_italia' ); ?>
									</div>
								</div>
							<?php
							}
							wp_reset_postdata();
						?>
				</div><!-- /container -->
			</section><!-- /section -->
		</div><!-- /container -->
	</form>
</main>

<!-- END CONTENT -->
<?php
get_footer();
