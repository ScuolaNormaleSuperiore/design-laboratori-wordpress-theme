<?php
$show_logo_header = esc_attr( text: dli_get_option( 'logo_header_visible' ) );

if ( $show_logo_header !== 'false' ) {
	$nome_laboratorio = esc_attr( dli_get_option_by_lang( 'nome_laboratorio' ) );
	$file_name        = esc_url( dli_get_option( 'logo_laboratorio' ) );

	if ( $file_name ) {
	?>

		<!-- Logo -->
		<img src="<?php echo esc_url( $file_name ); ?>" 
			alt="<?php echo esc_attr( $nome_laboratorio ); ?>" 
			title="<?php echo esc_attr( $nome_laboratorio ); ?>" 
			height="80"/>

	<?php
	} else { ?>

		<!-- Logo di default -->
		<img src="<?php echo esc_url( dli_get_default_logo() ); ?>" 
			alt="<?php echo esc_attr( $nome_laboratorio ); ?>" 
			title="<?php echo esc_attr( $nome_laboratorio ); ?>" 
			height="80"/>

<?php
	}
}
?>
