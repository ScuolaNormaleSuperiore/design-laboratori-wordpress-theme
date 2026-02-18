<?php
/**
 * Home featured news card.
 *
 * @package Design_Laboratori_Italia
 */

$dli_box_number    = $args[0];
$dli_box_label     = $args[1];
$dli_box_template  = $args[2];
$dli_box_post_type = $args[3];
$dli_box_items     = 1;
$dli_order_field   = 'post_date';

$dli_query = new WP_Query(
	array(
		'post_type'      => array( $dli_box_post_type ),
		'orderby'        => $dli_order_field,
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
$dli_num_items = $dli_query->post_count;
?>

<div class="col-12 col-lg-4 col-md-12 flex-column pb-5">
	<h2 class="h3 pb-2"><?php echo esc_html( $dli_box_label ); ?></h2>
	<div class="card-wrapper">
		<div class="card card-bg">
		<?php
		if ( 0 !== $dli_num_items ) {
			$dli_carditem = $dli_query->posts[0];
			$dli_postitem = dli_get_post_wrapper( $dli_carditem );
			?>
			<div class="card-body">
				<div class="category-top">
					<a class="category" 
						href="<?php echo esc_url( $dli_postitem['category_link'] ); ?>">
						<?php echo esc_html( $dli_postitem['category'] ); ?>
					</a>
					<span class="data"><?php echo esc_html( $dli_postitem['date'] ); ?></span>
				</div>
				<h3 class="card-title h4"><?php echo esc_html( $dli_postitem['title'] ); ?></h3>
				<p class="card-text"><?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
				<a class="read-more" href="<?php echo esc_url( $dli_postitem['link'] ); ?>">
					<span class="text"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
					<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo esc_attr__( 'Leggi di più', 'design_laboratori_italia' ); ?>">
						<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>">
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
