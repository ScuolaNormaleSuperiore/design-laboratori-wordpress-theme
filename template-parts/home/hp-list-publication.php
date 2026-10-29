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
					<?php foreach ( $dli_query->posts as $dli_post ) : ?>
						<!-- PUBBLICAZIONE -->
						<li class="col-12 col-md-6 col-lg-4 mb-3 mb-md-4">
							<article class="it-card it-card-height-full rounded shadow-sm border">
								<?php
								get_template_part(
									'template-parts/common/publication-card-content',
									null,
									array(
										'post_id'     => $dli_post->ID,
										'archive_url' => $dli_archive_url,
									)
								);
								?>
							</article>
						</li>
						<!-- FINE PUBBLICAZIONE -->
					<?php endforeach; ?>
				</ul>
				<?php if ( '' !== $dli_archive_url ) : ?>
					<div class="text-center pt-5">
						<a href="<?php echo esc_url( $dli_archive_url ); ?>" class="btn btn-secondary">
							<?php echo esc_html__( 'Tutte le pubblicazioni', 'design_laboratori_italia' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<!-- FINE ELENCO PUBBLICAZIONI HP -->
		<?php
	}
}
?>
