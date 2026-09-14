<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items       = ( isset( $args['items'] ) && is_array( $args['items'] ) ) ? $args['items'] : array();
$dli_section_id  = isset( $args['section_id'] ) ? sanitize_key( $args['section_id'] ) : '';
$dli_num_results = count( $dli_items );
?>

<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
	<?php if ( $dli_num_results ) : ?>
		<div class="it-list-wrapper">
			<ul class="it-list">
				<?php
				foreach ( $dli_items as $dli_item ) :
					$dli_post_title = isset( $dli_item['title'] ) ? sanitize_text_field( (string) $dli_item['title'] ) : '';
					$dli_post_url   = isset( $dli_item['url'] ) ? esc_url_raw( $dli_item['url'] ) : '';

					$dli_metadata = '';
					if ( ! empty( $dli_item['mime_type'] ) ) {
						$dli_mime_parts = explode( '/', $dli_item['mime_type'] );
						$dli_metadata   = strtoupper( end( $dli_mime_parts ) );
					}
					if ( ! empty( $dli_item['filesize'] ) ) {
						$dli_metadata .= ( $dli_metadata ? ', ' : '' ) . size_format( (int) $dli_item['filesize'] );
					}
					?>
					<li>
						<a class="list-item" href="<?php echo esc_url( $dli_post_url ); ?>" target="_blank" rel="noopener noreferrer">
							<div class="it-rounded-icon">
								<svg class="icon" aria-hidden="true" focusable="false">
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
								</svg>
							</div>
							<div class="it-right-zone">
								<span class="text"><?php echo esc_html( $dli_post_title ); ?></span>
								<?php if ( $dli_metadata ) : ?>
									<span class="metadata"><?php echo esc_html( $dli_metadata ); ?></span>
								<?php endif; ?>
							</div>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php else : ?>
		<p>-</p>
	<?php endif; ?>
</section>
