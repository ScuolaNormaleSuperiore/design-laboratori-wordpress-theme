<?php
/**
 * Homepage article list section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title      = $args['show_title'] ?? false;
$dli_order_field     = 'post_date';

if ( 'true' === $dli_section_enabled ) {
	$dli_query     = new WP_Query(
		array(
			'post_type'      => array( WP_DEFAULT_POST ),
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
		?>
	<!-- INIZIO ELENCO ARTICOLI HP (stessa struttura di #blocco-news) -->
	<section id="blocco-blog" class="section pt-3 pb-3" <?php echo ( 'true' === $dli_show_title ) ? 'aria-labelledby="blocco-blog-title"' : 'aria-label="' . esc_attr__( 'Blog', 'design_laboratori_italia' ) . '"'; ?>>
		<div class="section-content">
			<div class="container">
				<?php if ( 'true' === $dli_show_title ) : ?>
					<h2 id="blocco-blog-title" class="h3 pb-2"><?php echo esc_html__( 'Blog', 'design_laboratori_italia' ); ?></h2>
				<?php endif; ?>
				<div class="row">
				<?php
				foreach ( $dli_query->posts as $dli_post ) {
					$dli_postitem  = dli_get_post_wrapper( $dli_post );
					$dli_blog_date = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_postitem['date'] );
					?>
					<!-- ARTICOLI -->
					<div class="col-12 col-lg-4">
						<div class="card-wrapper">
							<article class="it-card it-card-image card-bg rounded border">
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
								<div class="it-card-body">
									<p class="it-card-text"><?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
									<?php if ( $dli_postitem['category'] || $dli_blog_date ) : ?>
										<footer class="it-card-footer">
											<?php if ( $dli_postitem['category'] ) : ?>
												<div class="it-card-taxonomy">
													<a class="it-card-category it-card-link" href="<?php echo esc_url( $dli_postitem['category_link'] ); ?>">
														<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
														<?php echo esc_html( $dli_postitem['category'] ); ?>
													</a>
												</div>
											<?php endif; ?>
											<?php if ( $dli_blog_date ) : ?>
												<time class="it-card-date" datetime="<?php echo esc_attr( $dli_blog_date->format( 'Y-m-d' ) ); ?>"><?php echo esc_html( trim( $dli_blog_date->format( 'd' ) . ' ' . dli_get_monthname( $dli_blog_date->format( 'm' ) ) . ' ' . $dli_blog_date->format( 'Y' ) ) ); ?></time>
											<?php endif; ?>
										</footer>
									<?php endif; ?>
								</div>
							</article>
						</div>
					</div>
					<!-- FINE ARTICOLI -->
					<?php
				}
				?>
				</div>
				<div class="text-center pt-5">
					<?php
						$dli_page_url = dli_get_translated_page_url_by_slug( SLUG_BLOG_IT );
					?>
					<a href="<?php echo esc_url( $dli_page_url ); ?>" class="btn btn-secondary">
						<?php echo esc_html__( 'Tutti gli articoli', 'design_laboratori_italia' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE ELENCO ARTICOLI HP -->
		<?php
	}
}
?>
