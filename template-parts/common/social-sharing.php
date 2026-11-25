<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_og_data          = DLI_ContentsManager::get_og_data();
$dli_page_url         = ! empty( $dli_og_data->url ) ? $dli_og_data->url : get_permalink();
$dli_enc_page_url     = rawurlencode( $dli_page_url );
$dli_shared_title     = __( 'Condivido con piacere questo post', 'design_laboratori_italia' ) . ' "' . $dli_og_data->shared_title . '"';
$dli_enc_shared_title = rawurlencode( $dli_shared_title );
$dli_sprite_base      = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#';
$dli_share_items      = array(
	array(
		'label' => __( 'Condividi su Facebook', 'design_laboratori_italia' ),
		'name'  => 'Facebook',
		'url'   => 'https://facebook.com/sharer/sharer.php?u=' . $dli_enc_page_url,
		'icon'  => 'it-facebook',
	),
	array(
		'label' => __( 'Condividi su Twitter', 'design_laboratori_italia' ),
		'name'  => 'Twitter',
		'url'   => 'https://twitter.com/share?url=' . $dli_enc_page_url . '&text=' . $dli_enc_shared_title,
		'icon'  => 'it-twitter',
	),
	array(
		'label' => __( 'Condividi su Linkedin', 'design_laboratori_italia' ),
		'name'  => 'Linkedin',
		'url'   => 'https://www.linkedin.com/sharing/share-offsite/?mini=true&url=' . $dli_enc_page_url,
		'icon'  => 'it-linkedin',
	),
	array(
		'label' => __( 'Condividi su WhatsApp', 'design_laboratori_italia' ),
		'name'  => 'Whatsapp',
		'url'   => 'https://api.whatsapp.com/send?text=' . $dli_enc_shared_title . ' ' . $dli_enc_page_url,
		'icon'  => 'it-whatsapp',
	),
);
?>

<div class="dropdown d-inline">
	<button class="btn btn-dropdown dropdown-toggle" type="button" id="shareActions" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<svg class="icon" aria-hidden="true" focusable="false" aria-label="Share">
			<title><?php echo esc_html__( 'Condividi', 'design_laboratori_italia' ); ?></title>
			<use xlink:href="<?php echo esc_url( $dli_sprite_base . 'it-share' ); ?>"></use>
		</svg>
		<small>
			<?php echo esc_html__( 'Condividi', 'design_laboratori_italia' ); ?>
		</small>
	</button>
	<div class="dropdown-menu shadow-lg" aria-labelledby="shareActions">
		<div class="link-list-wrapper">
			<ul class="link-list">
				<?php foreach ( $dli_share_items as $dli_share_item ) : ?>
					<li>
						<a class="list-item"
							href="<?php echo esc_url( $dli_share_item['url'] ); ?>"
							target="_blank" rel="noopener noreferrer"
							aria-label="<?php echo esc_attr( $dli_share_item['label'] ); ?>">
							<svg class="icon" aria-hidden="true" focusable="false" aria-label="<?php echo esc_attr( $dli_share_item['label'] ); ?>">
								<title><?php echo esc_html( $dli_share_item['label'] ); ?></title>
								<use xlink:href="<?php echo esc_url( $dli_sprite_base . $dli_share_item['icon'] ); ?>"></use>
							</svg>
							<span class="display_block"><?php echo esc_html( $dli_share_item['name'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
