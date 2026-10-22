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
$dli_col_lg_class  = $args[4] ?? 'col-lg-4';
$dli_box_items     = 3;
$dli_order_field   = 'post_date';
$dli_heading_id    = 'evidenza-notizie-title-' . $dli_box_number;

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
$dli_archive_page = dli_get_page_by_post_type( NEWS_POST_TYPE );
$dli_archive_url  = $dli_archive_page ? get_permalink( $dli_archive_page->ID ) : '';
?>

<div class="col-12 <?php echo esc_attr( $dli_col_lg_class ); ?> d-flex flex-column pb-5">
	<h3 id="<?php echo esc_attr( $dli_heading_id ); ?>" class="h3 pb-2"><?php echo esc_html( $dli_box_label ); ?></h3>

	<?php if ( $dli_num_items > 0 ) : ?>
		<ul class="it-card-list row" aria-labelledby="<?php echo esc_attr( $dli_heading_id ); ?>">
			<?php
			foreach ( $dli_query->posts as $dli_carditem ) {
				$dli_item_id    = $dli_carditem->ID;
				$dli_item_title = get_the_title( $dli_item_id );
				$dli_news_date  = get_the_date( DLI_ACF_DATE_FORMAT, $dli_item_id );
				$dli_news_date  = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_news_date );
				$dli_category   = dli_get_post_main_category( $dli_carditem, 'category' );
				?>
				<li class="col-12 mb-3">
					<article class="it-card border-bottom">
						<h4 class="it-card-title h5">
							<a href="<?php echo esc_url( get_permalink( $dli_item_id ) ); ?>"><?php echo esc_html( $dli_item_title ); ?></a>
						</h4>
						<?php if ( ! empty( $dli_category['id'] ) || $dli_news_date ) : ?>
							<footer class="it-card-footer">
								<?php if ( ! empty( $dli_category['id'] ) ) : ?>
									<div class="it-card-taxonomy">
										<a class="it-card-category it-card-link" href="<?php echo esc_url( $dli_category['url'] ); ?>">
											<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
											<?php echo esc_html( $dli_category['title'] ); ?>
										</a>
									</div>
								<?php endif; ?>
								<?php if ( $dli_news_date ) : ?>
									<time class="it-card-date" datetime="<?php echo esc_attr( $dli_news_date->format( 'Y-m-d' ) ); ?>"><?php echo esc_html( trim( $dli_news_date->format( 'd' ) . ' ' . dli_get_monthname( $dli_news_date->format( 'm' ) ) . ' ' . $dli_news_date->format( 'Y' ) ) ); ?></time>
								<?php endif; ?>
							</footer>
						<?php endif; ?>
					</article>
				</li>
				<?php
			}
			?>
		</ul>
	<?php endif; ?>

	<div class="pt-2 text-center">
		<a href="<?php echo esc_url( $dli_archive_url ); ?>" class="btn btn-secondary">
			<?php echo esc_html__( 'Tutte le news', 'design_laboratori_italia' ); ?>
		</a>
	</div>
</div>
