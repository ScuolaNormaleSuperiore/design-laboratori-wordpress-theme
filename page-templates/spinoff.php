<?php
/* Template Name: Spinoff
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'DLI_SPINOFF_CELLS_PER_ROW', 2 );

$selected_year    = '';
$selected_sectors = array();
$search_string    = '';
$all_sectors      = dli_get_all_categories_by_ct( BUSINESS_SECTOR_TAXONOMY, SPINOFF_POST_TYPE );
$all_sector_ids   = $all_sectors ? array_map( function( $item ) { return $item['id']; }, $all_sectors ) : [];
$all_years        = DLI_ContentsManager::dli_get_all_spinoff_years();
$per_page         = strval( DLI_PER_PAGE );
$per_page_values  = DLI_PER_PAGE_VALUES;

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	$per_page = sanitize_text_field( $_GET['per_page'] );
}
if ( isset( $_GET['business_sector'] ) && is_array(  $_GET['business_sector'] )  ) {
	foreach ($_GET['business_sector'] as $ar ) {
		array_push( $selected_sectors, sanitize_text_field( $ar ) );
	}
}
if ( isset( $_GET['search_string'] ) ) {
	$search_string = sanitize_text_field( $_GET['search_string'] );
}
if ( isset( $_GET['foundation_year'] ) && is_numeric( $_GET['foundation_year'] ) ) {
	$selected_year = sanitize_text_field( $_GET['foundation_year'] );
}
if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ) {
	$paged = 1;
} else {
	$paged = get_query_var( 'paged', 1 );
}

$params = array(
	'search_string'   => $search_string,
	'business_sector' => $selected_sectors ? $selected_sectors : $all_sector_ids,
	'foundation_year' => $selected_year ? array( $selected_year ) : $all_years,
	'per_page'        => $per_page,
	'paged'           => $paged,
);
$the_query     = DLI_ContentsManager::get_spinoff_data_query( $params );
$num_results   = $the_query->found_posts;

?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER SPINOFF -->
	<?php get_template_part( 'template-parts/hero/spinoff' ); ?>
	
	<!-- ELENCO SPINOFF -->
	<section id="spinoff">
		<div class="container p-5">
			<!-- inizio row principale -->
			<div class="row">
				<div class="col-12 col-lg-3 border-end pb-3">
					<form action="<?php $_SERVER['PHP_SELF']; ?>" id="spinoffform" method="GET">
						<!--COLONNA FILTRI -->
						
						<!-- FILTRO PER ANNO -->
						<div class="row pt-3">
							<h3 class="h6 text-uppercase border-bottom"><?php echo __( 'Anno di costituzione', 'design_laboratori_italia' ); ?></h3>
							<div class="select-wrapper">
								<label for="foundation_year" class="visually-hidden"><?php echo __( 'Anno di costituzione', 'design_laboratori_italia' ); ?></label>
								<select id="foundation_year" name="foundation_year">
									<option selected="" value=""><?php echo __( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
									<?php
										foreach ( $all_years as $foundation_year ) {
											$selected = ( $foundation_year === $selected_year );
									?>
									<option value="<?php echo $foundation_year; ?>"
										<?php if ( $selected ) { echo 'selected'; } ?>
									><?php echo $foundation_year; ?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>

						<!-- FILTRO PER SETTORE DI ATTIVITA' -->
						<div class="row pt-5">
							<h3 class="h6 text-uppercase border-bottom">
								<?php echo __( 'Settore di attività', 'design_laboratori_italia' ); ?>
							</h3>
							<div>
								<?php
								foreach ( $all_sectors as $business_sector ) {
								?>
								<div class="form-check">
								<input type="checkbox" name="business_sector[]" id="<?php echo $business_sector['slug']; ?>"
										value="<?php echo $business_sector['id']; ?>"
										<?php if ( in_array( $business_sector['id'], $selected_sectors ) ) { echo "checked='checked'"; } ?>
									>
									<label for="<?php echo $business_sector['slug']; ?>"><?php echo $business_sector['name']; ?></label>
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

				<!-- ELENCO SPINOFF 6 per pagina -->
				<?php
				// The mani loop of the page.
				$pindex = 0;
				if ( $num_results > 0 ) {
				?>
					<!-- inizio contenitore spinoff -->
					<div class="col-12 col-lg-9 pt-3">
						<?php
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							if ( ( $pindex % DLI_SPINOFF_CELLS_PER_ROW ) == 0 ) {
						?>
						<div class="row pb-5">
							<!-- row -->
							<?php
								}
								$post_id          = get_the_ID();
								$stato            = dli_get_field( 'stato' );
								$year             = dli_get_field( 'anno_costituzione' );
								$settore_attivita = dli_get_post_main_category( $post, BUSINESS_SECTOR_TAXONOMY );
								$image_metadata   = dli_get_image_metadata( $post, 'item-card-list' );
								$logo             = dli_get_field( 'logo' );
							?>
							<div class="col-12 col-lg-6"> 
								<!--start card-->
								<div class="card-wrapper shadow">
									<div class="card card-img no-after">
									<?php
									if ( ( $image_metadata['image_url'] ) || ( $logo ) ) {
									?>
										<div class="img-responsive-wrapper">
											<div class="img-responsive img-responsive-panoramic">
												<figure class="img-wrapper">
													<?php
													if ( $logo ){
													?>
														<img src="<?php echo esc_url( $logo['url'] ); ?>" 
															title="<?php echo esc_attr( $logo['title'] ); ?>" 
															alt="<?php echo esc_attr( $logo['title'] ); ?>">
													<?php
													} else {
													?>
														<img src="<?php echo $logo; ?>" 
															title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
															alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
													<?php
													}
													?>
												</figure>
											</div>
										</div>
									<?php
									}
									?>
										<div class="card-body">
											<h3 class="card-title cardTitlecustomSpacing h5"><?php the_title(); ?></h3>
											<p class="card-text font-serif">
												<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
											</p>
											<p class="card-text font-serif titolari">
													<?php echo __( 'Anno di costituzione', 'design_laboratori_italia' ); ?>:
													<em><?php echo esc_attr( $year ); ?></em>
											</p>
											<p class="card-text font-serif area">
												<?php
												if ($settore_attivita && array_key_exists('title', $settore_attivita ) ) {
												?>
												<strong><?php echo esc_attr( $settore_attivita['title'] ); ?></strong> - 
												<?php
												}
												?>
												<?php echo __( $stato, 'design_laboratori_italia' ); ?>
											</p>
											<div class="pt-5">
												<a class="read-more" href="<?php echo get_permalink(); ?>">
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
						if ( ( ( $pindex % DLI_SPINOFF_CELLS_PER_ROW ) === DLI_SPINOFF_CELLS_PER_ROW - 1 ) || ( $the_query->current_post + 1 === $the_query->post_count ) ) {
						?>
						</div>
						<!-- end row -->
						<?php
							}
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
						<?php echo __( 'Non è stata trovata alcuna spinoff', 'design_laboratori_italia' ); ?>
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
