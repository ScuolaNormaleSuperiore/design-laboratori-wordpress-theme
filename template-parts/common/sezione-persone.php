<?php
	$items     = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
?>
<section id="<?php echo 'sezione-' . $section_id; ?>">
<?php
	// The main loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		foreach ( $items as $item ) {
			if ( ( $pindex % PERSONE_PER_ROW ) == 0 ) {
		?>
			<!-- begin row  person-->
			<div class="row pb-3 pt-3">
			<?php
				}
				$id    = $item->ID;
				$terms = get_the_terms( $id, 'struttura' );
				if ( $terms ) {
					$nome_struttura = $terms[0]->name;
				} else {
					$nome_struttura = '';
				}
				$escludi_da_elenco          = dli_get_field( 'escludi_da_elenco', $id );
				$nome                       = dli_get_field( 'nome', $id );
				$cognome                    = dli_get_field( 'cognome', $id );
				$title                      = get_the_title( $id );
				$disattiva_pagina_dettaglio = dli_get_field( 'disattiva_pagina_dettaglio', $id );
				$post_link                  = get_the_permalink( $id );
				$categoria                  = dli_get_field( 'categoria_appartenenza', $id );
				$cat_label                  = $categoria && count($categoria) ? $categoria[0]->post_title : '';
				$sitoweb                    = dli_get_field( 'sito_web', $id );
				$link_persona               = get_the_permalink( $id );
				$abilita_link_diretto_pagina_persona = dli_get_field( 'abilita_link_diretto_pagina_persona', $id );
			?>

				<!-- begin card person -->
				<div class="col-lg-4">
					<div class="avatar-wrapper avatar-extra-text">

						<div class="avatar size-xl">
							<img src="<?php echo dli_get_persona_avatar( $item, $id ); ?>" 
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
											<a href="<?php echo $link_persona; ?>">
												<?php echo esc_attr( $nome ) . ' ' . esc_attr( $cognome ); ?>
											</a>
										</h4>
										<?php
									} else {
										?>
											<h4>
												<a href="<?php echo esc_attr( $sitoweb ); ?>" target="_blank">
													<?php echo esc_attr( $nome ) . ' ' . esc_attr( $cognome ); ?>
												</a>
											</h4>
										<?php
									}
								} else {
									?>
									<h4><?php echo esc_attr( $nome ) . " " . esc_attr( $cognome ); ?></h4>
								<?php
								}
							?>
							<span><?php echo esc_attr( $cat_label ); ?></span>
						</div>

					</div>
				</div>
				<!-- end card person -->

			<?php
				if ( ( ( $pindex % PERSONE_PER_ROW ) === PERSONE_PER_ROW - 1 ) || ( $pindex + 1 === $num_results ) ) {
			?>
			</div>
			<!-- end row person -->
		<?php
				}
				$pindex++;
			}
		} else {
			echo '<p>-</p>';
		}
		?>

</section>
