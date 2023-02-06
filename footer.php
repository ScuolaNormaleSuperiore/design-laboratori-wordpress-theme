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

<!-- START FOOTER -->
<footer class="it-footer">
	<!-- TAGLINE -->
	<div class="it-footer-main">
		<div class="container">
			<section>
				<div class="row clearfix">
					<div class="col-sm-12">
						<div class="it-brand-wrapper">
						<a href="<?php echo get_site_url(); ?>">
								<svg class="icon">
								<!-- footer logo -->
								<?php get_template_part( 'template-parts/common/logo' ); ?>
								</svg>
								<div class="it-brand-text">
									<h2 class="no_toc"><?php echo dli_get_option( 'nome_laboratorio' ); ?></h2>
									<h3 class="no_toc d-none d-md-block"><?php echo dli_get_option( 'tagline_laboratorio' ); ?></h3>
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
			<h4><a href="#" title="Vai alla pagina: Contatti"><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></a></h4>
			<p>
				<strong><?php echo dli_get_option( 'nome_laboratorio' ); ?></strong><br />
				<?php echo dli_get_option( 'indirizzo_laboratorio' ); ?>
			</p>
			<div class="link-list-wrapper">
				<?php
					$email    = dli_get_option( 'email_laboratorio' );
					$telefono = dli_get_option( 'telefono_laboratorio' );
					$pec      = dli_get_option( 'pec_laboratorio' );
				?>
				<ul class="footer-list link-list clearfix">
					<?php
						if ( $pec ) {
					?>
					<li>
						<a class="list-item" href="#" title="Vai alla pagina: Posta Elettronica Certificata">
							<?php echo __( 'Posta Elettronica Certificata', 'design_laboratori_italia' ) . ': ' . $pec; ?>
						</a>
					</li>
					<?php
						}
					?>

					<li>
						<a class="list-item" href="#" title="E-mail">
							<?php echo __( 'E-mail', 'design_laboratori_italia' ) . ': ' . $email; ?>
						</a>
					</li>
					<li>
						<a class="list-item" href="#" title="Telefono">
							<?php echo __( 'Telefono', 'design_laboratori_italia' ) . ': ' . $telefono; ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 pb-2">
			<!-- <h4><a href="#" title="Vai alla pagina: Lorem Ipsum">Lorem Ipsum</a></h4> -->
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
					$location = 'menu-footer';
					if ( has_nav_menu( $location ) ) {
						wp_nav_menu(
							array(
								'theme_location'  => $location,
								'depth'           => 1,
								'menu_class'      => 'it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row',
								'container'       => '',
								'list_item_class' => 'list-inline-item',
								'current_group'   => $current_group,
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
