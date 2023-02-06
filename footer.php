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
							<a href="#">
								<svg class="icon">
								<use xlink:href="bootstrap-italia/svg/sprites.svg#it-code-circle"></use>
								</svg>
								<div class="it-brand-text">
									<h2 class="no_toc">Lorem Ipsum</h2>
									<h3 class="no_toc d-none d-md-block">Inserire qui la tag line</h3>
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
			<h4><a href="#" title="Vai alla pagina: Contatti">Contatti</a></h4>
			<p>
				<strong>Comune di Lorem Ipsum</strong><br />
				Via Roma 0 - 00000 Lorem Ipsum Codice fiscale / P. IVA: 000000000
			</p>
			<div class="link-list-wrapper">
				<ul class="footer-list link-list clearfix">
					<li><a class="list-item" href="#" title="Vai alla pagina: Posta Elettronica Certificata">Posta
				Elettronica Certificata</a></li>
					<li>
			<a class="list-item" href="#" title="Vai alla pagina: URP - Ufficio Relazioni con il Pubblico">URP -
				Ufficio Relazioni con il Pubblico</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 pb-2">
			<h4><a href="#" title="Vai alla pagina: Lorem Ipsum">Lorem Ipsum</a></h4>
		</div>
		<div class="col-lg-4 col-md-4 pb-2">
			<div class="pb-2">
				<h4><a href="#" title="Vai alla pagina: Seguici su">Seguici su</a></h4>
				<ul class="list-inline text-left social">
					<li class="list-inline-item">
			<a class="p-2 text-white" href="#" target="_blank"><svg class="icon icon-sm icon-white align-top">
					<use xlink:href="bootstrap-italia/svg/sprites.svg#it-designers-italia"></use>
				</svg><span class="visually-hidden">Designers Italia</span></a>
					</li>
					<li class="list-inline-item">
			<a class="p-2 text-white" href="#" target="_blank"><svg class="icon icon-sm icon-white align-top">
					<use xlink:href="bootstrap-italia/svg/sprites.svg#it-twitter"></use>
				</svg><span class="visually-hidden">Twitter</span></a>
					</li>
					<li class="list-inline-item">
			<a class="p-2 text-white" href="#" target="_blank"><svg class="icon icon-sm icon-white align-top">
					<use xlink:href="bootstrap-italia/svg/sprites.svg#it-medium"></use>
				</svg><span class="visually-hidden">Medium</span></a>
					</li>
					<li class="list-inline-item">
			<a class="p-2 text-white" href="#" target="_blank"><svg class="icon icon-sm icon-white align-top">
					<use xlink:href="bootstrap-italia/svg/sprites.svg#it-behance"></use>
				</svg><span class="visually-hidden">Behance</span></a>
					</li>
				</ul>
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
				<ul class="it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row">
					<li class="list-inline-item"><a href="#" title="Note Legali">Media policy</a></li>
					<li class="list-inline-item"><a href="#" title="Note Legali">Note legali</a></li>
					<li class="list-inline-item"><a href="#" title="Privacy-Cookies">Privacy policy</a></li>
					<li class="list-inline-item"><a href="#" title="Mappa del sito">Mappa del sito</a></li>
				</ul>
			</div>
		</div>
	</section>
</footer>
<!-- END FOOTER -->

<?php wp_footer(); ?>
</body>
</html>
