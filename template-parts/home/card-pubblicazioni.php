<?php
/**
 * Home featured publications card.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args          = isset( $args ) && is_array( $args ) ? $args : array();
$dli_box_number    = $dli_args['box_number'] ?? 0;
$dli_box_label     = $dli_args['box_label'] ?? '';
$dli_box_template  = $dli_args['box_template'] ?? '';
$dli_box_post_type = $dli_args['box_post_type'] ?? '';
$dli_box_items     = 3;
$dli_order_field   = 'post_date';
$dli_heading_id    = 'evidenza-pubblicazioni-title-' . $dli_box_number;

$dli_query       = new WP_Query(
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
$dli_num_items   = $dli_query->post_count;
$dli_archive_url = dli_get_translated_page_url_by_slug( SLUG_PUBBLICAZIONI_IT );
?>

<div class="col-12 col-lg-4 col-md-12 flex-column pb-5">
	<h2 id="<?php echo esc_attr( $dli_heading_id ); ?>" class="h3 pb-2"><?php echo esc_html( $dli_box_label ); ?></h2>

	<?php if ( $dli_num_items > 0 ) : ?>
		<ul class="it-card-list row" aria-labelledby="<?php echo esc_attr( $dli_heading_id ); ?>">
			<?php foreach ( $dli_query->posts as $dli_carditem ) : ?>
				<li class="col-12 mb-3">
					<article class="it-card rounded shadow-sm">
						<?php
						get_template_part(
							'template-parts/common/publication-card-content',
							null,
							array(
								'post_id'     => $dli_carditem->ID,
								'archive_url' => $dli_archive_url,
								'heading'     => 'h3',
							)
						);
						?>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( '' !== $dli_archive_url ) : ?>
		<div class="pt-2 text-center">
			<a href="<?php echo esc_url( $dli_archive_url ); ?>" class="btn btn-secondary">
				<?php echo esc_html__( 'Tutte le pubblicazioni', 'design_laboratori_italia' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
