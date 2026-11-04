<?php
/**
 * Homepage banners section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args            = isset( $args ) && is_array( $args ) ? $args : array();
$dli_section_enabled = $dli_args['enabled'] ?? false;
$dli_show_title      = ( 'true' === ( $dli_args['show_title'] ?? '' ) );
$dli_instance        = isset( $dli_args['instance'] ) ? absint( $dli_args['instance'] ) : 1;
$dli_block_id        = 'blocco-banner-' . $dli_instance;
// The title is the one of the row in the language of the page, and is printed only when the row asks to show it: a
// title missing in this language, or not to be shown, leaves the section without a title and without a name, as the
// banners have always been.
$dli_title      = dli_get_hp_row_title( $dli_args, dli_current_language() === DLI_IT_SLUG );
$dli_show_title = $dli_show_title && '' !== $dli_title;

if ( 'true' === $dli_section_enabled ) {
	// The banners of the row: by priority or chosen by hand (see `dli_get_home_banners()`).
	$dli_banners = dli_get_home_banners( $dli_args );

	if ( ! empty( $dli_banners ) ) {
		?>
		<section id="<?php echo esc_attr( $dli_block_id ); ?>"<?php echo ( $dli_show_title ) ? ' aria-labelledby="' . esc_attr( $dli_block_id ) . '-title"' : ''; ?>>
			<?php if ( $dli_show_title ) : ?>
				<div class="container">
					<h2 id="<?php echo esc_attr( $dli_block_id ); ?>-title" class="h3 pb-2"><?php echo esc_html( $dli_title ); ?></h2>
				</div>
			<?php endif; ?>
		<div class="container-banner-home">
			<?php
			$dli_banner_index = 0;
			foreach ( $dli_banners as $dli_banner_post ) {
				$dli_image_metadata = dli_get_image_metadata( $dli_banner_post, 'full', '/assets/img/yourimage.png' );
				$dli_post_id        = $dli_banner_post->ID;
				$dli_banner_meta    = get_post_meta( $dli_post_id );
				$dli_section        = isset( $dli_banner_meta['sezione'][0] ) ? maybe_unserialize( $dli_banner_meta['sezione'][0] ) : '';
				$dli_button_label   = isset( $dli_banner_meta['label_pulsante'][0] ) ? maybe_unserialize( $dli_banner_meta['label_pulsante'][0] ) : '';
				$dli_button_link    = isset( $dli_banner_meta['link_pulsante'][0] ) ? maybe_unserialize( $dli_banner_meta['link_pulsante'][0] ) : '';
				$dli_is_external    = ! empty( $dli_banner_meta['apri_in_nuova_finestra'][0] );
				$dli_heading_id     = 'hero-school-title-' . $dli_instance . '-' . absint( $dli_post_id );
				$dli_banner_class   = 'it-hero-wrapper it-hero-small-size it-primary it-overlay' . ( $dli_banner_index > 0 ? ' mt-5' : '' );
				++$dli_banner_index;
				?>
			<section class="<?php echo esc_attr( $dli_banner_class ); ?>" aria-labelledby="<?php echo esc_attr( $dli_heading_id ); ?>">
				<div class="img-responsive-wrapper">
					<div class="img-responsive">
						<div class="img-wrapper">
							<img
								src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
								title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
								alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
								loading="lazy"
								decoding="async"
							>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-12">
							<div class="it-hero-text-wrapper mw-100 w-100 pt-0 pb-5 px-3 pt-lg-5 pb-lg-5 px-lg-5">
								<span class="it-category"><?php echo esc_html( $dli_section ); ?></span>
								<h2 id="<?php echo esc_attr( $dli_heading_id ); ?>"><?php echo esc_html( $dli_banner_post->post_title ); ?></h2>
								<p class="d-none d-lg-block"><?php echo wp_kses_post( $dli_banner_post->post_content ); ?></p>
								<?php if ( ! empty( $dli_button_link ) ) { ?>
									<div class="it-btn-container">
										<a
											class="btn btn-sm btn-secondary"
											href="<?php echo esc_url( $dli_button_link ); ?>"
											aria-label="<?php echo esc_attr( sprintf( /* translators: %s: banner title */ __( 'Scopri di più su: %s', 'design_laboratori_italia' ), $dli_banner_post->post_title ) ); ?>"
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
		</section>
		<?php

	}
}
