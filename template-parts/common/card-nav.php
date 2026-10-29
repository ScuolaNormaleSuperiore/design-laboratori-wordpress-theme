<?php
/**
 * Navigation card: a title, an optional abstract and a link to a page.
 *
 * Shared by the subpages of a page (`page.php`) and by the homepage sections that lead to pages.
 *
 * Arguments (`$args`):
 *  - `title`    (string) The title of the page.
 *  - `url`      (string) Its address.
 *  - `abstract` (string) Optional abstract.
 *  - `heading`  (string) Tag of the title, `h3` by default.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args     = isset( $args ) && is_array( $args ) ? $args : array();
$dli_title    = isset( $dli_args['title'] ) ? (string) $dli_args['title'] : '';
$dli_url      = isset( $dli_args['url'] ) ? (string) $dli_args['url'] : '';
$dli_abstract = isset( $dli_args['abstract'] ) ? (string) $dli_args['abstract'] : '';
$dli_heading  = isset( $dli_args['heading'] ) && in_array( $dli_args['heading'], array( 'h2', 'h3', 'h4', 'h5' ), true ) ? $dli_args['heading'] : 'h3';

if ( '' === $dli_title || '' === $dli_url ) {
	return;
}
?>
<article class="it-card it-card-height-full bg-white rounded shadow-sm border card-nav">
	<<?php echo esc_attr( $dli_heading ); ?> class="it-card-title h4 fw-bold"><?php echo esc_html( $dli_title ); ?></<?php echo esc_attr( $dli_heading ); ?>>
	<?php if ( '' !== $dli_abstract ) : ?>
		<div class="it-card-body">
			<p class="it-card-text text-muted"><?php echo esc_html( $dli_abstract ); ?></p>
		</div>
	<?php endif; ?>
	<footer class="it-card-footer justify-content-start">
		<a class="fw-semibold" href="<?php echo esc_url( $dli_url ); ?>">
			<?php echo esc_html__( 'Vai al servizio', 'design_laboratori_italia' ); ?>
			<span class="visually-hidden">: <?php echo esc_html( $dli_title ); ?></span>
			<svg class="icon icon-sm icon-primary ms-2" aria-hidden="true" focusable="false">
				<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
			</svg>
		</a>
	</footer>
</article>
