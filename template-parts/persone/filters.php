<?php
/**
 * Filtri della pagina Persone (vista chip).
 *
 * Riceve i dati via $args (terzo parametro di get_template_part).
 * Usato esclusivamente dalla vista chip; la vista tabella gestisce i filtri in modo autonomo.
 *
 * @package Design_Laboratori_Italia
 *
 * @var array $args {
 *     @type WP_Term[] $structures          Strutture disponibili.
 *     @type WP_Term[] $tags                Tag disponibili.
 *     @type string    $selected_structure  Slug struttura selezionata ('' = nessuna).
 *     @type string    $selected_level      Slug tag selezionato ('' = nessuno).
 *     @type string    $filter_mode         Modalità filtro struttura: chip|select.
 *     @type bool      $filter_level_enabled Filtro per TAG abilitato.
 *     @type string    $label_select_level  Etichetta select TAG.
 *     @type string    $label_all_levels    Etichetta opzione "Tutti i TAG".
 *     @type int       $result_count        Numero persone renderizzabili.
 * }
 */

$dli_f_structures              = isset( $args['structures'] ) ? $args['structures'] : array();
$dli_f_tags                    = isset( $args['tags'] ) ? $args['tags'] : array();
$dli_f_categories              = isset( $args['categories'] ) ? $args['categories'] : array();
$dli_f_selected_structure      = isset( $args['selected_structure'] ) ? $args['selected_structure'] : '';
$dli_f_selected_level          = isset( $args['selected_level'] ) ? $args['selected_level'] : '';
$dli_f_selected_type           = isset( $args['selected_type'] ) ? (string) $args['selected_type'] : '';
$dli_f_filter_mode             = isset( $args['filter_mode'] ) ? $args['filter_mode'] : 'chip';
$dli_f_filter_level_enabled    = isset( $args['filter_level_enabled'] ) ? (bool) $args['filter_level_enabled'] : false;
$dli_f_filter_structure_hidden = isset( $args['filter_structure_hidden'] ) ? (bool) $args['filter_structure_hidden'] : false;
$dli_f_filter_type_hidden      = isset( $args['filter_type_hidden'] ) ? (bool) $args['filter_type_hidden'] : false;
$dli_f_label_select_level      = isset( $args['label_select_level'] ) ? $args['label_select_level'] : '';
$dli_f_label_all_levels        = isset( $args['label_all_levels'] ) ? $args['label_all_levels'] : '';
$dli_f_result_count            = isset( $args['result_count'] ) ? (int) $args['result_count'] : 0;

$dli_f_has_structures = count( $dli_f_structures ) >= 1;

if (
	$dli_f_has_structures &&
	$dli_f_result_count &&
	! $dli_f_filter_structure_hidden &&
	'select' !== $dli_f_filter_mode
) :
	?>
	<div class="row mb-5">
		<div class="col-12 col-lg-9 offset-lg-3">
			<div class="title-section">
				<?php foreach ( $dli_f_structures as $dli_f_struttura ) : ?>
					<div class="chip chip-primary chip-lg chip-simple <?php echo ( $dli_f_selected_structure === $dli_f_struttura->slug ) ? 'chip-selected' : ''; ?>">
						<span class="chip-label customSpacing">
							<a class="hover-text-white"
								href="#"
								onclick="addParameterAndReloadPage('struttura', '<?php echo esc_attr( $dli_f_struttura->slug ); ?>'); return false;"
								title="<?php echo esc_attr__( 'Filtra per', 'design_laboratori_italia' ) . ': ' . esc_attr( $dli_f_struttura->name ); ?>">
								<?php echo esc_html( $dli_f_struttura->name ); ?>
							</a>
						</span>
					</div>
				<?php endforeach; ?>
				<div class="chip chip-primary chip-lg chip-simple <?php echo ( '' === $dli_f_selected_structure ) ? 'chip-selected' : ''; ?>">
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

<?php
$dli_f_show_structure_select = $dli_f_has_structures && $dli_f_result_count && 'select' === $dli_f_filter_mode && ! $dli_f_filter_structure_hidden;
$dli_f_show_type_filter      = ! $dli_f_filter_type_hidden && ! empty( $dli_f_categories ) && $dli_f_result_count;
$dli_f_show_level_filter     = $dli_f_filter_level_enabled && count( $dli_f_tags ) > 0;
?>
<?php if ( $dli_f_show_structure_select || $dli_f_show_type_filter || $dli_f_show_level_filter ) : ?>
	<div class="row mb-5 gy-3">
		<?php if ( $dli_f_show_structure_select ) : ?>
			<div class="col-12 col-lg-4">
				<div class="select-wrapper<?php echo ( '' !== $dli_f_selected_structure ) ? ' dli-filter-active' : ''; ?>">
					<label for="selectPeopleStructure"><?php echo esc_html__( 'Seleziona la struttura', 'design_laboratori_italia' ); ?></label>
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
		<?php if ( $dli_f_show_type_filter ) : ?>
			<div class="col-12 col-lg-4">
				<div class="select-wrapper<?php echo ( '' !== $dli_f_selected_type ) ? ' dli-filter-active' : ''; ?>">
					<label for="selectPeopleType"><?php echo esc_html__( 'Seleziona la tipologia', 'design_laboratori_italia' ); ?></label>
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
		<?php if ( $dli_f_show_level_filter ) : ?>
			<div class="col-12 col-lg-4">
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
	</div>
<?php endif; ?>
