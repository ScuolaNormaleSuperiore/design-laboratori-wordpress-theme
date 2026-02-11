<?php
/* Template Name: Contatti
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
$email_sito      = dli_get_option( 'email_laboratorio' );
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
$forminviato     = 'no'; // Submit da questa stessa pagina.

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

include_once( DLI_THEMA_PATH . '/template-parts/common/captcha.php' );


$postdata = $_POST;

$nomecognome    = sanitize_text_field( isset( $postdata['nomecognome'] ) ? $postdata['nomecognome'] : '' );
$indirizzoemail = sanitize_text_field( isset( $postdata['indirizzoemail'] ) ? $postdata['indirizzoemail'] : '' );
$numerotelefono = sanitize_text_field( isset( $postdata['numerotelefono'] ) ? $postdata['numerotelefono'] : '' );
$ricevuta       = sanitize_text_field( isset( $postdata['ricevuta'] ) ? $postdata['ricevuta'] : '' );
$forminviato    = sanitize_text_field( isset( $postdata['forminviato'] ) ? $postdata['forminviato'] : 'no' );
$testomessaggio = sanitize_text_field( isset( $postdata['testomessaggio'] ) ? $postdata['testomessaggio'] : '' );
$captcha_field  = sanitize_text_field( isset( $postdata['captcha-field'] ) ? $postdata['captcha-field'] : '' );
$captcha_prefix = sanitize_text_field( isset( $postdata['captcha-prefix'] ) ? $postdata['captcha-prefix'] : '' );


// Procedura di sottomissione del messaggio.
if ( 'yes' === $forminviato ) {

	// Verifica del nonce.
	if ( isset( $postdata['contatti_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( $postdata['contatti_nonce_field'] ), 'sf_contatti_nonce' ) ) {

		// Il NONCE è valido.
		$nonce_error = false;
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
			$form_valid     = false;
			$testorisultato = $testorisultato . '<BR/>' . __( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' );
		}
		// 2b - Controllo validità email.
		if ( ! ( filter_var( $indirizzoemail, FILTER_VALIDATE_EMAIL ) ) ) {
			$form_valid     = false;
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
					<h2 class="p-0  "><?php echo esc_html( __( 'Contatti', 'design_laboratori_italia' ) ); ?></h2>
					<p class="font-weight-normal">
						<?php echo esc_html( __( 'Utilizza i dati di contatto o compila il form sottostante', 'design_laboratori_italia' ) ); ?>
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
				<?php echo esc_html( __( 'Messaggio inviato correttamente', 'design_laboratori_italia' ) ) . '&nbsp;.'; ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
						<svg class="icon" role="img" aria-labelledby="Close">
							<title>Chiudi avviso</title>
							<use href="<?php echo esc_attr( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
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
				<?php echo esc_html( __( $testorisultato, 'design_laboratori_italia' ) ); ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
					<svg class="icon" role="img" aria-labelledby="Close">
						<title>Chiudi avviso</title>
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
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
							<h3 class="h6 text-uppercase border-bottom ps-3"><?php echo esc_html( __( 'Contatti', 'design_laboratori_italia' ) ); ?></h3>
							<ul class="it-list">
								<li>
									<div class="list-item">
										<div class="it-rounded-icon">
											<svg class="icon" role="img" aria-labelledby="Telephone">
												<title>Telephone</title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone">
											<span class="text">
												<a class="list-item" href="<?php echo esc_url( 'tel:' . $telefono ); ?>" title='Telefono'>
													<?php echo esc_html( $telefono ); ?>
												</a>
											</span>
										</div>
									</div>
								</li>
								<li>
									<a href="<?php echo esc_attr( 'mailto:' . $email_sito ); ?>" class="list-item">
									<div class="it-rounded-icon">
										<svg class="icon" role="img" aria-labelledby="Mail">
											<title>Mail</title>
											<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
										</svg>
									</div>
									<div class="it-right-zone">
										<span class="text">
											<?php echo esc_html( $email_sito ); ?>
										</span>
									</div>
									</a>
								</li>
								<li>
									<a class="list-item" href="<?php echo esc_url( $website ); ?>">
									<div class="it-rounded-icon">
									<svg class="icon" role="img" aria-labelledby="Link">
										<title>Website Link</title>
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link' ); ?>"></use>
									</svg>
									</div>
									<div class="it-right-zone"><span class="text"><?php echo esc_html( $website ); ?></span>
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
											<label class="active" for="nomecognome"><?php echo esc_html( __( 'Nome e cognome', 'design_laboratori_italia' ) ); ?>&nbsp;*</label>
											<input type="text" class="form-control" name="nomecognome" id="nomecognome"
												value="<?php echo esc_attr( $nomecognome ); ?>"
												placeholder="<?php echo esc_attr( __( 'Inserisci il tuo nome', 'design_laboratori_italia' ) ); ?>">
										</div>
									</div>
									<div class="row">
										<div class="form-group col">
											<label class="active" for="testomessaggio"><?php echo esc_html( __( 'Testo del messaggio', 'design_laboratori_italia' ) ); ?>&nbsp;*</label>
											<input type="text" class="form-control" name="testomessaggio" id="testomessaggio"
												value="<?php echo esc_attr( $testomessaggio ); ?>"
												placeholder="<?php echo esc_attr( __( 'Inserisci il testo del messaggio', 'design_laboratori_italia' ) ); ?>">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-6">
											<label class="active" for="indirizzoemail"><?php echo esc_html( __( 'E-mail', 'design_laboratori_italia' ) ); ?>&nbsp;*</label>
											<input type="email" class="form-control" id="indirizzoemail" name="indirizzoemail"
												value="<?php echo esc_attr( $indirizzoemail ); ?>"
												placeholder="<?php echo esc_attr( __( 'Inserisci il tuo indirizzo email', 'design_laboratori_italia' ) ); ?>">
										</div>
										<div class="form-group col-md-6">
											<label for="numerotelefono" class="active"><?php echo esc_html( __( 'Telefono', 'design_laboratori_italia' ) ); ?></label>
											<input type="tel" class="form-control" id="numerotelefono" name="numerotelefono"
												value="<?php echo esc_attr( $numerotelefono ); ?>"
												placeholder="<?php echo esc_html( __( 'Inserisci il tuo numero di telefono', 'design_laboratori_italia' ) ); ?>">
										</div>
									</div>
									<!-- NOTIFICA -->
									<div class="row">
										<div class="form-group col-md-9">
											<div class="toggles">
												<label for="ricevuta">
													<?php
													echo esc_html( __( 'Vuoi ricevere notifica al tuo indirizzo email', 'design_laboratori_italia' ) ) . ' ?';
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
											<img src="<?php echo esc_url( $captcha_obj_image_src ); ?>" alt="captcha"
														width="<?php echo esc_attr( $captcha_obj_image_width ); ?>" height="<?php echo esc_attr( $captcha_obj_image_height ); ?>" />
										</div>
										<div class="form-group col-md-6">
											<input name="captcha-field" id="captcha-field"  size="<?php echo esc_attr( $captcha_obj_image_width ); ?>" type="text" 
													placeholder="<?php echo esc_html( __( 'Riscrivi qui il codice di conferma', 'design_laboratori_italia' ) ); ?>"	/>
											<input name="captcha-prefix" id="captcha-prefix"  class="form-control" type="hidden" value="<?php echo esc_attr( $captcha_obj_prefix ); ?>" />
										</div>
									</div>
									<?php
									}
									?>
									<!-- SUBMIT -->
									<div class="row mt-4">
										<div class="form-group col text-center">
											<input type="hidden" name="forminviato" id="forminviato" value="yes" />
											<button type="button" class="btn btn-outline-primary"><?php echo esc_html( __( 'Annulla', 'design_laboratori_italia' ) ); ?></button>
											<button type="submit" class="btn btn-primary"><?php echo esc_html( __( 'Conferma', 'design_laboratori_italia' ) ); ?></button>
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
