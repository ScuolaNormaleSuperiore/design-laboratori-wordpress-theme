<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Design_Laboratori_Italia
 */

?>

<?php
	$dli_tagline       = dli_get_option_by_lang( 'tagline_laboratorio' );
	$dli_site_name     = dli_get_option_by_lang( 'nome_laboratorio' );
	$dli_label_footer  = dli_get_option( 'label_contact_footer' );
	$dli_label_contact = $dli_label_footer ? $dli_label_footer : $dli_site_name;
?>

<!-- START FOOTER -->
<footer class="it-footer" id="footer-container" role="contentinfo">
	<!-- TAGLINE -->
	<div class="it-footer-main">
		<div class="container">
			<section>
				<div class="row clearfix">
					<div class="col-sm-12">
						<div class="it-brand-wrapper">
							<a href="<?php echo esc_url( dli_homepage_url() ); ?>">
								<!-- footer logo -->
								<?php get_template_part( 'template-parts/common/logo_footer' ); ?>
								<div class="it-brand-text ms-4">
										<h2 class="no_toc h2"><?php echo esc_html( $dli_label_contact ); ?></h2>
										<h3 class="no_toc h3 d-none d-md-block"><?php echo esc_html( $dli_tagline ); ?></h3>
								</div>
							</a>
						</div>
					</div>
				</div>
			</section>
			<!-- CONTACTS -->
			<section class="py-4 border-white border-top">
				<div class="row">
		<div class="col-lg-4 col-md-4 pb-2">
				<h4 class="customSpacing"><a href="#" title="Vai alla pagina: Contatti"><?php echo esc_html__( 'Contatti', 'design_laboratori_italia' ); ?></a></h4>
				<p>
					<strong><?php echo esc_html( dli_get_option( 'nome_laboratorio' ) ); ?></strong><br />
					<?php echo esc_html( dli_get_option( 'indirizzo_laboratorio' ) ); ?>
				</p>
				<div class="link-list-wrapper">
					<?php
						$dli_email    = dli_get_option( 'email_laboratorio' );
						$dli_telefono = dli_get_option( 'telefono_laboratorio' );
						$dli_pec      = dli_get_option( 'pec_laboratorio' );
					?>
					<ul class="footer-list link-list clearfix">
						<?php
						if ( $dli_pec ) {
							?>
						<li>
							<a class="list-item" href="<?php echo esc_url( 'mailto:' . sanitize_email( $dli_pec ) ); ?>" title="Vai alla pagina: Posta Elettronica Certificata">
							<?php echo esc_html( esc_html__( 'Posta Elettronica Certificata', 'design_laboratori_italia' ) . ': ' . $dli_pec ); ?>
							</a>
						</li>
							<?php
						}
						if ( $dli_email ) {
							?>
						<li>
							<a class="list-item" href="<?php echo esc_url( 'mailto:' . sanitize_email( $dli_email ) ); ?>" title="E-mail">
							<?php echo esc_html( esc_html__( 'E-mail', 'design_laboratori_italia' ) . ': ' . $dli_email ); ?>
							</a>
						</li>
							<?php
						}
						if ( $dli_telefono ) {
							?>
						<li>
							<a class="list-item" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $dli_telefono ) ); ?>" title="Telefono">
							<?php echo esc_html( esc_html__( 'Telefono', 'design_laboratori_italia' ) . ': ' . $dli_telefono ); ?>
							</a>
						</li>
							<?php
						}
						?>
				</ul>
			</div>
		</div>
		<!-- LINK UTILI -->
		<div class="col-lg-4 col-md-4 pb-2">
			<?php
					$dli_location   = 'menu-links';
					$dli_locations  = get_nav_menu_locations();
					$dli_menu_id    = ( is_array( $dli_locations ) && isset( $dli_locations[ $dli_location ] ) ) ? $dli_locations[ $dli_location ] : 0;
					$dli_menu_items = $dli_menu_id ? wp_get_nav_menu_items( $dli_menu_id ) : array();
			if ( count( $dli_menu_items ) > 0 ) {
				?>
				<h4 class="customSpacing">
					<a href="#" title="<?php echo esc_attr( sprintf( 'Vai alla pagina: %s', esc_html__( 'Link utili', 'design_laboratori_italia' ) ) ); ?>">
					<?php echo esc_html__( 'Link utili', 'design_laboratori_italia' ); ?>
					</a>
				</h4>
				<?php
			}
			?>
			<div class="link-list-wrapper" id="link-utili">
			<?php
			if ( has_nav_menu( $dli_location ) && ( count( $dli_menu_items ) > 0 ) ) {
				wp_nav_menu(
					array(
						'theme_location' => $dli_location,
						'depth'          => 0,
						'menu_class'     => 'footer-list',
						'walker'         => new Footer_Menu_Walker(),
					)
				);
			}
			?>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 pb-2">
			<div class="pb-2">
				<?php get_template_part( 'template-parts/common/social_footer' ); ?>
				<?php get_template_part( 'template-parts/common/newsletter' ); ?>
			</div>
		</div>
				</div>
			</section>
		</div>
	</div>
	<!-- POLICIES (bottom footer) -->
	<section>
		<div class="it-footer-small-prints clearfix">
			<div class="container">
				<h3 class="visually-hidden">Sezione Link Utili</h3>
				<?php
						$dli_location = 'menu-footer';
				if ( has_nav_menu( $dli_location ) ) {
					wp_nav_menu(
						array(
							'theme_location'  => $dli_location,
							'depth'           => 1,
							'menu_class'      => 'it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row',
							'container'       => '',
							'list_item_class' => 'list-inline-item',
							'walker'          => new Footer_Menu_Walker(),
						)
					);
				}
				?>
			</div>
		</div>
	</section>
</footer>
<!-- END FOOTER -->

<?php wp_footer(); ?>
</body>
</html>
