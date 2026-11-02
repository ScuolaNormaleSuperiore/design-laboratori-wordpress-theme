<?php
/**
 * Homepage section "Indice di una pagina": the index of a page chosen in the row of Sezioni HP.
 *
 * The title is the title of the chosen page; its direct subpages are the columns (printed as text) and their own
 * subpages the links; a subpage without children of its own is left out (`dli_get_page_index()`). The columns and
 * the accordion of the phone come from `template-parts/common/page-groups.php`, shared with "Navigazione per pagine".
 * The page is shown in the current language: on the English site it is its translation, and without one nothing is
 * printed, as when the row has no page, the page is not published or is protected, or it has no column. The title is
 * always the `h2` of the section, which names it and comes before the `h3` of the columns: it is seen only when the row
 * asks for it and otherwise visually hidden, so that the headings never skip a level. The row also
 * chooses the most columns (`max_items`) and the most links of a column (`max_subitems`); without them the defaults
 * apply. The identifiers use the position of the row (`instance`), so the section can be repeated, also with the
 * same page.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args            = isset( $args ) && is_array( $args ) ? $args : array();
$dli_section_enabled = $dli_args['enabled'] ?? false;
$dli_show_title      = $dli_args['show_title'] ?? false;
$dli_instance        = max( 1, (int) ( $dli_args['instance'] ?? 1 ) );

if ( 'true' !== $dli_section_enabled ) {
	return;
}

$dli_index = dli_get_page_index(
	(int) ( $dli_args['page_id'] ?? 0 ),
	dli_normalize_hp_max_items( $dli_args['max_items'] ?? null, DLI_HP_PAGE_INDEX_DEFAULT_ITEMS ),
	dli_normalize_hp_max_items( $dli_args['max_subitems'] ?? null, DLI_HP_PAGE_INDEX_DEFAULT_SUBITEMS )
);
if ( null === $dli_index ) {
	return;
}

$dli_bg_class    = dli_get_hp_section_background_class( $dli_args, 'white' );
$dli_section_id  = 'blocco-page-index-' . $dli_instance;
$dli_title_id    = $dli_section_id . '-title';
$dli_title_shown = ( 'true' === $dli_show_title );
?>
<!-- INIZIO SEZIONE "INDICE DI UNA PAGINA" -->
<section id="<?php echo esc_attr( $dli_section_id ); ?>" class="section page-index-section<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-5" aria-labelledby="<?php echo esc_attr( $dli_title_id ); ?>">
	<div class="section-content">
		<div class="container">
			<?php if ( $dli_title_shown ) : ?>
				<div class="pb-3">
					<h2 id="<?php echo esc_attr( $dli_title_id ); ?>" class="mb-0"><?php echo esc_html( $dli_index['title'] ); ?></h2>
				</div>
			<?php else : ?>
				<h2 id="<?php echo esc_attr( $dli_title_id ); ?>" class="visually-hidden"><?php echo esc_html( $dli_index['title'] ); ?></h2>
			<?php endif; ?>
			<?php
			get_template_part(
				'template-parts/common/page-groups',
				null,
				array(
					'groups'       => $dli_index['groups'],
					'instance'     => $dli_instance,
					'prefix'       => 'page-index',
					'column_class' => dli_get_hp_groups_column_class( count( $dli_index['groups'] ) ),
				)
			);
			?>
		</div>
	</div>
</section>
<!-- FINE SEZIONE "INDICE DI UNA PAGINA" -->
