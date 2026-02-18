<?php
/**
 * Homepage banners section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title      = $args['show_title'] ?? false;

if ( 'true' === $dli_section_enabled ) {
	$dli_banners_query = new WP_Query(
		array(
			'post_type'      => array( BANNER_POST_TYPE ),
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'priorita',
			'order'          => 'ASC',
		)
	);

	$dli_num_items = $dli_banners_query->post_count;
	if ( $dli_num_items > 0 ) {
		?>
		<div class="container-banner-home">
			<?php
			foreach ( $dli_banners_query->posts as $dli_banner_post ) {
				$dli_image_metadata = dli_get_image_metadata( $dli_banner_post, 'full', '/assets/img/yourimage.png' );
				$dli_post_id        = $dli_banner_post->ID;
				$dli_section        = dli_get_field( 'sezione', $dli_post_id );
				$dli_button_label   = dli_get_field( 'label_pulsante', $dli_post_id );
				$dli_button_link    = dli_get_field( 'link_pulsante', $dli_post_id );
				$dli_is_external    = (bool) dli_get_field( 'apri_in_nuova_finestra', $dli_post_id );
				$dli_heading_id     = 'hero-school-title-' . absint( $dli_post_id );
				?>
			<section class="it-hero-wrapper it-hero-small-size it-primary it-overlay mt-5" aria-labelledby="<?php echo esc_attr( $dli_heading_id ); ?>">
				<div class="img-responsive-wrapper">
					<div class="img-responsive">
						<div class="img-wrapper">
							<img
								src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
								title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
								alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
							>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-12">
							<div class="it-hero-text-wrapper mw-100 w-100 pt-0 pb-5 px-3 pt-lg-5 pb-lg-5 px-lg-5">
								<span class="it-Categoria"><?php echo esc_html( $dli_section ); ?></span>
								<h2 id="<?php echo esc_attr( $dli_heading_id ); ?>"><?php echo esc_html( $dli_banner_post->post_title ); ?></h2>
								<p class="d-none d-lg-block"><?php echo wp_kses_post( $dli_banner_post->post_content ); ?></p>
								<?php if ( ! empty( $dli_button_link ) ) { ?>
									<div class="it-btn-container">
										<a
											class="btn btn-sm btn-secondary"
											href="<?php echo esc_url( $dli_button_link ); ?>"
											<?php if ( $dli_is_external ) { ?>
												target="_blank" rel="noopener noreferrer"
											<?php } ?>
										>
											<?php echo esc_html( $dli_button_label ); ?>
										</a>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</section>
				<?php
			}
			?>
		</div>
		<?php

	}
}
