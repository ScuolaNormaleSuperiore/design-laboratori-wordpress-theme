<?php
/* Template Name: Risorse-Tecniche
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$selected_year   = '';
$selected_types  = array();
$search_string   = '';
$all_types       = dli_get_all_categories_by_ct( RT_TYPE_TAXONOMY, TECHNICAL_RESOURCE_POST_TYPE );
$all_type_ids    = $all_types ? array_map( function( $item ) { return $item['id']; }, $all_types ) : [];
$all_years       = DLI_ContentsManager::dli_get_all_technical_res_years();
$per_page        = strval( DLI_PER_PAGE_BIG );
$per_page_values = DLI_PER_PAGE_VALUES;

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	$per_page = sanitize_text_field( $_GET['per_page'] );
}
if ( isset( $_GET['type_technical_resource'] ) && is_array(  $_GET['type_technical_resource'] )  ) {
	foreach ($_GET['type_technical_resource'] as $ar ) {
		array_push( $selected_types, sanitize_text_field( $ar ) );
	}
}
if ( isset( $_GET['search_string'] ) ) {
	$search_string = sanitize_text_field( $_GET['search_string'] );
}
if ( isset( $_GET['acquisition_year'] ) && is_numeric( $_GET['acquisition_year'] ) ) {
	$selected_year = sanitize_text_field( $_GET['acquisition_year'] );
}
if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ) {
	$paged = 1;
} else {
	$paged = get_query_var( 'paged', 1 );
}

$params = array(
	'search_string'           => $search_string,
	'type_technical_resource' => $selected_types? $selected_types: null,
	'acquisition_year'        => $selected_year ? array( $selected_year ) : null,
	'per_page'               => $per_page,
	'paged'                  => $paged,
);
$the_query   = DLI_ContentsManager::get_technical_resource_data_query( $params );
$num_results = $the_query->found_posts;

?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER -->
	<?php get_template_part( 'template-parts/hero/risorse-tecniche' ); ?>
	
	<!-- ELENCO RISORSE TECNICHE -->
	<section id="technical-resources">
		<div class="container p-5">
			<!-- inizio row principale -->
			<div class="row">
				<div class="col-12 col-lg-3 border-end pb-3">
					<form action="<?php $_SERVER['PHP_SELF']; ?>" id="techresorcesform" method="GET">
						<!--COLONNA FILTRI -->
						
						<!-- FILTRO PER ANNO -->
						<?php
						if ( count( $all_years ) > 0 ) {
						?>
						<div class="row pt-3">
							<h3 class="h6 text-uppercase border-bottom"><?php echo __( 'Anno di acquisizione', 'design_laboratori_italia' ); ?></h3>
							<div class="select-wrapper">
								<label for="acquisition_year" class="visually-hidden"><?php echo __( 'Anno di acquisizione', 'design_laboratori_italia' ); ?></label>
								<select id="acquisition_year" name="acquisition_year">
									<option selected="" value=""><?php echo __( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
									<?php
										foreach ( $all_years as $acquisition_year ) {
											$selected = ( $acquisition_year === $selected_year );
									?>
									<option value="<?php echo $acquisition_year; ?>"
										<?php if ( $selected ) { echo 'selected'; } ?>
									><?php echo $acquisition_year; ?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<?php
						}
						?>

						<!-- FILTRO PER TIPO DI RISORSA -->
						<?php
						if ( count( $all_types ) > 0 ) {
						?>
						<div class="row pt-5">
							<h3 class="h6 text-uppercase border-bottom">
								<?php echo __( 'Tipo di risorsa', 'design_laboratori_italia' ); ?>
							</h3>
							<div>
								<?php
								foreach ( $all_types as $type_technical_resource ) {
								?>
								<div class="form-check">
								<input type="checkbox" name="type_technical_resource[]" id="<?php echo $type_technical_resource['slug']; ?>"
										value="<?php echo $type_technical_resource['id']; ?>"
										<?php if ( in_array( $type_technical_resource['id'], $selected_types) ) { echo "checked='checked'"; } ?>
									>
									<label for="<?php echo $type_technical_resource['slug']; ?>"><?php echo $type_technical_resource['name']; ?></label>
								</div>
								<?php
								}
								?>
							</div>
						</div>
						<?php
						}
						?>

						<!-- FILTRO PER RICERCA LIBERA -->
						<!--fine filtri -->
						<div class="row mt-3 pt-4">
							<h3 class="h6 text-uppercase border-bottom vjs-hidden">
								<?php echo __( 'Ricerca libera', 'design_laboratori_italia' ); ?>
							</h3>
							<div>
								<div class="form-group">
									<div class="input-group"> <span class="input-group-text">
										<svg class="icon icon-sm">
											<title><?php echo __( 'Cerca contenuto', 'design_laboratori_italia' ); ?></title>
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-search'; ?>"></use>
										</svg>
										</span>
										<label for="search_string"><?php echo __( 'Cerca contenuto', 'design_laboratori_italia' ); ?></label>
										<input type="text" class="form-control" id="search_string" name="search_string" value="<?php echo esc_attr( $search_string ); ?>" />
									</div>
								</div>
							</div>
						</div>
						
						<!-- Pulsante filtro-->
						<div class="row text-center w-100">
							<button type="submit" style="margin: auto;" class="w-50 btn btn-primary">
								<?php echo __( 'Filtra', 'design_laboratori_italia' ); ?>
							</button>
						</div>
					</form>
				</div>

				<!-- ELENCO RISORSE TECNICHE -->
				<?php
				// The mani loop of the page.
				$pindex = 0;
				if ( $num_results > 0 ) {
				?>
					<!-- inizio contenitore spinoff -->
					<div class="col-12 col-lg-8">
						<?php
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							$result = dli_get_post_wrapper( $post, 'medium' );
						?>
						<!-- row -->
						<div class="row">
							
								<!--start card-->
								<div class="card-wrapper mb-4">
									<div class="card card-teaser rounded shadow">
										<div class="card-body">
											<?php
											if ( $result['image_url'] ) {
											?>
												<img
													src="<?php echo esc_url( $result['image_url'] ); ?>"
													height="150"
													width="150"
													class="img-fluid  float-start me-2 text-nowrap"
													title="<?php echo esc_attr( $result['image_title'] ); ?>"
													alt="<?php echo esc_attr( $result['image_alt'] ); ?>">
												<?php
												}
												?>

											<h3 class="card-title cardTitlecustomSpacing h5">
												<a href="<?php echo esc_url( $result['link'] ); ?>">
													<?php echo esc_attr( $result['title'] ); ?>
											</a>
											</h3>
											<p class="card-text">
												<?php echo esc_attr( wp_trim_words( $result['description'] , DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
											</p>
										</div>
									</div>
								</div>
								<!--end card-->

						</div>
						<!-- end row -->
						<?php
							$pindex++;
						}
						?>
					</div>
					<!-- end contenitore spinoff -->
				<?php
				} else {
				?>
				<div class="col-12 col-lg-8">
					<div clas="row pt-2">
						<?php echo __( 'Non è stata trovata alcuna risorsa tecnica', 'design_laboratori_italia' ); ?>
					</div>
				</div>
				<?php
				}
				?>
			</div>
			<!-- end row principale -->
		</div> <!-- end container -->
	</section>

	<!-- RESTORE ORIGINAL Post Data -->
	<?php
	wp_reset_postdata();
	?>
	
	<!-- PAGINAZIONE con selettore  -->
	<?php
		get_template_part(
			'template-parts/common/paginazione',
			null,
			array(
				'query'           => $the_query,
				'per_page'        => $per_page,
				'per_page_values' => $per_page_values,
			)
		);
	?>
	
</main>

<?php
get_footer();
