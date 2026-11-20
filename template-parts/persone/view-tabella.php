<?php
/**
 * Vista tabellare della pagina Persone.
 *
 * Riceve i dati via $args (terzo parametro di get_template_part).
 * Filtro, ordinamento e paginazione sono gestiti lato client da assets/js/people-filters.js: il template stampa solo i
 * controlli e passa i dati allo script (wp_add_inline_script).
 * Non va confusa con template-parts/common/sezione-persone.php, che è un widget
 * per pagine di dettaglio (progetti, indirizzi di ricerca, risorse tecniche).
 *
 * @package Design_Laboratori_Italia
 *
 * @var array $args {
 *     @type array  $page_data   Struttura dati prodotta da DLI_ContentsManager::get_people_page_data().
 *     @type array  $view_toggle Dati per template-parts/persone/view-toggle (active/chip_url/tabella_url).
 * }
 */

$dli_tb_page_data          = isset( $args['page_data'] ) ? $args['page_data'] : array();
$dli_tb_view_toggle        = isset( $args['view_toggle'] ) ? $args['view_toggle'] : array();
$dli_tb_people_rows        = isset( $dli_tb_page_data['people_rows'] ) ? $dli_tb_page_data['people_rows'] : array();
$dli_tb_categories         = isset( $dli_tb_page_data['categories'] ) ? $dli_tb_page_data['categories'] : array();
$dli_tb_structures         = isset( $dli_tb_page_data['structures'] ) ? $dli_tb_page_data['structures'] : array();
$dli_tb_tags               = isset( $dli_tb_page_data['tags'] ) ? $dli_tb_page_data['tags'] : array();
$dli_tb_selected_structure = isset( $dli_tb_page_data['selected_structure'] ) ? $dli_tb_page_data['selected_structure'] : '';
$dli_tb_selected_level     = isset( $dli_tb_page_data['selected_level'] ) ? $dli_tb_page_data['selected_level'] : '';
$dli_tb_selected_type      = isset( $dli_tb_page_data['selected_type'] ) ? (string) $dli_tb_page_data['selected_type'] : '';
$dli_tb_selected_search    = isset( $dli_tb_page_data['selected_search'] ) ? $dli_tb_page_data['selected_search'] : '';

$dli_tb_hide_email           = ( 'true' === dli_get_option( 'hide_people_table_email', 'persone' ) );
$dli_tb_hide_phone           = ( 'true' === dli_get_option( 'hide_people_table_phone', 'persone' ) );
$dli_tb_hide_type            = ( 'true' === dli_get_option( 'hide_people_table_type', 'persone' ) );
$dli_tb_hide_structure       = ( 'true' === dli_get_option( 'hide_people_table_structure', 'persone' ) );
$dli_tb_hide_tag             = ( 'true' === dli_get_option( 'hide_people_table_tag', 'persone' ) );
$dli_tb_filter_level_enabled = ( 'true' !== dli_get_option( 'hide_filter_tag', 'persone' ) );
$dli_tb_label_select_level   = dli_get_configuration_field_by_lang( 'seleziona_livello_persone', 'persone' );
$dli_tb_label_all_levels     = dli_get_configuration_field_by_lang( 'tutti_i_livelli_persone', 'persone' );
$dli_tb_pagination_enabled   = ( 'true' === dli_get_option( 'enable_people_table_pagination', 'persone' ) );

$dli_tb_hide_filter_structure = ( 'true' === dli_get_option( 'hide_filter_structure', 'persone' ) );
$dli_tb_hide_filter_type      = ( 'true' === dli_get_option( 'hide_filter_type', 'persone' ) );

$dli_tb_category_map = array();
foreach ( $dli_tb_categories as $dli_tb_cat ) {
	$dli_tb_category_map[ $dli_tb_cat->ID ] = dli_get_field( 'nome', $dli_tb_cat->ID );
}

$dli_tb_sprites_url     = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg';
$dli_tb_has_structures  = ! empty( $dli_tb_structures ) && ! $dli_tb_hide_filter_structure;
$dli_tb_has_tags        = $dli_tb_filter_level_enabled && ! empty( $dli_tb_tags );
$dli_tb_has_type_filter = ! empty( $dli_tb_categories ) && ! $dli_tb_hide_filter_type;

// Each structure of the filter with its substructures: choosing a structure finds the people of its substructures too,
// as in the cards view (where the taxonomy query includes the children).
$dli_tb_slug_by_id            = wp_list_pluck( $dli_tb_structures, 'slug', 'term_id' );
$dli_tb_structure_descendants = array();
foreach ( $dli_tb_structures as $dli_tb_struttura ) {
	$dli_tb_children = get_term_children( $dli_tb_struttura->term_id, STRUCTURE_TAXONOMY );
	$dli_tb_slugs    = array( $dli_tb_struttura->slug );
	foreach ( is_array( $dli_tb_children ) ? $dli_tb_children : array() as $dli_tb_child_id ) {
		if ( isset( $dli_tb_slug_by_id[ $dli_tb_child_id ] ) ) {
			$dli_tb_slugs[] = $dli_tb_slug_by_id[ $dli_tb_child_id ];
		}
	}
	$dli_tb_structure_descendants[ $dli_tb_struttura->slug ] = $dli_tb_slugs;
}

// --- Build JSON rows for client-side rendering ---
$dli_tb_json_rows = array();
foreach ( $dli_tb_people_rows as $dli_tb_row ) {
	$dli_tb_pid    = $dli_tb_row['person']->ID;
	$dli_tb_cat_id = $dli_tb_row['category_id'];

	$dli_tb_r_name    = (string) dli_get_field( 'nome', $dli_tb_pid );
	$dli_tb_r_surname = (string) dli_get_field( 'cognome', $dli_tb_pid );

	$dli_tb_r_disable = dli_get_field( 'disattiva_pagina_dettaglio', $dli_tb_pid );
	$dli_tb_r_direct  = dli_get_field( 'abilita_link_diretto_pagina_persona', $dli_tb_pid );
	// The site of the person is a link only when it is a web address (http or https), as in the cards view: an
	// address written without a scheme gets http://, and any other scheme, such as javascript:, is removed by
	// esc_url_raw() and leaves no web address, so the link falls back to the page of the person.
	$dli_tb_r_sito = esc_url_raw( trim( (string) dli_get_field( 'sito_web', $dli_tb_pid ) ) );
	$dli_tb_r_sito = preg_match( '#^https?://#i', $dli_tb_r_sito ) ? $dli_tb_r_sito : '';

	if ( $dli_tb_r_disable ) {
		$dli_tb_r_link   = '';
		$dli_tb_r_target = '';
	} elseif ( $dli_tb_r_direct && '' !== $dli_tb_r_sito ) {
		$dli_tb_r_link   = $dli_tb_r_sito;
		$dli_tb_r_target = '_blank';
	} else {
		$dli_tb_r_link   = (string) get_the_permalink( $dli_tb_pid );
		$dli_tb_r_target = '';
	}

	$dli_tb_r_type = isset( $dli_tb_category_map[ $dli_tb_cat_id ] )
		? (string) $dli_tb_category_map[ $dli_tb_cat_id ]
		: '';

	$dli_tb_r_struct_terms = get_the_terms( $dli_tb_pid, STRUCTURE_TAXONOMY );
	$dli_tb_r_struct_names = '';
	$dli_tb_r_struct_list  = array();
	$dli_tb_r_struct_slugs = array();
	if ( ! is_wp_error( $dli_tb_r_struct_terms ) && ! empty( $dli_tb_r_struct_terms ) ) {
		$dli_tb_r_struct_list  = wp_list_pluck( $dli_tb_r_struct_terms, 'name' );
		$dli_tb_r_struct_names = implode( ', ', $dli_tb_r_struct_list );
		$dli_tb_r_struct_slugs = wp_list_pluck( $dli_tb_r_struct_terms, 'slug' );
	}

	$dli_tb_r_tag_terms = wp_get_post_terms( $dli_tb_pid, WP_DEFAULT_TAGS );
	$dli_tb_r_tag_names = '';
	$dli_tb_r_tag_slugs = array();
	if ( ! is_wp_error( $dli_tb_r_tag_terms ) && ! empty( $dli_tb_r_tag_terms ) ) {
		$dli_tb_r_tag_names = implode( ', ', wp_list_pluck( $dli_tb_r_tag_terms, 'name' ) );
		$dli_tb_r_tag_slugs = wp_list_pluck( $dli_tb_r_tag_terms, 'slug' );
	}

	$dli_tb_r_email = '';
	if ( ! $dli_tb_hide_email ) {
		$dli_tb_r_email = antispambot( sanitize_email( (string) dli_get_field( 'email', $dli_tb_pid ) ) );
	}
	// With the column hidden the number is not even read: it is never in the page, nor in the data of the script.
	$dli_tb_r_phone = '';
	if ( ! $dli_tb_hide_phone ) {
		$dli_tb_r_phone = (string) dli_get_field( 'telefono', $dli_tb_pid );
	}
	$dli_tb_r_phone_href = preg_replace( '/[^+\d]/', '', $dli_tb_r_phone );

	$dli_tb_json_rows[] = array(
		'name'           => $dli_tb_r_name,
		'surname'        => $dli_tb_r_surname,
		'sortKey'        => mb_strtolower( $dli_tb_r_surname . ' ' . $dli_tb_r_name ),
		'link'           => $dli_tb_r_link,
		'linkTarget'     => $dli_tb_r_target,
		'type'           => $dli_tb_r_type,
		'typeSort'       => mb_strtolower( $dli_tb_r_type ),
		'typeId'         => (string) $dli_tb_cat_id,
		'structure'      => $dli_tb_r_struct_names,
		'structureSort'  => mb_strtolower( $dli_tb_r_struct_names ),
		'structureSlugs' => $dli_tb_r_struct_slugs,
		'structureNames' => $dli_tb_r_struct_list,
		'tag'            => $dli_tb_r_tag_names,
		'tagSort'        => mb_strtolower( $dli_tb_r_tag_names ),
		'tagSlugs'       => $dli_tb_r_tag_slugs,
		'email'          => $dli_tb_r_email,
		'phone'          => $dli_tb_r_phone,
		'phoneHref'      => $dli_tb_r_phone_href,
	);
}
?>

<div class="row mb-5 gy-4 align-items-end">
	<div class="col-12 col-md-6 col-lg">
		<div class="form-group mb-0">
			<label for="dliPeopleSearch">
				<?php echo esc_html__( 'Cerca testo', 'design_laboratori_italia' ); ?>
			</label>
			<input
				type="search"
				id="dliPeopleSearch"
				class="form-control"
				value="<?php echo esc_attr( $dli_tb_selected_search ); ?>"
				placeholder="<?php echo esc_attr__( 'Es. Rossi, Professore…', 'design_laboratori_italia' ); ?>"
				aria-label="<?php echo esc_attr__( 'Cerca per nome, cognome, tipologia o struttura', 'design_laboratori_italia' ); ?>"
			>
		</div>
	</div>
	<?php if ( $dli_tb_has_structures ) : ?>
		<div class="col-12 col-md-6 col-lg">
			<div class="select-wrapper<?php echo ( '' !== $dli_tb_selected_structure ) ? ' dli-filter-active' : ''; ?>">
				<label for="selectTableStructure">
					<?php echo esc_html__( 'Struttura', 'design_laboratori_italia' ); ?>
				</label>
				<select id="selectTableStructure">
					<option value="">
						<?php echo esc_html__( 'Tutte le strutture', 'design_laboratori_italia' ); ?>
					</option>
					<?php foreach ( $dli_tb_structures as $dli_tb_struttura ) : ?>
						<option value="<?php echo esc_attr( $dli_tb_struttura->slug ); ?>" <?php selected( $dli_tb_selected_structure, $dli_tb_struttura->slug ); ?>>
							<?php echo esc_html( $dli_tb_struttura->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( $dli_tb_has_type_filter ) : ?>
		<div class="col-12 col-md-6 col-lg">
			<div class="select-wrapper">
				<label for="selectTableType">
					<?php echo esc_html__( 'Tipologia', 'design_laboratori_italia' ); ?>
				</label>
				<select id="selectTableType">
					<option value="">
						<?php echo esc_html__( 'Tutte le tipologie', 'design_laboratori_italia' ); ?>
					</option>
					<?php foreach ( $dli_tb_categories as $dli_tb_cat ) : ?>
						<option value="<?php echo esc_attr( $dli_tb_cat->ID ); ?>" <?php selected( $dli_tb_selected_type, (string) $dli_tb_cat->ID ); ?>>
							<?php echo esc_html( $dli_tb_category_map[ $dli_tb_cat->ID ] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( $dli_tb_has_tags ) : ?>
		<div class="col-12 col-md-6 col-lg">
			<div class="select-wrapper<?php echo ( '' !== $dli_tb_selected_level ) ? ' dli-filter-active' : ''; ?>">
				<label for="selectTableLevel">
					<?php echo esc_html( $dli_tb_label_select_level ); ?>
				</label>
				<select id="selectTableLevel">
					<option value="">
						<?php echo esc_html( $dli_tb_label_all_levels ); ?>
					</option>
					<?php foreach ( $dli_tb_tags as $dli_tb_tag ) : ?>
						<option value="<?php echo esc_attr( $dli_tb_tag->slug ); ?>" <?php selected( $dli_tb_selected_level, $dli_tb_tag->slug ); ?>>
							<?php echo esc_html( $dli_tb_tag->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $dli_tb_view_toggle ) ) : ?>
		<div class="col-12 col-lg-auto d-flex justify-content-lg-end">
			<?php get_template_part( 'template-parts/persone/view-toggle', null, $dli_tb_view_toggle ); ?>
		</div>
	<?php endif; ?>
</div>

<div class="row align-items-end mb-3">
	<div class="col-lg-6">
		<p id="dliPeopleCount" class="mb-0 small text-secondary" aria-live="polite" aria-atomic="true"></p>
	</div>
	<div class="col-lg-6">
		<div class="d-flex align-items-center justify-content-lg-end gap-2">
			<label for="dliPeopleSort" class="mb-0 flex-shrink-0"><?php echo esc_html__( 'Ordina per', 'design_laboratori_italia' ); ?></label>
			<div class="select-wrapper mb-0" style="min-width: 220px;">
				<select id="dliPeopleSort" data-focus-mouse="false">
					<?php if ( $dli_tb_has_type_filter ) : ?>
						<option value="typeSort"><?php echo esc_html__( 'Tipologia', 'design_laboratori_italia' ); ?></option>
					<?php endif; ?>
					<option value="sortKey"><?php echo esc_html__( 'Nome / Cognome', 'design_laboratori_italia' ); ?></option>
					<?php if ( $dli_tb_has_structures ) : ?>
						<option value="structureSort"><?php echo esc_html__( 'Struttura', 'design_laboratori_italia' ); ?></option>
					<?php endif; ?>
					<?php if ( $dli_tb_has_tags ) : ?>
						<option value="tagSort"><?php echo esc_html__( 'TAG', 'design_laboratori_italia' ); ?></option>
					<?php endif; ?>
				</select>
			</div>
		</div>
	</div>
</div>

<div class="row mb-4">
	<div class="col-12">
		<div class="table-responsive">
	<table class="table table-striped table-hover" id="people-table">
		<caption class="visually-hidden"><?php echo esc_html__( 'Elenco persone', 'design_laboratori_italia' ); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo esc_html__( 'Nome / Cognome', 'design_laboratori_italia' ); ?></th>
				<?php if ( ! $dli_tb_hide_type ) : ?>
					<th scope="col"><?php echo esc_html__( 'Tipologia', 'design_laboratori_italia' ); ?></th>
				<?php endif; ?>
				<?php if ( ! $dli_tb_hide_structure ) : ?>
					<th scope="col" class="d-none d-md-table-cell"><?php echo esc_html__( 'Struttura', 'design_laboratori_italia' ); ?></th>
				<?php endif; ?>
				<?php if ( ! $dli_tb_hide_tag ) : ?>
					<th scope="col" class="d-none d-md-table-cell"><?php echo esc_html__( 'TAG', 'design_laboratori_italia' ); ?></th>
				<?php endif; ?>
				<?php if ( ! $dli_tb_hide_email ) : ?>
					<th scope="col" class="d-none d-md-table-cell"><?php echo esc_html__( 'Email', 'design_laboratori_italia' ); ?></th>
				<?php endif; ?>
				<?php if ( ! $dli_tb_hide_phone ) : ?>
					<th scope="col" class="d-none d-md-table-cell"><?php echo esc_html__( 'Telefono', 'design_laboratori_italia' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody id="people-tbody">
		</tbody>
	</table>
		</div>
		<nav
			id="people-pagination"
			class="pagination-wrapper justify-content-center mt-4"
			aria-label="<?php echo esc_attr__( 'Navigazione pagine persone', 'design_laboratori_italia' ); ?>"
		></nav>
	</div>
</div>

<?php
// The data of the table for assets/js/people-filters.js, which filters, sorts and pages it in the browser.
wp_add_inline_script(
	dli_enqueue_people_filters_script(),
	'window.DLI_PEOPLE_TABLE = ' . wp_json_encode(
		array(
			'rows'                 => $dli_tb_json_rows,
			'structureDescendants' => $dli_tb_structure_descendants,
			'perPage'              => PEOPLE_TABLE_PER_PAGE,
			'paginationEnabled'    => $dli_tb_pagination_enabled,
			'cols'                 => array(
				'type'      => ! $dli_tb_hide_type,
				'structure' => ! $dli_tb_hide_structure,
				'tag'       => ! $dli_tb_hide_tag,
				'email'     => ! $dli_tb_hide_email,
				'phone'     => ! $dli_tb_hide_phone,
			),
			'spritesUrl'           => $dli_tb_sprites_url,
			'i18n'                 => array(
				'prevPage'       => __( 'Pagina precedente', 'design_laboratori_italia' ),
				'nextPage'       => __( 'Pagina successiva', 'design_laboratori_italia' ),
				'resultSingular' => __( 'persona trovata', 'design_laboratori_italia' ),
				'resultPlural'   => __( 'persone trovate', 'design_laboratori_italia' ),
				'noResults'      => __( 'Nessuna persona trovata', 'design_laboratori_italia' ),
			),
		),
		JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	) . ';',
	'before'
);
