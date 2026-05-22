<?php
/**
 * Template Name: Persone
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_selected_structure_raw = filter_input( INPUT_GET, 'struttura', FILTER_UNSAFE_RAW );
$dli_selected_level_raw     = filter_input( INPUT_GET, 'level', FILTER_UNSAFE_RAW );
$dli_selected_type_raw      = filter_input( INPUT_GET, 'tipologia', FILTER_UNSAFE_RAW );

$dli_selected_structure = is_string( $dli_selected_structure_raw ) ? sanitize_text_field( wp_unslash( $dli_selected_structure_raw ) ) : '';
$dli_selected_level     = is_string( $dli_selected_level_raw ) ? sanitize_text_field( wp_unslash( $dli_selected_level_raw ) ) : '';
$dli_selected_type      = is_string( $dli_selected_type_raw ) ? sanitize_text_field( wp_unslash( $dli_selected_type_raw ) ) : '';

$dli_view_type = dli_get_option( 'people_view_type', 'persone' );

if ( 'tabella' === $dli_view_type ) {
	// Per la vista tabella tutti i record sono scaricati una volta sola;
	// filtro, ordinamento e paginazione sono gestiti lato client.
	// I filtri GET sono usati solo per pre-selezionare i controlli.
	$dli_page_data                       = DLI_ContentsManager::get_people_page_data( array() );
	$dli_page_data['selected_structure'] = $dli_selected_structure;
	$dli_page_data['selected_level']     = $dli_selected_level;
} else {
	$dli_page_data = DLI_ContentsManager::get_people_page_data(
		array(
			'selected_structure' => $dli_selected_structure,
			'selected_level'     => $dli_selected_level,
		)
	);
}

$dli_filter_mode          = dli_get_option( 'pagination_mode', 'persone' );
$dli_filter_level_enabled = ( 'true' !== dli_get_option( 'hide_filter_tag', 'persone' ) );
$dli_hide_icon            = dli_get_option( 'hide_person_icon', 'persone' );
$dli_label_select_level   = dli_get_configuration_field_by_lang( 'seleziona_livello_persone', 'persone' );
$dli_label_all_levels     = dli_get_configuration_field_by_lang( 'tutti_i_livelli_persone', 'persone' );

$dli_filter_structure_hidden = ( 'true' === dli_get_option( 'hide_filter_structure', 'persone' ) );
$dli_filter_type_hidden      = ( 'true' === dli_get_option( 'hide_filter_type', 'persone' ) );
?>

<main id="main-container" role="main">
	<form action="<?php echo esc_url( get_permalink() ); ?>" id="personeform" method="GET">

		<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>
		<?php get_template_part( 'template-parts/hero/persone' ); ?>

		<div class="container my-4">
			<section class="section bg-gray-light py-5">
				<div class="container">
					<?php if ( 'tabella' !== $dli_view_type ) : ?>
						<?php
						get_template_part(
							'template-parts/persone/filters',
							null,
							array(
								'structures'              => $dli_page_data['structures'],
								'tags'                    => $dli_page_data['tags'],
								'categories'              => $dli_page_data['categories'],
								'selected_structure'      => $dli_page_data['selected_structure'],
								'selected_level'          => $dli_page_data['selected_level'],
								'selected_type'           => $dli_selected_type,
								'filter_mode'             => $dli_filter_mode,
								'filter_level_enabled'    => $dli_filter_level_enabled,
								'filter_structure_hidden' => $dli_filter_structure_hidden,
								'filter_type_hidden'      => $dli_filter_type_hidden,
								'label_select_level'      => $dli_label_select_level,
								'label_all_levels'        => $dli_label_all_levels,
								'result_count'            => $dli_page_data['result_count'],
							)
						);
						?>
					<?php endif; ?>

					<?php if ( $dli_page_data['result_count'] ) : ?>
						<?php if ( 'tabella' === $dli_view_type ) : ?>
							<?php
							get_template_part(
								'template-parts/persone/view-tabella',
								null,
								array(
									'page_data' => $dli_page_data,
								)
							);
							?>
						<?php else : ?>
							<?php
							get_template_part(
								'template-parts/persone/view-chip',
								null,
								array(
									'people_by_category' => $dli_page_data['people_by_category'],
									'categories'         => $dli_page_data['categories'],
									'selected_type'      => $dli_selected_type,
									'hide_icon'          => $dli_hide_icon,
								)
							);
							?>
						<?php endif; ?>
					<?php else : ?>
						<div class="col-12 col-lg-8">
							<div class="row pt-2">
								<?php echo esc_html__( 'Non è stata trovata nessuna persona', 'design_laboratori_italia' ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</section>
		</div>
	</form>
</main>

<?php
get_footer();
