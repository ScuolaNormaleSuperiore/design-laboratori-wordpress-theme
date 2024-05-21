<?php
$box_number    = $args[0];
$box_label     = $args[1];
$box_template  = $args[2];
$box_post_type = $args[3];
$box_items     = 2;
$order_field   = 'post_date';

$query = new WP_Query(
	array(
		'post_type'      => array( $box_post_type ),
		'orderby'        => $order_field,
		'order'          => 'DESC',
		'posts_per_page' => $box_items ,
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
	<h2 class="h3 pb-2"><?php echo __( $box_label, 'design_laboratori_italia' ); ?></h2>

	<?php
		foreach( $query->posts as $carditem ) {
			$postitem = dli_get_post_wrapper( $carditem );
	?>
	<div class="card card-teaser rounded shadow">
		<div class="card-body">
			<h3 class="card-title h5"><?php echo esc_attr( $postitem['title'] ); ?></h3>
			<p class="card-text"><?php echo esc_attr( $postitem['description'] ); ?></p>
		</div>
	</div>
	<?php
			// Restore original post data. 
			wp_reset_postdata();
		}
	?>
</div>
