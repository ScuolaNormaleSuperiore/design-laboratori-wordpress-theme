<?php
	$items     = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = count( $items );
	define( 'PERSONE_PER_ROW', 3 );
?>
<section id="<?php echo $section_id; ?>">
<?php
	// The mani loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		foreach ( $items as $item ) {
			if ( ( $pindex % PERSONE_PER_ROW ) == 0 ) {
		?>
			<!-- begin row  person-->
			<div class="row pb-3 pt-3">
			<?php
				}
				$id                         = $item->ID;
				$foto                       = get_field( 'foto', $id );
				$terms                      = get_the_terms( $id, 'struttura' );
				if ( $terms ) {
					$nome_struttura = $terms[0]->name;
				} else {
					$nome_struttura = '';
				}
				$escludi_da_elenco          = get_field( 'escludi_da_elenco', $id );
				$nome                       = get_field( 'nome', $id );
				$cognome                    = get_field( 'cognome', $id );
				$disattiva_pagina_dettaglio = get_field( 'disattiva_pagina_dettaglio', $id );
				$link_persona               = get_the_permalink( $id );
			?>

				<!-- begin card person -->
				<div class="col-lg-4">
					<div class="avatar-wrapper avatar-extra-text">
					<div class="avatar size-xl">
						<img src="<?php echo dli_get_persona_avatar( $foto, $id ); ?>" alt="<?php echo $foto['alt']; ?>" aria-hidden="true">
					</div>
					<div class="extra-text">
					<h4><a href="<?php echo $link_persona; ?>"><?php echo esc_attr( $nome ) . " " . esc_attr( $cognome ); ?></a></h4>
					<span><?php echo esc_attr( $nome_struttura ); ?></span>
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
