<?php
/**
 * Home featured event card.
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
	<div class="card-wrapper">
		<div class="card card-img no-after card-bg">
		<?php
		if ( 0 !== $dli_num_items ) {
			$dli_carditem      = $dli_query->posts[0];
			$dli_postitem      = dli_get_post_wrapper( $dli_carditem );
			$dli_date          = $dli_postitem['date'];
			$dli_item_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
			$dli_item_day      = $dli_item_date ? intval( $dli_item_date->format( 'd' ) ) : '';
			$dli_item_month    = $dli_item_date ? dli_get_monthname( $dli_item_date->format( 'm' ) ) : '';
			$dli_item_year     = $dli_item_date ? intval( $dli_item_date->format( 'Y' ) ) : '';
			$dli_orario_inizio = $dli_postitem['orario_inizio'];
			?>
			<div class="img-responsive-wrapper">
				<div class="img-responsive img-responsive-panoramic">
					<figure class="img-wrapper">
						<img src="<?php echo esc_url( $dli_postitem['image_url'] ); ?>"
							alt="<?php echo esc_attr( $dli_postitem['image_alt'] ); ?>"
							title="<?php echo esc_attr( $dli_postitem['image_title'] ); ?>"
						>
					</figure>
					<?php if ( $dli_item_date ) { ?>
					<div class="card-calendar d-flex flex-column justify-content-center">
						<span class="card-date"><?php echo esc_html( $dli_item_day ); ?></span>
						<span class="card-day"><?php echo esc_html( $dli_item_month ); ?>
							&nbsp;<?php echo esc_html( $dli_item_year ); ?>
						</span>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="card-body p-4">
			<h3 class="card-title h4"><?php echo esc_html( $dli_postitem['title'] ); ?></h3>
			<p class="card-text"><?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
			<?php
			if ( $dli_orario_inizio ) {
				?>
				<p class="card-text">
					<?php echo esc_html( $dli_orario_inizio ); ?>
				</p>
				<?php
			}
			?>
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
