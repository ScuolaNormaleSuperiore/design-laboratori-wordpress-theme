<?php
/**
 * Filtri della pagina Persone (vista "Schede").
 *
 * Riceve i dati via $args (terzo parametro di get_template_part).
 * Usato esclusivamente dalla vista a schede; la vista tabella gestisce i filtri in modo autonomo
 * (template-parts/persone/view-tabella.php), ma con lo stesso principio: tutti i filtri sulla
 * stessa riga, nessuna chip.
 *
 * @package Design_Laboratori_Italia
 *
 * @var array $args {
 *     @type WP_Term[] $structures            Strutture disponibili.
 *     @type WP_Term[] $tags                  Tag disponibili.
 *     @type string    $selected_structure    Slug struttura selezionata ('' = nessuna).
 *     @type string    $selected_level        Slug tag selezionato ('' = nessuno).
 *     @type string    $selected_type         ID categoria selezionata ('' = nessuna).
 *     @type string    $selected_cognome      Testo di ricerca sul cognome ('' = nessuno).
 *     @type bool      $filter_level_enabled  Filtro per TAG abilitato.
 *     @type bool      $filter_structure_hidden Filtro struttura nascosto da configurazione.
 *     @type bool      $filter_type_hidden    Filtro tipologia nascosto da configurazione.
 *     @type string    $label_select_level    Etichetta select TAG.
 *     @type string    $label_all_levels      Etichetta opzione "Tutti i TAG".
 *     @type int       $result_count          Numero persone renderizzabili.
 *     @type array     $view_toggle           Dati per template-parts/persone/view-toggle (active/chip_url/tabella_url).
 * }
 */

$dli_f_structures              = isset( $args['structures'] ) ? $args['structures'] : array();
$dli_f_tags                    = isset( $args['tags'] ) ? $args['tags'] : array();
$dli_f_categories              = isset( $args['categories'] ) ? $args['categories'] : array();
$dli_f_selected_structure      = isset( $args['selected_structure'] ) ? $args['selected_structure'] : '';
$dli_f_selected_level          = isset( $args['selected_level'] ) ? $args['selected_level'] : '';
$dli_f_selected_type           = isset( $args['selected_type'] ) ? (string) $args['selected_type'] : '';
$dli_f_selected_cognome        = isset( $args['selected_cognome'] ) ? (string) $args['selected_cognome'] : '';
$dli_f_filter_level_enabled    = isset( $args['filter_level_enabled'] ) ? (bool) $args['filter_level_enabled'] : false;
$dli_f_filter_structure_hidden = isset( $args['filter_structure_hidden'] ) ? (bool) $args['filter_structure_hidden'] : false;
$dli_f_filter_type_hidden      = isset( $args['filter_type_hidden'] ) ? (bool) $args['filter_type_hidden'] : false;
$dli_f_label_select_level      = isset( $args['label_select_level'] ) ? $args['label_select_level'] : '';
$dli_f_label_all_levels        = isset( $args['label_all_levels'] ) ? $args['label_all_levels'] : '';
$dli_f_result_count            = isset( $args['result_count'] ) ? (int) $args['result_count'] : 0;
$dli_f_view_toggle             = isset( $args['view_toggle'] ) ? $args['view_toggle'] : array();

$dli_f_has_structures = count( $dli_f_structures ) >= 1;

/*
 * Ogni filtro resta visibile anche quando la combinazione attuale produce 0
 * risultati (a patto di essere lui stesso il filtro attivo), altrimenti
 * l'utente resterebbe intrappolato senza un modo per correggere la ricerca.
 */
$dli_f_show_search    = $dli_f_result_count || '' !== $dli_f_selected_cognome;
$dli_f_show_structure = $dli_f_has_structures && ! $dli_f_filter_structure_hidden && ( $dli_f_result_count || '' !== $dli_f_selected_structure );
$dli_f_show_type      = ! $dli_f_filter_type_hidden && ! empty( $dli_f_categories ) && ( $dli_f_result_count || '' !== $dli_f_selected_type );
$dli_f_show_level     = $dli_f_filter_level_enabled && count( $dli_f_tags ) > 0 && ( $dli_f_result_count || '' !== $dli_f_selected_level );
?>
<?php if ( $dli_f_show_search || $dli_f_show_structure || $dli_f_show_type || $dli_f_show_level || ! empty( $dli_f_view_toggle ) ) : ?>
	<div class="row mb-5 gy-3 align-items-end">
		<?php if ( $dli_f_show_search ) : ?>
			<div class="col-12 col-md-6 col-lg">
				<div class="form-group mb-0">
					<label for="searchPeopleCognome"><?php echo esc_html__( 'Cerca per cognome', 'design_laboratori_italia' ); ?></label>
					<input
						type="search"
						id="searchPeopleCognome"
						class="form-control"
						value="<?php echo esc_attr( $dli_f_selected_cognome ); ?>"
						placeholder="<?php echo esc_attr__( 'Es. Rossi', 'design_laboratori_italia' ); ?>"
						onsearch="reloadWithSelectedItem('searchPeopleCognome', 'cognome')"
					>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $dli_f_show_structure ) : ?>
			<div class="col-12 col-md-6 col-lg">
				<div class="select-wrapper<?php echo ( '' !== $dli_f_selected_structure ) ? ' dli-filter-active' : ''; ?>">
					<label for="selectPeopleStructure"><?php echo esc_html__( 'Struttura', 'design_laboratori_italia' ); ?></label>
					<select id="selectPeopleStructure" onchange="reloadWithSelectedItem('selectPeopleStructure', 'struttura')">
						<option value="" <?php selected( $dli_f_selected_structure, '' ); ?>>
							<?php echo esc_html__( 'Tutte le strutture', 'design_laboratori_italia' ); ?>
						</option>
						<?php foreach ( $dli_f_structures as $dli_f_struttura ) : ?>
							<option value="<?php echo esc_attr( $dli_f_struttura->slug ); ?>" <?php selected( $dli_f_selected_structure, $dli_f_struttura->slug ); ?>>
								<?php echo esc_html( $dli_f_struttura->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $dli_f_show_type ) : ?>
			<div class="col-12 col-md-6 col-lg">
				<div class="select-wrapper<?php echo ( '' !== $dli_f_selected_type ) ? ' dli-filter-active' : ''; ?>">
					<label for="selectPeopleType"><?php echo esc_html__( 'Tipologia', 'design_laboratori_italia' ); ?></label>
					<select id="selectPeopleType" onchange="reloadWithSelectedItem('selectPeopleType', 'tipologia')">
						<option value="" <?php selected( $dli_f_selected_type, '' ); ?>>
							<?php echo esc_html__( 'Tutte le tipologie', 'design_laboratori_italia' ); ?>
						</option>
						<?php foreach ( $dli_f_categories as $dli_f_cat ) : ?>
							<option value="<?php echo esc_attr( (string) $dli_f_cat->ID ); ?>" <?php selected( $dli_f_selected_type, (string) $dli_f_cat->ID ); ?>>
								<?php echo esc_html( dli_get_field( 'nome', $dli_f_cat->ID ) ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $dli_f_show_level ) : ?>
			<div class="col-12 col-md-6 col-lg">
				<div class="select-wrapper<?php echo ( '' !== $dli_f_selected_level ) ? ' dli-filter-active' : ''; ?>">
					<label for="selectPeopleLevel"><?php echo esc_html( $dli_f_label_select_level ); ?></label>
					<select id="selectPeopleLevel" onchange="reloadWithSelectedItem('selectPeopleLevel', 'level')">
						<option value="" <?php selected( $dli_f_selected_level, '' ); ?>>
							<?php echo esc_html( $dli_f_label_all_levels ); ?>
						</option>
						<?php foreach ( $dli_f_tags as $dli_f_tag ) : ?>
							<option value="<?php echo esc_attr( $dli_f_tag->slug ); ?>" <?php selected( $dli_f_selected_level, $dli_f_tag->slug ); ?>>
								<?php echo esc_html( $dli_f_tag->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $dli_f_view_toggle ) ) : ?>
			<div class="col-12 col-lg-auto d-flex justify-content-lg-end">
				<?php get_template_part( 'template-parts/persone/view-toggle', null, $dli_f_view_toggle ); ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
