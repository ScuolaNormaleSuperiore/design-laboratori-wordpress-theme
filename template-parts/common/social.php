<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_show_socials = dli_get_option( 'show_socials', 'socials' );
if ( 'true' === $dli_show_socials ) {
	$dli_sprite_base = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#';
	$dli_socials     = array(
		array(
			'option' => 'facebook',
			'label'  => 'Facebook',
			'icon'   => 'it-facebook',
		),
		array(
			'option' => 'youtube',
			'label'  => 'Youtube',
			'icon'   => 'it-youtube',
		),
		array(
			'option' => 'instagram',
			'label'  => 'Instagram',
			'icon'   => 'it-instagram',
		),
		array(
			'option' => 'twitter',
			'label'  => 'Twitter',
			'icon'   => 'it-twitter',
		),
		array(
			'option' => 'linkedin',
			'label'  => 'Linkedin',
			'icon'   => 'it-linkedin',
		),
		array(
			'option' => 'github',
			'label'  => 'GitHub',
			'icon'   => 'it-github',
		),
		array(
			'option' => 'pinterest',
			'label'  => 'Pinterest',
			'icon'   => 'it-pinterest',
		),
		array(
			'option' => 'mastodon',
			'label'  => 'Mastodon',
			'icon'   => 'it-mastodon',
		),
		array(
			'option' => 'iris',
			'label'  => 'Iris',
			'icon'   => 'it-pa',
		),
		array(
			'option' => 'alumni',
			'label'  => 'Alumni',
			'icon'   => 'it-open-source',
		),
	);
	?>
	<span><?php echo esc_html__( 'Seguici su', 'design_laboratori_italia' ); ?></span>
	<ul>
		<?php
		foreach ( $dli_socials as $dli_social ) {
			$dli_social_url = dli_get_option( $dli_social['option'], 'socials' );

			if ( empty( $dli_social_url ) ) {
				continue;
			}
			?>
			<li>
				<a href="<?php echo esc_url( $dli_social_url ); ?>" aria-label="<?php echo esc_attr( $dli_social['label'] ); ?>" target="_blank" rel="noopener noreferrer">
					<svg class="icon" role="img" aria-label="<?php echo esc_attr( $dli_social['label'] ); ?>">
						<title><?php echo esc_html( $dli_social['label'] ); ?></title>
						<use href="<?php echo esc_url( $dli_sprite_base . $dli_social['icon'] ); ?>"></use>
					</svg>
				</a>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}
?>
