<?php
if ( dli_get_option( "logo_laboratorio" ) ) {
?>

	<image src="<?php echo dli_get_option( 'logo_laboratorio' ); ?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" 
	title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" xlink:href="<?php echo dli_get_option( 'logo_laboratorio' ); ?>" height="80"/>

<?php } else { ?>

	<image src="<?php echo dli_get_default_logo(); ?>" xlink:href="<?php echo dli_get_default_logo(); ?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" 
title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" height="80"/>

<?php } ?>