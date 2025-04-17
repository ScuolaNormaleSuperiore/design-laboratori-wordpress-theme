<?php
/**
 * The template for displaying home
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();

$all_sections    = DLI_ContentsManager::get_hp_sections();
$active_sections = DLI_ContentsManager::get_hp_section_options( true );
?>
<main id="main-container" class="main-container redbrown" role="main">

<?php
foreach ( $active_sections as $section ) {
	$sec_id   = $section['id'];
	$sec_data = ( $sec_id && array_key_exists( $sec_id, $all_sections ) ) ? $all_sections[$sec_id] : null;
	if ( $sec_data ){
		get_template_part(
			$sec_data['template'],
			null,
			array(
				'id'         => $section['id'],
				'show_title' => $section['show_title'],
				'enabled'    => $section['enabled'],
				'name'       => $sec_data['name'],
				'template'   => $sec_data['template'],
			)
		);
?>

<?
	}
}
?>
</main>
<?php
get_footer();
