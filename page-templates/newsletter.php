<?php
/* Template Name: Newsletter.
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
$email           = dli_get_option( 'email_laboratorio' );
$mostraerrore    = false;
$mostrainviato   = false;
$captcha_enabled = false;
$form_valid      = true;
$sent            = false;
$testorisultato  = '';
$nonce_error     = false;
$forminviato     = 'no'; // Submit da questa stessa pagina.
$fromredirection = 'no'; // Redirect da HP.
$afterconfirm    = 'no'; // Conferma da email (GET).

if ( isset( $_POST['emailutente'] ) ) {
	$emailutente = sanitize_email( $_POST['emailutente'] );
} else {
	$emailutente = '';
}
if ( isset( $_POST['fromredirection'] ) ) {
	$fromredirection = sanitize_text_field( $_POST['fromredirection'] );
} else {
	$fromredirection = 'no';
}
if ( isset( $_POST['nomeutente'] ) ) {
	$nomeutente = sanitize_text_field( $_POST['nomeutente'] );
} else {
	$nomeutente = '';
}
if ( isset( $_POST['cognomeutente'] ) ) {
	$cognomeutente = sanitize_text_field( $_POST['cognomeutente'] );
} else {
	$cognomeutente = '';
}
if ( isset( $_POST['numerotelefono'] ) ) {
	$numerotelefono = sanitize_text_field( $_POST['numerotelefono'] );
} else {
	$numerotelefono = '';
}
if ( isset( $_POST['forminviato'] ) ) {
	$forminviato = sanitize_text_field( $_POST['forminviato'] );
} else {
	$forminviato = 'no';
}
if ( isset( $_GET['afterconfirm'] ) ) {
	$afterconfirm = sanitize_text_field( $_GET['afterconfirm'] );
} else {
	$afterconfirm = 'no';
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

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

include_once( DLI_THEMA_PATH . '/template-parts/common/captcha.php' );

// Procedura di sottomissione del messaggio.
if ( 'yes' === $forminviato ) {

	// Verifica del nonce.
	if ( isset( $_POST['newsletter_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( $_POST['newsletter_nonce_field'] ), 'sf_newsletter_nonce' ) ) {

		// Il NONCE è valido.
		$nonce_error = false;

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
		if ( '' === $nomeutente || '' === $cognomeutente ) {
			$form_valid     = $form_valid && true;
			$testorisultato = $testorisultato . '<BR/>' . __( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' );
		}
		// 2b - Controllo validità email.
		if ( ! ( filter_var( $emailutente, FILTER_VALIDATE_EMAIL ) ) ) {
			$form_valid     = $form_valid && true;
			$testorisultato = $testorisultato . '<BR/>' . __( 'Indicare un indirizzo email valido.', 'design_laboratori_italia' );
		}

		// 3 - Calcolo validità.
		// Il form è valido se i campi sono validi e se è valido il captcha oppure non è attivo.
		$form_valid = $form_valid && ( $captcha_valid || ! $captcha_enabled );

		// 4 - INVIO EMAIL.
		if ( $form_valid ) {
			$url           = 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation';
			$list_ids      = array();
			$api_token     = dli_get_option( 'newsletter_api_token', 'setup' );
			$newsletter_id = dli_get_option( 'newsletter_list_id', 'setup' );
			$template_id   = dli_get_option( 'newsletter_template_id', 'setup' );
			array_push( $list_ids, $newsletter_id );
			$news_page    = get_page_by_path( 'newsletter' );
			$page_id      = $news_page->ID;
			$redirect_url = get_permalink( $page_id ) . '?afterconfirm=1';
			$data = array(
				'attributes' => array(
					'FNAME' => $nomeutente,
					'LNAME' => $cognomeutente,
				),
				'email'          => $emailutente,
				'includeListIds' => $list_ids,
				'templateId'     => $template_id,
				'redirectionUrl' => $redirect_url,
			);
			$json_data = json_encode( $data );

			$header = array(
				'Accept'       => 'application/json',
				'api-key'      => $api_token,
				'Content-Type' => 'application/json',
			);
			$response = wp_remote_post( $url, array( 'body' => $json_data, 'headers' => $header, ) );

			// $response['response']['code']
			// $response['response']['message']

			$testorisultato = $testorisultato . '<BR/>' . __( 'Copia del messaggio non inviata.', 'design_laboratori_italia' );
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
	<section id="banner-newsletter" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-newsletter">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0  "><?php echo __( 'Newsletter', 'design_laboratori_italia' ); ?></h2>
					<p class="font-weight-normal">
						<?php echo __( 'Compila il form seguente per iscriverti alla newsletter, riceverai una e-mail per confermare l\'iscrizione', 'design_laboratori_italia' ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- SEZIONE MESSAGGI -->
	<div id="newsletter_messaggi">
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
	<?php if ( $afterconfirm === 'no' ) {
	?>
		<div id="newsletter_form">
			<FORM action="." id="formnewsletter" name="formnewsletter" method="POST">
				<?php wp_nonce_field( 'sf_newsletter_nonce', 'newsletter_nonce_field' ); ?>
				<div class="container my-4 pt-4">
					<div class="row">
						<!-- FORM ISCRIZIONE NEWSLETTER -->
						<div class="col-lg-9">
							<div class="row ">
								<div class="col-lg-12">  
									<div class="p-5">
										<div class="row">
											<div class="form-group col-md-6">
												<label class="active" for="nomeutente"><?php echo __( 'Nome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
												<input type="text" class="form-control" name="nomeutente" id="nomeutente" 
													value="<?php echo $nomeutente; ?>"
													placeholder="<?php echo __( 'Inserisci il tuo nome', 'design_laboratori_italia' ); ?>">
											</div>
											<div class="form-group col-md-6">
												<label class="active" for="cognomeutente"><?php echo __( 'Cognome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
												<input type="text" class="form-control" name="cognomeutente" id="cognomeutente" 
													value="<?php echo $cognomeutente; ?>"
													placeholder="<?php echo __( 'Inserisci il tuo cognome', 'design_laboratori_italia' ); ?>">
											</div>
										</div>
										<div class="row">
											<div class="form-group col">
												<label class="active" for="emailutente"><?php echo __( 'E-mail', 'design_laboratori_italia' ); ?>&nbsp;*</label>
												<input type="email" class="form-control" id="emailutente" name="emailutente" 
													value="<?php echo $emailutente; ?>"
													placeholder="<?php echo __( 'Inserisci il tuo indirizzo email', 'design_laboratori_italia' ); ?>">
											</div>
										</div>
										<div class="row">
											<div class="form-group col-md-6">
												<label for="numerotelefono" class="active">
													<?php echo __( 'Telefono', 'design_laboratori_italia' ); ?>&nbsp;(<?php echo __( 'facoltativo', 'design_laboratori_italia' ); ?>)
												</label>
												<input type="tel" class="form-control" id="numerotelefono" name="numerotelefono"
													value="<?php echo $numerotelefono; ?>"
													placeholder="<?php echo __( 'Inserisci il tuo numero di telefono', 'design_laboratori_italia' ); ?>">
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
												<button type="submit" class="btn btn-primary"><?php echo __( 'Iscrivimi alla newsletter', 'design_laboratori_italia' ); ?></button>
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
	<?php 
		}
	?>
</main>

<?php
get_footer();
