<?php
/**
 * Template Name: Persone
 *
 * Persone template file
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

?>

<!-- START CONTENT -->
<main id="main-container">
	<!-- BREADCRUMB -->
	<section id ="breadcrumb">
		<div class="container">
			<div class="row">
				<div class="col-12 ms-4 ">
					<nav class="breadcrumb-container" aria-label="Percorso di navigazione">
						<ol class="breadcrumb pb-0">
							<li class="breadcrumb-item"><a href="sf-index.html">Home</a><span class="separator">&gt;</span></li>
							<li class="breadcrumb-item active" aria-current="Elenco persone">Persone</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</section>

	<!-- BANNER PERSONE -->
	<section id="banner-persone" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-persone">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0  ">Le persone</h2>
					<p class="font-weight-normal">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ELENCO PERSONE -->
	<div class="container my-4">
		<section class="section bg-gray-light py-5">
			<div class="container">

				<!-- FILTRI SU STRUTTURE chips se presenti -->
				<div class="title-section text-center mb-5">
					<div class="chip chip-simple">
						<span class="chip-label">Scuola Normale</span>
					</div>
					<div class="chip chip-simple">
						<span class="chip-label">IIT</span>
					</div>
					<div class="chip chip-simple">
						<span class="chip-label">Tutte le strutture</span>
					</div>
				</div>
				<!-- FINE FILTRI -->

				<!-- ELENCO AVATAR PERSONE -->
				<div class="row  mb-4">
					<div class="col-lg-3">
						<h3 class="text-lg-right mb-3 h4">Direttore</h3>
					</div><!-- /col-lg-3 -->
					<div class="col-lg-9">
						<div class="row ">

							<div class="col-lg-4">
								<div class="avatar-wrapper avatar-extra-text">
									<div class="avatar size-xl">
										<img src="https://randomuser.me/api/portraits/men/33.jpg" alt="" aria-hidden="true">
									</div>
									<div class="extra-text">
										<h4><a href="sf-scheda-persona.html">Mario Rossi</a></h4>
										<time datetime="2023-09-15">Scuola normale superiore&nbsp;</time>
									</div>
								</div>
							</div>

							<div class="col-lg-4">
								<div class="avatar-wrapper avatar-extra-text">
									<div class="avatar size-xl">
										<img src="https://randomuser.me/api/portraits/women/33.jpg" alt="" aria-hidden="true">
									</div>
									<div class="extra-text">
										<h4>Giulia Neri</h4>
										<p>IIT</p>
									</div>
								</div>
							</div>

							<div class="col-lg-4">
									<div class="avatar-wrapper avatar-extra-text">
											<div class="avatar size-xl">
												<img src="https://randomuser.me/api/portraits/men/15.jpg" alt="" aria-hidden="true">
											</div>
											<div class="extra-text">
												<h4>Michele Dotti</h4>
											</div>
									</div>
							</div>
						</div><!-- /row -->
					</div><!-- /col-lg-9 -->
				</div><!-- /row -->
				<!-- /row -->
				<!-- /row -->

				<div class="row  mb-4">
					<div class="col-lg-3">
						<h3 class="text-lg-right mb-3 h4">Professori</h3>
					</div><!-- /col-lg-3 -->
					<div class="col-lg-9">
						<div class="row ">
							<div class="col-lg-4">
								<div class="avatar-wrapper avatar-extra-text">
									<div class="avatar size-xl">
										<img src="img/img-avatar-250x250.png" alt="" aria-hidden="true">
									</div>
									<div class="extra-text">
										<h4><a href="#">Antonio Rossi</a></h4>
										<time datetime="2023-09-15">Scuola normale superiore&nbsp;</time>
									</div>
								</div>
							</div>

							<div class="col-lg-4">
								<div class="avatar-wrapper avatar-extra-text">
									<div class="avatar size-xl">
											<p aria-hidden="true">GN</p>
											<span class="visually-hidden">Mario Rossi</span>
										</div>
									<div class="extra-text">
										<h4>Giulia Neri</h4>
										<p>IIT</p>
									</div>
								</div>
							</div>

							<div class="col-lg-4">
									<div class="avatar-wrapper avatar-extra-text">
											<div class="avatar size-xl">
												<img src="https://randomuser.me/api/portraits/men/15.jpg" alt="" aria-hidden="true">
											</div>
											<div class="extra-text">
												<h4><a href="#">Michele Dotti</a></h4>
												<time datetime="2023-05-12">CRN/nano</time>
											</div>
									</div>
							</div>
						</div><!-- /row -->

						<div class="row ">
							<div class="col-lg-4">
								<div class="avatar-wrapper avatar-extra-text">
									<div class="avatar size-xl">
										<img src="https://randomuser.me/api/portraits/men/33.jpg" alt="" aria-hidden="true">
									</div>
									<div class="extra-text">
										<h4><a href="#">Mario Rossi</a></h4>
										<time datetime="2023-09-15">Scuola normale superiore&nbsp;</time>
									</div>
								</div>
							</div>

							<div class="col-lg-4">
								<div class="avatar-wrapper avatar-extra-text">
									<div class="avatar size-xl">
										<img src="https://randomuser.me/api/portraits/women/33.jpg" alt="" aria-hidden="true">
									</div>
									<div class="extra-text">
										<h4>Giulia Neri</h4>
										<p>IIT</p>
									</div>
								</div>
							</div>

							<div class="col-lg-4">
								<div class="avatar-wrapper avatar-extra-text">
										<div class="avatar size-xl">
											<img src="https://randomuser.me/api/portraits/men/15.jpg" alt="" aria-hidden="true">
										</div>
										<div class="extra-text">
											<h4><a href="#">Michele Dotti</a></h4>
											<time datetime="2023-05-12">CRN/nano</time>
										</div>
								</div>
							</div>

						</div>
					</div><!-- /col-lg-9 -->
				</div>

				<div class="row  mb-4">
					<div class="col-lg-3">
						<h3 class="text-lg-right mb-3 h4">Post-doc</h3>
					</div><!-- /col-lg-3 -->
					<div class="col-lg-9">
						<div class="row ">
							<span class="text-lg-right mb-3 pt-1 pl-4">Mario Rossi, Federica Bianchi, Viola Gialli</span>
						</div><!-- /row -->
					</div><!-- /col-lg-9 -->
				</div>
			</div><!-- /container -->
		</section><!-- /section -->
		<!-- /section -->
	</div>
</main>
<!-- END CONTENT -->
