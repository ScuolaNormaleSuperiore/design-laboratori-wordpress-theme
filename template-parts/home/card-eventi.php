<?php
$box_number    = $args[0];
$box_label     = $args[1];
$box_template  = $args[2];
$box_post_type = $args[3];
$box_items     = $args[4];
$order_field   = 'post_date';

$query = new WP_Query(
	array(
		'post_type'      => array( $box_post_type ),
		'orderby'        => $order_field,
		'order'          => 'DESC',
		'posts_per_page' => -1,
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
		<div class="card card-img no-after card-bg">
		<?php
		if ( $num_items != 0 ) {
			$carditem      = $query->posts[0];
			$postitem      = dli_get_post_wrapper( $carditem );
			$date          = $postitem['date'];
			$item_date     = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
			$orario_inizio = $postitem['orario_inizio'];
		?>
			<div class="img-responsive-wrapper">
				<div class="img-responsive img-responsive-panoramic">
					<figure class="img-wrapper">
						<img src="<?php echo esc_url( $postitem['image_url'] ); ?>"
							alt="<?php echo esc_attr( $postitem['image_alt'] ); ?>"
							title="<?php echo esc_attr( $postitem['image_title'] ); ?>"
						>
					</figure>
					<div class="card-calendar d-flex flex-column justify-content-center">
						<span class="card-date"><?php echo intval( $item_date->format( 'd' ) ); ?></span>
						<span class="card-day"><?php echo __( dli_get_monthname( $item_date->format( 'm' ), 'design_laboratori_italia' ) ); ?>
							&nbsp;<?php echo intval( $item_date->format( 'Y' ) ); ?>
						</span>
					</div>
				</div>
			</div>
			<div class="card-body p-4">
			<h3 class="card-title h4"><?php echo esc_attr( $postitem['title'] ); ?></h3>
			<p class="card-text"><?php echo esc_html( wp_trim_words( $postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
			<?php if( $orario_inizio ) {
				?>
				<p class="card-text">
					<?php echo $orario_inizio; ?>
				</p>
				<?php
			}
				?>
			<a class="read-more" href="<?php echo $postitem['link']; ?>">
				<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
				<svg class="icon" role="img" aria-labelledby="Arrow right">
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
