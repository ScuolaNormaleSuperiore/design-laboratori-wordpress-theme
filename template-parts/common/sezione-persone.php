<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_args        = is_array( $args ) ? $args : array();
$dli_items       = isset( $dli_args['items'] ) && is_array( $dli_args['items'] ) ? $dli_args['items'] : array();
$dli_section_id  = $dli_args['section_id'] ?? '';
$dli_num_results = count( $dli_items );
$dli_hide_icon   = dli_get_option( 'hide_person_icon', 'persone' );
?>
<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
	<?php if ( $dli_num_results ) : ?>
		<div class="row g-3">
			<?php
			foreach ( $dli_items as $dli_item ) :
				$dli_id    = $dli_item->ID;
				$dli_terms = get_the_terms( $dli_id, 'struttura' );
				$dli_nome_struttura = $dli_terms ? $dli_terms[0]->name : '';

				$dli_disattiva_pagina_dettaglio          = dli_get_field( 'disattiva_pagina_dettaglio', $dli_id );
				$dli_nome                                = dli_get_field( 'nome', $dli_id );
				$dli_cognome                              = dli_get_field( 'cognome', $dli_id );
				$dli_title                                = get_the_title( $dli_id );
				$dli_post_link                            = get_the_permalink( $dli_id );
				$dli_categoria                             = dli_get_field( 'categoria_appartenenza', $dli_id );
				$dli_cat_label                             = $dli_categoria && count( $dli_categoria ) ? $dli_categoria[0]->post_title : '';
				$dli_sitoweb                               = dli_get_field( 'sito_web', $dli_id );
				$dli_link_persona                          = get_the_permalink( $dli_id );
				$dli_abilita_link_diretto_pagina_persona   = dli_get_field( 'abilita_link_diretto_pagina_persona', $dli_id );
				?>
				<div class="col-lg-4">
					<div class="avatar-wrapper avatar-extra-text">
						<?php if ( 'true' !== $dli_hide_icon ) : ?>
							<div class="avatar size-xl">
								<img src="<?php echo esc_url( dli_get_persona_avatar( $dli_item, $dli_id ) ); ?>"
									alt="<?php echo esc_attr( dli_get_persona_display_name( $dli_nome, $dli_cognome, $dli_title ) ); ?>"
									title="<?php echo esc_attr( dli_get_persona_display_name( $dli_nome, $dli_cognome, $dli_title ) ); ?>"
									aria-hidden="true">
							</div>
						<?php endif; ?>
						<div class="extra-text">
							<?php if ( ! $dli_disattiva_pagina_dettaglio ) : ?>
								<h4>
									<a href="<?php echo esc_url( $dli_abilita_link_diretto_pagina_persona ? $dli_sitoweb : $dli_link_persona ); ?>"<?php echo $dli_abilita_link_diretto_pagina_persona ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
										<?php echo esc_html( $dli_nome ) . ' ' . esc_html( $dli_cognome ); ?>
									</a>
								</h4>
							<?php else : ?>
								<h4><?php echo esc_html( $dli_nome ) . ' ' . esc_html( $dli_cognome ); ?></h4>
							<?php endif; ?>
							<span><?php echo esc_html( $dli_cat_label ); ?></span>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p>-</p>
	<?php endif; ?>
</section>
