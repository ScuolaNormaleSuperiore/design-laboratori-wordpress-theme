<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_item = isset( $args['item'] ) ? $args['item'] : null;
if ( $dli_item ) {
	$dli_postitem = dli_get_post_wrapper( $dli_item );
	?>

	<!--start card-->
	<div class="card card-bg">
		<div class="card-body">
		<div class="category-top">
			<a class="category"
				href="<?php echo esc_url( $dli_postitem['category_link'] ); ?>">
				<?php echo esc_html( $dli_postitem['category'] ); ?>
			</a>
			<span class="data"><?php echo esc_html( $dli_postitem['date'] ); ?></span>
		</div>
			<h3 class="card-title h4">
				<?php echo esc_html( $dli_postitem['title'] ); ?>
			</h3>
			<p class="card-text">
					<?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
			</p>
			<p class="pt-1">
			<a class="read-more" href="<?php echo esc_url( $dli_postitem['link'] ); ?>">
				<span class="text customSpacing">
					<?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?>
				</span>
				<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?>">
					<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>">
				</svg>
			</a>
			</p>
		</div>
	</div>
	<!--end card-->

	<?php
}
?>
