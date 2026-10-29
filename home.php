<?php
/**
 * The template for displaying home.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();

$dli_all_sections    = DLI_ContentsManager::get_hp_sections();
$dli_active_sections = DLI_ContentsManager::get_hp_section_options( true );
?>
<main id="main-container" class="main-container redbrown" role="main">
	<?php
	foreach ( $dli_active_sections as $dli_section ) {
		$dli_section_id   = $dli_section['id'];
		$dli_section_data = ( $dli_section_id && array_key_exists( $dli_section_id, $dli_all_sections ) ) ? $dli_all_sections[ $dli_section_id ] : null;

		if ( $dli_section_data ) {
			// The whole row is passed: besides `id`, `show_title` and `enabled` it carries `instance` (its position in
			// the saved list, to build identifiers that are different for each copy of a section) and the properties
			// that only some sections use, such as `background`.
			get_template_part( $dli_section_data['template'], null, $dli_section );
		}
	}
	?>
</main>
<?php
get_footer();
