<?php
/**
 * Homepage sponsor section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title      = $args['show_title'] ?? false;

if ( 'true' === $dli_section_enabled ) {
	$dli_sponsor_query = new WP_Query(
		array(
			'post_type'      => array( SPONSOR_POST_TYPE ),
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'priorita',
			'order'          => 'ASC',
		)
	);

	$dli_num_items = $dli_sponsor_query->post_count;

	if ( $dli_num_items > 0 ) {
		$dli_items_per_row = dli_get_option( 'num_row_sponsor', 'sponsors' );
		?>
		<section id="sponsor" class="section" aria-labelledby="sponsor-title">
			<div class="container my-12">
				<?php if ( $dli_show_title  ) { ?>
				<h2 id="sponsor-title" class="visually-hidden">
					<?php echo __( 'Partner e collaborazioni', 'design_laboratori_italia' ); ?>
				</h2>
				<?php } ?>

				<div class="it-grid-list-wrapper it-image-label-grid">
					<div class="grid-row">
						<?php
						foreach ( $dli_sponsor_query->posts as $dli_sponsor_post ) {
							$dli_image_metadata = dli_get_image_metadata( $dli_sponsor_post, 'full', '/assets/img/yourimage.png' );
							$dli_post_id        = $dli_sponsor_post->ID;
							$dli_external_link  = dli_get_field( 'link_esterno', $dli_post_id );
							?>
							<div class="col-6 col-lg-2">
								<div class="it-grid-item-wrapper">
									<?php if ( ! empty( $dli_external_link ) ) { ?>
										<a href="<?php echo esc_url( $dli_external_link ); ?>" target="_blank" rel="noopener noreferrer">
									<?php } ?>
											<figure class="figure w-100 mb-0 d-flex align-items-center justify-content-center" style="height: 120px;">
												<img
													src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
													title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
													class="figure-img img-fluid w-100 h-100 mb-0"
													style="object-fit: contain;"
													alt="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
												>
											</figure>
									<?php if ( ! empty( $dli_external_link ) ) { ?>
										</a>
									<?php } ?>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
