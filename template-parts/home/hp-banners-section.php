<?php
$section_enabled = $args['enabled'] ?? false;
$show_title      = $args['show_title'] ?? false;

if ( 'true' === $section_enabled ) {
	$query = new WP_Query(
		array(
			'post_type'      => array( BANNER_POST_TYPE ),
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'priorita',
			'order'          => 'ASC',
		)
	);
	$num_items = $query->post_count;
	if ( $num_items > 0 ) {
?>

	<?php
	foreach ( $query->posts as $post ){
		$image_metadata = dli_get_image_metadata( $post, 'full', '/assets/img/yourimage.png' );
		$post_id        = $post->ID;
		$section        = dli_get_field( 'sezione', $post_id );
		$button_label   = __( dli_get_field( 'label_pulsante', $post_id ), 'design_laboratori_italia' );
		$button_link    = dli_get_field(  'link_pulsante', $post_id );
		$is_external    = dli_get_field( 'apri_in_nuova_finestra', $post_id ) ? true : false;
	?>

	<div class="container-banner-home">
		<section class="it-hero-wrapper it-hero-small-size it-primary it-overlay mt-5">
			<!-- img -->
			<div class="img-responsive-wrapper">
				<div class="img-responsive">
					<div class="img-wrapper">
						<img
							src="<?php echo $image_metadata['image_url'] ?>"
							title="<?php echo $image_metadata['image_title'] ?>"
							alt="<?php echo $image_metadata['image_alt'] ?>"
						>
					</div>
				</div>
			</div>
			<!-- text -->
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="it-hero-text-wrapper">
							<span class="it-Categoria"><?php echo esc_attr( $section ) ?></span>
							<h2><?php echo esc_attr( $post->post_title ) ?></h2>
							<p class="d-none d-lg-block"><?php echo esc_attr( $post->post_content ) ?> </p>
							<?php
								if ( $button_link ){
							?>
								<div class="it-btn-container">
									<a class="btn btn-sm btn-secondary"
										<?php
										if ( $is_external ){
										?>
										target="_blank"
										<?php
										}
										?>
										href="<?php echo esc_attr( $button_link ); ?>">
										<?php echo esc_attr( $button_label ); ?>
									</a>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

<?php
		}
	}
}
?>