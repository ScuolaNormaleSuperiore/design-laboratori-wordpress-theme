<?php
/**
 * Detail page for the post-type: brevetto.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
$image_metadata = dli_get_image_metadata( $post, 'full' );
$abstract       = ( $post->post_content === '.' ) ? '' : apply_filters( 'the_content', $post->post_content );
$summary        = dli_get_field( 'sommario_elenco' );
$note           = dli_get_field( 'note' );
$stato          = dli_get_field( 'stato_legale' );
$titolari       = dli_get_field( 'titolari' );
$inventori      = dli_get_field( 'inventori' );
$cod_brevetto   = dli_get_field( 'codice_brevetto' );
$data_deposito  = dli_get_field( 'data_deposito' );
$num_deposito   = dli_get_field( 'numero_deposito' );
$area_tematica  = dli_get_post_main_category( $post, THEMATIC_AREA_TAXONOMY );
$str_famiglia   = dli_get_field( 'famiglia' );
$jsonObject     = json_decode( $str_famiglia );
$famiglia       = null;
if ( json_last_error() === JSON_ERROR_NONE ) {
	$famiglia = $jsonObject;
}
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>


	<!-- INIZIO BANNER HERO -->
	<section class="it-hero-wrapper it-dark it-overlay it-hero-small-size"> 
		<!-- - img-->
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo $image_metadata['image_url']; ?>"
						alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>" 
						title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
						class="d-block mx-lg-auto img-fluid figure-img" loading="lazy">
					>
					<?php
							if( $image_metadata['image_caption'] ) {
						?>
							<figcaption class="figure-caption"><?php echo esc_attr( $image_metadata['image_caption'] ); ?></figcaption>
						<?php
							}
					?>
				</div>
			</div>
		</div>
		<!-- - texts-->
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<span class="it-Categoria"></span>
						<h2><?php echo get_the_title(); ?></h2>
						<p class="d-none d-lg-block">
						<?php echo esc_html( $summary ); ?>
						</p>
						<!-- categorie -->
						<div class="chip chip-primary chip-lg chip-simple border-light mt-3">
							<?php
								if ( ! empty( $area_tematica ) ) {
							?>
							<span class="chip-label text-light">
								<a href="<?php echo esc_url( site_url() . '/brevetti?thematic_area[]=' . $area_tematica['id'] );?>" class="text-white text-decoration-white">
									<?php echo esc_attr( $area_tematica['title'] ); ?>
								</a>
							</span>
							<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE BANNER HERO -->


		<!-- BODY EVENTO -->
		<div class="container py-lg-5">
		<div class="row">

			<!-- Dettagli dell'evento -->
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
							> <span class="it-list"></span></button>
						<div class="progress custom-navbar-progressbar">
							<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="navbar-collapsable" id="navbarNav">
							<div class="overlay"></div>
							<a class="it-back-button" href="#" role="button">
								<svg class="icon icon-sm icon-primary align-top">
									<title><?php echo __( 'Indietro', 'design_laboratori_italia' ); ?></title>
									<use href="bootstrap-italia/svg/sprites.svg#it-chevron-left" xlink:href="bootstrap-italia/svg/sprites.svg#it-chevron-left"></use>
								</svg>
								<span><?php echo __( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo __( 'IL BREVETTO', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
										</div>
									</div>
									<ul class="link-list">
										<?php
										if ( $abstract ) {
										?>
										<li class="nav-item">
											<a class="nav-link active" href="#abstract">
												<span><?php echo __( 'Abstract', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $titolari ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#titolari">
												<span><?php echo __( 'Titolari', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $inventori ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#inventori">
												<span><?php echo __( 'Inventori', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $num_deposito || $data_deposito ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#domanda">
												<span><?php echo __( 'Domanda di priorità', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $famiglia && ( ! empty( $famiglia ) ) ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#famiglia">
												<span><?php echo __( 'Famiglia brevettuale', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $stato ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#status">
												<span><?php echo __( 'Status', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $area_tematica  ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#areatematica">
												<span><?php echo __( 'Area tematica', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										if ( $note ) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="#altreinformazioni">
												<span><?php echo __( 'Altre informazioni', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
										<?php
										}
										?>
									</ul>
								</div>
							</div>
						</div>
					</nav>
				</div>
			</div>

			<!-- DESCRIZIONE -->
			<div class="col-12 col-lg-9 it-page-sections-container">
				<?php
				if ( $abstract ) {
				?>
					<article id="abstract" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="it-page-section h4 visually-hidden"><?php echo __( 'Abstract brevetto', 'design_laboratori_italia' ); ?></h3>
						<p>
							<?php echo $abstract; ?>
						</p>
					
					</article>
				<?php
				}
				?>
				<!-- TTOLARI -->
				<?php
				if ( $titolari ) {
				?>
					<article id="titolari" class="it-page-section mb-4 anchor-offset clearfix">
						<h3 class="h4"><?php echo __( 'Titolari', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo esc_attr( $titolari ) ; ?></p>
					</article>
				<?php
				}
				if ( $inventori ) {
				?>
				<!-- inventori -->
				<article id="inventori" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Inventori', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_attr( $inventori ) ; ?></p>
				</article>
				<?php
				}
				if ( $num_deposito || $data_deposito ) {
				?>
				<!-- Deposito -->
				<article id="domanda" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Domanda di priorità', 'design_laboratori_italia' ); ?></h3>
					<p>
					<?php echo __( 'Numero deposito', 'design_laboratori_italia' ); ?>: <?php echo esc_attr( $num_deposito ) ; ?>
						<br/>
					<?php echo __( 'Data deposito', 'design_laboratori_italia' ); ?>: <?php echo esc_attr( $data_deposito ) ; ?>
					</p>
				</article>
				<?php
				}
				if ( $stato ) {
				?>
					<!-- Status -->
				<article id="status" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Status', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_attr( $stato ) ; ?></p>
				</article>
				<?php
				}
				if ( $famiglia && ( ! empty ( $famiglia ) ) ) {
				?>
				<article id="famiglia" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Famiglia brevettuale', 'design_laboratori_italia' ); ?></h3>
					<table class="table table-striped">
						<thead>
							<tr> 
								<th scope="col"><?php echo __( 'Numero di deposito', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php echo __( 'Data deposito', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php echo __( 'Titolo', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php echo __( 'Nazione deposito', 'design_laboratori_italia' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row"><?php echo $famiglia[0]->numero_deposito; ?></th>
								<td><?php echo $famiglia[0]->data_deposito; ?></td>
								<td><?php echo $famiglia[0]->titolo; ?></td>
								<td><?php echo $famiglia[0]->nazione_deposito; ?></td>
							</tr>
						</tbody>
					</table>
				</article>
				<?php
				}
				if ( $area_tematica ) {
				?>
				<!-- AREA TEMATICA -->
				<article id="areatematica" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Area tematica', 'design_laboratori_italia' ); ?></h3>
						<p><?php echo $area_tematica['title']; ?></p>
				</article>
				<?php
				}
				if ( $note ) {
				?>
				<!-- ALTRE INFORMAZIONI -->
				<article id="altreinformazioni" class="it-page-section mb-4 anchor-offset clearfix">
					<h3 class="h4"><?php echo __( 'Altre informazioni', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo $note; ?></p>
				</article>
				<?php
				}
				?>
			</div>

		</div> <!-- END row -->
	</div> <!-- END container -->
	
</main>


<?php
get_footer();
