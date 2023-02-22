<?php
/**
 * Servizio template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	breadcrumb

	<!-- BANNER PROGETTI -->
	<section class="it-hero-wrapper it-hero-small-size it-dark it-overlay it-primary">
		<div class="img-responsive-wrapper">
		<div class="img-responsive">
		<div class="img-wrapper">
			<img src="<?php echo the_post_thumbnail_url( 'item-gallery' ); ?>" title="titolo immagine" alt="descrizione immagine">
		</div>
		</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<h2><?php echo esc_attr( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block"><?php echo wp_trim_words( get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO PROGETTO -->
	<div class="container py-lg-5" id="scheda_progetto">
		<div class="row">
			<div class="col-12 col-lg-3">
				<div data-bs-toggle="sticky" data-bs-stackable="true">
					<nav class="navbar it-navscroll-wrapper navbar-expand-lg it-bottom-navscroll it-right-side" data-bs-navscroll>
						<button
							class="custom-navbar-toggler"
							type="button"
							aria-controls="navbarNav"
							aria-expanded="false"
							aria-label="Toggle navigation"
							data-bs-toggle="navbarcollapsible"
							data-bs-target="#navbarNav"
						>
						<span class="it-list"></span>1. Introduzione
						</button>
						<div class="progress custom-navbar-progressbar">
							<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
								<svg class="icon icon-sm icon-primary align-top">
									<use href="bootstrap-italia/svg/sprites.svg#it-chevron-left" xlink:href="bootstrap-italia/svg/sprites.svg#it-chevron-left"></use>
								</svg>
								<span>Indietro</span>
							</a>
							<div id="menu_laterale" class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo __( 'Dettagli del progetto', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<ul class="link-list">
										<li class="nav-item">
											<a class="nav-link active" href="#p1"><span><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p2"><span><?php echo __( 'Responsabile', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p3"><span><?php echo __( 'Partecipanti', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p4"><span><?php echo __( 'Indirizzi di ricerca', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p5"><span><?php echo __( 'Pubblicazioni', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#p6"><span><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></span></a>
										</li>
										<li class="nav-item">
											<a class="nav-link link-100" href="#p6"><span><?php echo __( 'Eventi', 'design_laboratori_italia' ); ?></span></a>
										</li>
									</ul>
								</div>
							</div> <!-- menu_laterale -->
						</div>
					</nav>
				</div>
			</div> <!-- row -->
			<div class="col-12 col-lg-9 it-page-sections-container">

			<h3 class="it-page-section h4" id="p1"><?php echo __( 'Descrizione', 'design_laboratori_italia' ); ?></h3>

			<div class="row pb-3">
				<p>
					<?php
						$content = apply_filters( 'the_content', $post->post_content );
						echo $content;
					?>
				</p>
			</div>

			<!-- RESPONSABILE -->
			<?php $responsabili = get_field( 'responsabile_del_progetto' ); ?>
			<h3 class="it-page-section h4 pt-3" id="p2"><?php echo __( 'Responsabile', 'design_laboratori_italia' ); ?></h3>
			<?php
				get_template_part(
					'template-parts/common/sezione-persone',
					null,
					array(
						'section_id' => 'responsabile',
						'items'    => $responsabili,
					)
				);
			?>

			<!-- PARTECIPANTI -->
			<?php $partecipanti = get_field( 'persone' ); ?>
			<h3 class="it-page-section h4 pt-3" id="p3"><?php echo __( 'Partecipanti', 'design_laboratori_italia' ); ?></h3>
			<?php
				get_template_part(
					'template-parts/common/sezione-persone',
					null,
					array(
						'section_id' => 'partecipanti',
						'items'    => $partecipanti,
					)
				);
			?>

		<!-- INDIRIZZI DI RICERCA -->
		<h3 class="it-page-section h4 pt-3" id="p4"><?php echo __( 'Attività di ricerca', 'design_laboratori_italia' ); ?></h3>
		<?php
				get_template_part(
					'template-parts/common/sezione-indirizzidiricerca',
					null,
					array(
						'section_id' => 'indirizzi-di-ricerca',
						'items'    => array(),
					)
				);
		?>
					
		<h3 class="it-page-section pt-3 h4" id="p5"><?php echo __( 'Pubblicazioni', 'design_laboratori_italia' ); ?></h3>

			<!-- PUBBLICAZIONI -->
			<section id="pubblicazioni">    
				<div class="row pb-3">
								
					<div class="card-wrapper card-teaser-wrapper">
										<!--start card-->
											<div class="card card-teaser rounded shadow ">
											<div class="card-body">
												<h3 class="card-title h5 ">
													<svg class="icon">
														<use href="bootstrap-italia/svg/sprites.svg#it-note"></use>
													</svg>
													<a href="#">Titolo pubblicazione</a>
												</h3>
												<div class="card-text">
													<p>Elenco autori</p>
												</div>
											</div>
										</div>
											<!--end card-->
											<!--start card-->
											<div class="card card-teaser rounded shadow">
											<div class="card-body">
												<h3 class="card-title h5 ">
													<svg class="icon">
														<use href="bootstrap-italia/svg/sprites.svg#it-note"></use>
													</svg>
													<a href="#">Titolo pubblicazione 2</a>
												</h3>
												<div class="card-text">
													<p>Elenco autori</p>
												</div>
											</div>
										</div>
											<!--end card-->
											<!--start card-->
											<div class="card card-teaser rounded shadow">
											<div class="card-body">
												<h3 class="card-title h5 ">
													<svg class="icon">
														<use href="bootstrap-italia/svg/sprites.svg#it-note"></use>
													</svg>
													<a href="#">Titolo pubblicazione</a>
												</h3>
												<div class="card-text">
													<p>Elenco autori</p>
												</div>
											</div>
										</div>
											<!--end card-->
									</div>
								
				</div>
			</section>
			
		<h3 class="it-page-section h4 pt-3" id="p6"><?php echo __( 'Allegati', 'design_laboratori_italia' ); ?></h3>

			<section id="allegati">    
					<div class="row pb-3">
								
									<div class="card-wrapper card-teaser-wrapper">
										<!--start card-->
											<div class="card card-teaser rounded shadow ">
											<div class="card-body">
												<h3 class="card-title h5 ">
													<svg class="icon">
														<use href="bootstrap-italia/svg/sprites.svg#it-file-pdf"></use>
													</svg>
													<a href="#">Curriculum vitae&nbsp;</a>
												</h3>
											</div>
										</div>
											<!--end card-->
											<!--start card-->
											<div class="card card-teaser rounded shadow ">
											<div class="card-body">
												<h3 class="card-title h5 ">
													<svg class="icon">
														<use href="bootstrap-italia/svg/sprites.svg#it-file-pdf"></use>
													</svg>
													<a href="#">Elenco pubblicazioni PDF</a>
												</h3>
											</div>
										</div>
											<!--end card-->
									</div>
								
				</div>
			</section>

		<h3 class="it-page-section h4 pt-3" id="p7"><?php echo __( 'Eventi', 'design_laboratori_italia' ); ?></h3>

		<section id="eventi" >
						<div class="section-content">
								<div class="row pt-3">
											
											<div class="col-12 col-lg-4">
												<!--start card-->
												<div class="card-wrapper">
														<div class="card card-img no-after card-bg">
															<div class="img-responsive-wrapper">
																<div class="img-responsive img-responsive-panoramic">
																	<figure class="img-wrapper">
																		<img src="img/img-avatar-250x250.png" title="titolo immagine" alt="descrizione immagine">
																	</figure>
																	<div class="card-calendar d-flex flex-column justify-content-center">
																		<span class="card-date">30</span>
																		<span class="card-day">novembre</span>
																	</div>
																</div>
															</div>
															<div class="card-body p-4">
								<h3 class="card-title h4">Titolo evento</h3>
								<p class="card-text">Abstract della news su più righe con riduzione testo o utilizzo campo riassunto standard wordpress&nbsp;</p>
																	<a class="read-more" href="#">
																	<span class="text">Leggi di più</span>
																	<span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
																	<svg class="icon">
																		<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
																	</svg></a>
															</div>
														</div>
													</div>
												<!--end card-->
											</div>
											<div class="col-12 col-lg-4">
												<!--start card-->
												<div class="card-wrapper">
														<div class="card card-img no-after card-bg">
															<div class="img-responsive-wrapper">
																<div class="img-responsive img-responsive-panoramic">
																	<figure class="img-wrapper">
																		<img src="img/img-avatar-250x250.png" title="titolo immagine" alt="descrizione immagine">
																	</figure>
																	<div class="card-calendar d-flex flex-column justify-content-center">
																		<span class="card-date">23</span>
																		<span class="card-day">Dicembre</span>
																	</div>
																</div>
															</div>
															<div class="card-body p-4">
								<h3 class="card-title h4">Titolo evento 2</h3>
								<p class="card-text">Abstract della news su più righe con riduzione testo o utilizzo campo riassunto standard wordpress&nbsp;</p>
																	<a class="read-more" href="#">
																	<span class="text">Leggi di più</span>
																	<span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
																	<svg class="icon">
																		<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
																	</svg></a>
															</div>
														</div>
													</div>
												<!--end card-->
											</div>
										</div>
								
						</div>
				</section>
			
			
			</div>
		</div>
	</div> <!-- scheda_progetto -->

</main>


<?php
get_footer();
