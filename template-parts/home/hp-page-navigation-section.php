<?php
/**
 * Homepage section "Navigazione per pagine": the promoted pages with the links to their subpages.
 *
 * Each page with the field "Promuovi in Home" on that has published subpages is a group: its title (plain text,
 * not a link) and the list of links to its subpages. The groups are ordered by the field "Ordine" of the page and
 * then by title, and so are the subpages; the numbers shown (`max_items` groups, `max_subitems` per group) are
 * chosen in the row of Sezioni HP, with defaults (`dli_get_promoted_page_groups()`). The same content is printed
 * twice: columns on a tablet and a desktop, an accordion with the first panel open on a phone, the way the
 * layout prototype does. Nothing is printed without groups. Title and subtitle come from Configurazione → Layout HP.
 * The identifiers use the position of the row (`instance`) and the page of the group, so the section can be repeated,
 * and the hidden copy is not reachable from the keyboard (it is `display: none`).
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

$dli_groups = dli_get_promoted_page_groups(
	dli_normalize_hp_max_items( $dli_args['max_items'] ?? null, DLI_HP_PAGE_NAVIGATION_DEFAULT_ITEMS ),
	dli_normalize_hp_max_items( $dli_args['max_subitems'] ?? null, DLI_HP_PAGE_NAVIGATION_DEFAULT_SUBITEMS )
);
if ( empty( $dli_groups ) ) {
	return;
}

$dli_title        = (string) dli_get_configuration_field_by_lang( 'home_page_navigation_title', 'homepage' );
$dli_text         = (string) dli_get_configuration_field_by_lang( 'home_page_navigation_text', 'homepage' );
$dli_bg_class     = dli_get_hp_section_background_class( $dli_args, 'muted' );
$dli_section_id   = 'blocco-page-navigation-' . $dli_instance;
$dli_title_id     = $dli_section_id . '-title';
$dli_title_shown  = ( 'true' === $dli_show_title && '' !== $dli_title );
$dli_column_class = dli_get_hp_groups_column_class( count( $dli_groups ) );
?>
<!-- INIZIO SEZIONE "NAVIGAZIONE PER PAGINE" -->
<section id="<?php echo esc_attr( $dli_section_id ); ?>" class="section page-navigation-section<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-5" <?php echo $dli_title_shown ? 'aria-labelledby="' . esc_attr( $dli_title_id ) . '"' : 'aria-label="' . esc_attr__( 'Navigazione per pagine', 'design_laboratori_italia' ) . '"'; ?>>
	<div class="section-content">
		<div class="container">
			<?php if ( $dli_title_shown ) : ?>
				<h2 id="<?php echo esc_attr( $dli_title_id ); ?>" class="h3 pb-2"><?php echo esc_html( $dli_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $dli_text ) : ?>
				<p class="pb-3"><?php echo esc_html( $dli_text ); ?></p>
			<?php endif; ?>

			<?php
			get_template_part(
				'template-parts/common/page-groups',
				null,
				array(
					'groups'       => $dli_groups,
					'instance'     => $dli_instance,
					'prefix'       => 'page-navigation',
					'column_class' => $dli_column_class,
				)
			);
			?>
		</div>
	</div>
</section>
<!-- FINE SEZIONE "NAVIGAZIONE PER PAGINE" -->
