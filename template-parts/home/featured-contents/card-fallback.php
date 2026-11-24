<?php
/**
 * Item of a box of "Contenuti in evidenza" for a type without a layout of its own: the essential one.
 *
 * Shows only the title, with its link, and a short abstract, and never simulates the layout of events or
 * publications. It looks like the items of the news and of the events (a line under the item, no frame and no
 * image), so that the boxes of a row look alike. The abstract is the summary of the content or, without it, the
 * beginning of its text, cut at `DLI_HP_FEATURED_EXCERPT_WORDS` words in both cases. Receives the content already
 * read and prints only its markup. The title leads where the editor chose (`dli_get_content_link()`). A
 * password-protected content shows its title only: its text is what the password protects.
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

$dli_link     = dli_get_content_link( $dli_item );
$dli_abstract = '';
if ( ! post_password_required( $dli_item ) ) {
	$dli_abstract = trim( $dli_item->post_excerpt );
	if ( '' === $dli_abstract ) {
		$dli_abstract = strip_shortcodes( excerpt_remove_blocks( $dli_item->post_content ) );
	}
	$dli_abstract = wp_trim_words( wp_strip_all_tags( $dli_abstract ), DLI_HP_FEATURED_EXCERPT_WORDS );
}
?>
<article class="it-card border-bottom">
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
			<p class="it-card-text mb-0 text-break" style="overflow-wrap: anywhere;"><?php echo esc_html( $dli_abstract ); ?></p>
		</div>
	<?php endif; ?>
</article>
