<?php
/**
 * Homepage publication list section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title      = $args['show_title'] ?? false;
$dli_order_field     = 'post_date';

if ( 'true' === $dli_section_enabled ) {
	$dli_query     = new WP_Query(
		array(
			'post_type'      => array( PUBLICATION_POST_TYPE ),
			'orderby'        => $dli_order_field,
			'order'          => 'DESC',
			'posts_per_page' => 3,
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
	if ( $dli_num_items > 0 ) {
		$dli_archive_url = dli_get_translated_page_url_by_slug( SLUG_PUBBLICAZIONI_IT );
		?>
	<!-- INIZIO ELENCO PUBBLICAZIONI HP -->
	<section id="blocco-pubblicazioni" class="section pt-3" <?php echo ( 'true' === $dli_show_title ) ? 'aria-labelledby="blocco-pubblicazioni-title"' : 'aria-label="' . esc_attr__( 'Pubblicazioni', 'design_laboratori_italia' ) . '"'; ?>>
		<div class="section-content">
			<div class="container">
				<?php if ( 'true' === $dli_show_title ) : ?>
					<h2 id="blocco-pubblicazioni-title" class="h3 pb-2"><?php echo esc_html__( 'Pubblicazioni', 'design_laboratori_italia' ); ?></h2>
				<?php endif; ?>
				<ul class="it-card-list row" aria-label="<?php echo esc_attr__( 'Ultime pubblicazioni', 'design_laboratori_italia' ); ?>">

				<?php
				foreach ( $dli_query->posts as $dli_post ) {
					$dli_item_id     = $dli_post->ID;
					$dli_item_title  = get_the_title( $dli_item_id );
					$dli_item_anno   = dli_get_field( 'anno', $dli_item_id );
					$dli_item_autori = dli_get_field( 'autori', $dli_item_id );
					$dli_item_autori = is_array( $dli_item_autori ) ? wp_list_pluck( $dli_item_autori, 'post_title' ) : array();
					$dli_item_terms  = get_the_terms( $dli_item_id, PUBLICATION_TYPE_TAXONOMY );
					$dli_item_terms  = ( is_array( $dli_item_terms ) && ! is_wp_error( $dli_item_terms ) ) ? $dli_item_terms : array();
					?>
					<!-- PUBBLICAZIONE -->
					<li class="col-12 col-md-6 col-lg-4 mb-3 mb-md-4">
						<article class="it-card it-card-height-full rounded shadow-sm border">
							<h3 class="it-card-title h5">
								<?php if ( DLI_PUBLICATION_HAS_DETAIL ) : ?>
									<a href="<?php echo esc_url( get_permalink( $dli_item_id ) ); ?>"><?php echo esc_html( $dli_item_title ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $dli_item_title ); ?>
								<?php endif; ?>
							</h3>
							<?php if ( ! empty( $dli_item_autori ) ) : ?>
								<div class="it-card-body">
									<p class="it-card-text"><?php echo esc_html( implode( ', ', $dli_item_autori ) ); ?></p>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $dli_item_terms ) || $dli_item_anno ) : ?>
								<footer class="it-card-footer">
									<?php if ( ! empty( $dli_item_terms ) ) : ?>
										<div class="it-card-taxonomy">
											<?php foreach ( $dli_item_terms as $dli_item_term ) : ?>
												<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'tipologia', array( $dli_item_term->slug ), $dli_archive_url ) ); ?>">
													<span class="visually-hidden"><?php esc_html_e( 'Tipologia:', 'design_laboratori_italia' ); ?></span>
													<?php echo esc_html( $dli_item_term->name ); ?>
												</a>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
									<?php if ( $dli_item_anno ) : ?>
										<time class="it-card-date" datetime="<?php echo esc_attr( $dli_item_anno ); ?>"><?php echo esc_html( $dli_item_anno ); ?></time>
									<?php endif; ?>
								</footer>
							<?php endif; ?>
						</article>
					</li>
					<!-- FINE PUBBLICAZIONE -->
					<?php
				}
				?>
				</ul>
				<div class="text-center pt-5">
					<a href="<?php echo esc_url( $dli_archive_url ); ?>" class="btn btn-secondary">
						<?php echo esc_html__( 'Tutte le pubblicazioni', 'design_laboratori_italia' ); ?>
					</a>
				</div>
		</div>
		</div>
	</section>
	<!-- FINE ELENCO PUBBLICAZIONI HP -->
		<?php
	}
}
?>
