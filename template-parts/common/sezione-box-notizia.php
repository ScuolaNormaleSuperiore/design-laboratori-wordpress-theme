<?php
$item = $args['item'];
if ( $item ) {
	$postitem = dli_get_post_wrapper( $item );
?>

	<!--start card-->
	<div class="card card-bg">
		<div class="card-body">
		<div class="category-top">
			<a class="category"
				href="<?php echo esc_url( $postitem['category_link'] ); ?>">
				<?php echo esc_attr( $postitem['category'] ); ?>
			</a>
			<span class="data"><?php echo $postitem['date'] ?></span>
		</div>
			<h3 class="card-title h4">
				<?php echo esc_attr( $postitem['title'] ); ?>
			</h3>
			<p class="card-text">
				<?php echo wp_trim_words( $postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ); ?>
			</p>
			<p class="pt-1">
			<a class="read-more" href="<?php echo esc_url( $postitem['link'] ); ?>">
				<span class="text customSpacing">
					<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>
				</span>
				<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>">
					<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ?>">
				</svg>
			</a>
			</p>
		</div>
	</div>
	<!--end card-->

<?php
}
?>