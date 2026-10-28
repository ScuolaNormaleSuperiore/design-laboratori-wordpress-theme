<?php
/**
 * Title of a content with an optional link.
 *
 * Prints the title as a link when there is an address, as plain text when there is none (never an
 * empty `href`, which would point to the same page). A link that leaves the site (another site or a
 * file) opens in a new window and says so to screen readers.
 *
 * Arguments (`$args`):
 *  - `title`    (string) The title.
 *  - `url`      (string) The address; empty for no link.
 *  - `external` (bool)   Whether the address leaves the site. Default false.
 *  - `class`    (string) Optional class of the link.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args     = isset( $args ) && is_array( $args ) ? $args : array();
$dli_title    = isset( $dli_args['title'] ) ? (string) $dli_args['title'] : '';
$dli_url      = isset( $dli_args['url'] ) ? (string) $dli_args['url'] : '';
$dli_external = ! empty( $dli_args['external'] );
$dli_class    = isset( $dli_args['class'] ) ? (string) $dli_args['class'] : '';

if ( '' === $dli_url ) {
	echo esc_html( $dli_title );
	return;
}
?>
<a href="<?php echo esc_url( $dli_url ); ?>"<?php echo '' !== $dli_class ? ' class="' . esc_attr( $dli_class ) . '"' : ''; ?><?php echo $dli_external ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
	<?php echo esc_html( $dli_title ); ?>
	<?php if ( $dli_external ) : ?>
		<span class="visually-hidden">(<?php echo esc_html__( 'si apre in una nuova finestra', 'design_laboratori_italia' ); ?>)</span>
	<?php endif; ?>
</a>
