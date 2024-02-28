<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
$email           = dli_get_option( 'email_laboratorio' );
$telefono        = dli_get_option( 'telefono_laboratorio' );
$pec             = dli_get_option( 'pec_laboratorio' );
$website         = get_site_url();
$mostraerrore    = false;
$mostrainviato   = false;
$captcha_enabled = false;
$form_valid      = true;
$sent            = false;
$testorisultato  = '';
$nonce_error     = false;
$forminviato     = 'no';

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( plugin_basename( 'really-simple-captcha/really-simple-captcha.php' ) ) ) {
	if ( class_exists( 'ReallySimpleCaptcha' ) ) {
		$captcha_enabled          = true;
		$captcha_obj              = new ReallySimpleCaptcha();
		$captcha_obj->chars       = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$captcha_obj->char_length = '4';

		// Width/Height dimensions of CAPTCHA image.
		$captcha_obj->img_size = array( '72', '24' );
		// Font color of CAPTCHA characters, in RGB (0 – 255).
		$captcha_obj->fg = array( '0', '0', '0' );
		// Background color of CAPTCHA image, in RGB (0 – 255).
		$captcha_obj->bg = array( '255', '255', '255' );
		// Font Size of CAPTCHA characters.
		$captcha_obj->font_size = '16';
		// Width between CAPTCHA characters.
		$captcha_obj->font_char_width = '15';
		// CAPTCHA image type. Can be 'png', 'jpeg', or 'gif'.
		$captcha_obj->img_type = 'png';

		$captcha_obj_word         = $captcha_obj->generate_random_word();
		$captcha_obj_prefix       = mt_rand();
		$captcha_obj_image_name   = $captcha_obj->generate_image( $captcha_obj_prefix, $captcha_obj_word );
		$captcha_obj_image_url    = get_bloginfo( 'wpurl' ) . '/wp-content/plugins/really-simple-captcha/tmp/';
		$captcha_obj_image_src    = $captcha_obj_image_url . $captcha_obj_image_name;
		$captcha_obj_image_width  = $captcha_obj->img_size[0];
		$captcha_obj_image_height = $captcha_obj->img_size[1];
	}
}

if ( isset( $_POST['nomecognome'] ) ) {
	$nomecognome = sanitize_text_field( $_POST['nomecognome'] );
} else {
	$nomecognome = '';
}
if ( isset( $_POST['indirizzoemail'] ) ) {
	$indirizzoemail = sanitize_email( $_POST['indirizzoemail'] );
} else {
	$indirizzoemail = '';
}
if ( isset( $_POST['numerotelefono'] ) ) {
	$numerotelefono = sanitize_text_field( $_POST['numerotelefono'] );
} else {
	$numerotelefono = '';
}
if ( isset( $_POST['ricevuta'] ) ) {
	$ricevuta = sanitize_text_field( $_POST['ricevuta'] );
} else {
	$ricevuta = '';
}
if ( isset( $_POST['forminviato'] ) ) {
	$forminviato = sanitize_text_field( $_POST['forminviato'] );
} else {
	$forminviato = 'no';
}

if ( isset( $_POST['testomessaggio'] ) ) {
	$testomessaggio = sanitize_text_field( $_POST['testomessaggio'] );
} else {
	$testomessaggio = '';
}

if ( isset( $_POST['captcha-field'] ) ) {
	$captcha_field = sanitize_text_field( $_POST['captcha-field'] );
} else {
	$captcha_field = '';
}

if ( isset( $_POST['captcha-prefix'] ) ) {
	$captcha_prefix = sanitize_text_field( $_POST['captcha-prefix'] );
} else {
	$captcha_prefix = '';
}

// Procedura di sottomissione del messaggio.
if ( 'yes' === $forminviato ) {

	// Verifica del nonce.
	if ( isset( $_POST['contatti_nonce_field'] ) &&
		wp_verify_nonce( $_POST['contatti_nonce_field'], 'sf_contatti_nonce' ) ) {

		// Il NONCE è valido.
		$nonce_error = false;
		$email_sito = $email;
		// $email_sito = get_option( 'admin_email' );
		$name       = $nomecognome;
		$to         = $email_sito;
		$subject    = '[FormContatti] Email dal sito: ' . dli_get_option( 'nome_laboratorio' );
		$headers    = 'From: ' . $indirizzoemail . '\r\n' . 'Reply-To: ' . $indirizzoemail . '\r\n';

		// 1 - Controllo del captcha.
		if ( $captcha_enabled ) {
			$captcha_valid = $captcha_obj->check( $captcha_prefix, $captcha_field );
			if ( ! $captcha_valid ) {
				$testorisultato = $testorisultato . '<BR/>' . __( 'Il codice di controllo non è valido.', 'design_laboratori_italia' );
			}
		} else {
			$captcha_valid = true;
		}

		// 2 - Validazione dei campi.
		// 2a - Controllo campi obbligatori
		if ( '' === $nomecognome || '' === $indirizzoemail || '' === $testomessaggio ) {
			$form_valid     = $form_valid && true;
			$testorisultato = $testorisultato . '<BR/>' . __( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' );
		}
		// 2b - Controllo validità email.
		if ( ! ( filter_var( $indirizzoemail, FILTER_VALIDATE_EMAIL ) ) ) {
			$form_valid     = $form_valid && true;
			$testorisultato = $testorisultato . '<BR/>' . __( 'Indicare un indirizzo email valido.', 'design_laboratori_italia' );
		}

		// 3 - Calcolo validità.
		// Il form è valido se i campi sono validi e se è valido il captcha oppure non è attivo.
		$form_valid = $form_valid && ( $captcha_valid || ! $captcha_enabled );

		// 4 - INVIO EMAIL.
		if ( $form_valid ) {
			// 4a - Invio email al laboratorio.
			$sent = wp_mail( $to, $subject, strip_tags( $testomessaggio ), $headers );
			if ( ! $sent ) {
				$testorisultato = $testorisultato . '<BR/>' . __( 'Messaggio non inviato', 'design_laboratori_italia' ) . '&nbsp;.';
			}
			if ( 'on' === $ricevuta ) {
				// 4b - Invio Email di copia.
				$testo_ricevuta = __( 'Ricevuta', 'design_laboratori_italia' );
				$subject        .= '(' . $testo_ricevuta . ')';
				$sent           = $sent && wp_mail( $indirizzoemail, $subject, strip_tags( $testomessaggio ), $headers );
				if ( ! $sent ) {
					$testorisultato = $testorisultato . '<BR/>' . __( 'Copia del messaggio non inviata.', 'design_laboratori_italia' );
				}
			}
		}

		// 5 - Visualizzazione risultato.
		if ( $forminviato && $sent ) {
			$mostrainviato = true;
		}
		if ( ( ! $form_valid ) || ( $forminviato && ! $sent ) ) {
			$mostraerrore  = true;
		}
	} else {
		// Il NONCE non è valido.
		$mostraerrore = true;
		$nonce_error  = true;
		$testorisultato = $testorisultato . '<BR/>' . __( 'Valore di Nonce errato.', 'design_laboratori_italia' );
	}

}

?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- SEZIONE BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- SEZIONE HEADER -->
	<section id="banner-contatti" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-contatti">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0  "><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></h2>
					<p class="font-weight-normal">
						<?php echo __( 'Utilizza i dati di contatto o compila il form sottostante', 'design_laboratori_italia' ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- SEZIONE MESSAGGI -->
	<div id="contatti_messaggi">
		<?php
		if ( $mostrainviato ) {
		?>
			<!-- ALERT OK -->
			<div class="container my-12 p-2">
				<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
				<?php echo __( 'Messaggio inviato correttamente', 'design_laboratori_italia' ) . '&nbsp;.'; ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
						<svg class="icon" role="img" aria-labelledby="Close">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close'; ?>"></use>
						</svg>
					</button>
				</div>
			</div>
		<?php
		}
		if ( $mostraerrore ) {
		?>
			<!-- ALERT KO -->
			<div class="container my-12 p-2">
			<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
				<?php echo __( $testorisultato, 'design_laboratori_italia' ); ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
					<svg class="icon" role="img" aria-labelledby="Close">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close'; ?>"></use>
					</svg>
				</button>
			</div>
		</div>
		<?php
		}
		?>
	</div>

	<!-- SEZIONE FORM -->
	<div id="contatti_form">
		<FORM action="." id="formcontatti" name="formcontatti" method="POST">
			<?php wp_nonce_field( 'sf_contatti_nonce', 'contatti_nonce_field' ); ?>
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
											<svg class="icon" role="img" aria-labelledby="Telephone">
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
									<a href="<?php echo 'mailto:' . $email; ?>" class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Mail">
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
									<a class="list-item" href="<?php echo $website; ?>">
									<div class="it-rounded-icon">
									<svg class="icon" role="img" aria-labelledby="Link">
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
											<label class="active" for="nomecognome"><?php echo __( 'Nome e cognome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
											<input type="text" class="form-control" name="nomecognome" id="nomecognome" 
												value="<?php echo $nomecognome; ?>"
												placeholder="<?php echo __( 'Inserisci il tuo nome', 'design_laboratori_italia' ); ?>">
										</div>
									</div>
									<div class="row">
										<div class="form-group col">
											<label class="active" for="testomessaggio"><?php echo __( 'Testo del messaggio', 'design_laboratori_italia' ); ?>&nbsp;*</label>
											<input type="text" class="form-control" name="testomessaggio" id="testomessaggio" 
												value="<?php echo $testomessaggio; ?>"
												placeholder="<?php echo __( 'Inserisci il testo del messaggio', 'design_laboratori_italia' ); ?>">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-6">
											<label class="active" for="indirizzoemail"><?php echo __( 'E-mail', 'design_laboratori_italia' ); ?>&nbsp;*</label>
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
									<!-- NOTIFICA -->
									<div class="row">
										<div class="form-group col-md-9">
											<div class="toggles">
												<label for="ricevuta">
													<?php
													echo __( 'Vuoi ricevere notifica al tuo indirizzo email', 'design_laboratori_italia' );
													echo '?';
														?>
													<input type="checkbox" id="ricevuta" name="ricevuta">
													<span class="lever"></span>
												</label>
											</div>
										</div>
									</div>
									<!-- CAPTCHA -->
									<?php
									if ( $captcha_enabled ) {
									?>
									<div class="row" style="margin-top: 20px;">
										<div class="form-group col-md-6" style="text-align: center">
											<img src="<?php echo $captcha_obj_image_src; ?>" alt="captcha"
														width="<?php echo $captcha_obj_image_width; ?>" height="<?php echo $captcha_obj_image_height; ?>" />
										</div>
										<div class="form-group col-md-6">
											<input name="captcha-field" id="captcha-field"  size="<?php echo $captcha_obj_image_width; ?>" type="text" 
													placeholder="<?php echo __( 'Riscrivi qui il codice di conferma', 'design_laboratori_italia' ); ?>"	/>
											<input name="captcha-prefix" id="captcha-prefix"  class="form-control" type="hidden" value="<?php echo $captcha_obj_prefix; ?>" />
										</div>
									</div>
									<?php
									}
									?>
									<!-- SUBMIT -->
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
	</div>

</main>

<?php
get_footer();
