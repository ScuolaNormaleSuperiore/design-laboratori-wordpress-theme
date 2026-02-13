<?php
/* Template Name: Brevetti
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'DLI_PATENT_CELLS_PER_ROW', 2 );

$selected_year   = '';
$selected_areas  = array();
$search_string   = '';
$all_areas       = dli_get_all_categories_by_ct( THEMATIC_AREA_TAXONOMY, PATENT_POST_TYPE );
$all_area_ids    = $all_areas ? array_map( function( $item ) { return $item['id']; }, $all_areas ) : [];
$all_years       = DLI_ContentsManager::dli_get_all_patent_years();
$per_page        = strval( DLI_PER_PAGE );
$per_page_values = DLI_PER_PAGE_VALUES;

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	$per_page = sanitize_text_field( $_GET['per_page'] );
}
if ( isset( $_GET['thematic_area'] ) && is_array(  $_GET['thematic_area'] )  ) {
	foreach ($_GET['thematic_area'] as $ar ) {
		array_push( $selected_areas, sanitize_text_field( $ar ) );
	}
}
if ( isset( $_GET['search_string'] ) ) {
	$search_string = sanitize_text_field( $_GET['search_string'] );
}
if ( isset( $_GET['deposit_year'] ) && is_numeric( $_GET['deposit_year'] ) ) {
	$selected_year = sanitize_text_field( $_GET['deposit_year'] );
}
$paged = absint( get_query_var( 'paged' ) );
if ( 0 === $paged ) {
	$paged = absint( get_query_var( 'page' ) );
}
if ( 0 === $paged ) {
	$paged = 1;
}

$params = array(
	'search_string' => $search_string,
	'thematic_area' => $selected_areas ? $selected_areas : $all_area_ids,
	'deposit_year'  => $selected_year ? array( $selected_year ) : $all_years,
	'per_page'      => $per_page,
	'paged'         => $paged,
);
$the_query     = DLI_ContentsManager::get_patent_data_query( $params );
$num_results   = $the_query->found_posts;

?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER BREVETTI -->
	<?php get_template_part( 'template-parts/hero/brevetti' ); ?>

	<!-- ELENCO BREVETTI -->
	<section id="brevetti">
		<div class="container p-5">
			<!-- inizio row principale -->
			<div class="row">
				<div class="col-12 col-lg-3 border-end pb-3">
					<form action="<?php $_SERVER['PHP_SELF']; ?>" id="brevettiform" method="GET">
						<!--COLONNA FILTRI -->
						
						<!-- FILTRO PER ANNO -->
						<div class="row pt-3">
							<h3 class="h6 text-uppercase border-bottom"><?php echo __( 'Anno di deposito', 'design_laboratori_italia' ); ?></h3>
							<div class="select-wrapper">
								<label for="deposit_year" class="visually-hidden"><?php echo __( 'Anno di deposito', 'design_laboratori_italia' ); ?></label>
								<select id="deposit_year" name="deposit_year">
									<option selected="" value=""><?php echo __( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
									<?php
										foreach ( $all_years as $deposit_year ) {
											$selected = ( $deposit_year === $selected_year );
									?>
									<option value="<?php echo esc_attr( $deposit_year ); ?>"
										<?php if ( $selected ) { echo 'selected'; } ?>
									><?php echo esc_html( $deposit_year ); ?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>

						<!-- FILTRO PER AREA TEMATICA - Se esiste -->
						<div class="row pt-5">
							<h3 class="h6 text-uppercase border-bottom">
								<?php echo __( 'Area tematica', 'design_laboratori_italia' ); ?>
							</h3>
							<div>
								<?php
								foreach ( $all_areas as $thematic_area ) {
								?>
								<div class="form-check">
								<input type="checkbox" name="thematic_area[]" id="<?php echo esc_attr( $thematic_area['slug'] ); ?>"
										value="<?php echo esc_attr( $thematic_area['id'] ); ?>"
										<?php if ( in_array( $thematic_area['id'], $selected_areas ) ) { echo "checked='checked'"; } ?>
									>
									<label for="<?php echo esc_attr( $thematic_area['slug'] ); ?>"><?php echo esc_html( $thematic_area['name'] ); ?></label>
								</div>
								<?php
								}
								?>
							</div>
						</div>

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

				<!-- ELENCO BREVETTI 6 per pagina -->
				<?php
				// The mani loop of the page.
				$pindex = 0;
				if ( $num_results > 0 ) {
				?>
					<!-- inizio contenitore brevetti -->
					<div class="col-12 col-lg-9 pt-3">
						<?php
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							if ( ( $pindex % DLI_PATENT_CELLS_PER_ROW ) == 0 ) {
						?>
						<div class="row pb-5">
							<!-- row -->
							<?php
								}
								$post_id        = get_the_ID();
								$summary        = dli_get_field( 'sommario_elenco' );
								$stato          = dli_get_field( 'stato_legale_custom' );
								$titolari       = dli_get_field( 'titolari' );
								$area_tematica  = dli_get_post_main_category( $post, THEMATIC_AREA_TAXONOMY );
								$image_metadata = dli_get_image_metadata( $post, 'item-card-list' );
							?>
							<div class="col-12 col-lg-6"> 
								<!--start card-->
								<div class="card-wrapper shadow">
									<div class="card card-img no-after">
									<?php
									if ( $image_metadata['image_url'] ) {
									?>
										<div class="img-responsive-wrapper">
											<div class="img-responsive img-responsive-panoramic">
												<figure class="img-wrapper">
													<img src="<?php echo esc_url( $image_metadata['image_url'] ); ?>" 
													title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
													alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
												</figure>
											</div>
										</div>
									<?php
									}
									?>
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5"><?php echo esc_html( get_the_title() ); ?></h3>
											<p class="card-text font-serif"><?php echo wp_kses_post( $summary ); ?></p>
											<p class="card-text font-serif titolari">
												<em><?php echo esc_html( $titolari ); ?></em>
											</p>
											<p class="card-text font-serif area">
												<?php
												if ($area_tematica && array_key_exists('title', $area_tematica ) ) {
												?>
												<strong><?php echo esc_html( $area_tematica['title'] ); ?></strong> - 
												<?php
												}
												?>
												<?php echo esc_html( $stato ); ?>
											</p>
											<div class="pt-5">
												<a class="read-more" href="<?php echo esc_url( get_permalink() ); ?>">
													<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
													<svg class="icon">
														<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
														<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right'; ?>"></use>
													</svg>
												</a>
											</div>
										</div>
									</div>
								</div>
								<!--end card-->
							</div>
						<?php
						if ( ( ( $pindex % DLI_PATENT_CELLS_PER_ROW ) === DLI_PATENT_CELLS_PER_ROW - 1 ) || ( $the_query->current_post + 1 === $the_query->post_count ) ) {
						?>
						</div>
						<!-- end row -->
						<?php
							}
							$pindex++;
						}
						?>
					</div>
					<!-- end contenitore brevetti -->
				<?php
				} else {
				?>
				<div class="col-12 col-lg-8">
					<div class="row pt-2">
						<?php echo __( 'Non è stato trovato alcun brevetto', 'design_laboratori_italia' ); ?>
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

