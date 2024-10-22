<?php
$box_number    = $args[0];
$box_label     = $args[1];
$box_template  = $args[2];
$box_post_type = $args[3];
$box_items     = 1;
$order_field   = 'post_date';

$query = new WP_Query(
	array(
		'post_type'      => array( $box_post_type ),
		'orderby'        => $order_field,
		'order'          => 'DESC',
		'posts_per_page' => 1,
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
	<div class="card-wrapper">
		<div class="card card-bg">
		<?php
		if ( $num_items != 0 ) {
			$carditem = $query->posts[0];
			$postitem = dli_get_post_wrapper( $carditem );
		?>
			<div class="card-body">
				<div class="category-top">
					<a class="category" 
						href="<?php echo esc_url( $postitem['category_link'] ); ?>">
						<?php echo esc_attr( $postitem['category'] ); ?>
					</a>
					<span class="data"><?php echo $postitem['date'] ?></span>
				</div>
				<h3 class="card-title h4"><?php echo esc_attr( $postitem['title'] ); ?></h3>
				<p class="card-text"><?php echo wp_trim_words( $postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ); ?></p>
				<a class="read-more" href="<?php echo esc_url( $postitem['link'] ); ?>">
					<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
					<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ?>">
						</use>
					</svg>
				</a>
			</div>
		<?php
		}
		?>
		</div>
	</div>
</div>