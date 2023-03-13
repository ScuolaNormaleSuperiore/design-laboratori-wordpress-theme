<?php
/* Template Name: Ricerca.
 *$allcontentypes
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'SITESEARCH_CELLS_PER_PAGE', 10 );

// BEGIN preparazione dei parametri di ricerca.
$allcontentypes = dli_get_all_contenttypes();

if ( isset( $_GET['selected_contents'] ) ) {
	$selected_contents = $_GET['selected_contents'];
} else {
	$selected_contents = array();
}

if ( isset( $_GET['searchstring'] ) ) {
	$searchstring = $_GET['searchstring'];
} else {
	$searchstring = '';
}

if ( $searchstring !== '' ) {
	$the_query = dli_main_search_query(
		$selected_contents,
		$searchstring,
		SITESEARCH_CELLS_PER_PAGE
	);
	$num_results = $the_query->found_posts;
} else {
	$num_results = 0;
}
// END preparazione dei parametri di ricerca.

?>

<main id="main-container" class="main-container bluelectric">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- SEZIONE RICERCA NELL SITO -->
	<form action="." id="notizieform" method="GET">
	<section id="news" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">

				<!--COLONNA FILTRI -->
				<div class="col-12 col-lg-3 border-end">
					<div class="row pt-4">
					<?php
						if( count( $allcontentypes ) > 0 ) {
					?>
						<h3 class="h6 text-uppercase border-bottom"><?php echo __( 'Tipo di contenuto', 'design_laboratori_italia' ); ?></h3>
						<div>
								<?php
									foreach( $allcontentypes as $ct ) {
								?>
								<div class="form-check">
									<input type="checkbox" name="selected_contents[]" id="<?php echo $ct; ?>" 
										value="<?php echo $ct; ?>"
										<?php if ( in_array( $ct, $selected_contents ) ) { echo "checked='checked'"; } ?>
									>
									<label for="<?php echo $ct; ?>"><?php echo $ct; ?></label>
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
				<!--COLONNA FILTRI -->

				<!-- Inizio ELENCO NEWS -->
				<div class="col-12 col-lg-8">
						<div>
							<p>
								<span>
									<input type="text" id="searchstring" name="searchstring" placeholder="Testo da cercare..." ù
										value="<?php echo $searchstring; ?>" />
								</span>
							</p>
							<p>

								<button type="submit" class="btn btn-primary">
									<?php echo __( 'Cerca', 'design_laboratori_italia' ); ?>
								</button>

								<button type="button" class="btn btn-primary">
									<?php echo __( 'Reimposta filtri', 'design_laboratori_italia' ); ?>
								</button>
							</p>
							<p>
								<em>
									<span><?php echo __( 'Numero dei contenuti trovati', 'design_laboratori_italia' ); ?>:</span>
									<span><?php echo $num_results; ?></span>
								</em>
						</p>
						</div>
				<?php
				// The mani loop of the page.
				$pindex = 0;
				if ( ( $num_results > 0 ) && ( $searchstring !== '' ) ) {
				?>

				<?php
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
					?>
					<!-- begin row -->
					<div class="row pt-5">
					<?php
						$result = dli_format_search_result( $post )
					?>
						<!-- start card-->
						<div class="col-12 col-lg-12">
							<div class="card-wrapper">

							<p>
								<a href="<?php echo esc_url( $result['link'] ); ?>">
									<span><b><?php echo esc_attr( $result['title'] ); ?></b></span>
								</a>:
								<span><em><?php echo esc_attr( wp_trim_words( $result['description'] , DLI_ACF_SHORT_DESC_LENGTH ) ); ?></em></span>
							</p>

							</div>
						</div>
						<!--end card-->
					</div>
					<!-- end row -->
					<?php
					$pindex++;
					}
				} else {
					?>
				<div clas="row pt-2">
					<?php echo __( 'Non è stata trovato alcun contenuto che soddisfa i requisiti', 'design_laboratori_italia' ); ?>
				</div>
				<?php
					}
				 ?>


				</div> 
				<!-- Fine elenco news-->

			</div>
		</div>
	</section>
	</form>
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
