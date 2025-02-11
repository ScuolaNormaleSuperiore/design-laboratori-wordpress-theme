<?php
$section_enabled = dli_get_option( 'home_sponsor_list_is_visible', 'homepage' );

if ( 'true' === $section_enabled ) {
	$query = new WP_Query(
		array(
			'post_type'      => array( SPONSOR_POST_TYPE ),
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'priorita',
			'order'          => 'ASC',
		)
	);
	$num_items = $query->post_count;
	if ( $num_items > 0 ) {
?>


	<section id="sponsor" class="section">
		<div class="container my-12">
			<div class="it-grid-list-wrapper it-image-label-grid">
				<div class="grid-row">

				<?php
					foreach ( $query->posts as $post ){
						$image_metadata = dli_get_image_metadata( $post, 'full', '/assets/img/yourimage.png' );
						$post_id        = $post->ID;
						$external_link  = dli_get_field( 'link_esterno', $post_id );
					?>
					<div class="col-6 col-lg-2">
						<div class="it-grid-item-wrapper">
							<a href="<?php echo esc_attr( $external_link ); ?>" target="_blank">
								<figure class="figure img-full w-100">
									<img
										src="<?php echo $image_metadata['image_url'] ?>"
										title="<?php echo $image_metadata['image_title'] ?>"
										class="figure-img img-fluid rounded"
										alt="<?php echo $image_metadata['image_title'] ?>"
									>
								</figure>
							</a>
						</div>
					</div>
					<?php
						}
					?>

				</div>
			</div>
		</div>
	</section>


<?php
	}
}
?>
