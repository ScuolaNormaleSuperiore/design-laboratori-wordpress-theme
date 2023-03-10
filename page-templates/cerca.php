<?php
/* Template Name: Ricerca.
 *$allcontentypes
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'SITESEARCH_CELLS_PER_PAGE', 10 );


$allcontentypes = dli_get_all_contenttypes();

if ( isset( $_GET['selected_contents'] ) ){
	$selected_contents = $_GET['selected_contents'];
} else {
	$selected_contents = array();
}
$selected_contents = $allcontentypes; // @TODO: Remove this line.

if ( isset( $_GET['searchstring'] ) ){
	$searchstring = $_GET['searchstring'];
} else {
	$searchstring = '';
}

// @TODO: Far ritornare un wrapper !!!
$the_query = dli_main_search_query(
	$selected_contents,
	$searchstring,
	SITESEARCH_CELLS_PER_PAGE
);

$num_results    = $the_query->found_posts;
?>

<main id="main-container" class="main-container bluelectric">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>


	<!-- SEZIONE RICERCA NELL SITO -->
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
							<form action="." id="notizieform" method="GET">
								<?php
									foreach( $allcontentypes as $ct ) {
								?>
								<div class="form-check">
									<input type="checkbox" name="cat[]" id="<?php echo $ct; ?>" 
										value="<?php echo $ct; ?>" onChange="this.form.submit()"
										<?php if ( in_array( $ct, $selected_contents ) ) { echo "checked='checked'"; } ?>
									>
									<label for="<?php echo $ct; ?>"><?php echo $ct; ?></label>
								</div>
								<?php
									}
								?>
							</form>
						</div>
					<?php
					}
					?>
					</div>
				</div>
				<!--COLONNA FILTRI -->

				<?php
				// The mani loop of the page.
				$pindex = 0;
				if ( $num_results ) {
				?>
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
							<button type="button" class="btn btn-primary">Cerca</button>
							<button type="button" class="btn btn-primary">Reset</button>
						</p>
						<p>
							<em>
								<span><?php echo __( 'Numero dei contenuti trovati', 'design_laboratori_italia' ); ?>:</span>
								<span><?php echo $num_results; ?></span>
							</em>
					</p>
					</div>

				<?php
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
					?>
					<!-- begin row -->
					<div class="row pt-5">
					<?php
						$post_id  = get_the_ID();
						$descr1   = dli_get_field( 'descrizione_breve' );
						$descr2   = get_the_content();
						$text     =  $descr1 ? $descr1 : $descr2;
						$link     = get_the_permalink();
					?>
						<!-- start card-->
						<div class="col-12 col-lg-12">
							<div class="card-wrapper">

							<p>
								<a href="<?php echo esc_url( $link ); ?>">
									<span><b><?php echo esc_attr( get_the_title() ); ?></b></span>
								</a>:
								<span><em><?php echo esc_attr( wp_trim_words( $text , DLI_ACF_SHORT_DESC_LENGTH ) ); ?></em></span>
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
