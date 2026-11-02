<?php
/**
 * Groups of page links of a homepage section: columns on a tablet and a desktop, an accordion on a phone.
 *
 * Shared by "Navigazione per pagine" and "Indice di una pagina", which differ only in where the groups come from.
 * Each group is a title, printed as text, and a list of links (`template-parts/common/page-link-list.php`). The same
 * groups are printed twice, as columns (`d-none d-md-flex`) and as an accordion (`d-md-none`, the first panel open):
 * the hidden copy is not read by screen readers, and every identifier is built from the prefix, the position of the
 * row of Sezioni HP (`instance`) and the page of the group, so that two copies of a section never share one, even
 * when they show the same page. The titles of the groups are `h3`, below the `h2` of the section.
 *
 * Arguments (`$args`):
 *  - `groups`       (array)  The groups: `id`, `title` and `items` (`id`, `title`, `url`).
 *  - `instance`     (int)    The position of the row of Sezioni HP.
 *  - `prefix`       (string) Start of the identifiers, for example `page-navigation`.
 *  - `column_class` (string) Classes of the column of each group (`dli_get_hp_groups_column_class()`).
 *
 * @package Design_Laboratori_Italia
 */

$dli_args         = isset( $args ) && is_array( $args ) ? $args : array();
$dli_groups       = isset( $dli_args['groups'] ) && is_array( $dli_args['groups'] ) ? $dli_args['groups'] : array();
$dli_instance     = max( 1, (int) ( $dli_args['instance'] ?? 1 ) );
$dli_prefix       = isset( $dli_args['prefix'] ) ? sanitize_html_class( (string) $dli_args['prefix'] ) : 'page-groups';
$dli_column_class = isset( $dli_args['column_class'] ) ? (string) $dli_args['column_class'] : 'col-12 col-md-6';

if ( empty( $dli_groups ) ) {
	return;
}

$dli_accordion_id = $dli_prefix . '-' . $dli_instance . '-accordion';
?>
<!-- Tablet e desktop: colonne -->
<div class="row d-none d-md-flex">
	<?php foreach ( $dli_groups as $dli_group ) : ?>
		<?php $dli_group_title_id = $dli_prefix . '-' . $dli_instance . '-title-' . $dli_group['id']; ?>
		<div class="<?php echo esc_attr( $dli_column_class ); ?> mb-4">
			<h3 id="<?php echo esc_attr( $dli_group_title_id ); ?>" class="h5 fw-bold"><?php echo esc_html( $dli_group['title'] ); ?></h3>
			<?php
			get_template_part(
				'template-parts/common/page-link-list',
				null,
				array(
					'items'       => $dli_group['items'],
					'labelled_by' => $dli_group_title_id,
				)
			);
			?>
		</div>
	<?php endforeach; ?>
</div>

<!-- Telefono: accordion, il primo pannello aperto -->
<div class="accordion d-md-none" id="<?php echo esc_attr( $dli_accordion_id ); ?>">
	<?php foreach ( $dli_groups as $dli_index => $dli_group ) : ?>
		<?php
		$dli_is_first = ( 0 === $dli_index );
		$dli_head_id  = $dli_prefix . '-' . $dli_instance . '-head-' . $dli_group['id'];
		$dli_body_id  = $dli_prefix . '-' . $dli_instance . '-body-' . $dli_group['id'];
		?>
		<div class="accordion-item">
			<h3 class="accordion-header" id="<?php echo esc_attr( $dli_head_id ); ?>">
				<button
					class="accordion-button<?php echo $dli_is_first ? '' : ' collapsed'; ?>"
					type="button"
					data-bs-toggle="collapse"
					data-bs-target="#<?php echo esc_attr( $dli_body_id ); ?>"
					aria-expanded="<?php echo $dli_is_first ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr( $dli_body_id ); ?>"
				>
					<?php echo esc_html( $dli_group['title'] ); ?>
				</button>
			</h3>
			<div id="<?php echo esc_attr( $dli_body_id ); ?>" class="accordion-collapse collapse<?php echo $dli_is_first ? ' show' : ''; ?>" role="region" aria-labelledby="<?php echo esc_attr( $dli_head_id ); ?>" data-bs-parent="#<?php echo esc_attr( $dli_accordion_id ); ?>">
				<div class="accordion-body">
					<?php get_template_part( 'template-parts/common/page-link-list', null, array( 'items' => $dli_group['items'] ) ); ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
