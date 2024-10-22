<?php
$section_enabled = dli_get_option( 'home_news_list_is_visible', 'homepage' );
$order_field = 'post_date';

if ( 'true' === $section_enabled ) {
	$query = new WP_Query(
		array(
			'post_type'      => array( NEWS_POST_TYPE ),
			'orderby'        => $order_field,
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
	$num_items = $query->post_count;
	if ( $num_items > 0 ) {
?>
	<!-- INIZIO ELENCO NOTIZIE HP -->
	<section id="blocco-news" class="section pt-3 pb-3" >
		<div class="section-content">
			<div class="container">
				<h2 class="h3 pb-2 ">
					<?php echo __('Notizie', 'design_laboratori_italia' ); ?>
				</h2>
				<div class="row">
				<?php
					foreach ( $query->posts as $post ){
						$postitem = dli_get_post_wrapper( $post );
				?>
					<!-- NEWS -->
					<div class="col-12 col-lg-4"> 
						<div class="card-wrapper">
							<div class="card card-bg">
								<div class="img-responsive-wrapper">
									<div class="img-responsive">
										<figure class="img-wrapper">
											<img src="<?php echo esc_url( $postitem['image_url'] ); ?>"
														alt="<?php echo esc_attr( $postitem['image_alt'] ); ?>"
														title="<?php echo esc_attr( $postitem['image_title'] ); ?>"
											>
										</figure>
									</div>
								</div>
								<div class="card-body">
									<div class="category-top">
										<a class="category" 
											href="<?php echo esc_url( $postitem['category_link'] ); ?>">
											<?php echo esc_attr( $postitem['category'] ); ?>
										</a>
										<span class="data"><?php echo $postitem['date'] ?></span>
									</div>
									<h3 class="card-title h4">
										<?php echo esc_attr( $postitem['title'] ); ?>
									</h3>
									<p class="card-text">
										<?php echo wp_trim_words( $postitem['description'], DLI_ACF_SHORT_DESC_LENGTH ); ?>
									</p>
									<a class="read-more" href="<?php echo esc_url( $postitem['link'] ); ?>">
										<span class="text">
											<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>
										</span>
										<svg class="icon" aria-label="<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>">
											<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ?>">
										</svg>
									</a>
								</div>
							</div>
						</div>
					</div>
					<!-- FINE NEWS -->
					<?php
						}
					?>
				</div>
				<div class="text-center pt-5">
					<?php
						$page_url = dli_get_translated_page_url_by_slug( SLUG_NOTIZIE_IT );
					?>
					<a href="<?php echo  $page_url; ?>" class="btn btn-secondary">
						<?php echo __( 'Tutte le notizie', 'design_laboratori_italia' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE ELENCO NOTIZIE HP -->
<?php
	}
}
?>
