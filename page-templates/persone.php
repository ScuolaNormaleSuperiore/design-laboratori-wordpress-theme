<?php
/**
 * Template Name: Persone
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_selected_structure_raw = filter_input( INPUT_GET, 'struttura', FILTER_DEFAULT );
$dli_selected_level_raw     = filter_input( INPUT_GET, 'level', FILTER_DEFAULT );

$dli_selected_structure = is_string( $dli_selected_structure_raw ) ? sanitize_text_field( wp_unslash( $dli_selected_structure_raw ) ) : '';
$dli_selected_level     = is_string( $dli_selected_level_raw ) ? sanitize_text_field( wp_unslash( $dli_selected_level_raw ) ) : '';

$dli_people_params = array(
	'paged'     => get_query_var( 'paged', 1 ),
	'post_type' => PEOPLE_POST_TYPE,
	'per_page'  => -1,
	'tag_level' => $dli_selected_level,
);

$dli_people_query             = DLI_ContentsManager::get_people_query( $dli_people_params );
$dli_num_results              = $dli_people_query->found_posts;
$dli_filter_mode              = dli_get_option( 'pagination_mode', 'persone' );
$dli_select_structure_enabled = ( 'disabled' !== $dli_filter_mode );
$dli_filter_level_enabled     = ( 'true' === dli_get_option( 'level_filter_enabled', 'persone' ) );
$dli_tags                     = DLI_ContentsManager::get_tags_by_post_type( PEOPLE_POST_TYPE );
$dli_label_select_level       = dli_get_configuration_field_by_lang( 'seleziona_livello_persone', 'persone' );
$dli_label_all_levels         = dli_get_configuration_field_by_lang( 'tutti_i_livelli_persone', 'persone' );
$dli_hide_icon                = dli_get_option( 'hide_person_icon', 'persone' );
?>
<script>
	function redirectToPage(baseUrl, parname, selectedValue) {
		if (selectedValue) {
			window.location.href = baseUrl + '?' + parname + '=' + encodeURIComponent(selectedValue);
		} else {
			window.location.href = baseUrl;
		}
	}
</script>

<main id="main-container" role="main">
	<form action="<?php echo esc_url( get_permalink() ); ?>" id="personeform" method="GET">

		<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>
		<?php get_template_part( 'template-parts/hero/persone' ); ?>

		<div class="container my-4">
			<section class="section bg-gray-light py-5">
				<div class="container">
					<?php
					$dli_strutture = get_terms(
						array(
							'taxonomy'   => 'struttura',
							'hide_empty' => false,
						)
					);
					$dli_strutture = ( is_wp_error( $dli_strutture ) || ! is_array( $dli_strutture ) ) ? array() : $dli_strutture;

					if (
						count( $dli_strutture ) >= 1 &&
						$dli_num_results &&
						$dli_select_structure_enabled &&
						'combobox' !== $dli_filter_mode
					) :
						?>
						<div class="row mb-5">
							<div class="col-lg-3"></div>
							<div class="col-lg-9">
								<div class="title-section">
									<?php foreach ( $dli_strutture as $dli_struttura ) : ?>
										<div class="chip chip-primary chip-lg chip-simple <?php echo ( $dli_selected_structure === $dli_struttura->slug ) ? 'chip-selected' : ''; ?>">
											<span class="chip-label customSpacing">
												<a class="hover-text-white"
													href="#"
													onclick="addParameterAndReloadPage('struttura', '<?php echo esc_attr( $dli_struttura->slug ); ?>'); return false;"
													title="<?php echo esc_attr__( 'Filtra per', 'design_laboratori_italia' ) . ': ' . esc_attr( $dli_struttura->name ); ?>">
													<?php echo esc_html( $dli_struttura->name ); ?>
												</a>
											</span>
										</div>
									<?php endforeach; ?>
									<div class="chip chip-primary chip-lg chip-simple <?php echo ( '' === $dli_selected_structure ) ? 'chip-selected' : ''; ?>">
										<span class="chip-label customSpacing">
											<a class="hover-text-white"
												href="#"
												onclick="addParameterAndReloadPage('struttura', ''); return false;"
												title="<?php echo esc_attr__( 'Tutte le strutture', 'design_laboratori_italia' ); ?>">
												<?php echo esc_html__( 'Tutte le strutture', 'design_laboratori_italia' ); ?>
											</a>
										</span>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $dli_select_structure_enabled || $dli_filter_level_enabled ) : ?>
						<div class="row mb-5">
							<div class="col-lg-3"></div>
							<div class="col-lg-4">
								<?php
								if (
									count( $dli_strutture ) >= 1 &&
									$dli_num_results &&
									$dli_select_structure_enabled &&
									'combobox' === $dli_filter_mode
								) :
									?>
									<div class="select-wrapper">
										<label for="selectPeopleStructure"><?php echo esc_html__( 'Seleziona la struttura', 'design_laboratori_italia' ); ?></label>
										<select id="selectPeopleStructure" onchange="reloadWithSelectedItem('selectPeopleStructure', 'struttura')">
											<option value=" " <?php selected( $dli_selected_structure, '' ); ?>>
												<?php echo esc_html__( 'Tutte le strutture', 'design_laboratori_italia' ); ?>
											</option>
											<?php foreach ( $dli_strutture as $dli_struttura ) : ?>
												<option value="<?php echo esc_attr( $dli_struttura->slug ); ?>" <?php selected( $dli_selected_structure, $dli_struttura->slug ); ?>>
													<?php echo esc_html( $dli_struttura->name ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php endif; ?>
							</div>
							<div class="col-lg-4">
								<?php if ( $dli_filter_level_enabled && count( $dli_tags ) > 0 ) : ?>
									<div class="select-wrapper">
										<label for="selectPeopleLevel"><?php echo esc_html( $dli_label_select_level ); ?></label>
										<select id="selectPeopleLevel" onchange="reloadWithSelectedItem('selectPeopleLevel', 'level')">
											<option value=" " <?php selected( $dli_selected_level, '' ); ?>>
												<?php echo esc_html( $dli_label_all_levels ); ?>
											</option>
											<?php foreach ( $dli_tags as $dli_tag ) : ?>
												<option value="<?php echo esc_attr( $dli_tag->slug ); ?>" <?php selected( $dli_selected_level, $dli_tag->slug ); ?>>
													<?php echo esc_html( $dli_tag->name ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

						<?php if ( $dli_num_results ) : ?>
							<?php
							$dli_categories_query = new WP_Query(
								array(
									'posts_per_page' => -1,
									'post_type'      => 'tipologia-persona',
									'meta_key'       => 'priorita',
									'orderby'        => 'meta_value_num',
									'order'          => 'ASC',
								)
							);

							$dli_people_args = array(
								'posts_per_page' => -1,
								'post_type'      => 'persona',
								'meta_key'       => 'cognome',
								'orderby'        => 'meta_value',
								'order'          => 'ASC',
							);

							if ( '' !== $dli_selected_structure ) {
								$dli_people_args['tax_query'] = array(
									array(
										'taxonomy' => 'struttura',
										'field'    => 'slug',
										'terms'    => $dli_selected_structure,
									),
								);
							}

							if ( $dli_selected_level ) {
								if ( ! isset( $dli_people_args['tax_query'] ) ) {
									$dli_people_args['tax_query'] = array();
								}

								$dli_people_args['tax_query'][] = array(
									'taxonomy' => 'post_tag',
									'field'    => 'slug',
									'terms'    => $dli_people_params['tag_level'],
								);
							}

							$dli_people_query_all   = new WP_Query( $dli_people_args );
							$dli_people_by_category = array();

							if ( $dli_people_query_all->have_posts() ) {
								while ( $dli_people_query_all->have_posts() ) {
									$dli_people_query_all->the_post();
									$dli_person_id = get_the_ID();

									if ( dli_get_field( 'escludi_da_elenco', $dli_person_id ) ) {
										continue;
									}

									$dli_person_categories = dli_get_field( 'categoria_appartenenza', $dli_person_id );
									if ( empty( $dli_person_categories ) ) {
										continue;
									}

									$dli_person_category_ids = array();
									if ( is_array( $dli_person_categories ) ) {
										foreach ( $dli_person_categories as $dli_person_category ) {
											if ( $dli_person_category instanceof WP_Post && ! empty( $dli_person_category->ID ) ) {
												$dli_person_category_ids[] = (int) $dli_person_category->ID;
											} elseif ( is_array( $dli_person_category ) && ! empty( $dli_person_category['ID'] ) ) {
												$dli_person_category_ids[] = (int) $dli_person_category['ID'];
											} elseif ( is_numeric( $dli_person_category ) ) {
												$dli_person_category_ids[] = (int) $dli_person_category;
											}
										}
									} elseif ( $dli_person_categories instanceof WP_Post && ! empty( $dli_person_categories->ID ) ) {
										$dli_person_category_ids[] = (int) $dli_person_categories->ID;
									} elseif ( is_numeric( $dli_person_categories ) ) {
										$dli_person_category_ids[] = (int) $dli_person_categories;
									}

									if ( empty( $dli_person_category_ids ) ) {
										continue;
									}

									$dli_person_post = get_post( $dli_person_id );
									if ( ! ( $dli_person_post instanceof WP_Post ) ) {
										continue;
									}

									foreach ( array_unique( $dli_person_category_ids ) as $dli_person_category_id ) {
										if ( ! isset( $dli_people_by_category[ $dli_person_category_id ] ) ) {
											$dli_people_by_category[ $dli_person_category_id ] = array();
										}
										$dli_people_by_category[ $dli_person_category_id ][] = $dli_person_post;
									}
								}
							}

							wp_reset_postdata();

							while ( $dli_categories_query->have_posts() ) :
								$dli_categories_query->the_post();
								$dli_category_name = dli_get_field( 'nome' );
								$dli_category_id   = get_the_ID();

								if ( empty( $dli_people_by_category[ $dli_category_id ] ) ) {
									continue;
								}
								?>
								<div class="row mb-4">
									<div class="col-lg-3">
										<h3 class="text-lg-right mb-3 h4"><?php echo esc_html( $dli_category_name ); ?></h3>
									</div>
									<div class="col-lg-9">
										<div class="row">
											<?php foreach ( $dli_people_by_category[ $dli_category_id ] as $dli_person_post ) : ?>
												<?php
												$dli_person_id               = $dli_person_post->ID;
												$dli_name                    = dli_get_field( 'nome', $dli_person_id );
												$dli_surname                 = dli_get_field( 'cognome', $dli_person_id );
												$dli_disable_detail_page     = dli_get_field( 'disattiva_pagina_dettaglio', $dli_person_id );
												$dli_enable_direct_site_link = dli_get_field( 'abilita_link_diretto_pagina_persona', $dli_person_id );
												$dli_person_permalink        = get_the_permalink( $dli_person_id );
												$dli_person_site_url         = dli_get_field( 'sito_web', $dli_person_id );
												$dli_person_title            = get_the_title( $dli_person_id );
												$dli_person_levels           = wp_get_post_terms( $dli_person_id, 'post_tag' );
												$dli_person_levels           = ( is_wp_error( $dli_person_levels ) || ! is_array( $dli_person_levels ) ) ? array() : $dli_person_levels;
												?>
												<div class="col-lg-4">
													<div class="avatar-wrapper avatar-extra-text">
														<?php if ( 'true' !== $dli_hide_icon ) : ?>
															<div class="avatar size-xl">
																<img src="<?php echo esc_url( dli_get_persona_avatar( $dli_person_id, $dli_person_id ) ); ?>"
																	alt="<?php echo esc_attr( dli_get_persona_display_name( $dli_name, $dli_surname, $dli_person_title ) ); ?>"
																	title="<?php echo esc_attr( dli_get_persona_display_name( $dli_name, $dli_surname, $dli_person_title ) ); ?>"
																	aria-hidden="true">
															</div>
														<?php endif; ?>
														<div class="extra-text">
															<?php if ( ! $dli_disable_detail_page ) : ?>
																<?php if ( ! $dli_enable_direct_site_link ) : ?>
																	<h4>
																		<a class="text-decoration-none" href="<?php echo esc_url( $dli_person_permalink ); ?>">
																			<?php echo esc_html( $dli_name ) . ' ' . esc_html( $dli_surname ); ?>
																		</a>
																	</h4>
																<?php else : ?>
																	<h4>
																		<a class="text-decoration-none" href="<?php echo esc_url( $dli_person_site_url ); ?>" target="_blank" rel="noopener noreferrer">
																			<?php echo esc_html( $dli_name ) . ' ' . esc_html( $dli_surname ); ?>
																		</a>
																	</h4>
																<?php endif; ?>
															<?php else : ?>
																<h4><?php echo esc_html( $dli_name ) . ' ' . esc_html( $dli_surname ); ?></h4>
															<?php endif; ?>

															<?php
															$dli_structure_terms = get_the_terms( $dli_person_id, 'struttura' );
															if ( ! is_wp_error( $dli_structure_terms ) && ! empty( $dli_structure_terms ) ) :
																$dli_structure_name = $dli_structure_terms[0]->name;
																?>
																<p><?php echo esc_html( $dli_structure_name ); ?>&nbsp;</p>
																<?php if ( count( $dli_person_levels ) ) : ?>
																	<p><?php echo esc_html( $dli_person_levels[0]->name ); ?></p>
																<?php endif; ?>
															<?php endif; ?>
														</div>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							<?php endwhile; ?>
						<?php else : ?>
						<div class="col-12 col-lg-8">
							<div class="row pt-2">
								<?php echo esc_html__( 'Non è stata trovata nessuna persona', 'design_laboratori_italia' ); ?>
							</div>
						</div>
					<?php endif; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</section>
		</div>
	</form>
</main>

<?php
get_footer();
