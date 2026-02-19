<?php
/**
 * Newsletter page template.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Template Name: Newsletter
 */

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! class_exists( 'Newsletter_Manager' ) ) {
	require_once DLI_THEMA_PATH . '/inc/classes/class-newslettermanager.php';
}

get_header();

$dli_post_data = wp_unslash( $_POST );
$dli_get_data  = wp_unslash( $_GET );

// Conferma da email (GET).
$dli_after_confirm = isset( $dli_get_data['after_confirm'] ) ? sanitize_text_field( $dli_get_data['after_confirm'] ) : 'no';
$dli_user_name     = isset( $dli_post_data['user_name'] ) ? sanitize_text_field( $dli_post_data['user_name'] ) : '';
$dli_user_surname  = isset( $dli_post_data['user_surname'] ) ? sanitize_text_field( $dli_post_data['user_surname'] ) : '';
$dli_user_mail     = isset( $dli_post_data['user_mail'] ) ? sanitize_email( $dli_post_data['user_mail'] ) : '';
// Submit da questa stessa pagina.
$dli_form_sent         = isset( $dli_post_data['form_sent'] ) ? sanitize_text_field( $dli_post_data['form_sent'] ) : 'no';
$dli_captcha_field     = isset( $dli_post_data['captcha-field'] ) ? sanitize_text_field( $dli_post_data['captcha-field'] ) : '';
$dli_captcha_prefix    = isset( $dli_post_data['captcha-prefix'] ) ? sanitize_text_field( $dli_post_data['captcha-prefix'] ) : '';
$dli_form_submission   = ( 'yes' !== $dli_after_confirm ) && ( 'yes' === $dli_form_sent );
$dli_sent_successfully = false;
$dli_form_errors       = array();
$dli_user_phone        = ''; // TODO: Add international prefix management if required.

require_once DLI_THEMA_PATH . '/template-parts/common/captcha.php';

// Verifica nonce.
if (
	$dli_form_submission &&
	(
		! (
			isset( $dli_post_data['newsletter_nonce_field'] ) &&
			wp_verify_nonce( sanitize_text_field( $dli_post_data['newsletter_nonce_field'] ), 'sf_newsletter_nonce' )
		)
	)
) {
	// Il NONCE non è valido.
	$dli_form_errors[] = __( 'Valore di Nonce errato.', 'design_laboratori_italia' );
}

// Verifica del captcha.
if ( $dli_form_submission && $captcha_enabled ) {
	$dli_captcha_valid = $captcha_obj->check( $dli_captcha_prefix, $dli_captcha_field );
	if ( ! $dli_captcha_valid ) {
		$dli_form_errors[] = __( 'Il codice di controllo non è valido.', 'design_laboratori_italia' );
	}
}


// Procedura di verifica e sottomissione della sottoscrizione.
if ( 0 === count( $dli_form_errors ) && true === $dli_form_submission ) {
	$dli_manager = new Newsletter_Manager();
	$dli_manager->setup( $dli_user_name, $dli_user_surname, $dli_user_mail, $dli_user_phone );
	$dli_manager->validate();
	$dli_form_errors = $dli_manager->get_errors();
	if ( 0 === count( $dli_form_errors ) ) {
		$dli_result = $dli_manager->subscribe_user();
		if ( 201 === $dli_result['code'] || 204 === $dli_result['code'] ) {
			$dli_sent_successfully = true;
		} else {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( wp_json_encode( $dli_result ) );
			}
			$dli_form_errors[] = __( 'Errore durante l\'iscrizione alla newsletter.', 'design_laboratori_italia' );
		}
	}
}

?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- SEZIONE BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- SEZIONE HEADER -->
		<section id="banner-newsletter" aria-labelledby="newsletter-title" class="bg-banner-newsletter">
			<div class="section-muted p-3 primary-bg-c1">
				<div class="container">
					<div class="hero-title text-left ms-4 pb-3 pt-3">
						<h2 id="newsletter-title" class="p-0"><?php echo esc_html__( 'Newsletter', 'design_laboratori_italia' ); ?></h2>
						<p class="font-weight-normal">
							<?php echo esc_html__( 'Compila il form seguente per iscriverti alla newsletter, riceverai una e-mail per confermare l\'iscrizione', 'design_laboratori_italia' ); ?>
						</p>
					</div>
				</div>
		</div>
	</section>

	<!-- SEZIONE MESSAGGI -->
	<div id="newsletter_messaggi">
		<?php
		if ( 'yes' === $dli_after_confirm ) {
			?>
			<!-- CONFERMA  -->
			<div class="container my-12 p-2" style="min-height: 150px;">
				<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
				<?php echo esc_html__( 'Iscrizione alla newsletter avvenuta con successo.', 'design_laboratori_italia' ); ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
						<svg class="icon" role="img" aria-labelledby="Close" aria-label="Close">
							<title>Chiudi avviso</title>
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
						</svg>
					</button>
				</div>
			</div>
			<?php
		}
		if ( $dli_sent_successfully ) {
			?>
			<!-- SOTTOSCRIZIONE INVIATA -->
			<div class="container my-12 p-2">
				<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
				<?php echo esc_html__( 'Richiesta di iscrizione ricevuta correttamente, verifica la tua email per completare la procedura.', 'design_laboratori_italia' ); ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
						<svg class="icon" role="img" aria-labelledby="Close" aria-label="Close">
							<title>Chiudi avviso</title>
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
						</svg>
					</button>
				</div>
			</div>
			<?php
		}
		if ( 0 !== count( $dli_form_errors ) ) {
			?>
			<!-- ERRORI NELLA SOTTOSCRIZIONE -->
			<div class="container my-12 p-2">
			<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
					<ul class="mb-0">
					<?php foreach ( $dli_form_errors as $dli_error_text ) : ?>
							<li><?php echo esc_html( $dli_error_text ); ?></li>
						<?php endforeach; ?>
					</ul>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
					<svg class="icon" role="img" aria-labelledby="Close" aria-label="Close">
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
	<?php
	if ( 'no' === $dli_after_confirm && true !== $dli_sent_successfully ) {
		$dli_current_language = dli_current_language( 'slug' );
		$dli_page_url         = dli_get_newsletter_link( $dli_current_language );
		?>
			<div id="newsletter_form">
				<form action="<?php echo esc_url( $dli_page_url ); ?>" id="formnewsletter" name="formnewsletter" method="post">
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
													<label class="active" for="user_name"><?php echo esc_html__( 'Nome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
													<input type="text" class="form-control" name="user_name" id="user_name"
														value="<?php echo esc_attr( $dli_user_name ); ?>"
														placeholder="<?php echo esc_attr__( 'Inserisci il tuo nome', 'design_laboratori_italia' ); ?>">
												</div>
												<div class="form-group col-md-6">
													<label class="active" for="user_surname"><?php echo esc_html__( 'Cognome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
													<input type="text" class="form-control" name="user_surname" id="user_surname"
														value="<?php echo esc_attr( $dli_user_surname ); ?>"
														placeholder="<?php echo esc_attr__( 'Inserisci il tuo cognome', 'design_laboratori_italia' ); ?>">
												</div>
										</div>
										<div class="row">
											<div class="form-group col">
													<label class="active" for="user_mail"><?php echo esc_html__( 'E-mail', 'design_laboratori_italia' ); ?>&nbsp;*</label>
													<input type="email" class="form-control" id="user_mail" name="user_mail" 
														value="<?php echo esc_attr( $dli_user_mail ); ?>"
														placeholder="<?php echo esc_attr__( 'Inserisci il tuo indirizzo email', 'design_laboratori_italia' ); ?>">
											</div>
										</div>

										<!-- CAPTCHA -->
										<?php
										if ( $captcha_enabled ) {
											?>
											<div class="row" style="margin-top: 20px;">
												<div class="form-group col-md-6" style="text-align: center">
													<img src="<?php echo esc_url( $captcha_obj_image_src ); ?>" alt="<?php echo esc_attr__( 'Captcha', 'design_laboratori_italia' ); ?>"
																width="<?php echo esc_attr( $captcha_obj_image_width ); ?>" height="<?php echo esc_attr( $captcha_obj_image_height ); ?>" />
												</div>
												<div class="form-group col-md-6">
													<input name="captcha-field" id="captcha-field"  size="<?php echo esc_attr( $captcha_obj_image_width ); ?>" type="text" 
															placeholder="<?php echo esc_attr__( 'Riscrivi qui il codice di conferma', 'design_laboratori_italia' ); ?>"	/>
													<input name="captcha-prefix" id="captcha-prefix"  class="form-control" type="hidden" value="<?php echo esc_attr( $captcha_obj_prefix ); ?>" />
												</div>
											</div>
											<?php
										}
										?>

										<!-- SUBMIT -->
										<div class="row mt-4">
											<div class="form-group col text-center">
												<input type="hidden" name="form_sent" id="form_sent" value="yes" />
													<button type="submit" class="btn btn-primary"><?php echo esc_html__( 'Iscrivimi alla newsletter', 'design_laboratori_italia' ); ?></button>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
				</form>
		</div>
		<?php
	}
	?>
</main>

<?php
get_footer();
