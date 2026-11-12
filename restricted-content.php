<?php
/**
 * The response to a request for a content that Groups reserved: 401 for a visitor who is not logged in, 403 for a
 * logged-in user without permission. Chosen by `dli_restricted_content_template()`.
 *
 * It never prints the title of the content nor a breadcrumb, which would reveal it.
 *
 * @package Design_Laboratori_Italia
 */

$dli_state     = dli_restricted_content_state();
$dli_status    = is_array( $dli_state ) && 403 === $dli_state['status'] ? 403 : 401;
$dli_url       = is_array( $dli_state ) && ! empty( $dli_state['url'] ) ? (string) $dli_state['url'] : home_url( '/' );
$dli_user      = wp_get_current_user();
$dli_logged_in = is_user_logged_in();

get_header();
?>

<main id="main-container" class="main-container" role="main">

	<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-restricted-title">
		<div class="container">
			<div class="row align-items-stretch">
				<div class="col-12 col-lg-7">
					<div class="it-hero-text-wrapper px-lg-2">
						<span class="it-category"><?php echo esc_html( (string) $dli_status ); ?></span>
						<h2 id="dli-hero-restricted-title">
							<?php
							echo esc_html(
								403 === $dli_status
									? __( 'Non hai i permessi per vedere questo contenuto', 'design_laboratori_italia' )
									: __( 'Contenuto riservato', 'design_laboratori_italia' )
							);
							?>
						</h2>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="section bg-white">
		<div class="container">
			<article class="article-wrapper">
				<p class="fs-5">
					<?php
					echo esc_html(
						403 === $dli_status
							? __( 'Il tuo account non è abilitato a vedere questo contenuto. Se pensi che sia un errore, contatta l\'amministratore del sito.', 'design_laboratori_italia' )
							: __( 'Per vedere questo contenuto devi accedere.', 'design_laboratori_italia' )
					);
					?>
				</p>
				<?php
				get_template_part(
					'template-parts/common/reserved-area-access',
					null,
					array(
						'logged_in'  => $dli_logged_in,
						'pending'    => $dli_logged_in && in_array( DLI_PENDING_USER_ROLE_SLUG, (array) $dli_user->roles, true ),
						'name'       => $dli_user->display_name,
						'login_url'  => dli_get_reserved_area_login_url( $dli_url ),
						'logout_url' => wp_logout_url( $dli_url ),
					)
				);
				?>
			</article>
		</div>
	</section>
</main><!-- #main -->

<?php
get_footer();
