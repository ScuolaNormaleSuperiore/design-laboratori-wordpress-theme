<?php
$messages = dli_get_option( 'messages', 'home_messages' );
if ( is_array( $messages ) && ! empty( $messages ) ) {
	$allowed_alert_colors = array( 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark' );

	foreach ( $messages as $msg ) {
		if ( ! isset( $msg['testo_message'] ) ) {
			continue;
		}

		$message_text = esc_html( (string) $msg['testo_message'] );
		if ( '' === $message_text ) {
			continue;
		}

		$raw_color     = isset( $msg['colore_message'] ) ? sanitize_key( $msg['colore_message'] ) : '';
		$message_color = in_array( $raw_color, $allowed_alert_colors, true ) ? $raw_color : 'info';
		$message_link  = isset( $msg['link_message'] ) ? esc_url( $msg['link_message'] ) : '';
?>
	<div class="container my-12 p-2">
		<div class="alert alert-<?php echo esc_attr( $message_color ); ?> alert-dismissible fade show mb-0" role="alert">

			<?php
			if ( ! empty( $message_link ) ) {
			?>
			<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $message_link ); ?>">
			<?php
			}
			?>
				<?php echo $message_text; ?>
			<?php
			if ( ! empty( $message_link ) ) {
			?>
			</a>
			<?php
			}
			?>

			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo esc_attr__( 'Chiudi avviso', 'design_laboratori_italia' ); ?>">
				<svg class="icon" role="img" aria-labelledby="Close" aria-label="<?php echo esc_attr__( 'Chiudi avviso', 'design_laboratori_italia' ); ?>">
					<title><?php echo esc_html__( 'Chiudi avviso', 'design_laboratori_italia' ); ?></title>
					<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
				</svg>
			</button>
		</div>
	</div>
<?php
	}
}
?>
