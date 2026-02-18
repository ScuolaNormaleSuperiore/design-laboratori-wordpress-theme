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
	$dli_query = new WP_Query(
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
	<!-- INIZIO ELENCO ARTICOLI HP -->
	<section id="blocco-articoli" class="section pt-3 pb-3" >
		<div class="section-content">
			<div class="container">
				<?php
				if ( 'true' === $dli_show_title ) {
					?>
					<h2 class="h3 pb-2 ">
						<?php echo esc_html__( 'Blog', 'design_laboratori_italia' ); ?>
					</h2>
					<?php
				}
				?>
				<div class="row">
				<?php
				foreach ( $dli_query->posts as $dli_post ) {
					$dli_postitem = dli_get_post_wrapper( $dli_post );
					?>
					<!-- ARTICOLI -->
					<div class="col-12 col-lg-4"> 
						<div class="card-wrapper">
							<div class="card card-bg">
								<div class="img-responsive-wrapper">
									<div class="img-responsive">
										<figure class="img-wrapper">
											<img src="<?php echo esc_url( $dli_postitem['image_url'] ); ?>"
														alt="<?php echo esc_attr( $dli_postitem['image_alt'] ); ?>"
														title="<?php echo esc_attr( $dli_postitem['image_title'] ); ?>"
											>
										</figure>
									</div>
								</div>
								<div class="card-body">
									<div class="category-top">
										<a class="category"
											href="<?php echo esc_url( $dli_postitem['category_link'] ); ?>">
										<?php echo esc_attr( $dli_postitem['category'] ); ?>
										</a>
										<span class="data"><?php echo esc_html( $dli_postitem['date'] ); ?></span>
									</div>
									<h3 class="card-title h4"><?php echo esc_html( $dli_postitem['title'] ); ?></h3>
									<p class="card-text"><?php echo esc_html( wp_trim_words( $dli_postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
									<a class="read-more" href="<?php echo esc_url( $dli_postitem['link'] ); ?>">
										<span class="text"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
										<svg class="icon" aria-label="<?php echo esc_attr__( 'Leggi di più', 'design_laboratori_italia' ); ?>">
											<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
											<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
										</svg>
									</a>
								</div>
							</div>
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
