<?php
/**
 * Homepage hero section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_img_identita = dli_get_option( 'immagine', 'il_laboratorio' );
$dli_colid        = 6;
$dli_showimage    = true;
if ( '' === $dli_img_identita ) {
	// If no image is configured, render full-width text layout.
	$dli_colid     = 12;
	$dli_showimage = false;
}
?>

<section class="section bg-white section-hero">
	<div class="container">
		<div class="row variable-gutters">
			<div class="col-md-<?php echo esc_attr( $dli_colid ); ?>">
				<div class="hero-title">
					<small><?php echo esc_html( get_the_title() ); ?></small>
					<h1><?php echo esc_html( dli_get_option( 'tipologia_scuola' ) ); ?> <?php echo esc_html( dli_get_option_by_lang( 'nome_laboratorio' ) ); ?><br /><span class="text-redbrown"><?php echo esc_html( dli_get_option( 'luogo_laboratorio' ) ); ?></span></h1>
				</div><!-- /hero-title -->
			</div><!-- /col-md-<?php echo esc_attr( $dli_colid ); ?> -->
		</div><!-- /row -->
	</div><!-- /container -->
	<?php if ( $dli_showimage ) { ?>
		<div class="hero-img" style="background: url('<?php echo esc_url( $dli_img_identita ); ?>')  no-repeat center top; background-size: cover; "></div>
	<?php } ?>
</section><!-- /section -->
<?php
