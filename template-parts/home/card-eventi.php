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

$dli_query     = new WP_Query(
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

<div class="col-12 col-lg-4 col-md-12 d-flex flex-column pb-5">
	<h2 class="h3 pb-2"><?php echo esc_html( $dli_box_label ); ?></h2>
	<div class="card-wrapper flex-grow-1">
		<?php
		if ( 0 !== $dli_num_items ) {
			$dli_carditem      = $dli_query->posts[0];
			$dli_postitem      = dli_get_post_wrapper( $dli_carditem );
			$dli_date          = $dli_postitem['date'];
			$dli_item_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
			$dli_orario_inizio = $dli_postitem['orario_inizio'];
			$dli_subtitle      = trim( $dli_date . ( $dli_orario_inizio ? ', ' . $dli_orario_inizio : '' ) );
			?>
			<article class="it-card it-card-image it-card-height-full card-bg rounded shadow-sm border">
				<h3 class="it-card-title h4">
					<a href="<?php echo esc_url( $dli_postitem['link'] ); ?>"><?php echo esc_html( $dli_postitem['title'] ); ?></a>
				</h3>
				<div class="it-card-image-wrapper">
					<div class="ratio ratio-16x9">
						<figure class="figure img-full">
							<img src="<?php echo esc_url( $dli_postitem['image_url'] ); ?>"
								alt="<?php echo esc_attr( $dli_postitem['image_alt'] ); ?>"
								title="<?php echo esc_attr( $dli_postitem['image_title'] ); ?>"
							>
						</figure>
					</div>
				</div>
				<div class="it-card-body p-4">
					<?php if ( $dli_subtitle ) : ?>
						<p class="it-card-subtitle"><?php echo esc_html( $dli_subtitle ); ?></p>
					<?php endif; ?>
					<p class="it-card-text"><?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
				</div>
				<?php if ( $dli_item_date ) : ?>
					<footer class="it-card-footer">
						<time class="it-card-date" datetime="<?php echo esc_attr( $dli_item_date->format( 'Y-m-d' ) ); ?>"><?php echo esc_html( $dli_date ); ?></time>
					</footer>
				<?php endif; ?>
			</article>
			<?php
		}
		?>
	</div>
</div>
