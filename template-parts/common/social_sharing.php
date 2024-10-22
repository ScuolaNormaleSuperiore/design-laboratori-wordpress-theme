<?php
global $post;
// $current_url = get_permalink();
$og_data          = DLI_ContentsManager::get_og_data();
$page_url         = $og_data->url;
$enc_page_url     = urlencode( $page_url );
$shared_title     = __( 'I\'m pleased to share the post', 'kk_writer_theme' ) . ' "' . $og_data->shared_title . '"';
$enc_shared_title = urlencode( $shared_title );
// Prepare complete url to share the page on many platforms.
$fb_share_url = 'https://facebook.com/sharer/sharer.php?u=' . $enc_page_url;
$tw_share_url = 'https://twitter.com/share?url=' . $enc_page_url .'&text=' . $enc_shared_title;
$lk_share_url = 'https://www.linkedin.com/sharing/share-offsite/?mini=true&url=' . $enc_page_url;
$wa_share_url = 'https://api.whatsapp.com/send?text=' . $enc_shared_title . ' ' . $enc_page_url;
?>

<div class="dropdown d-inline">
	<button class="btn btn-dropdown dropdown-toggle" type="button" id="shareActions" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<svg class="icon" aria-hidden="true" focusable="false" aria-label="Share">
			<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-share' ?>"></use>
		</svg>
		<small>
			<?php echo __( 'Condividi', 'design_laboratori_italia' ); ?>
		</small>
	</button>
	<div class="dropdown-menu shadow-lg" aria-labelledby="shareActions">
		<div class="link-list-wrapper">
			<ul class="link-list">
				<li>
					<a class="list-item"
						href="<?php echo esc_url( $fb_share_url ); ?>"
						target="_blank" rel="noopener"
						aria-label="<?php echo __( 'Share on Facebook', 'design_laboratori_italia' ); ?>">
						<svg class="icon" aria-hidden="true" focusable="false" aria-label="<?php echo __( 'Share on Facebook', 'design_laboratori_italia' ); ?>">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'?>"></use>
						</svg>
						<span class="display_block">Facebook</span>
					</a>
				</li>
				<li>
					<a class="list-item"
						href="<?php echo esc_url( $tw_share_url ); ?>"
						target="_blank" rel="noopener noreferrer"
						aria-label="<?php echo __( 'Share on Twitter', 'design_laboratori_italia' ); ?>">
						<svg class="icon" aria-hidden="true" focusable="false" aria-label="<?php echo __( 'Share on Twitter', 'design_laboratori_italia' ); ?>">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'?>"></use>
						</svg>
						<br/>
						<span class="display_block">Twitter</span>
					</a>
				</li>
				<li>
					<a class="list-item"
						href="<?php echo esc_url( $lk_share_url ); ?>"
						target="_blank" rel="noopener noreferrer"
						aria-label="<?php echo __( 'Share on Linkedin', 'design_laboratori_italia' ); ?>">
						<svg class="icon" aria-hidden="true" focusable="false" aria-label="<?php echo __( 'Share on Linkedin', 'design_laboratori_italia' ); ?>">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-linkedin'?>"></use>
						</svg>
						<span class="display_block">Linkedin</span>
					</a>
				</li>
				<li>
					<a class="list-item"
						href="<?php echo esc_url( $wa_share_url ); ?>"
						target="_blank" rel="noopener noreferrer"
						aria-label="<?php echo __( 'Share on WhatsApp', 'design_laboratori_italia' ); ?>">
						<svg class="icon" aria-hidden="true" focusable="false" aria-label="<?php echo __( 'Share on WhatsApp', 'design_laboratori_italia' ); ?>">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-whatsapp'?>"></use>
						</svg>
						<span class="display_block">Whatsapp</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
