<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
$email         = dli_get_option( 'email_laboratorio' );
$telefono      = dli_get_option( 'telefono_laboratorio' );
$pec           = dli_get_option( 'pec_laboratorio' );
$website       = get_site_url();
$mostraerrore  = false;
$mostrainviato = false;

if ( isset( $_POST['nomecognome'] ) ) {
	$nomecognome = $_POST['nomecognome'];
} else {
	$nomecognome = '';
}
if ( isset( $_POST['indirizzoemail'] ) ) {
	$indirizzoemail = $_POST['indirizzoemail'];
} else {
	$indirizzoemail = '';
}
if ( isset( $_POST['numerotelefono'] ) ) {
	$numerotelefono = $_POST['numerotelefono'];
} else {
	$numerotelefono = '';
}
if ( isset( $_POST['ricevuta'] ) ) {
	$ricevuta = $_POST['ricevuta'];
} else {
	$ricevuta = '';
}
if ( isset( $_POST['forminviato'] ) ) {
	$forminviato = $_POST['forminviato'];
} else {
	$forminviato = '';
}

if ( isset( $_POST['testomessaggio'] ) ) {
	$testomessaggio = $_POST['testomessaggio'];
} else {
	$testomessaggio = '';
}

if ( 'yes' === $forminviato ) {

	$email_sito = $email;
	$email_sito = get_bloginfo( 'admin_email' );
	$name       = $nomecognome;
	$to         = $email_sito;
	$subject    = '[FormContatti] Email dal sito: ' . dli_get_option( 'nome_laboratorio' );
	$headers    = 'From: ' . $indirizzoemail . '\r\n' . 'Reply-To: ' . $indirizzoemail . '\r\n';

	// @TODO: Validazione.

	// INVIO EMAIL.
	$sent          = wp_mail( $to, $subject, strip_tags( $testomessaggio ), $headers );

	if ( $forminviato && $sent ) {
		$mostrainviato = true;
	}
	if ( $forminviato && ! $sent ) {
		$mostraerrore  = true;
	}

}

?>

<main id="main-container" class="main-container bluelectric">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<section id="banner-contatti" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-contatti">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0  "><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></h2>
					<p class="font-weight-normal"><?php echo __( 'Utilizza i dati di contatto o compila il form sottostante', 'design_laboratori_italia' ); ?></p>
				</div>
			</div>
		</div>
	</section>

		<!-- ALERT OK -->
	<?php
	 if ( $mostrainviato ){
	?>
	<div class="container my-12 p-2">
		<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
		<?php echo __( 'Messaggio inviato correttamente.', 'design_laboratori_italia' ); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
				<svg class="icon">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close'; ?>"></use>
				</svg>
			</button>
		</div>
	</div>
	<?php
	 }
	 if ( $mostraerrore ){
	?>
		<!-- ALERT KO -->
		<div class="container my-12 p-2">
		<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
		<?php echo __( 'Messaggio non inviato.', 'design_laboratori_italia' ); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
				<svg class="icon">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close'; ?>"></use>
				</svg>
			</button>
		</div>
	</div>
	<?php
	 }
	?>

	<FORM action="." id="formcontatti" name="formcontatti" method="POST">
		<div class="container my-4 pt-4">

		<!-- CONTATTI DEL LABORATORIO -->
			<div class="row">
				<div class="col-12 col-lg-3 border-end pe-0 ps-0">
					<div class="it-list-wrapper pt-4">
						<h3 class="h6 text-uppercase border-bottom ps-3"><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></h3>
						<ul class="it-list">
							<li>
								<div class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon">
										<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone'; ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone">
										<span class="text">
											<a class="list-item" href="<?php echo 'tel:' . $telefono; ?>" title='Telefono'>
												<?php echo $telefono; ?>
											</a>
										</span>
									</div>
								</div>
							</li>
							<li>
								<a href="#" class="list-item">
								<div class="it-rounded-icon">
									<svg class="icon">
								<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail'; ?>"></use>
									</svg>
								</div>
								<div class="it-right-zone">
									<span class="text">
										<?php echo $email; ?>
									</span>
								</div>
								</a>
							</li>
							<li>
								<a class="list-item" href="#">
								<div class="it-rounded-icon">
								<svg class="icon">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link'; ?>"></use>
								</svg>
								</div>
								<div class="it-right-zone"><span class="text"><?php echo $website; ?></span>
								</div>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<!-- FORM DEI CONTATTI -->
				<div class="col-lg-9">
					<div class="row ">
						<div class="col-lg-12">  
						<div class="p-5">
							<div class="row">
								<div class="form-group col">
									<label class="active" for="nomecognome"><?php echo __( 'Nome e cognome', 'design_laboratori_italia' ); ?></label>
									<input type="text" class="form-control" name="nomecognome" id="nomecognome" 
										value="<?php echo $nomecognome; ?>"
										placeholder="<?php echo __( 'Inserisci il tuo nome', 'design_laboratori_italia' ); ?>">
								</div>
							</div>
							<div class="row">
								<div class="form-group col">
									<label class="active" for="testomessaggio"><?php echo __( 'Testo', 'design_laboratori_italia' ); ?></label>
									<input type="text" class="form-control" name="testomessaggio" id="testomessaggio" 
										value="<?php echo $testomessaggio; ?>"
										placeholder="<?php echo __( 'Inserisci il testo del messaggio', 'design_laboratori_italia' ); ?>">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-6">
									<label class="active" for="indirizzoemail"><?php echo __( 'E-mail', 'design_laboratori_italia' ); ?></label>
									<input type="email" class="form-control" id="indirizzoemail" name="indirizzoemail" 
										value="<?php echo $indirizzoemail; ?>"
										placeholder="<?php echo __( 'Inserisci il tuo indirizzo email', 'design_laboratori_italia' ); ?>">
								</div>
								<div class="form-group col-md-6">
									<label for="numerotelefono" class="active"><?php echo __( 'Telefono', 'design_laboratori_italia' ); ?></label>
									<input type="tel" class="form-control" id="numerotelefono" name="numerotelefono"
										value="<?php echo $numerotelefono; ?>"
										placeholder="<?php echo __( 'Inserisci il tuo numero di telefono', 'design_laboratori_italia' ); ?>">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-9">
									<div class="toggles">
										<label for="toggleEsempio1a">
											<?php echo __( 'Vuoi ricevere notifica al tuo indirizzo email', 'design_laboratori_italia' ); ?>
											<input type="checkbox" id="toggleEsempio1a">
											<span class="lever"></span>
										</label>
									</div>
								</div>
							</div>
								<div class="row mt-4">
									<div class="form-group col text-center">
										<input type="hidden" name="forminviato" id="forminviato" value="yes" />
										<button type="button" class="btn btn-outline-primary"><?php echo __( 'Annulla', 'design_laboratori_italia' ); ?></button>
										<button type="submit" class="btn btn-primary"><?php echo __( 'Conferma', 'design_laboratori_italia' ); ?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				

			</div>
		</div>

	</FORM>	
</main>

<?php
get_footer();
