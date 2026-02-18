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
	$dli_query = new WP_Query(
		array(
			'post_type'      => array( PUBLICATION_POST_TYPE ),
			'orderby'        => $dli_order_field,
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
	$dli_num_items = $dli_query->post_count;
	if ( $dli_num_items > 0 ) {
		?>
	<!-- INIZIO ELENCO PUBBLICAZIONI HP -->
	<section id="blocco-pubblicazioni" class="section pt-3 " >
		<div class="section-content">
			<div class="container">
				<?php
				if ( 'true' === $dli_show_title ) {
					?>
					<h2 class="h3 pb-2 ">
						<?php echo esc_html__( 'Pubblicazioni', 'design_laboratori_italia' ); ?>
					</h2>
					<?php
				}
				?>
				<div class="row">

				<?php
				foreach ( $dli_query->posts as $dli_post ) {
					$dli_postitem = dli_get_post_wrapper( $dli_post );
					?>
					<!-- PUBBLICAZIONE -->
					<div class="col-12 col-lg-3"> 
						<div class="card card-teaser">
							<div class="card-body">
								<h3 class="card-title h5"><?php echo esc_html( $dli_postitem['title'] ); ?></h3>
								<p class="card-text"><?php echo esc_html( $dli_postitem['description'] ); ?></p>
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
							$dli_page_url = dli_get_translated_page_url_by_slug( SLUG_PUBBLICAZIONI_IT );
					?>
					<a href="<?php echo esc_url( $dli_page_url ); ?>" class="btn btn-secondary">
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
