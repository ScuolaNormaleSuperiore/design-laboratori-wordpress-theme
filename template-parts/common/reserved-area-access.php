<?php
/**
 * Access box of the page "Area riservata": the login button for a visitor, the state of the request and the logout
 * for a logged-in user. Printed by `dli_render_reserved_area_access()`.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args = isset( $args ) && is_array( $args ) ? $args : array();
$dli_args = wp_parse_args(
	$dli_args,
	array(
		'logged_in'  => false,
		'pending'    => false,
		'name'       => '',
		'login_url'  => '',
		'logout_url' => '',
	)
);
?>
<section id="accesso-area-riservata" class="mb-4" aria-labelledby="accesso-area-riservata-title">
	<h2 id="accesso-area-riservata-title" class="h4"><?php echo esc_html__( 'Accesso all\'area riservata', 'design_laboratori_italia' ); ?></h2>
	<?php if ( ! $dli_args['logged_in'] ) : ?>
		<p><?php echo esc_html__( 'Accedi con le credenziali del tuo istituto per vedere i contenuti riservati ai tuoi gruppi.', 'design_laboratori_italia' ); ?></p>
		<a class="btn btn-primary" href="<?php echo esc_url( $dli_args['login_url'] ); ?>"><?php echo esc_html__( 'Accedi', 'design_laboratori_italia' ); ?></a>
	<?php else : ?>
		<p>
			<?php
			/* translators: %s: name of the logged-in user. */
			echo esc_html( sprintf( __( 'Sei collegato come %s.', 'design_laboratori_italia' ), $dli_args['name'] ) );
			?>
		</p>
		<?php if ( $dli_args['pending'] ) : ?>
			<div class="alert alert-info">
				<?php echo esc_html__( 'La tua richiesta di accesso è in attesa di approvazione: vedrai i contenuti riservati quando un amministratore ti avrà inserito in un gruppo.', 'design_laboratori_italia' ); ?>
			</div>
		<?php endif; ?>
		<a class="btn btn-outline-primary" href="<?php echo esc_url( $dli_args['logout_url'] ); ?>"><?php echo esc_html__( 'Esci', 'design_laboratori_italia' ); ?></a>
	<?php endif; ?>
</section>
