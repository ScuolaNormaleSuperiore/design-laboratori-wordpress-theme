<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items           = $args['items'];
	$dli_section_id  = $args['section_id'];
	$dli_num_results = is_array( $dli_items ) ? count( $dli_items ) : 0;
	$dli_hide_icon   = dli_get_option( 'hide_person_icon', 'persone' );
?>
<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
<?php
	// The main loop of the page.
	$dli_pindex = 0;
if ( $dli_num_results ) {
	foreach ( $dli_items as $dli_item ) {
		if ( ( $dli_pindex % PERSONE_PER_ROW ) == 0 ) {
			?>
			<!-- begin row  person-->
			<div class="row pb-3 pt-3">
			<?php
		}
			$dli_id    = $dli_item->ID;
			$dli_terms = get_the_terms( $dli_id, 'struttura' );
		if ( $dli_terms ) {
			$dli_nome_struttura = $dli_terms[0]->name;
		} else {
			$dli_nome_struttura = '';
		}
			$dli_escludi_da_elenco                   = dli_get_field( 'escludi_da_elenco', $dli_id );
			$dli_nome                                = dli_get_field( 'nome', $dli_id );
			$dli_cognome                             = dli_get_field( 'cognome', $dli_id );
			$dli_title                               = get_the_title( $dli_id );
			$dli_disattiva_pagina_dettaglio          = dli_get_field( 'disattiva_pagina_dettaglio', $dli_id );
			$dli_post_link                           = get_the_permalink( $dli_id );
			$dli_categoria                           = dli_get_field( 'categoria_appartenenza', $dli_id );
			$dli_cat_label                           = $dli_categoria && count( $dli_categoria ) ? $dli_categoria[0]->post_title : '';
			$dli_sitoweb                             = dli_get_field( 'sito_web', $dli_id );
			$dli_link_persona                        = get_the_permalink( $dli_id );
			$dli_abilita_link_diretto_pagina_persona = dli_get_field( 'abilita_link_diretto_pagina_persona', $dli_id );
		?>

				<!-- begin card person -->
				<div class="col-lg-4">
					<div class="avatar-wrapper avatar-extra-text">

				<?php
				if ( 'true' !== $dli_hide_icon ) {
					?>
						<div class="avatar size-xl">
							<img src="<?php echo esc_url( dli_get_persona_avatar( $dli_item, $dli_id ) ); ?>" 
								alt="<?php echo esc_attr( dli_get_persona_display_name( $dli_nome, $dli_cognome, $dli_title ) ); ?>"
								title="<?php echo esc_attr( dli_get_persona_display_name( $dli_nome, $dli_cognome, $dli_title ) ); ?>"
								aria-hidden="true">
						</div>
					<?php
				}
				?>

						<div class="extra-text">
						<?php
						if ( ! $dli_disattiva_pagina_dettaglio ) {
							if ( ! $dli_abilita_link_diretto_pagina_persona ) {
								?>
										<h4>
											<a href="<?php echo esc_url( $dli_link_persona ); ?>">
										<?php echo esc_html( $dli_nome ) . ' ' . esc_html( $dli_cognome ); ?>
											</a>
										</h4>
									<?php
							} else {
								?>
											<h4>
												<a href="<?php echo esc_url( $dli_sitoweb ); ?>" target="_blank">
											<?php echo esc_html( $dli_nome ) . ' ' . esc_html( $dli_cognome ); ?>
												</a>
											</h4>
									<?php
							}
						} else {
							?>
									<h4><?php echo esc_html( $dli_nome ) . ' ' . esc_html( $dli_cognome ); ?></h4>
								<?php
						}
						?>
							<span><?php echo esc_html( $dli_cat_label ); ?></span>
						</div>

					</div>
				</div>
				<!-- end card person -->

			<?php
			if ( ( ( $dli_pindex % PERSONE_PER_ROW ) === PERSONE_PER_ROW - 1 ) || ( $dli_pindex + 1 === $dli_num_results ) ) {
				?>
			</div>
			<!-- end row person -->
				<?php
			}
			++$dli_pindex;
	}
} else {
	echo '<p>-</p>';
}
?>

</section>
