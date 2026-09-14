<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_item = isset( $args['item'] ) ? $args['item'] : null;
if ( $dli_item ) {
	$dli_postitem = dli_get_post_wrapper( $dli_item );
	$dli_datetime = get_the_date( 'c', $dli_item );
	?>
	<article class="it-card rounded border shadow-sm h-100">
		<h3 class="it-card-title h5">
			<a href="<?php echo esc_url( $dli_postitem['link'] ); ?>"><?php echo esc_html( $dli_postitem['title'] ); ?></a>
		</h3>
		<div class="it-card-body">
			<p class="it-card-text"><?php echo esc_html( $dli_postitem['description'] ); ?></p>
		</div>
		<footer class="it-card-footer">
			<div class="it-card-taxonomy">
				<a href="<?php echo esc_url( $dli_postitem['category_link'] ); ?>" class="it-card-category it-card-link">
					<span class="visually-hidden"><?php echo esc_html__( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
					<?php echo esc_html( $dli_postitem['category'] ); ?>
				</a>
			</div>
			<time class="it-card-date" datetime="<?php echo esc_attr( $dli_datetime ); ?>"><?php echo esc_html( $dli_postitem['date'] ); ?></time>
		</footer>
	</article>
	<?php
}
?>
