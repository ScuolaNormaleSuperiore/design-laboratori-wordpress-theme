<?php
/**
 * Item of a box of "Contenuti in evidenza" for a publication: title, authors and type.
 *
 * Receives the content already read and prints only its markup, through the content shared with the archive
 * and the publications section (`template-parts/common/publication-card-content.php`), so that they all show
 * the same thing. `archive_url` is the publications page, used by the links of the types. The card is the one
 * of the box in the playground: a line under it, no shadow, the authors without a bottom margin and no year.
 *
 * Arguments (`$args`):
 *  - `post`        (WP_Post) The publication.
 *  - `archive_url` (string)  Address of the publications page.
 *  - `heading`     (string)  Tag of the title, `h4` by default.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args    = isset( $args ) && is_array( $args ) ? $args : array();
$dli_item    = isset( $dli_args['post'] ) && $dli_args['post'] instanceof WP_Post ? $dli_args['post'] : null;
$dli_heading = isset( $dli_args['heading'] ) ? (string) $dli_args['heading'] : 'h4';

if ( null === $dli_item ) {
	return;
}
?>
<article class="it-card border-bottom">
	<?php
	get_template_part(
		'template-parts/common/publication-card-content',
		null,
		array(
			'post_id'     => $dli_item->ID,
			'archive_url' => isset( $dli_args['archive_url'] ) ? (string) $dli_args['archive_url'] : '',
			'heading'     => $dli_heading,
			'compact'     => true,
			'show_year'   => false,
		)
	);
	?>
</article>
