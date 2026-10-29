<?php
/**
 * A list of links to pages, in the style "Azioni, con freccia" of Bootstrap Italia.
 *
 * Used by the homepage section "Navigazione per pagine", which prints the same list twice (columns and accordion).
 *
 * Arguments (`$args`):
 *  - `items`         (array)  The pages: each with `title` and `url`.
 *  - `labelled_by`   (string) Optional id of the element that names the list, for `aria-labelledby`.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args        = isset( $args ) && is_array( $args ) ? $args : array();
$dli_items       = isset( $dli_args['items'] ) && is_array( $dli_args['items'] ) ? $dli_args['items'] : array();
$dli_labelled_by = isset( $dli_args['labelled_by'] ) ? (string) $dli_args['labelled_by'] : '';

if ( empty( $dli_items ) ) {
	return;
}
?>
<div class="it-list-wrapper">
	<ul class="it-list"<?php echo '' !== $dli_labelled_by ? ' aria-labelledby="' . esc_attr( $dli_labelled_by ) . '"' : ''; ?>>
		<?php foreach ( $dli_items as $dli_item ) : ?>
			<?php
			if ( empty( $dli_item['url'] ) || ! isset( $dli_item['title'] ) ) {
				continue;
			}
			?>
			<li>
				<a class="list-item" href="<?php echo esc_url( $dli_item['url'] ); ?>">
					<div class="it-right-zone">
						<span class="text fw-semibold"><?php echo esc_html( $dli_item['title'] ); ?></span>
						<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
						</svg>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
