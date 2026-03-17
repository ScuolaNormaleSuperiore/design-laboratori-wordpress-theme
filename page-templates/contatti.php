<?php
/**
 * Template Name: Contatti
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

$dli_site_email      = dli_get_option( 'email_laboratorio' );
$dli_phone           = dli_get_option( 'telefono_laboratorio' );
$dli_pec             = dli_get_option( 'pec_laboratorio' );
$dli_website         = get_site_url();
$dli_show_error      = false;
$dli_show_success    = false;
$dli_captcha_enabled = false;
$dli_form_valid      = true;
$dli_sent            = false;
$dli_result_text     = '';
$dli_nonce_error     = false;
$dli_form_submitted  = 'no';

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once DLI_THEMA_PATH . '/template-parts/common/captcha.php';

// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Raw POST is read to repopulate the form; email sending remains nonce-gated below.
$dli_post_data = wp_unslash( $_POST );

$dli_full_name      = sanitize_text_field( $dli_post_data['nomecognome'] ?? '' );
$dli_email_address  = sanitize_email( $dli_post_data['indirizzoemail'] ?? '' );
$dli_phone_number   = sanitize_text_field( $dli_post_data['numerotelefono'] ?? '' );
$dli_receipt        = sanitize_text_field( $dli_post_data['ricevuta'] ?? '' );
$dli_form_submitted = sanitize_text_field( $dli_post_data['forminviato'] ?? 'no' );
$dli_message_text   = sanitize_text_field( $dli_post_data['testomessaggio'] ?? '' );
$dli_captcha_field  = sanitize_text_field( $dli_post_data['captcha-field'] ?? '' );
$dli_captcha_prefix = sanitize_text_field( $dli_post_data['captcha-prefix'] ?? '' );

if ( 'yes' === $dli_form_submitted ) {
	if (
		isset( $dli_post_data['contatti_nonce_field'] ) &&
		wp_verify_nonce(
			sanitize_text_field( $dli_post_data['contatti_nonce_field'] ),
			'sf_contatti_nonce'
		)
	) {
		$dli_nonce_error = false;
		$dli_to          = $dli_site_email;
		$dli_subject     = '[FormContatti] Email dal sito: ' . dli_get_option( 'nome_laboratorio' );
		$dli_header_mail = str_replace( array( "\r", "\n" ), '', $dli_email_address );
		$dli_headers     = "From: {$dli_header_mail}\r\nReply-To: {$dli_header_mail}";

		if ( $dli_captcha_enabled ) {
			$dli_captcha_valid = $captcha_obj->check( $dli_captcha_prefix, $dli_captcha_field );
			if ( ! $dli_captcha_valid ) {
				$dli_result_text .= ( '' === $dli_result_text ? '' : '<br />' ) . esc_html__( 'Il codice di controllo non è valido.', 'design_laboratori_italia' );
			}
		} else {
			$dli_captcha_valid = true;
		}

		if ( '' === $dli_full_name || '' === $dli_email_address || '' === $dli_message_text ) {
			$dli_form_valid  = false;
			$dli_result_text .= ( '' === $dli_result_text ? '' : '<br />' ) . esc_html__( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' );
		}

		if ( ! is_email( $dli_email_address ) ) {
			$dli_form_valid  = false;
			$dli_result_text .= ( '' === $dli_result_text ? '' : '<br />' ) . esc_html__( 'Indicare un indirizzo email valido.', 'design_laboratori_italia' );
		}

		$dli_form_valid = $dli_form_valid && ( $dli_captcha_valid || ! $dli_captcha_enabled );

		if ( $dli_form_valid ) {
			$dli_sent = wp_mail( $dli_to, $dli_subject, wp_strip_all_tags( $dli_message_text ), $dli_headers );
			if ( ! $dli_sent ) {
				$dli_result_text .= ( '' === $dli_result_text ? '' : '<br />' ) . esc_html__( 'Messaggio non inviato', 'design_laboratori_italia' ) . '&nbsp;.';
			}

			if ( 'on' === $dli_receipt ) {
				$dli_receipt_label = esc_html__( 'Ricevuta', 'design_laboratori_italia' );
				$dli_subject      .= '(' . $dli_receipt_label . ')';
				$dli_sent          = $dli_sent && wp_mail( $dli_email_address, $dli_subject, wp_strip_all_tags( $dli_message_text ), $dli_headers );
				if ( ! $dli_sent ) {
					$dli_result_text .= ( '' === $dli_result_text ? '' : '<br />' ) . esc_html__( 'Copia del messaggio non inviata.', 'design_laboratori_italia' );
				}
			}
		}

		if ( 'yes' === $dli_form_submitted && $dli_sent ) {
			$dli_show_success = true;
		}

		if ( ! $dli_form_valid || ( 'yes' === $dli_form_submitted && ! $dli_sent ) ) {
			$dli_show_error = true;
		}
	} else {
		$dli_show_error  = true;
		$dli_nonce_error = true;
		$dli_result_text .= ( '' === $dli_result_text ? '' : '<br />' ) . esc_html__( 'Valore di Nonce errato.', 'design_laboratori_italia' );
	}
}
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- SEZIONE BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- SEZIONE HEADER -->
	<section id="banner-contatti" aria-describedby="dli-contatti-intro-desc" class="bg-banner-contatti">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0"><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></h2>
					<p id="dli-contatti-intro-desc" class="font-weight-normal">
						<?php echo esc_html__( 'Utilizza i dati di contatto o compila il form sottostante', 'design_laboratori_italia' ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- SEZIONE MESSAGGI -->
	<div id="contatti_messaggi">
		<?php if ( $dli_show_success ) : ?>
			<div class="container my-12 p-2">
				<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
					<?php echo esc_html__( 'Messaggio inviato correttamente', 'design_laboratori_italia' ) . '&nbsp;.'; ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo esc_attr__( 'Chiudi avviso', 'design_laboratori_italia' ); ?>">
						<svg class="icon" role="img" aria-labelledby="close-success-alert">
							<title id="close-success-alert"><?php echo esc_html__( 'Chiudi avviso', 'design_laboratori_italia' ); ?></title>
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
						</svg>
					</button>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $dli_show_error ) : ?>
			<div class="container my-12 p-2">
				<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
					<?php echo wp_kses_post( $dli_result_text ); ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo esc_attr__( 'Chiudi avviso', 'design_laboratori_italia' ); ?>">
						<svg class="icon" role="img" aria-labelledby="close-error-alert">
							<title id="close-error-alert"><?php echo esc_html__( 'Chiudi avviso', 'design_laboratori_italia' ); ?></title>
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
						</svg>
					</button>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<!-- SEZIONE FORM -->
	<div id="contatti_form">
		<form action="." id="formcontatti" name="formcontatti" method="post">
			<?php wp_nonce_field( 'sf_contatti_nonce', 'contatti_nonce_field' ); ?>
			<div class="container my-4 pt-4">

				<!-- CONTATTI DEL LABORATORIO -->
				<div class="row">
					<div class="col-12 col-lg-3 border-end pe-0 ps-0">
						<div class="it-list-wrapper pt-4">
							<h3 class="h6 text-uppercase border-bottom ps-3"><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></h3>
							<ul class="it-list">
								<li>
									<div class="list-item">
										<div class="it-rounded-icon">
											<svg class="icon" role="img" aria-labelledby="telephone-icon">
												<title id="telephone-icon">Telephone</title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone">
											<span class="text">
												<a class="list-item" href="<?php echo esc_url( 'tel:' . $dli_phone ); ?>" title="Telefono">
													<?php echo esc_html( $dli_phone ); ?>
												</a>
											</span>
										</div>
									</div>
								</li>
								<li>
									<a href="<?php echo esc_url( 'mailto:' . $dli_site_email ); ?>" class="list-item">
										<div class="it-rounded-icon">
											<svg class="icon" role="img" aria-labelledby="mail-icon">
												<title id="mail-icon">Mail</title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone">
											<span class="text"><?php echo esc_html( $dli_site_email ); ?></span>
										</div>
									</a>
								</li>
								<li>
									<a class="list-item" href="<?php echo esc_url( $dli_website ); ?>">
										<div class="it-rounded-icon">
											<svg class="icon" role="img" aria-labelledby="link-icon">
												<title id="link-icon">Website Link</title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link' ); ?>"></use>
											</svg>
										</div>
										<div class="it-right-zone">
											<span class="text"><?php echo esc_html( $dli_website ); ?></span>
										</div>
									</a>
								</li>
								<?php if ( $dli_pec ) : ?>
									<li>
										<a href="<?php echo esc_url( 'mailto:' . $dli_pec ); ?>" class="list-item">
											<div class="it-rounded-icon">
												<svg class="icon" role="img" aria-labelledby="pec-icon">
													<title id="pec-icon">PEC</title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
												</svg>
											</div>
											<div class="it-right-zone">
												<span class="text"><?php echo esc_html( $dli_pec ); ?></span>
											</div>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>

					<!-- FORM DEI CONTATTI -->
					<div class="col-lg-9">
						<div class="row">
							<div class="col-lg-12">
								<div class="p-5">
									<div class="row">
										<div class="form-group col">
											<label class="active" for="nomecognome"><?php echo esc_html__( 'Nome e cognome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
											<input
												type="text"
												class="form-control"
												name="nomecognome"
												id="nomecognome"
												value="<?php echo esc_attr( $dli_full_name ); ?>"
												placeholder="<?php echo esc_attr__( 'Inserisci il tuo nome', 'design_laboratori_italia' ); ?>"
											>
										</div>
									</div>
									<div class="row">
										<div class="form-group col">
											<label class="active" for="testomessaggio"><?php echo esc_html__( 'Testo del messaggio', 'design_laboratori_italia' ); ?>&nbsp;*</label>
											<input
												type="text"
												class="form-control"
												name="testomessaggio"
												id="testomessaggio"
												value="<?php echo esc_attr( $dli_message_text ); ?>"
												placeholder="<?php echo esc_attr__( 'Inserisci il testo del messaggio', 'design_laboratori_italia' ); ?>"
											>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-6">
											<label class="active" for="indirizzoemail"><?php echo esc_html__( 'E-mail', 'design_laboratori_italia' ); ?>&nbsp;*</label>
											<input
												type="email"
												class="form-control"
												id="indirizzoemail"
												name="indirizzoemail"
												value="<?php echo esc_attr( $dli_email_address ); ?>"
												placeholder="<?php echo esc_attr__( 'Inserisci il tuo indirizzo email', 'design_laboratori_italia' ); ?>"
											>
										</div>
										<div class="form-group col-md-6">
											<label for="numerotelefono" class="active"><?php echo esc_html__( 'Telefono', 'design_laboratori_italia' ); ?></label>
											<input
												type="tel"
												class="form-control"
												id="numerotelefono"
												name="numerotelefono"
												value="<?php echo esc_attr( $dli_phone_number ); ?>"
												placeholder="<?php echo esc_attr__( 'Inserisci il tuo numero di telefono', 'design_laboratori_italia' ); ?>"
											>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-9">
											<div class="toggles">
												<label for="ricevuta">
													<?php echo esc_html__( 'Vuoi ricevere notifica al tuo indirizzo email', 'design_laboratori_italia' ) . ' ?'; ?>
													<input type="checkbox" id="ricevuta" name="ricevuta" <?php checked( 'on', $dli_receipt ); ?>>
													<span class="lever"></span>
												</label>
											</div>
										</div>
									</div>

									<?php if ( $dli_captcha_enabled ) : ?>
										<div class="row" style="margin-top: 20px;">
											<div class="form-group col-md-6" style="text-align: center">
												<img
													src="<?php echo esc_url( $captcha_obj_image_src ); ?>"
													alt="captcha"
													width="<?php echo esc_attr( $captcha_obj_image_width ); ?>"
													height="<?php echo esc_attr( $captcha_obj_image_height ); ?>"
												>
											</div>
											<div class="form-group col-md-6">
												<label class="active visually-hidden" for="captcha-field">
													<?php echo esc_html__( 'Codice di conferma', 'design_laboratori_italia' ); ?>
												</label>
												<input
													name="captcha-field"
													id="captcha-field"
													size="<?php echo esc_attr( $captcha_obj_image_width ); ?>"
													type="text"
													placeholder="<?php echo esc_attr__( 'Riscrivi qui il codice di conferma', 'design_laboratori_italia' ); ?>"
												>
												<input
													name="captcha-prefix"
													id="captcha-prefix"
													class="form-control"
													type="hidden"
													value="<?php echo esc_attr( $captcha_obj_prefix ); ?>"
												>
											</div>
										</div>
									<?php endif; ?>

									<div class="row mt-4">
										<div class="form-group col text-center">
											<input type="hidden" name="forminviato" id="forminviato" value="yes">
											<button type="button" class="btn btn-outline-primary"><?php echo esc_html__( 'Annulla', 'design_laboratori_italia' ); ?></button>
											<button type="submit" class="btn btn-primary"><?php echo esc_html__( 'Conferma', 'design_laboratori_italia' ); ?></button>
										</div>
									</div>

									<?php if ( $dli_nonce_error ) : ?>
										<div class="visually-hidden"><?php echo esc_html__( 'Errore nonce rilevato durante l\'invio del form.', 'design_laboratori_italia' ); ?></div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</main>

<?php get_footer(); ?>
