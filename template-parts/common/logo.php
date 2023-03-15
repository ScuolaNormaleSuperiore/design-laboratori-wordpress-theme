<?php
if ( dli_get_option( "logo_laboratorio" ) ) {
?>

	<svg width="82" height="82" class="icon" aria-hidden="true">
		<image xlink:href="<?php echo dli_get_option( 'logo_laboratorio' ); ?>" width="82" height="82"/>
	</svg>

<?php } else { ?>

	<svg width="82" height="82" class="icon" aria-hidden="true">
		<image xlink:href="<?php echo dli_get_default_logo(); ?>" width="82" height="82"/>
	</svg>

<?php } ?>