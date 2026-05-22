<?php
/**
 * Vista tabellare della pagina Persone.
 *
 * Riceve i dati via $args (terzo parametro di get_template_part).
 * Non va confusa con template-parts/common/sezione-persone.php, che è un widget
 * per pagine di dettaglio (progetti, indirizzi di ricerca, risorse tecniche).
 *
 * @package Design_Laboratori_Italia
 *
 * @var array $args {
 *     @type array $page_data Struttura dati prodotta da DLI_ContentsManager::get_people_page_data().
 * }
 */

// --- Dati dalla struttura centralizzata ---
$dli_tb_page_data          = isset( $args['page_data'] ) ? $args['page_data'] : array();
$dli_tb_people_rows        = isset( $dli_tb_page_data['people_rows'] ) ? $dli_tb_page_data['people_rows'] : array();
$dli_tb_categories         = isset( $dli_tb_page_data['categories'] ) ? $dli_tb_page_data['categories'] : array();
$dli_tb_structures         = isset( $dli_tb_page_data['structures'] ) ? $dli_tb_page_data['structures'] : array();
$dli_tb_tags               = isset( $dli_tb_page_data['tags'] ) ? $dli_tb_page_data['tags'] : array();
$dli_tb_selected_structure = isset( $dli_tb_page_data['selected_structure'] ) ? $dli_tb_page_data['selected_structure'] : '';
$dli_tb_selected_level     = isset( $dli_tb_page_data['selected_level'] ) ? $dli_tb_page_data['selected_level'] : '';
$dli_tb_current_page       = isset( $dli_tb_page_data['current_page'] ) ? (int) $dli_tb_page_data['current_page'] : 1;
$dli_tb_total_pages        = isset( $dli_tb_page_data['total_pages'] ) ? (int) $dli_tb_page_data['total_pages'] : 1;

// --- Configurazione ---
$dli_tb_hide_email           = ( 'true' === dli_get_option( 'hide_people_table_email', 'persone' ) );
$dli_tb_hide_phone           = ( 'true' === dli_get_option( 'hide_people_table_phone', 'persone' ) );
$dli_tb_hide_type            = ( 'true' === dli_get_option( 'hide_people_table_type', 'persone' ) );
$dli_tb_hide_structure       = ( 'true' === dli_get_option( 'hide_people_table_structure', 'persone' ) );
$dli_tb_filter_level_enabled = ( 'true' === dli_get_option( 'level_filter_enabled', 'persone' ) );
$dli_tb_label_select_level   = dli_get_configuration_field_by_lang( 'seleziona_livello_persone', 'persone' );
$dli_tb_label_all_levels     = dli_get_configuration_field_by_lang( 'tutti_i_livelli_persone', 'persone' );
$dli_tb_pagination_enabled   = ( 'true' === dli_get_option( 'enable_people_table_pagination', 'persone' ) );

// --- Mappa categorie ID => nome ---
$dli_tb_category_map = array();
foreach ( $dli_tb_categories as $dli_tb_cat ) {
	$dli_tb_category_map[ $dli_tb_cat->ID ] = dli_get_field( 'nome', $dli_tb_cat->ID );
}

$dli_tb_sprites_url    = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg';
$dli_tb_has_structures = count( $dli_tb_structures ) >= 1;
$dli_tb_has_tags       = $dli_tb_filter_level_enabled && count( $dli_tb_tags ) > 0;

if ( $dli_tb_has_structures || $dli_tb_has_tags ) :
	?>
	<div class="row mb-5">
		<div class="col-lg-3"></div>
		<div class="col-lg-4">
			<?php if ( $dli_tb_has_structures ) : ?>
				<div class="select-wrapper">
					<label for="selectTableStructure"><?php echo esc_html__( 'Seleziona la struttura', 'design_laboratori_italia' ); ?></label>
					<select id="selectTableStructure" onchange="reloadWithSelectedItem('selectTableStructure', 'struttura')">
						<option value="" <?php selected( $dli_tb_selected_structure, '' ); ?>>
							<?php echo esc_html__( 'Tutte le strutture', 'design_laboratori_italia' ); ?>
						</option>
						<?php foreach ( $dli_tb_structures as $dli_tb_struttura ) : ?>
							<option value="<?php echo esc_attr( $dli_tb_struttura->slug ); ?>" <?php selected( $dli_tb_selected_structure, $dli_tb_struttura->slug ); ?>>
								<?php echo esc_html( $dli_tb_struttura->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-lg-4">
			<?php if ( $dli_tb_has_tags ) : ?>
				<div class="select-wrapper">
					<label for="selectTableLevel"><?php echo esc_html( $dli_tb_label_select_level ); ?></label>
					<select id="selectTableLevel" onchange="reloadWithSelectedItem('selectTableLevel', 'level')">
						<option value="" <?php selected( $dli_tb_selected_level, '' ); ?>>
							<?php echo esc_html( $dli_tb_label_all_levels ); ?>
						</option>
						<?php foreach ( $dli_tb_tags as $dli_tb_tag ) : ?>
							<option value="<?php echo esc_attr( $dli_tb_tag->slug ); ?>" <?php selected( $dli_tb_selected_level, $dli_tb_tag->slug ); ?>>
								<?php echo esc_html( $dli_tb_tag->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<div class="table-responsive">
	<table class="table table-striped table-hover" id="people-table">
		<caption class="visually-hidden"><?php echo esc_html__( 'Elenco persone', 'design_laboratori_italia' ); ?></caption>
		<thead>
			<tr>
				<th scope="col" aria-sort="ascending">
					<button type="button" data-sort-col="surname">
						<?php echo esc_html__( 'Nome / Cognome', 'design_laboratori_italia' ); ?>
						<svg class="icon icon-sm" aria-hidden="true">
							<use href="<?php echo esc_url( $dli_tb_sprites_url ); ?>#it-expand"></use>
						</svg>
					</button>
				</th>
				<?php if ( ! $dli_tb_hide_type ) : ?>
					<th scope="col" aria-sort="none">
						<button type="button" data-sort-col="type">
							<?php echo esc_html__( 'Tipologia', 'design_laboratori_italia' ); ?>
							<svg class="icon icon-sm" aria-hidden="true">
								<use href="<?php echo esc_url( $dli_tb_sprites_url ); ?>#it-expand"></use>
							</svg>
						</button>
					</th>
				<?php endif; ?>
				<?php if ( ! $dli_tb_hide_structure ) : ?>
					<th scope="col" aria-sort="none">
						<button type="button" data-sort-col="structure">
							<?php echo esc_html__( 'Struttura', 'design_laboratori_italia' ); ?>
							<svg class="icon icon-sm" aria-hidden="true">
								<use href="<?php echo esc_url( $dli_tb_sprites_url ); ?>#it-expand"></use>
							</svg>
						</button>
					</th>
				<?php endif; ?>
				<th scope="col" aria-sort="none">
					<button type="button" data-sort-col="tag">
						<?php echo esc_html__( 'TAG', 'design_laboratori_italia' ); ?>
						<svg class="icon icon-sm" aria-hidden="true">
							<use href="<?php echo esc_url( $dli_tb_sprites_url ); ?>#it-expand"></use>
						</svg>
					</button>
				</th>
				<?php if ( ! $dli_tb_hide_email ) : ?>
					<th scope="col" aria-sort="none">
						<button type="button" data-sort-col="email">
							<?php echo esc_html__( 'Email', 'design_laboratori_italia' ); ?>
							<svg class="icon icon-sm" aria-hidden="true">
								<use href="<?php echo esc_url( $dli_tb_sprites_url ); ?>#it-expand"></use>
							</svg>
						</button>
					</th>
				<?php endif; ?>
				<?php if ( ! $dli_tb_hide_phone ) : ?>
					<th scope="col" aria-sort="none">
						<button type="button" data-sort-col="phone">
							<?php echo esc_html__( 'Telefono', 'design_laboratori_italia' ); ?>
							<svg class="icon icon-sm" aria-hidden="true">
								<use href="<?php echo esc_url( $dli_tb_sprites_url ); ?>#it-expand"></use>
							</svg>
						</button>
					</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $dli_tb_people_rows as $dli_tb_row ) : ?>
				<?php
				$dli_tb_person      = $dli_tb_row['person'];
				$dli_tb_person_id   = $dli_tb_person->ID;
				$dli_tb_category_id = $dli_tb_row['category_id'];

				$dli_tb_name      = dli_get_field( 'nome', $dli_tb_person_id );
				$dli_tb_surname   = dli_get_field( 'cognome', $dli_tb_person_id );
				$dli_tb_sort_name = strtolower( trim( $dli_tb_surname . ' ' . $dli_tb_name ) );

				$dli_tb_disable_detail = dli_get_field( 'disattiva_pagina_dettaglio', $dli_tb_person_id );
				$dli_tb_enable_direct  = dli_get_field( 'abilita_link_diretto_pagina_persona', $dli_tb_person_id );
				$dli_tb_permalink      = get_the_permalink( $dli_tb_person_id );
				$dli_tb_site_url       = dli_get_field( 'sito_web', $dli_tb_person_id );

				$dli_tb_type_name = isset( $dli_tb_category_map[ $dli_tb_category_id ] ) ? (string) $dli_tb_category_map[ $dli_tb_category_id ] : '';

				$dli_tb_structure_terms = get_the_terms( $dli_tb_person_id, STRUCTURE_TAXONOMY );
				$dli_tb_structure_names = ( ! is_wp_error( $dli_tb_structure_terms ) && ! empty( $dli_tb_structure_terms ) )
					? implode( ', ', wp_list_pluck( $dli_tb_structure_terms, 'name' ) )
					: '';

				$dli_tb_tag_terms = wp_get_post_terms( $dli_tb_person_id, WP_DEFAULT_TAGS );
				$dli_tb_tag_names = ( ! is_wp_error( $dli_tb_tag_terms ) && ! empty( $dli_tb_tag_terms ) )
					? implode( ', ', wp_list_pluck( $dli_tb_tag_terms, 'name' ) )
					: '';

				$dli_tb_email_raw  = (string) dli_get_field( 'email', $dli_tb_person_id );
				$dli_tb_phone_raw  = (string) dli_get_field( 'telefono', $dli_tb_person_id );
				$dli_tb_phone_href = preg_replace( '/[^+\d]/', '', $dli_tb_phone_raw );
				?>
				<tr>
					<th scope="row" data-col="surname" data-sort="<?php echo esc_attr( $dli_tb_sort_name ); ?>">
						<?php if ( ! $dli_tb_disable_detail ) : ?>
							<?php if ( ! $dli_tb_enable_direct ) : ?>
								<a class="text-decoration-none" href="<?php echo esc_url( $dli_tb_permalink ); ?>">
									<?php echo esc_html( $dli_tb_name ) . ' ' . esc_html( $dli_tb_surname ); ?>
								</a>
							<?php else : ?>
								<a class="text-decoration-none" href="<?php echo esc_url( $dli_tb_site_url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $dli_tb_name ) . ' ' . esc_html( $dli_tb_surname ); ?>
								</a>
							<?php endif; ?>
						<?php else : ?>
							<?php echo esc_html( $dli_tb_name ) . ' ' . esc_html( $dli_tb_surname ); ?>
						<?php endif; ?>
					</th>
					<?php if ( ! $dli_tb_hide_type ) : ?>
						<td data-col="type" data-sort="<?php echo esc_attr( strtolower( $dli_tb_type_name ) ); ?>">
							<?php echo esc_html( $dli_tb_type_name ); ?>
						</td>
					<?php endif; ?>
					<?php if ( ! $dli_tb_hide_structure ) : ?>
						<td data-col="structure" data-sort="<?php echo esc_attr( strtolower( $dli_tb_structure_names ) ); ?>">
							<?php echo esc_html( $dli_tb_structure_names ); ?>
						</td>
					<?php endif; ?>
					<td data-col="tag" data-sort="<?php echo esc_attr( strtolower( $dli_tb_tag_names ) ); ?>">
						<?php echo esc_html( $dli_tb_tag_names ); ?>
					</td>
					<?php if ( ! $dli_tb_hide_email ) : ?>
						<td data-col="email" data-sort="<?php echo esc_attr( strtolower( $dli_tb_email_raw ) ); ?>">
							<?php if ( '' !== $dli_tb_email_raw ) : ?>
								<a href="mailto:<?php echo antispambot( $dli_tb_email_raw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
									<?php echo antispambot( $dli_tb_email_raw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
						</td>
					<?php endif; ?>
					<?php if ( ! $dli_tb_hide_phone ) : ?>
						<td data-col="phone" data-sort="<?php echo esc_attr( $dli_tb_phone_href ); ?>">
							<?php if ( '' !== $dli_tb_phone_raw ) : ?>
								<a href="tel:<?php echo esc_attr( $dli_tb_phone_href ); ?>">
									<?php echo esc_html( $dli_tb_phone_raw ); ?>
								</a>
							<?php endif; ?>
						</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php if ( $dli_tb_pagination_enabled && $dli_tb_total_pages > 1 ) : ?>
	<?php
	$dli_tb_add_args = array();
	if ( '' !== $dli_tb_selected_structure ) {
		$dli_tb_add_args['struttura'] = $dli_tb_selected_structure;
	}
	if ( '' !== $dli_tb_selected_level ) {
		$dli_tb_add_args['level'] = $dli_tb_selected_level;
	}
	$dli_tb_prev_label = '<svg class="icon icon-primary" role="img" aria-label="' . esc_attr__( 'Pagina precedente', 'design_laboratori_italia' ) . '"><use href="' . esc_url( $dli_tb_sprites_url . '#it-chevron-left' ) . '"></use></svg>';
	$dli_tb_next_label = '<svg class="icon icon-primary" role="img" aria-label="' . esc_attr__( 'Pagina successiva', 'design_laboratori_italia' ) . '"><use href="' . esc_url( $dli_tb_sprites_url . '#it-chevron-right' ) . '"></use></svg>';
	?>
	<nav class="pagination-wrapper justify-content-center mt-4" aria-label="<?php echo esc_attr__( 'Navigazione pagine persone', 'design_laboratori_italia' ); ?>">
		<?php
		echo wp_kses_post(
			paginate_links(
				array(
					'total'     => $dli_tb_total_pages,
					'current'   => $dli_tb_current_page,
					'prev_text' => $dli_tb_prev_label,
					'next_text' => $dli_tb_next_label,
					'type'      => 'list',
					'add_args'  => $dli_tb_add_args,
				)
			)
		);
		?>
	</nav>
<?php endif; ?>

<script>
( function () {
	var table = document.getElementById( 'people-table' );
	if ( ! table ) return;

	var spritesUrl = '<?php echo esc_js( $dli_tb_sprites_url ); ?>';
	var btns       = table.querySelectorAll( 'thead button[data-sort-col]' );

	btns.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var th          = btn.closest( 'th' );
			var ascending   = 'ascending' !== th.getAttribute( 'aria-sort' );
			var colName     = btn.getAttribute( 'data-sort-col' );

			btns.forEach( function ( b ) {
				b.closest( 'th' ).setAttribute( 'aria-sort', 'none' );
				b.querySelector( 'use' ).setAttribute( 'href', spritesUrl + '#it-expand' );
			} );

			th.setAttribute( 'aria-sort', ascending ? 'ascending' : 'descending' );
			btn.querySelector( 'use' ).setAttribute( 'href', spritesUrl + ( ascending ? '#it-arrow-up' : '#it-arrow-down' ) );

			var tbody = table.querySelector( 'tbody' );
			var rows  = Array.from( tbody.querySelectorAll( 'tr' ) );

			rows.sort( function ( a, b ) {
				var aEl  = a.querySelector( '[data-col="' + colName + '"]' );
				var bEl  = b.querySelector( '[data-col="' + colName + '"]' );
				var aVal = aEl ? ( aEl.getAttribute( 'data-sort' ) || aEl.textContent.trim() ) : '';
				var bVal = bEl ? ( bEl.getAttribute( 'data-sort' ) || bEl.textContent.trim() ) : '';
				return ascending
					? aVal.localeCompare( bVal, 'it', { sensitivity: 'base', numeric: true } )
					: bVal.localeCompare( aVal, 'it', { sensitivity: 'base', numeric: true } );
			} );

			rows.forEach( function ( r ) {
				tbody.appendChild( r );
			} );
		} );
	} );
}() );
</script>
