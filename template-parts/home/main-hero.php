
<?php
$section_enabled = $args['enabled'] ?? false;
$show_title      = $args['show_title'] ?? false;

if ( 'true' === $section_enabled ) {
	$hero_size_big = dli_get_option( 'home_main_hero_size', 'homepage' ) !== 'small' ? true : false;
	$hero_class    = $hero_size_big ? '' : 'it-hero-small-size';
	$image_url     = dli_get_option( 'home_main_hero_image', 'homepage' );
	$image_id      = attachment_url_to_postid( $image_url );
	$image_alt     = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
	$image_title   = get_the_title( $image_id );
	$hero_title    = dli_get_configuration_field_by_lang( 'home_main_hero_title', 'homepage' );
	$hero_text     = dli_get_configuration_field_by_lang( 'home_main_hero_text', 'homepage' );
	$hero_url      = dli_get_option( 'home_main_hero_url', 'homepage' );
	$button_label  = dli_get_configuration_field_by_lang( 'home_main_hero_button_label', 'homepage' );

?>
	<section class="it-hero-wrapper it-dark it-overlay <?php echo $hero_class; ?>">
	<?php
	if ( $image_url && '' !== $image_url ) {
		?>
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo $image_url; ?>" title="<?php echo $image_title; ?>" alt="<?php echo $image_alt; ?>">
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
						<h2><?php echo esc_attr( $hero_title) ; ?></h2>
						<p class="d-none d-lg-block"><?php echo esc_html( $hero_text) ; ?></p>
						<?php
					if ( $hero_url && '' !== $hero_url ) {
						?>
						<div class="it-btn-container">
							<a class="btn btn-sm btn-secondary" href="<?php echo esc_url( $hero_url) ; ?>"><?php echo esc_attr( $button_label) ; ?></a>
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