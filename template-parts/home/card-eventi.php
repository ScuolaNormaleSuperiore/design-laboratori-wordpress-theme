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
$dli_col_lg_class  = $args[4] ?? 'col-lg-4';
$dli_box_items     = 3;
$dli_order_field   = 'post_date';
$dli_heading_id    = 'evidenza-eventi-title-' . $dli_box_number;

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
$dli_num_items    = $dli_query->post_count;
$dli_archive_page = dli_get_page_by_post_type( EVENT_POST_TYPE );
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
				$dli_datetime   = dli_get_event_datetime_display( $dli_item_id );
				$dli_luogo      = dli_get_field( 'luogo', $dli_item_id );
				$dli_category   = dli_get_post_main_category( $dli_carditem, 'category' );
				?>
				<li class="col-12 mb-3">
					<article class="it-card border-bottom">
						<h4 class="it-card-title h5">
							<a href="<?php echo esc_url( get_permalink( $dli_item_id ) ); ?>"><?php echo esc_html( $dli_item_title ); ?></a>
						</h4>
						<?php if ( $dli_datetime || $dli_luogo ) : ?>
							<div class="it-card-body">
								<?php if ( $dli_datetime ) : ?>
									<p class="it-card-subtitle"><span class="visually-hidden"><?php esc_html_e( 'Data evento:', 'design_laboratori_italia' ); ?></span> <?php echo wp_kses_post( $dli_datetime ); ?></p>
								<?php endif; ?>
								<?php if ( $dli_luogo ) : ?>
									<p class="it-card-text mb-0">
										<span class="visually-hidden"><?php esc_html_e( 'Luogo:', 'design_laboratori_italia' ); ?></span>
										<?php echo esc_html( $dli_luogo ); ?>
									</p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $dli_category['id'] ) ) : ?>
							<footer class="it-card-footer">
								<div class="it-card-taxonomy">
									<a class="it-card-category it-card-link" href="<?php echo esc_url( $dli_category['url'] ); ?>">
										<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
										<?php echo esc_html( $dli_category['title'] ); ?>
									</a>
								</div>
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
			<?php echo esc_html__( 'Tutti gli eventi', 'design_laboratori_italia' ); ?>
		</a>
	</div>
</div>
