<?php
/**
 * Vista a schede (chip) della pagina Persone.
 *
 * Riceve i dati via $args (terzo parametro di get_template_part).
 * Non va confusa con template-parts/common/sezione-persone.php, che è un widget
 * per pagine di dettaglio (progetti, indirizzi di ricerca, risorse tecniche).
 *
 * @package Design_Laboratori_Italia
 *
 * @var array $args {
 *     @type array     $people_by_category  Persone indicizzate per ID categoria.
 *     @type WP_Post[] $categories          Categorie (tipologia-persona) ordinate per priorità.
 *     @type string    $selected_type       ID categoria selezionata ('' = nessuna).
 *     @type string    $hide_icon           'true' per nascondere l'avatar, 'false' per mostrarlo.
 * }
 */

$dli_v_people_by_category = isset( $args['people_by_category'] ) ? $args['people_by_category'] : array();
$dli_v_categories         = isset( $args['categories'] ) ? $args['categories'] : array();
$dli_v_hide_icon          = isset( $args['hide_icon'] ) ? $args['hide_icon'] : 'false';
$dli_v_selected_type      = isset( $args['selected_type'] ) ? (string) $args['selected_type'] : '';

foreach ( $dli_v_categories as $dli_v_category ) :
	$dli_v_category_id   = $dli_v_category->ID;
	$dli_v_category_name = dli_get_field( 'nome', $dli_v_category_id );

	if ( '' !== $dli_v_selected_type && (string) $dli_v_category_id !== $dli_v_selected_type ) {
		continue;
	}

	if ( empty( $dli_v_people_by_category[ $dli_v_category_id ] ) ) {
		continue;
	}
	?>
	<div class="row mb-4">
		<div class="col-lg-3">
			<h3 class="text-lg-right mb-3 h4"><?php echo esc_html( $dli_v_category_name ); ?></h3>
		</div>
		<div class="col-lg-9">
			<div class="row">
				<?php foreach ( $dli_v_people_by_category[ $dli_v_category_id ] as $dli_v_person_post ) : ?>
					<?php
					$dli_v_person_id               = $dli_v_person_post->ID;
					$dli_v_name                    = dli_get_field( 'nome', $dli_v_person_id );
					$dli_v_surname                 = dli_get_field( 'cognome', $dli_v_person_id );
					$dli_v_disable_detail_page     = dli_get_field( 'disattiva_pagina_dettaglio', $dli_v_person_id );
					$dli_v_enable_direct_site_link = dli_get_field( 'abilita_link_diretto_pagina_persona', $dli_v_person_id );
					$dli_v_person_permalink        = get_the_permalink( $dli_v_person_id );
					$dli_v_person_site_url         = dli_get_field( 'sito_web', $dli_v_person_id );
					$dli_v_person_title            = get_the_title( $dli_v_person_id );
					$dli_v_person_levels           = wp_get_post_terms( $dli_v_person_id, WP_DEFAULT_TAGS );
					$dli_v_person_levels           = ( is_wp_error( $dli_v_person_levels ) || ! is_array( $dli_v_person_levels ) ) ? array() : $dli_v_person_levels;
					?>
					<div class="col-lg-4">
						<div class="avatar-wrapper avatar-extra-text">
							<?php if ( 'true' !== $dli_v_hide_icon ) : ?>
								<div class="avatar size-xl">
									<img src="<?php echo esc_url( dli_get_persona_avatar( $dli_v_person_id, $dli_v_person_id ) ); ?>"
										alt="<?php echo esc_attr( dli_get_persona_display_name( $dli_v_name, $dli_v_surname, $dli_v_person_title ) ); ?>"
										title="<?php echo esc_attr( dli_get_persona_display_name( $dli_v_name, $dli_v_surname, $dli_v_person_title ) ); ?>"
										aria-hidden="true">
								</div>
							<?php endif; ?>
							<div class="extra-text">
								<?php if ( ! $dli_v_disable_detail_page ) : ?>
									<?php if ( ! $dli_v_enable_direct_site_link ) : ?>
										<h4>
											<a class="text-decoration-none" href="<?php echo esc_url( $dli_v_person_permalink ); ?>">
												<?php echo esc_html( $dli_v_name ) . ' ' . esc_html( $dli_v_surname ); ?>
											</a>
										</h4>
									<?php else : ?>
										<h4>
											<a class="text-decoration-none" href="<?php echo esc_url( $dli_v_person_site_url ); ?>" target="_blank" rel="noopener noreferrer">
												<?php echo esc_html( $dli_v_name ) . ' ' . esc_html( $dli_v_surname ); ?>
											</a>
										</h4>
									<?php endif; ?>
								<?php else : ?>
									<h4><?php echo esc_html( $dli_v_name ) . ' ' . esc_html( $dli_v_surname ); ?></h4>
								<?php endif; ?>

								<?php
								$dli_v_structure_terms = get_the_terms( $dli_v_person_id, STRUCTURE_TAXONOMY );
								if ( ! is_wp_error( $dli_v_structure_terms ) && ! empty( $dli_v_structure_terms ) ) :
									?>
									<p><?php echo esc_html( $dli_v_structure_terms[0]->name ); ?>&nbsp;</p>
									<?php if ( count( $dli_v_person_levels ) ) : ?>
										<p><?php echo esc_html( $dli_v_person_levels[0]->name ); ?></p>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
