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
				$escludi_da_elenco          = get_field( 'escludi_da_elenco', $id );
				$nome                       = get_field( 'nome', $id );
				$cognome                    = get_field( 'cognome', $id );
				$title                      = get_the_title( $id );
				$disattiva_pagina_dettaglio = get_field( 'disattiva_pagina_dettaglio', $id );
				$post_link                  = get_the_permalink( $id );
				$categoria                  = get_field( 'categoria_appartenenza', $id );
				$cat_label                  = count( $categoria ) ? $categoria[0]->post_title : '';
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
					<h4><a href="<?php echo $post_link; ?>"><?php echo esc_attr( $nome ) . " " . esc_attr( $cognome ); ?></a></h4>
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
