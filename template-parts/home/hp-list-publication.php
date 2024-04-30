<?php
$section_enabled = dli_get_option( 'home_publication_list_is_visible', 'homepage' );
$order_field = 'post_date';

if ( 'true' === $section_enabled ) {
	$query = new WP_Query(
		array(
			'post_type'      => array( PUBLICATION_POST_TYPE ),
			'orderby'        => $order_field,
			'order'          => 'DESC',
			'posts_per_page' => 4,
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
	<!-- INIZIO ELENCO PUBBLICAZIONI HP -->
	<section id="blocco-pubblicazioni" class="section pt-3 " >
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
					<!-- PUBBLICAZIONE -->
					<div class="col-12 col-lg-3"> 
						<div class="card card-teaser">
							<div class="card-body">
								<h3 class="card-title h5"><?php echo esc_attr( $postitem['title'] ); ?></h3>
								<p class="card-text"><?php echo esc_attr( $postitem['description'] ); ?></p>
							</div>
						</div>
					</div>
					<!-- FINE PUBBLICAZIONE -->
				<?php
					}
				?>
				</div>
				<div class="text-center pt-5">
					<?php
							$page_url = dli_get_translated_page_url_by_slug( SLUG_PUBBLICAZIONI_IT );
					?>
					<a href="<?php echo  $page_url; ?>" class="btn btn-secondary">
						<?php echo __( 'Tutte le pubblicazioni', 'design_laboratori_italia' ); ?>
					</a>
				</div>
		</div>
	</section>
	<!-- FINE ELENCO PUBBLICAZIONI HP -->
<?php
	}
}
?>
