<?php
/**
 * Vista tabellare della pagina Persone.
 *
 * Riceve i dati via $args (terzo parametro di get_template_part).
 * Filtro, ordinamento e paginazione sono gestiti interamente lato client via JS.
 * Non va confusa con template-parts/common/sezione-persone.php, che è un widget
 * per pagine di dettaglio (progetti, indirizzi di ricerca, risorse tecniche).
 *
 * @package Design_Laboratori_Italia
 *
 * @var array $args {
 *     @type array $page_data Struttura dati prodotta da DLI_ContentsManager::get_people_page_data().
 * }
 */

$dli_tb_page_data          = isset( $args['page_data'] ) ? $args['page_data'] : array();
$dli_tb_people_rows        = isset( $dli_tb_page_data['people_rows'] ) ? $dli_tb_page_data['people_rows'] : array();
$dli_tb_categories         = isset( $dli_tb_page_data['categories'] ) ? $dli_tb_page_data['categories'] : array();
$dli_tb_structures         = isset( $dli_tb_page_data['structures'] ) ? $dli_tb_page_data['structures'] : array();
$dli_tb_tags               = isset( $dli_tb_page_data['tags'] ) ? $dli_tb_page_data['tags'] : array();
$dli_tb_selected_structure = isset( $dli_tb_page_data['selected_structure'] ) ? $dli_tb_page_data['selected_structure'] : '';
$dli_tb_selected_level     = isset( $dli_tb_page_data['selected_level'] ) ? $dli_tb_page_data['selected_level'] : '';

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

// --- Build JSON rows for client-side rendering ---
$dli_tb_json_rows = array();
foreach ( $dli_tb_people_rows as $dli_tb_row ) {
	$dli_tb_pid    = $dli_tb_row['person']->ID;
	$dli_tb_cat_id = $dli_tb_row['category_id'];

	$dli_tb_r_name    = (string) dli_get_field( 'nome', $dli_tb_pid );
	$dli_tb_r_surname = (string) dli_get_field( 'cognome', $dli_tb_pid );

	$dli_tb_r_disable = dli_get_field( 'disattiva_pagina_dettaglio', $dli_tb_pid );
	$dli_tb_r_direct  = dli_get_field( 'abilita_link_diretto_pagina_persona', $dli_tb_pid );
	$dli_tb_r_sito    = (string) dli_get_field( 'sito_web', $dli_tb_pid );

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
	$dli_tb_r_struct_slugs = array();
	if ( ! is_wp_error( $dli_tb_r_struct_terms ) && ! empty( $dli_tb_r_struct_terms ) ) {
		$dli_tb_r_struct_names = implode( ', ', wp_list_pluck( $dli_tb_r_struct_terms, 'name' ) );
		$dli_tb_r_struct_slugs = wp_list_pluck( $dli_tb_r_struct_terms, 'slug' );
	}

	$dli_tb_r_tag_terms = wp_get_post_terms( $dli_tb_pid, WP_DEFAULT_TAGS );
	$dli_tb_r_tag_names = '';
	$dli_tb_r_tag_slugs = array();
	if ( ! is_wp_error( $dli_tb_r_tag_terms ) && ! empty( $dli_tb_r_tag_terms ) ) {
		$dli_tb_r_tag_names = implode( ', ', wp_list_pluck( $dli_tb_r_tag_terms, 'name' ) );
		$dli_tb_r_tag_slugs = wp_list_pluck( $dli_tb_r_tag_terms, 'slug' );
	}

	$dli_tb_r_email      = (string) dli_get_field( 'email', $dli_tb_pid );
	$dli_tb_r_phone      = (string) dli_get_field( 'telefono', $dli_tb_pid );
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
		'tag'            => $dli_tb_r_tag_names,
		'tagSort'        => mb_strtolower( $dli_tb_r_tag_names ),
		'tagSlugs'       => $dli_tb_r_tag_slugs,
		'email'          => $dli_tb_r_email,
		'phone'          => $dli_tb_r_phone,
		'phoneHref'      => $dli_tb_r_phone_href,
	);
}
?>

<div class="row mb-5 gy-4">
	<div class="col-12 col-lg-4">
		<div class="form-group">
			<label for="dliPeopleSearch">
				<?php echo esc_html__( 'Cerca testo', 'design_laboratori_italia' ); ?>
			</label>
			<input
				type="search"
				id="dliPeopleSearch"
				class="form-control"
				placeholder="<?php echo esc_attr__( 'Es. Rossi, Professore…', 'design_laboratori_italia' ); ?>"
				aria-label="<?php echo esc_attr__( 'Cerca per nome, cognome, tipologia o struttura', 'design_laboratori_italia' ); ?>"
			>
		</div>
	</div>
	<?php if ( $dli_tb_has_structures ) : ?>
		<div class="col-12 col-lg-4">
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
		<div class="col-12 col-lg-4">
			<div class="select-wrapper">
				<label for="selectTableType">
					<?php echo esc_html__( 'Tipologia', 'design_laboratori_italia' ); ?>
				</label>
				<select id="selectTableType">
					<option value="">
						<?php echo esc_html__( 'Tutte le tipologie', 'design_laboratori_italia' ); ?>
					</option>
					<?php foreach ( $dli_tb_categories as $dli_tb_cat ) : ?>
						<option value="<?php echo esc_attr( $dli_tb_cat->ID ); ?>">
							<?php echo esc_html( $dli_tb_category_map[ $dli_tb_cat->ID ] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( $dli_tb_has_tags ) : ?>
		<div class="col-12 col-lg-4">
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

<script>
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
var DLI_PEOPLE_TABLE = 
<?php
echo wp_json_encode(
	array(
		'rows'              => $dli_tb_json_rows,
		'perPage'           => PEOPLE_TABLE_PER_PAGE,
		'paginationEnabled' => $dli_tb_pagination_enabled,
		'cols'              => array(
			'type'      => ! $dli_tb_hide_type,
			'structure' => ! $dli_tb_hide_structure,
			'tag'       => ! $dli_tb_hide_tag,
			'email'     => ! $dli_tb_hide_email,
			'phone'     => ! $dli_tb_hide_phone,
		),
		'spritesUrl'        => $dli_tb_sprites_url,
		'i18n'              => array(
			'prevPage'       => __( 'Pagina precedente', 'design_laboratori_italia' ),
			'nextPage'       => __( 'Pagina successiva', 'design_laboratori_italia' ),
			'resultSingular' => __( 'persona trovata', 'design_laboratori_italia' ),
			'resultPlural'   => __( 'persone trovate', 'design_laboratori_italia' ),
			'noResults'      => __( 'Nessuna persona trovata', 'design_laboratori_italia' ),
		),
	)
);
?>
;
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
( function () {
	var cfg     = DLI_PEOPLE_TABLE;
	var allRows = cfg.rows;
	var tbody    = document.getElementById( 'people-tbody' );
	var countEl  = document.getElementById( 'dliPeopleCount' );
	var paginEl  = document.getElementById( 'people-pagination' );
	var selSort      = document.getElementById( 'dliPeopleSort' );
	var selStruttura = document.getElementById( 'selectTableStructure' );
	var selLevel     = document.getElementById( 'selectTableLevel' );
	var selType      = document.getElementById( 'selectTableType' );
	var inputSearch  = document.getElementById( 'dliPeopleSearch' );

	var state = {
		search:    '',
		struttura: selStruttura ? selStruttura.value : '',
		level:     selLevel ? selLevel.value : '',
		type:      selType ? selType.value : '',
		sortCol:   selSort ? selSort.value : 'sortKey',
		sortAsc:   true,
		page:      1,
	};

	function escHtml( s ) {
		return String( s )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#39;' );
	}

	function applyFilters() {
		return allRows.filter( function ( row ) {
			if ( state.struttura && row.structureSlugs.indexOf( state.struttura ) === -1 ) {
				return false;
			}
			if ( state.level && row.tagSlugs.indexOf( state.level ) === -1 ) {
				return false;
			}
			if ( state.type && row.typeId !== state.type ) {
				return false;
			}
			if ( state.search ) {
				var q = state.search.toLowerCase();
				if (
					row.sortKey.indexOf( q ) === -1 &&
					row.typeSort.indexOf( q ) === -1 &&
					row.structureSort.indexOf( q ) === -1
				) {
					return false;
				}
			}
			return true;
		} );
	}

	function applySort( rows ) {
		return rows.slice().sort( function ( a, b ) {
			var av  = String( a[ state.sortCol ] || '' );
			var bv  = String( b[ state.sortCol ] || '' );
			var cmp = av.localeCompare( bv, 'it', { sensitivity: 'base', numeric: true } );
			return state.sortAsc ? cmp : -cmp;
		} );
	}

	function renderTbody( pageRows ) {
		var html = '';
		if ( ! pageRows.length ) {
			var colCount = 2
				+ ( cfg.cols.type ? 1 : 0 )
				+ ( cfg.cols.structure ? 1 : 0 )
				+ ( cfg.cols.tag ? 1 : 0 )
				+ ( cfg.cols.email ? 1 : 0 )
				+ ( cfg.cols.phone ? 1 : 0 );
			html = '<tr><td colspan="' + colCount + '" class="text-center py-4">'
				+ escHtml( cfg.i18n.noResults ) + '</td></tr>';
		} else {
			pageRows.forEach( function ( row ) {
				html += '<tr>';
				var nameText = escHtml( row.name + ' ' + row.surname );
				if ( row.link ) {
					var rel    = '_blank' === row.linkTarget ? ' rel="noopener noreferrer"' : '';
					var target = row.linkTarget ? ' target="' + escHtml( row.linkTarget ) + '"' : '';
					html += '<td><a href="'
						+ escHtml( row.link ) + '"' + target + rel + '>' + nameText + '</a></td>';
				} else {
					html += '<td>' + nameText + '</td>';
				}
				if ( cfg.cols.type )      { html += '<td>' + escHtml( row.type ) + '</td>'; }
				if ( cfg.cols.structure ) { html += '<td class="d-none d-md-table-cell">' + escHtml( row.structure ) + '</td>'; }
				if ( cfg.cols.tag )       { html += '<td class="d-none d-md-table-cell">' + escHtml( row.tag )       + '</td>'; }
				if ( cfg.cols.email ) {
					html += '<td class="d-none d-md-table-cell">';
					if ( row.email ) {
						html += '<a href="mailto:' + escHtml( row.email ) + '">' + escHtml( row.email ) + '</a>';
					}
					html += '</td>';
				}
				if ( cfg.cols.phone ) {
					html += '<td class="d-none d-md-table-cell">';
					if ( row.phone ) {
						html += '<a href="tel:' + escHtml( row.phoneHref ) + '">' + escHtml( row.phone ) + '</a>';
					}
					html += '</td>';
				}
				html += '</tr>';
			} );
		}
		tbody.innerHTML = html;
	}

	function updateFilterIndicators() {
		var pairs = [
			[ selStruttura, state.struttura ],
			[ selType,      state.type      ],
			[ selLevel,     state.level     ],
		];
		pairs.forEach( function ( pair ) {
			var el = pair[ 0 ];
			var active = '' !== pair[ 1 ];
			if ( ! el ) { return; }
			var wrapper = el.closest( '.select-wrapper' );
			if ( wrapper ) {
				if ( active ) {
					wrapper.classList.add( 'dli-filter-active' );
				} else {
					wrapper.classList.remove( 'dli-filter-active' );
				}
			}
		} );
	}

	function renderCount( total ) {
		if ( ! countEl ) { return; }
		countEl.textContent = total + ' '
			+ ( 1 === total ? cfg.i18n.resultSingular : cfg.i18n.resultPlural );
	}

	function renderPagination( totalPages ) {
		if ( ! paginEl ) { return; }
		if ( ! cfg.paginationEnabled || totalPages <= 1 ) {
			paginEl.innerHTML = '';
			return;
		}
		var html = '<ul class="pagination">';
		if ( state.page > 1 ) {
			html += '<li class="page-item"><button class="page-link" data-page="' + ( state.page - 1 ) + '"'
				+ ' aria-label="' + escHtml( cfg.i18n.prevPage ) + '">'
				+ '<svg class="icon icon-primary" aria-hidden="true"><use href="' + escHtml( cfg.spritesUrl ) + '#it-chevron-left"></use></svg>'
				+ '</button></li>';
		}
		for ( var p = 1; p <= totalPages; p++ ) {
			var isCurrent = ( p === state.page );
			html += '<li class="page-item' + ( isCurrent ? ' active' : '' ) + '">'
				+ '<button class="page-link" data-page="' + p + '"'
				+ ( isCurrent ? ' aria-current="page"' : '' ) + '>'
				+ p + '</button></li>';
		}
		if ( state.page < totalPages ) {
			html += '<li class="page-item"><button class="page-link" data-page="' + ( state.page + 1 ) + '"'
				+ ' aria-label="' + escHtml( cfg.i18n.nextPage ) + '">'
				+ '<svg class="icon icon-primary" aria-hidden="true"><use href="' + escHtml( cfg.spritesUrl ) + '#it-chevron-right"></use></svg>'
				+ '</button></li>';
		}
		html += '</ul>';
		paginEl.innerHTML = html;
		paginEl.querySelectorAll( 'button[data-page]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				state.page = parseInt( btn.getAttribute( 'data-page' ), 10 );
				render();
				document.getElementById( 'people-table' ).scrollIntoView( { behavior: 'smooth', block: 'start' } );
			} );
		} );
	}

	function render() {
		var filtered   = applyFilters();
		var sorted     = applySort( filtered );
		var total      = sorted.length;
		var perPage    = cfg.paginationEnabled ? cfg.perPage : total;
		var totalPages = cfg.paginationEnabled ? Math.max( 1, Math.ceil( total / perPage ) ) : 1;
		if ( state.page > totalPages ) { state.page = Math.max( 1, totalPages ); }
		var offset   = ( state.page - 1 ) * perPage;
		var pageRows = cfg.paginationEnabled ? sorted.slice( offset, offset + perPage ) : sorted;
		renderTbody( pageRows );
		renderCount( total );
		renderPagination( totalPages );
		updateFilterIndicators();
	}

	if ( selSort ) {
		selSort.addEventListener( 'change', function () {
			state.sortCol = selSort.value;
			state.page = 1;
			render();
		} );
	}

	if ( selStruttura ) {
		selStruttura.addEventListener( 'change', function () {
			state.struttura = selStruttura.value;
			state.page = 1;
			render();
		} );
	}

	if ( selLevel ) {
		selLevel.addEventListener( 'change', function () {
			state.level = selLevel.value;
			state.page = 1;
			render();
		} );
	}

	if ( selType ) {
		selType.addEventListener( 'change', function () {
			state.type = selType.value;
			state.page = 1;
			render();
		} );
	}

	if ( inputSearch ) {
		inputSearch.addEventListener( 'input', function () {
			state.search = inputSearch.value.trim();
			state.page = 1;
			render();
		} );
		inputSearch.addEventListener( 'keydown', function ( e ) {
			if ( 'Enter' === e.key ) {
				e.preventDefault();
			}
		} );
	}

	render();
}() );
</script>
