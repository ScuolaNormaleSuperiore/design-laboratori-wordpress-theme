
<?php

$main_hero_enabled = dli_get_option( 'home_main_hero_enabled', 'homepage' );
if ( $main_hero_enabled === 'true' ) {
	$image_url    = dli_get_option( 'home_main_hero_image', 'homepage' );
	$image_id     = attachment_url_to_postid( $image_url );
	$image_alt    = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
	$image_title  = get_the_title( $image_id );
	$hero_text    = dli_get_option( 'home_main_hero_text', 'homepage' );
	$hero_title   = dli_get_option( 'home_main_hero_title', 'homepage' );
	$hero_url     = dli_get_option( 'home_main_hero_url', 'homepage' );
	$button_label = dli_get_option( 'home_main_hero_button_label', 'homepage' );
?>
	<section class="it-hero-wrapper it-dark it-overlay">
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo $image_url; ?>" title="<?php echo $image_title; ?>" alt="<?php echo $image_alt; ?>">
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<h2><?php echo esc_attr( $hero_title) ; ?></h2>
						<p class="d-none d-lg-block"><?php echo esc_html( $hero_text) ; ?></p>
						<div class="it-btn-container">
							<a class="btn btn-sm btn-secondary" href="<?php echo esc_url( $hero_url) ; ?>"><?php echo esc_attr( $button_label) ; ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php 
}
?>