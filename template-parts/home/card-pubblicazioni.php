<?php
$query = new WP_Query(
	array(
		'post_type'      => array( 'pubblicazione' ),
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'posts_per_page' => 2,
		'meta_query'     => array(
			array(
				'key'     => 'promuovi_in_home',
				'compare' => '=',
				'value'   => 1,
			),
		),
	)
);
$num_items = $query->post_count;
?>

<div class="col-12 col-lg-4 col-md-12 flex-column pb-5">
	<h2 class="h3 pb-2"><?php echo __( 'Pubblicazioni', 'design_laboratori_italia' ); ?></h2>

	<?php
		while( $query->have_posts() ) {
			$query->the_post();
	?>
	<div class="card card-teaser rounded shadow">
		<div class="card-body">
			<h3 class="card-title h5"><?php the_title() ?></h3>
			<p class="card-text"><?php echo esc_attr( the_content() ); ?></p>
		</div>
	</div>
	<?php
			// Restore original post data. 
			wp_reset_postdata();
		}
	?>
</div>
