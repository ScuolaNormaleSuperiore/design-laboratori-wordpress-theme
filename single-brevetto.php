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
$dli_image_metadata = dli_get_image_metadata( $post, 'full' );
$dli_abstract       = ( '.' === $post->post_content ) ? '' : wpautop( do_shortcode( $post->post_content ) );
$dli_summary        = dli_get_field( 'sommario_elenco' );
$dli_note           = dli_get_field( 'note' );
$dli_stato          = dli_get_field( 'stato_legale_custom' );
$dli_titolari       = dli_get_field( 'titolari' );
$dli_inventori      = dli_get_field( 'inventori' );
$dli_inventori_ref  = dli_get_field( 'inventori_referenti' );
$dli_inventori_oth  = dli_get_field( 'altri_inventori' );
$dli_parts          = array_filter( array( $dli_inventori_ref, $dli_inventori, $dli_inventori_oth ) );
$dli_inventori      = implode( ', ', $dli_parts );
$dli_cod_brevetto   = dli_get_field( 'codice_brevetto' );
$dli_data_deposito  = dli_get_field( 'data_deposito' );
$dli_num_deposito   = dli_get_field( 'numero_deposito' );
$dli_video          = dli_get_field( 'video' );
$dli_area_tematica  = dli_get_post_main_category( $post, THEMATIC_AREA_TAXONOMY );
$dli_str_famiglia   = dli_get_field( 'famiglia' );
$dli_json_object    = json_decode( $dli_str_famiglia );
$dli_famiglie       = null;
if ( json_last_error() === JSON_ERROR_NONE ) {
	$dli_famiglie = $dli_json_object;
}
?>

<main id="main-container" role="main">

	<!-- BANNER BREVETTO: foto di copertina con overlay + breadcrumb in overlay
	     assoluto sopra la foto, stesso pattern di single-progetto.php. Niente
	     box scuro locale (bg-dark) né chip area tematica nell'hero: pattern
	     del prototipo statico (sf-scheda-brevetto.html), leggibilità già
	     garantita dall'overlay + text-shadow globali. -->
	<section class="it-hero-wrapper it-hero-small-size it-dark it-overlay">
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
						alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
						title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>">
				</div>
			</div>
		</div>
		<div class="container it-hero-breadcrumb">
			<div class="row">
				<div class="col-12">
					<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper">
						<h2><?php echo esc_html( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block"><?php echo wp_kses_post( $dli_summary ); ?></p>
						<?php if ( $dli_image_metadata['image_caption'] ) : ?>
							<p class="figure-caption mt-3 text-light"><?php echo esc_html( $dli_image_metadata['image_caption'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO BREVETTO -->
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
									<title><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></title>
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>" xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left' ); ?>"></use>
								</svg>
								<span><?php echo esc_html__( 'Indietro', 'design_laboratori_italia' ); ?></span>
							</a>
							<div class="menu-wrapper">
								<div class="link-list-wrapper">
									<h3><?php echo esc_html__( 'Dettagli del brevetto', 'design_laboratori_italia' ); ?></h3>
									<div class="progress">
										<div class="progress-bar it-navscroll-progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
										</div>
									</div>
									<ul class="link-list">
										<?php
										if ( $dli_abstract ) {
											?>
										<li class="nav-item">
											<a class="nav-link active" href="#abstract">
												<span><?php echo esc_html__( 'Abstract', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_titolari ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#titolari">
												<span><?php echo esc_html__( 'Titolari', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_inventori ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#inventori">
												<span><?php echo esc_html__( 'Inventori', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_num_deposito || $dli_data_deposito ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#domanda">
												<span><?php echo esc_html__( 'Domanda di priorità', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_famiglie && ( ! empty( $dli_famiglie ) ) ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#famiglia">
												<span><?php echo esc_html__( 'Famiglia brevettuale', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_stato ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#status">
												<span><?php echo esc_html__( 'Stato legale', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_area_tematica ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#areatematica">
												<span><?php echo esc_html__( 'Area tematica', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_note ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#altreinformazioni">
												<span><?php echo esc_html__( 'Altre informazioni', 'design_laboratori_italia' ); ?></span>
											</a>
										</li>
											<?php
										}
										if ( $dli_video ) {
											?>
										<li class="nav-item">
											<a class="nav-link" href="#video">
											<span><?php echo esc_html__( 'Video', 'design_laboratori_italia' ); ?></span>
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
			<div class="col-12 col-lg-8 offset-lg-1 it-page-sections-container">
				<?php if ( $dli_abstract ) : ?>
					<h3 class="it-page-section h4 visually-hidden" id="abstract"><?php echo esc_html__( 'Abstract brevetto', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_abstract ); ?>
				<?php endif; ?>

				<?php if ( $dli_titolari ) : ?>
					<h3 class="it-page-section h4 pt-3" id="titolari"><?php echo esc_html__( 'Titolari', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_titolari ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_inventori ) : ?>
					<h3 class="it-page-section h4 pt-3" id="inventori"><?php echo esc_html__( 'Inventori', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_inventori ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_num_deposito || $dli_data_deposito ) : ?>
					<h3 class="it-page-section h4 pt-3" id="domanda"><?php echo esc_html__( 'Domanda di priorità', 'design_laboratori_italia' ); ?></h3>
					<p>
						<?php echo esc_html__( 'Numero deposito', 'design_laboratori_italia' ); ?>: <?php echo esc_html( $dli_num_deposito ); ?>
						<br>
						<?php echo esc_html__( 'Data deposito', 'design_laboratori_italia' ); ?>: <?php echo esc_html( $dli_data_deposito ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $dli_famiglie && ! empty( $dli_famiglie ) ) : ?>
					<h3 class="it-page-section h4 pt-3" id="famiglia"><?php echo esc_html__( 'Famiglia brevettuale', 'design_laboratori_italia' ); ?></h3>
					<div class="table-responsive">
						<table class="table table-striped">
							<caption class="visually-hidden"><?php echo esc_html__( 'Famiglia brevettuale', 'design_laboratori_italia' ); ?></caption>
							<thead>
								<tr>
									<th scope="col"><?php echo esc_html__( 'Numero di deposito', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php echo esc_html__( 'Data deposito', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php echo esc_html__( 'Titolo', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php echo esc_html__( 'Nazione deposito', 'design_laboratori_italia' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $dli_famiglie as $dli_fam ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( $dli_fam->numero_deposito ); ?></th>
										<td><?php echo esc_html( $dli_fam->data_deposito ); ?></td>
										<td><?php echo esc_html( dli_decode_unicode_string( $dli_fam->titolo ) ); ?></td>
										<td><?php echo esc_html( $dli_fam->nazione_deposito ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>

				<?php if ( $dli_stato ) : ?>
					<h3 class="it-page-section h4 pt-3" id="status"><?php echo esc_html__( 'Stato legale', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_stato ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_area_tematica ) : ?>
					<h3 class="it-page-section h4 pt-3" id="areatematica"><?php echo esc_html__( 'Area tematica', 'design_laboratori_italia' ); ?></h3>
					<p><?php echo esc_html( $dli_area_tematica['title'] ); ?></p>
				<?php endif; ?>

				<?php if ( $dli_note ) : ?>
					<h3 class="it-page-section h4 pt-3" id="altreinformazioni"><?php echo esc_html__( 'Altre informazioni', 'design_laboratori_italia' ); ?></h3>
					<?php echo wp_kses_post( $dli_note ); ?>
				<?php endif; ?>

				<?php if ( $dli_video ) : ?>
					<h3 class="it-page-section h4 pt-3" id="video"><?php echo esc_html__( 'Video', 'design_laboratori_italia' ); ?></h3>
					<?php
					get_template_part(
						'template-parts/common/sezione-video',
						null,
						array(
							'video'       => $dli_video,
							'video_text'  => null,
							'video_title' => get_the_title(),
						)
					);
					?>
				<?php endif; ?>
			</div>
		</div> <!-- END row -->
	</div> <!-- END container -->
</main>

<?php
get_footer();
