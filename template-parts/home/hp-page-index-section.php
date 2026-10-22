<?php
/**
 * Homepage "Indice di una pagina" section.
 *
 * Esplode su due livelli la struttura di una pagina scelta in configurazione:
 * una colonna per ogni sua sottopagina diretta, con l'elenco delle
 * sottopagine di quella sottopagina come lista di link ("Azioni, con
 * freccia"). Il titolo della sezione è il titolo della pagina selezionata.
 * Doppio markup: colonne su tablet/desktop, accordion su mobile (primo
 * pannello aperto), sfondo bianco — stesso pattern del prototipo statico
 * bs-playground (sf-index.html, #blocco-indice-fase, "Research Data
 * Management").
 *
 * La pagina da mostrare è un campo per-riga della sezione in "Sezioni HP"
 * (non un'opzione globale): la sezione può quindi comparire più volte in
 * home, ciascuna con una pagina diversa — per questo gli id HTML della
 * sezione sono costruiti sull'ID della pagina selezionata, per restare unici
 * quando la sezione è ripetuta.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_page_id          = absint( $args['page_id'] ?? 0 );
$dli_bg_class         = dli_get_hp_section_background_class( $args, 'white' );

if ( 'true' === $dli_section_enabled && $dli_page_id ) {
	$dli_page = get_post( $dli_page_id );

	if ( $dli_page && WP_DEFAULT_PAGE === $dli_page->post_type && 'publish' === $dli_page->post_status ) {
		$dli_colonne = array();
		$dli_figli   = get_posts(
			array(
				'post_type'      => WP_DEFAULT_PAGE,
				'post_status'    => 'publish',
				'post_parent'    => $dli_page_id,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => -1,
			)
		);
		foreach ( $dli_figli as $dli_figlio ) {
			$dli_azioni = get_posts(
				array(
					'post_type'      => WP_DEFAULT_PAGE,
					'post_status'    => 'publish',
					'post_parent'    => $dli_figlio->ID,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'posts_per_page' => -1,
				)
			);
			if ( ! empty( $dli_azioni ) ) {
				$dli_colonne[] = array(
					'id'     => $dli_figlio->ID,
					'title'  => get_the_title( $dli_figlio->ID ),
					'azioni' => $dli_azioni,
				);
			}
		}

		if ( ! empty( $dli_colonne ) ) {
			// Larghezza delle colonne da lg in su: si adatta al numero di
			// colonne effettive (mezza con 2, un terzo con 3, un quarto da 4
			// in su), invece di restare fissa a un terzo indipendentemente da
			// quante colonne ci sono davvero.
			$dli_col_lg_class = 'col-lg-' . intdiv( 12, min( count( $dli_colonne ), 4 ) );
			?>
	<!-- INIZIO SEZIONE "INDICE DI UNA PAGINA" -->
	<section id="<?php echo esc_attr( 'blocco-indice-fase-' . $dli_page_id ); ?>" class="section blocco-indice-fase<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-5" aria-labelledby="<?php echo esc_attr( 'blocco-indice-fase-' . $dli_page_id . '-title' ); ?>">
		<div class="section-content">
			<div class="container">
				<div class="pb-3">
					<h2 id="<?php echo esc_attr( 'blocco-indice-fase-' . $dli_page_id . '-title' ); ?>" class="mb-0"><?php echo esc_html( get_the_title( $dli_page_id ) ); ?></h2>
				</div>

				<!-- Tablet e desktop: colonne -->
				<div class="row d-none d-md-flex">
					<?php foreach ( $dli_colonne as $dli_colonna ) : ?>
						<div class="col-12 col-md-6 <?php echo esc_attr( $dli_col_lg_class ); ?> mb-4">
							<h3 id="<?php echo esc_attr( 'idx-' . $dli_colonna['id'] . '-title' ); ?>" class="h5 fw-bold"><?php echo esc_html( $dli_colonna['title'] ); ?></h3>
							<div class="it-list-wrapper">
								<ul class="it-list">
									<?php foreach ( $dli_colonna['azioni'] as $dli_azione ) : ?>
										<li>
											<a class="list-item" href="<?php echo esc_url( get_permalink( $dli_azione->ID ) ); ?>">
												<div class="it-right-zone">
													<span class="text fw-semibold"><?php echo esc_html( get_the_title( $dli_azione->ID ) ); ?></span>
													<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
														<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
													</svg>
												</div>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Mobile: accordion -->
				<div class="accordion d-md-none" id="<?php echo esc_attr( 'idx-accordion-' . $dli_page_id ); ?>">
					<?php foreach ( $dli_colonne as $dli_index => $dli_colonna ) : ?>
						<?php
						$dli_is_first = ( 0 === $dli_index );
						$dli_head_id  = 'idx-acc-head-' . $dli_colonna['id'];
						$dli_body_id  = 'idx-acc-' . $dli_colonna['id'];
						?>
						<div class="accordion-item">
							<h3 class="accordion-header" id="<?php echo esc_attr( $dli_head_id ); ?>">
								<button
									class="accordion-button<?php echo $dli_is_first ? '' : ' collapsed'; ?>"
									type="button"
									data-bs-toggle="collapse"
									data-bs-target="#<?php echo esc_attr( $dli_body_id ); ?>"
									aria-expanded="<?php echo $dli_is_first ? 'true' : 'false'; ?>"
									aria-controls="<?php echo esc_attr( $dli_body_id ); ?>"
								>
									<?php echo esc_html( $dli_colonna['title'] ); ?>
								</button>
							</h3>
							<div id="<?php echo esc_attr( $dli_body_id ); ?>" class="accordion-collapse collapse<?php echo $dli_is_first ? ' show' : ''; ?>" role="region" aria-labelledby="<?php echo esc_attr( $dli_head_id ); ?>">
								<div class="accordion-body">
									<div class="it-list-wrapper">
										<ul class="it-list">
											<?php foreach ( $dli_colonna['azioni'] as $dli_azione ) : ?>
												<li>
													<a class="list-item" href="<?php echo esc_url( get_permalink( $dli_azione->ID ) ); ?>">
														<div class="it-right-zone">
															<span class="text fw-semibold"><?php echo esc_html( get_the_title( $dli_azione->ID ) ); ?></span>
															<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
																<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
															</svg>
														</div>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE SEZIONE "INDICE DI UNA PAGINA" -->
			<?php
		}
	}
}
?>
