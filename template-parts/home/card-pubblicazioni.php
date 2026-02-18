<?php
/**
 * Home featured publications card.
 *
 * @package Design_Laboratori_Italia
 */

$dli_box_number    = $args[0];
$dli_box_label     = $args[1];
$dli_box_template  = $args[2];
$dli_box_post_type = $args[3];
$dli_box_items     = 2;
$dli_order_field   = 'post_date';

$dli_query = new WP_Query(
	array(
		'post_type'      => array( $dli_box_post_type ),
		'orderby'        => $dli_order_field,
		'order'          => 'DESC',
		'posts_per_page' => $dli_box_items,
		'meta_query'     => array(
			array(
				'key'     => 'promuovi_in_home',
				'compare' => '=',
				'value'   => 1,
			),
		),
	)
);
$dli_num_items = $dli_query->post_count;
?>

<div class="col-12 col-lg-4 col-md-12 flex-column pb-5">
	<h2 class="h3 pb-2"><?php echo esc_html( $dli_box_label ); ?></h2>

	<?php
	foreach ( $dli_query->posts as $dli_carditem ) {
		$dli_postitem = dli_get_post_wrapper( $dli_carditem );
		?>
	<div class="card card-teaser rounded shadow">
		<div class="card-body">
			<h3 class="card-title h5"><?php echo esc_html( $dli_postitem['title'] ); ?></h3>
			<p class="card-text"><?php echo esc_html( $dli_postitem['description'] ); ?></p>
		</div>
	</div>
		<?php
	}
		// Restore original post data.
		wp_reset_postdata();
	?>
</div>
