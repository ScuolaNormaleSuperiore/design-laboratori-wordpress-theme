<?php
/**
 * Homepage "Pannello tab" tabs section.
 *
 * Componente ufficiale Bootstrap Italia 3 Tabs, variante "Con controllo
 * pannelli, testuali": nessun JavaScript aggiuntivo, tastiera/focus/
 * aria-selected già gestiti dal bundle BI3. Ogni tab (gruppo ripetibile
 * `home_tab_items`) ha un'etichetta, una descrizione e, facoltativamente,
 * un elenco di pagine mostrato come lista "Azioni, con freccia" in fondo al
 * pannello — se nessuna pagina è selezionata il pannello mostra solo la
 * descrizione. Il primo tab della lista è quello aperto di default. Stesso
 * pattern del prototipo statico bs-playground (sf-index.html, #blocco-tab).
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title       = $args['show_title'] ?? false;
$dli_title            = dli_get_configuration_field_by_lang( 'home_tab_title', 'homepage' );
$dli_tabs_raw         = dli_get_option( 'home_tab_items', 'homepage' );
$dli_tabs_raw         = is_array( $dli_tabs_raw ) ? $dli_tabs_raw : array();
$dli_bg_class         = dli_get_hp_section_background_class( $args, 'white' );

if ( 'true' === $dli_section_enabled ) {
	$dli_tabs = array();
	foreach ( $dli_tabs_raw as $dli_tab_row ) {
		$dli_etichetta = dli_pick_lang_value( $dli_tab_row['etichetta'] ?? '', $dli_tab_row['etichetta_eng'] ?? '' );
		if ( ! $dli_etichetta ) {
			continue;
		}
		$dli_descrizione = dli_pick_lang_value( $dli_tab_row['descrizione'] ?? '', $dli_tab_row['descrizione_eng'] ?? '' );
		$dli_page_ids    = isset( $dli_tab_row['pagine'] ) && is_array( $dli_tab_row['pagine'] ) ? array_map( 'absint', $dli_tab_row['pagine'] ) : array();
		$dli_tabs[]      = array(
			'etichetta'   => $dli_etichetta,
			'descrizione' => $dli_descrizione,
			'page_ids'    => array_filter( $dli_page_ids ),
		);
	}

	if ( ! empty( $dli_tabs ) ) {
		?>
	<!-- INIZIO SEZIONE "PANNELLO TAB" -->
	<section id="blocco-tab" class="section<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-5" <?php echo ( 'true' === $dli_show_title && $dli_title ) ? 'aria-labelledby="blocco-tab-title"' : 'aria-label="' . esc_attr__( 'Pannello tab', 'design_laboratori_italia' ) . '"'; ?>>
		<div class="section-content">
			<div class="container">
				<?php if ( 'true' === $dli_show_title && $dli_title ) : ?>
					<h2 id="blocco-tab-title" class="h3 pb-3"><?php echo esc_html( $dli_title ); ?></h2>
				<?php endif; ?>
				<ul class="nav nav-tabs" id="tab-aree" role="tablist">
					<?php foreach ( $dli_tabs as $dli_index => $dli_tab ) : ?>
						<?php $dli_is_first = ( 0 === $dli_index ); ?>
						<li class="nav-item" role="presentation">
							<a
								class="nav-link<?php echo $dli_is_first ? ' active' : ''; ?>"
								id="<?php echo esc_attr( 'tab-' . $dli_index . '-tab' ); ?>"
								data-bs-toggle="tab"
								href="<?php echo esc_attr( '#tab-' . $dli_index ); ?>"
								role="tab"
								aria-controls="<?php echo esc_attr( 'tab-' . $dli_index ); ?>"
								aria-selected="<?php echo $dli_is_first ? 'true' : 'false'; ?>"
							>
								<?php echo esc_html( $dli_tab['etichetta'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content" id="tab-aree-content">
					<?php foreach ( $dli_tabs as $dli_index => $dli_tab ) : ?>
						<?php $dli_is_first = ( 0 === $dli_index ); ?>
						<div class="tab-pane p-4 fade<?php echo $dli_is_first ? ' show active' : ''; ?>" id="<?php echo esc_attr( 'tab-' . $dli_index ); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( 'tab-' . $dli_index . '-tab' ); ?>">
							<?php if ( $dli_tab['descrizione'] ) : ?>
								<?php echo wp_kses_post( $dli_tab['descrizione'] ); ?>
							<?php endif; ?>
							<?php if ( ! empty( $dli_tab['page_ids'] ) ) : ?>
								<div class="it-list-wrapper">
									<ul class="it-list">
										<?php foreach ( $dli_tab['page_ids'] as $dli_page_id ) : ?>
											<?php if ( 'publish' === get_post_status( $dli_page_id ) ) : ?>
												<li>
													<a class="list-item" href="<?php echo esc_url( get_permalink( $dli_page_id ) ); ?>">
														<div class="it-right-zone">
															<span class="text fw-semibold"><?php echo esc_html( get_the_title( $dli_page_id ) ); ?></span>
															<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
																<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
															</svg>
														</div>
													</a>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE SEZIONE "PANNELLO TAB" -->
		<?php
	}
}
?>
