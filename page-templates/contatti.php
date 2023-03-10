<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
?>

<main id="main-container" class="main-container bluelectric">
	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<section id="banner-contatti" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-contatti">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0  "><?php echo __( 'Contatti', 'design_laboratori_italia' ); ?></h2>
					<p class="font-weight-normal">Utilizza i dati di contatto o compila il form sottostante</p>
				</div>
			</div>
		</div>
	</section>

	<div class="container my-4 pt-4">

	<!-- CONTATTI  letti da configurazione -->
		<div class="row">
			<div class="col-12 col-lg-3 border-end pe-0 ps-0">
				<div class="it-list-wrapper pt-4">
					<h3 class="h6 text-uppercase border-bottom ps-3">Contatti</h3>
					<ul class="it-list">
						<li>
							<div class="list-item">
								<div class="it-rounded-icon">
									<svg class="icon">
										<use href="bootstrap-italia/svg/sprites.svg#it-telephone"></use>
									</svg>
								</div>
								<div class="it-right-zone"><span class="text">050 509662</span>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="list-item">
							<div class="it-rounded-icon">
								<svg class="icon">
								<use href="bootstrap-italia/svg/sprites.svg#it-mail"></use>
								</svg>
							</div>
							<div class="it-right-zone">
								<span class="text">mail@sns.it</span>
							</div>
							</a>
						</li>
						<li>
							<a class="list-item" href="#">
							<div class="it-rounded-icon">
							<svg class="icon">
							<use href="bootstrap-italia/svg/sprites.svg#it-link"></use>
							</svg>
							</div>
							<div class="it-right-zone"><span class="text">www.sns.it</span>
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
					<div class="p-5 shadow">
						<div class="row">
							<div class="form-group col">
								<label class="active" for="inputAddress">Nome e cognome&nbsp;</label>
								<input type="text" class="form-control" id="nome e cognome" placeholder="inserisci il tuo nome">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label class="active" for="inputEmail4">Email</label>
								<input type="email" class="form-control" id="inputEmail4" placeholder="inserisci il tuo indirizzo email">
							</div>
							<div class="form-group col-md-6">
								<label for="exampleInputTelephone" class="active">Telefono</label>
								<input type="tel" class="form-control" id="exampleInputTelephone" placeholder="inserisci il tuo telefono">
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-9">
								<div class="toggles">
									<label for="toggleEsempio1a">
									Vuoi ricevere notifica al tuo indirizzo email
									<input type="checkbox" id="toggleEsempio1a">
									<span class="lever"></span>
									</label>
								</div>
							</div>
						</div>
							<div class="row mt-4">
								<div class="form-group col text-center">
									<button type="button" class="btn btn-outline-primary">Annulla</button>
									<button type="submit" class="btn btn-primary">Conferma</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

</main>

<?php
get_footer();
