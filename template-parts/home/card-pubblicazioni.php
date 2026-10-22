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
$dli_col_lg_class  = $args[4] ?? 'col-lg-4';
$dli_box_items     = 3;
$dli_order_field   = 'post_date';
$dli_heading_id    = 'evidenza-pubblicazioni-title-' . $dli_box_number;

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
$dli_archive_url = dli_get_translated_page_url_by_slug( SLUG_PUBBLICAZIONI_IT );
?>

<div class="col-12 <?php echo esc_attr( $dli_col_lg_class ); ?> d-flex flex-column pb-5">
	<h3 id="<?php echo esc_attr( $dli_heading_id ); ?>" class="h3 pb-2"><?php echo esc_html( $dli_box_label ); ?></h3>

	<?php if ( $dli_num_items > 0 ) : ?>
		<ul class="it-card-list row" aria-labelledby="<?php echo esc_attr( $dli_heading_id ); ?>">
			<?php
			foreach ( $dli_query->posts as $dli_carditem ) {
				$dli_item_id     = $dli_carditem->ID;
				$dli_item_title  = get_the_title( $dli_item_id );
				$dli_item_autori = dli_get_field( 'autori', $dli_item_id );
				$dli_item_autori = is_array( $dli_item_autori ) ? wp_list_pluck( $dli_item_autori, 'post_title' ) : array();
				$dli_item_terms  = get_the_terms( $dli_item_id, PUBLICATION_TYPE_TAXONOMY );
				$dli_item_terms  = ( is_array( $dli_item_terms ) && ! is_wp_error( $dli_item_terms ) ) ? $dli_item_terms : array();
				?>
				<li class="col-12 mb-3">
					<article class="it-card border-bottom">
						<h4 class="it-card-title h5">
							<?php if ( DLI_PUBLICATION_HAS_DETAIL ) : ?>
								<a href="<?php echo esc_url( get_permalink( $dli_item_id ) ); ?>"><?php echo esc_html( $dli_item_title ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $dli_item_title ); ?>
							<?php endif; ?>
						</h4>
						<?php if ( ! empty( $dli_item_autori ) ) : ?>
							<div class="it-card-body">
								<p class="it-card-text mb-0"><?php echo esc_html( implode( ', ', $dli_item_autori ) ); ?></p>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $dli_item_terms ) ) : ?>
							<footer class="it-card-footer">
								<div class="it-card-taxonomy">
									<?php foreach ( $dli_item_terms as $dli_item_term ) : ?>
										<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'tipologia', array( $dli_item_term->slug ), $dli_archive_url ) ); ?>">
											<span class="visually-hidden"><?php esc_html_e( 'Tipologia:', 'design_laboratori_italia' ); ?></span>
											<?php echo esc_html( $dli_item_term->name ); ?>
										</a>
									<?php endforeach; ?>
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
			<?php echo esc_html__( 'Tutte le pubblicazioni', 'design_laboratori_italia' ); ?>
		</a>
	</div>
</div>
