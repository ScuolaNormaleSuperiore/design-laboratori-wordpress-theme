<?php
if ( dli_get_option( "logo_laboratorio" ) ) {
?>

	<img width="82"  class="color-invert" src="<?php echo dli_get_option( "logo_laboratorio" );?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" />

<?php } else { ?>

	<img width="82" class="color-invert" src="<?php echo dli_get_default_logo();?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" />

<?php } ?>