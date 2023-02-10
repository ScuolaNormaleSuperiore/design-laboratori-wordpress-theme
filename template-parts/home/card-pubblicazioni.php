<?php
$query = new WP_Query(
	array(
		'post_type'      => array( 'pubblicazione' ),
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'posts_per_page' => 2,
		'meta_query'     => array(
			array(
				'key'     => 'promuovi_in_hero',
				'compare' => '=',
				'value'   => 1,
			),
		),
	)
);
$num_items = $query->post_count;
?>

<div class="col-12 col-lg-4">
	<h2 class="h3 pb-2">Pubblicazioni</h2>

	<?php
		while( $query->have_posts() ) {
			$query->the_post();
	?>
	<div class="card card-teaser rounded shadow">
		<div class="card-body">
			<h3 class="card-title h5"><?php the_title() ?></h3>
			<p class="card-text"><?php echo the_field( 'abstract' ); ?></p>
		</div>
	</div>
	<?php
			// Restore original post data. 
			wp_reset_postdata();
		}
	?>
</div>
