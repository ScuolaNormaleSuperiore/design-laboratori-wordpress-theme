<?php
/* Template Name: Ricerca
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'SITESEARCH_CELLS_PER_PAGE', 10 );

// BEGIN preparazione dei parametri di ricerca.
$allcontentypes = dli_get_all_contenttypes_with_results();
$num_results = 0;

if ( isset( $_GET['isreset'] ) && ( sanitize_text_field( $_GET['isreset'] ) === 'yes' ) ) {
	$selected_contents = array();
	$searchstring      = '';
} else {
	if ( isset( $_GET['selected_contents'] ) ) {
		$selected_contents = $_GET['selected_contents'];
	} else {
		$selected_contents = array();
	}
	if ( ! is_array( $selected_contents ) ) {
		$selected_contents = array();
	}
	if ( isset( $_GET['searchstring'] ) ) {
		$searchstring = sanitize_text_field( $_GET['searchstring'] );
	} else {
		$searchstring = '';
	}
}

$the_query = null;

if ( '' !== $searchstring ) {
	// Verifica del NONCE.
	if ( isset( $_GET['cercasito_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( $_GET['cercasito_nonce_field'] ), 'sf_cercasito_nonce' ) ) {
		$the_query = dli_main_search_query(
			$selected_contents,
			$searchstring,
			SITESEARCH_CELLS_PER_PAGE
		);
		$num_results = $the_query->found_posts;
	}
} else {
	$num_results = 0;
}
// END preparazione dei parametri di ricerca.

?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- SEZIONE BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<FORM action="." id="ricercasitoform" method="GET">
		<?php wp_nonce_field( 'sf_cercasito_nonce', 'cercasito_nonce_field' ); ?>

	<!-- SEZIONE BANNER -->
	<section id="banner-cerca"  class="bg-banner-cerca">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="pt-0 pb-0"><?php echo esc_html( __( 'Cerca nel sito', 'design_laboratori_italia' ) ); ?></h2>
					<div class="row m-0">
						<div class="form-group col-md-12 mb-4 text-left">
							<label class="active visually-hidden" for="searchstring">
								<?php echo esc_html( __( 'Cerca nel sito', 'design_laboratori_italia' ) ); ?>
							</label>
							<input type="text" id="searchstring" name="searchstring" class="form-control" 
								value="<?php echo esc_attr( $searchstring ? $searchstring : '' );  ?>"
								placeholder="<?php echo esc_html( __( 'Inserisci il testo da cercare', 'design_laboratori_italia' ) ); ?>">
							<input type="hidden" name="isreset" id="isreset" value=""/>
						</div>
					</div>
					<div class="row">
							<div class="form-group col text-left ps-4 mb-2">
								<button type="reset" value="reset" onclick="resetForm('ricercasitoform', 'isreset');" class="btn btn-outline-primary">
									<?php echo esc_html( __( 'Cancella', 'design_laboratori_italia' ) ); ?>
								</button>
								<button type="submit" value="submit" class="btn btn-primary"><?php echo esc_html( __( 'Cerca', 'design_laboratori_italia' ) ); ?></button>
							</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- SEZIONE RICERCA NEL SITO -->
	<section id="RISULTATI" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">

				<!--COLONNA FILTRI -->
				<div class="col-12 col-lg-3 border-end">
					<div class="row pt-4">
					<?php
						if( count( $allcontentypes ) > 0 ) {
					?>
						<h3 class="h6 text-uppercase border-bottom">
							<?php echo esc_html( __( 'Filtra per tipo di contenuto', 'design_laboratori_italia' ) ); ?>
						</h3>
						<div>
								<?php
									foreach( $allcontentypes as $ct ) {
								?>
								<div class="form-check">
									<input type="checkbox" name="selected_contents[]" id="<?php echo esc_attr( $ct ); ?>" 
										value="<?php echo esc_attr( $ct ); ?>"
										<?php if (count( $selected_contents ) > 0 && in_array( $ct, $selected_contents ) ) {
											 echo "checked='checked'";
										} ?>
									>
									<label for="<?php echo esc_attr( $ct ) ; ?>">
										<?php echo esc_html( __( ucfirst( str_replace("-", " ", $ct ) ), 'design_laboratori_italia' ) ); ?>
									</label>
								</div>
								<?php
									}
								?>
						</div>
					<?php
					}
					?>
					</div>
				</div>

				<!-- Inizio ELENCO RISULTATI -->
				<div class="col-12 col-lg-8">
						<div class="row ps-4">
							<p>
								<em>
									<span><?php echo esc_html( __( 'Risultati', 'design_laboratori_italia' ) ); ?>:</span>
									<span><?php echo esc_attr( $num_results ); ?></span>
								</em>
						</p>
						</div>
				<?php
				// The main loop of the page.
				$pindex = 0;
				if ( ( $num_results > 0 ) && ( $searchstring !== '' ) ) {
				?>
				<?php
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
					?>
					<!-- begin row -->
					<div class="row">
					<?php
						$result = dli_format_search_result( $post )
					?>
						<!-- start card-->
						<div class="col-12 col-lg-12">
							<div class="card-wrapper ">
								<div class="card">
									<div class="card-body mb-0">
										<?php
										 if ( $result['image'] ) {
										?>
										<img src="<?php echo esc_url( $result['image'] ); ?>"
											height="100"
											width="100" 
											class="img-thumbnail float-sm-start me-2 text-nowrap"
											title="<?php echo esc_attr( $result['image_title'] ); ?>"
											alt="<?php echo esc_attr( $result['image_alt'] ); ?>"
										/>
										<?php
										 }
										?>
										<span class="text" style="text-transform: uppercase;">
											<a class="text-decoration-none" href="<?php echo esc_url( $result['link_category'] ); ?>"><?php echo esc_attr( $result['type'] ); ?></a>
										</span>
										<span>&nbsp;-&nbsp;</span>
										<a class="text-decoration-none" href="<?php echo esc_url( $result['link'] ); ?>">
											<h3 class="card-title h5"><?php echo esc_attr( $result['title'] ); ?></h3>
										</a>
										<p class="card-text"><?php echo esc_attr( wp_trim_words( $result['description'] , DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
									</div>
								</div>
							</div>
						</div>
						<!--end card-->
					</div>
					<!-- end row -->
					<?php
					$pindex++;
					}
				}
				?>
				</div>
				<!-- FINE elenco RISULTATI-->

			</div>
		</div>
	</section>

	</FORM>

		<!-- RESTORE ORIGINAL Post Data -->
		<?php
		wp_reset_postdata();
	?>

	<!-- PAGINAZIONE -->
	<?php
		get_template_part(
			'template-parts/common/paginazione',
			null,
			array(
				'query' => $the_query,
			)
		);
	?>

</main>

<?php
get_footer();
