<?php
$show_logo_header = esc_attr( text: dli_get_option( 'logo_header_visible' ) );

if ( $show_logo_header !== 'false' ) {
	$nome_laboratorio = esc_attr( dli_get_option_by_lang( 'nome_laboratorio' ) );
	$file_name        = esc_url( dli_get_option( 'logo_laboratorio' ) );

	if ( $file_name ) {
		if( pathinfo($file_name, PATHINFO_EXTENSION) !== 'svg') {
	?>

		<!-- Logo non SVG -->
		<img src="<?php echo $file_name; ?>" 
			alt="<?php echo $nome_laboratorio; ?>" 
			title="<?php echo $nome_laboratorio; ?>" 
			xlink:href="<?php echo $file_name; ?>" height="80"/>

		<?php 
		} else {
		?>

		<!-- Logo SVG -->
		<svg xmlns="http://www.w3.org/2000/svg" 
			xmlns:xlink="http://www.w3.org/1999/xlink" width="82" height="82" 
			alt="<?php echo $nome_laboratorio; ?>" 
			title="<?php echo $nome_laboratorio; ?>">
			<?php echo file_get_contents( $file_name ); ?>
		</svg>

		<?php
		}
	} else { ?>

		<!-- Logo di default -->
		<img src="<?php echo dli_get_default_logo(); ?>" 
			xlink:href="<?php echo dli_get_default_logo(); ?>" 
			alt="<?php echo $nome_laboratorio; ?>" 
			title="<?php echo $nome_laboratorio; ?>" 
			height="80"/>

<?php
	}
}
?>