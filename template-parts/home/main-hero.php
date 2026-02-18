<?php
/**
 * Homepage main hero section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title      = $args['show_title'] ?? false;

if ( 'true' === $dli_section_enabled ) {
	$dli_hero_size_big = dli_get_option( 'home_main_hero_size', 'homepage' ) !== 'small';
	$dli_hero_class    = $dli_hero_size_big ? '' : 'it-hero-small-size';
	$dli_image_url     = dli_get_option( 'home_main_hero_image', 'homepage' );
	$dli_image_id      = attachment_url_to_postid( $dli_image_url );
	$dli_image_alt     = get_post_meta( $dli_image_id, '_wp_attachment_image_alt', true );
	$dli_image_title   = get_the_title( $dli_image_id );
	$dli_hero_title    = dli_get_configuration_field_by_lang( 'home_main_hero_title', 'homepage' );
	$dli_hero_text     = dli_get_configuration_field_by_lang( 'home_main_hero_text', 'homepage' );
	$dli_hero_url      = dli_get_option( 'home_main_hero_url', 'homepage' );
	$dli_button_label  = dli_get_configuration_field_by_lang( 'home_main_hero_button_label', 'homepage' );

	?>
	<section class="it-hero-wrapper it-dark it-overlay <?php echo esc_attr( $dli_hero_class ); ?>">
	<?php
	if ( $dli_image_url && '' !== $dli_image_url ) {
		?>
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo esc_url( $dli_image_url ); ?>" title="<?php echo esc_attr( $dli_image_title ); ?>" alt="<?php echo esc_attr( $dli_image_alt ); ?>">
				</div>
			</div>
		</div>
		<?php
	}
	?>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<h2><?php echo esc_html( $dli_hero_title ); ?></h2>
						<p class="d-none d-lg-block"><?php echo wp_kses_post( $dli_hero_text ); ?></p>
						<?php
						if ( $dli_hero_url && '' !== $dli_hero_url ) {
							?>
						<div class="it-btn-container">
							<a class="btn btn-sm btn-secondary" href="<?php echo esc_url( $dli_hero_url ); ?>"><?php echo esc_html( $dli_button_label ); ?></a>
						</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
}
?>
