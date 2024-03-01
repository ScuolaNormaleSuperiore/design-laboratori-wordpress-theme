<?php
/** Template Name: Newsletter.
 *
 * @package Design_Laboratori_Italia
 */

include_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! class_exists( 'Newsletter_Manager' ) ) {
	include_once DLI_THEMA_PATH . '/inc/classes/class-newslettermanager.php';
}

global $postdata;
get_header();

$postdata = $_POST;
$getdata  = $_GET;

// Conferma da email (GET).
$after_confirm = sanitize_text_field( isset( $getdata['after_confirm'] ) ? $getdata['after_confirm'] : 'no' );
$redirection   = sanitize_text_field( isset( $postdata['redirection'] ) ? $postdata['redirection'] : 'no' );
$user_name     = sanitize_text_field( isset( $postdata['user_name'] ) ? $postdata['user_name'] : '' );
$user_surname  = sanitize_text_field( isset( $postdata['user_surname'] ) ? $postdata['user_surname'] : '' );
$user_mail     = sanitize_email( isset( $postdata['user_mail'] ) ? $postdata['user_mail'] : '' );
$user_phone    = sanitize_text_field( isset( $postdata['user_phone'] ) ? $postdata['user_phone'] : '' );
// Submit da questa stessa pagina.
$form_sent         = sanitize_text_field( isset( $postdata['form_sent'] ) ? $postdata['form_sent'] : 'no' );
$captcha_field     = sanitize_text_field( isset( $postdata['captcha-field'] ) ? $postdata['captcha-field'] : '' );
$captcha_prefix    = sanitize_text_field( isset( $postdata['captcha-prefix'] ) ? $postdata['captcha-prefix'] : '' );
$form_submission   = ( 'yes' !== $after_confirm ) && ( 'yes' === $form_sent );
$sent_successfully = false;
$form_errors       = array();

include_once( DLI_THEMA_PATH . '/template-parts/common/captcha.php' );

// Verifica nonce.
if ( $form_submission &&
	( ! ( isset(  $postdata['newsletter_nonce_field'] ) && 
		wp_verify_nonce( sanitize_text_field(  $postdata['newsletter_nonce_field'] ), 'sf_newsletter_nonce' ) 
		) )
	)
{
	// Il NONCE non è valido.
	array_push( $form_errors, __( 'Valore di Nonce errato.', 'design_laboratori_italia' ) );
}

// Verifica del captcha.
if ( $form_submission && $captcha_enabled ) {
	$captcha_valid = $captcha_obj->check( $captcha_prefix, $captcha_field );
	if ( ! $captcha_valid ) {
		array_push( $form_errors, __( 'Il codice di controllo non è valido.', 'design_laboratori_italia' ) );
	}
}


// Procedura di verifica e sottomissione della sottoscrizione.
if ( ( count( $form_errors ) === 0 ) && ( true === $form_submission ) ) {
	$manager   = new Newsletter_Manager();
	$manager->setup( $user_name, $user_surname, $user_mail, $user_phone );
	$manager->validate();
	$form_errors = $manager->get_errors();
	if ( count( $form_errors ) === 0 ) {
		$result = $manager->subscribe_user();
		if ( ( $result['code'] === 201 ) || ( $result['code'] === 204 ) ) {
			$sent_successfully = true;
		} else {
			error_log( json_encode( $result ) );
			array_push( $form_errors, __( 'Errore durante l\'iscrizione alla newsletter.', 'design_laboratori_italia' ) );
		}
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
					<h2 class="p-0  "><?php echo esc_html( __( 'Newsletter', 'design_laboratori_italia' ) ); ?></h2>
					<p class="font-weight-normal">
						<?php echo esc_html ( __( 'Compila il form seguente per iscriverti alla newsletter, riceverai una e-mail per confermare l\'iscrizione', 'design_laboratori_italia' ) ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- SEZIONE MESSAGGI -->
	<div id="newsletter_messaggi">
		<?php
		if ( $after_confirm === 'yes' ) {
			?>
			<!-- CONFERMA  -->
			<div class="container my-12 p-2" style="min-height: 150px;">
				<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
				<?php echo esc_html ( __( 'Conferma dell\'iscrizione alla newsletter avvenuta con successo', 'design_laboratori_italia' ) . '&nbsp;.' ); ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
						<svg class="icon" role="img" aria-labelledby="Close">
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
						</svg>
					</button>
				</div>
			</div>
		<?php
		}
		if ( $sent_successfully ) {
			?>
			<!-- SOTTOSCRIZIONE INVIATA -->
			<div class="container my-12 p-2">
				<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
				<?php echo esc_html ( __( 'Iscrizione avvenuta correttamente', 'design_laboratori_italia' ) . '&nbsp;.' ); ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
						<svg class="icon" role="img" aria-labelledby="Close">
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
						</svg>
					</button>
				</div>
			</div>
		<?php
		}
		if ( count( $form_errors ) !== 0 ) {
			?>
			<!-- ERRORI NELLA SOTTOSCRIZIONE -->
			<div class="container my-12 p-2">
			<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
				<?php
				$error_text = join( '<BR />', $form_errors );
				echo $error_text;
				?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi avviso">
					<svg class="icon" role="img" aria-labelledby="Close">
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
	if ( 'no' === $after_confirm ) {
		$page_url = get_permalink( get_page_by_path( 'newsletter' ) );
		?>
		<div id="newsletter_form">
			<FORM action="<?php echo esc_url( $page_url ); ?>" id="formnewsletter" name="formnewsletter" method="POST">
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
												<label class="active" for="user_name"><?php echo __( 'Nome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
												<input type="text" class="form-control" name="user_name" id="user_name"
													value="<?php echo $user_name; ?>"
													placeholder="<?php echo __( 'Inserisci il tuo nome', 'design_laboratori_italia' ); ?>">
											</div>
											<div class="form-group col-md-6">
												<label class="active" for="user_surname"><?php echo __( 'Cognome', 'design_laboratori_italia' ); ?>&nbsp;*</label>
												<input type="text" class="form-control" name="user_surname" id="user_surname"
													value="<?php echo $user_surname; ?>"
													placeholder="<?php echo __( 'Inserisci il tuo cognome', 'design_laboratori_italia' ); ?>">
											</div>
										</div>
										<div class="row">
											<div class="form-group col">
												<label class="active" for="user_mail"><?php echo __( 'E-mail', 'design_laboratori_italia' ); ?>&nbsp;*</label>
												<input type="email" class="form-control" id="user_mail" name="user_mail" 
													value="<?php echo $user_mail; ?>"
													placeholder="<?php echo __( 'Inserisci il tuo indirizzo email', 'design_laboratori_italia' ); ?>">
											</div>
										</div>
										<div class="row">
											<div class="form-group col-md-6">
												<label for="user_phone" class="active">
													<?php echo esc_html ( __( 'Telefono', 'design_laboratori_italia' ) ); ?>&nbsp;(<?php echo esc_html( __( 'facoltativo', 'design_laboratori_italia' ) ); ?>)
												</label>
												<input type="tel" class="form-control" id="user_phone" name="user_phone"
													value="<?php echo $user_phone; ?>"
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
												<input type="hidden" name="form_sent" id="form_sent" value="yes" />
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
