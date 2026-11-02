<?php
/**
 * Item of a box of "Contenuti in evidenza" for a type without a layout of its own: the essential one.
 *
 * Shows only the title, the featured image when there is one, an abstract and the link, and never simulates the
 * layout of events or publications. Receives the content already read and prints only its markup. The title leads
 * where the editor chose (`dli_get_content_link()`). A password-protected content shows its title only: its text
 * and image are what the password protects.
 *
 * Arguments (`$args`):
 *  - `post`        (WP_Post) The content.
 *  - `archive_url` (string)  Not used by this layout.
 *  - `heading`     (string)  Tag of the title, `h4` by default.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args    = isset( $args ) && is_array( $args ) ? $args : array();
$dli_item    = isset( $dli_args['post'] ) && $dli_args['post'] instanceof WP_Post ? $dli_args['post'] : null;
$dli_heading = isset( $dli_args['heading'] ) && in_array( $dli_args['heading'], array( 'h2', 'h3', 'h4', 'h5' ), true ) ? $dli_args['heading'] : 'h4';

if ( null === $dli_item ) {
	return;
}

$dli_link      = dli_get_content_link( $dli_item );
$dli_protected = post_password_required( $dli_item );
$dli_abstract  = '';
$dli_image_url = '';
if ( ! $dli_protected ) {
	$dli_abstract = trim( $dli_item->post_excerpt );
	if ( '' === $dli_abstract ) {
		$dli_abstract = wp_trim_words( wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $dli_item->post_content ) ) ), DLI_ACF_SHORT_DESC_LENGTH );
	}
	$dli_image_url = (string) get_the_post_thumbnail_url( $dli_item, 'medium' );
}
?>
<article class="it-card rounded border">
	<?php if ( '' !== $dli_image_url ) : ?>
		<div class="it-card-image-wrapper">
			<div class="ratio ratio-16x9">
				<img src="<?php echo esc_url( $dli_image_url ); ?>" alt="" class="img-full">
			</div>
		</div>
	<?php endif; ?>
	<<?php echo esc_attr( $dli_heading ); ?> class="it-card-title h5 text-break" style="overflow-wrap: anywhere;">
		<?php
		get_template_part(
			'template-parts/common/linked-title',
			null,
			array(
				'title'    => get_the_title( $dli_item ),
				'url'      => $dli_link['url'],
				'external' => $dli_link['external'],
			)
		);
		?>
	</<?php echo esc_attr( $dli_heading ); ?>>
	<?php if ( '' !== $dli_abstract ) : ?>
		<div class="it-card-body">
			<p class="it-card-text text-break" style="overflow-wrap: anywhere;"><?php echo esc_html( $dli_abstract ); ?></p>
		</div>
	<?php endif; ?>
</article>
