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
			'post_type'              => array( BANNER_POST_TYPE ),
			'post_status'            => 'publish',
			'posts_per_page'         => 3,
			'orderby'                => 'meta_value_num',
			'meta_key'               => 'priorita',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		)
	);

	$dli_num_items = $dli_banners_query->post_count;
	if ( $dli_num_items > 0 ) {
		$dli_banner_ids = wp_list_pluck( $dli_banners_query->posts, 'ID' );
		$dli_thumb_ids  = array_filter( array_map( 'get_post_thumbnail_id', $dli_banner_ids ) );
		if ( ! empty( $dli_thumb_ids ) ) {
			$dli_thumb_ids = array_map( 'absint', array_unique( $dli_thumb_ids ) );
			update_meta_cache( 'post', $dli_thumb_ids );
			get_posts(
				array(
					'post_type'              => 'attachment',
					'post_status'            => 'inherit',
					'post__in'               => $dli_thumb_ids,
					'posts_per_page'         => count( $dli_thumb_ids ),
					'orderby'                => 'post__in',
					'no_found_rows'          => true,
					'update_post_meta_cache' => true,
					'update_post_term_cache' => false,
				)
			);
		}

		?>
		<div class="container-banner-home">
			<?php
			foreach ( $dli_banners_query->posts as $dli_banner_post ) {
				$dli_image_metadata = dli_get_image_metadata( $dli_banner_post, 'full', '/assets/img/yourimage.png' );
				$dli_post_id        = $dli_banner_post->ID;
				$dli_banner_meta    = get_post_meta( $dli_post_id );
				$dli_section        = isset( $dli_banner_meta['sezione'][0] ) ? maybe_unserialize( $dli_banner_meta['sezione'][0] ) : '';
				$dli_button_label   = isset( $dli_banner_meta['label_pulsante'][0] ) ? maybe_unserialize( $dli_banner_meta['label_pulsante'][0] ) : '';
				$dli_button_link    = isset( $dli_banner_meta['link_pulsante'][0] ) ? maybe_unserialize( $dli_banner_meta['link_pulsante'][0] ) : '';
				$dli_is_external    = ! empty( $dli_banner_meta['apri_in_nuova_finestra'][0] );
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
