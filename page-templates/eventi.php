<?php
/* Template Name: Eventi
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'EVENTS_CELLS_PER_ROW', 3 );
$per_page        = DLI_POSTS_PER_PAGE;
$per_page_values = DLI_POST_PER_PAGE_VALUES;

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	$per_page = sanitize_text_field( $_GET['per_page'] );
}
if ( isset( $_GET['cat'] ) ){
	$selected_categories = $_GET['cat'];
} else {
	$selected_categories = array();
}

$params = array(
	'selected_categories' => $selected_categories,
	'per_page'            => $per_page,
	'paged'               => get_query_var( 'paged', 1 ),
);
$the_query      = DLI_ContentsManager::dli_get_event_data_query( $params );
$num_results    = $the_query->found_posts;
$all_categories = dli_get_all_categories_by_ct( 'category', EVENT_POST_TYPE );
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER EVENTI -->
	<?php get_template_part( 'template-parts/hero/eventi' ); ?>


	<!-- SEZIONE EVENTI -->
	<section id="events" class="p-4">   
		<div class="container my-4"> 
			<div class="row pt-0">

				<!--COLONNA FILTRI -->
				<div class="col-12 col-lg-3 border-end">
					<div class="row pt-4">
					<?php
						if( count( $all_categories ) > 0 ) {
					?>
						<h3 class="h6 text-uppercase border-bottom"><?php echo __( 'Categoria', 'design_laboratori_italia' ); ?></h3>
						<div>
							<form action="." id="eventiform" method="GET">
								<?php
									foreach( $all_categories as $category ) {
								?>
								<div class="form-check">
									<input type="checkbox" name="cat[]" id="<?php echo $category['slug']; ?>" 
										value="<?php echo $category['id']; ?>" onChange="this.form.submit()"
										<?php if ( in_array( $category['id'], $selected_categories ) ) { echo "checked='checked'"; } ?>
									>
									<label for="<?php echo $category['slug']; ?>"><?php echo $category['name']; ?></label>
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
				<!-- Inizio ELENCO EVENTI -->
				<div class="col-12 col-lg-8">
				<?php
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						if ( ( $pindex % EVENTS_CELLS_PER_ROW ) == 0 ) {
					?>
					<!-- begin row -->
					<div class="row pt-5">
					<?php
					}
					$post_id        = get_the_ID();
					$termitem       = dli_get_post_main_category( $post, 'category' );
					$date           = dli_get_field( 'data_inizio', $post_id );
					$event_date     = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
					$orario_inizio  = dli_get_field( 'orario_inizio', $post_id );
					$evento         = get_post( $post_id );
					$image_metadata = dli_get_image_metadata( $evento, 'item-hero-event' );
					?>

						<!-- start card-->
						<div class="col-12 col-lg-4">
							<div class="card-wrapper">
								<div class="card card-img no-after card-bg">
									<div class="img-responsive-wrapper">
										<div class="img-responsive img-responsive-panoramic">
											<figure class="img-wrapper">
												<img src="<?php echo $image_metadata['image_url']; ?>"
													alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>"
													title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>">
											</figure>
											<div class="card-calendar d-flex flex-column justify-content-center">
												<span class="card-date"><?php echo intval( $event_date->format( 'd' ) ); ?></span>
												<span class="card-day">
													<?php echo __( dli_get_monthname( $event_date->format( 'm' ), 'design_laboratori_italia' ) ); ?> <?php echo intval( $event_date->format( 'Y' ) ); ?>
												</span>
											</div>
										</div>
									</div>
									<div class="card-body p-4">
										<h3 class="card-title h4"><?php echo get_the_title(); ?></h3>
										<p class="card-text">
											<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
										</p>
										<?php if( $orario_inizio ) {
											?>
											<p class="card-text">
												<?php echo $orario_inizio; ?>
											</p>
											<?php
										}
										?>
										<a class="read-more" href="<?php echo get_permalink(); ?>">
											<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
											<svg class="icon" role="img" aria-labelledby="Arrow right">
												<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
												<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right'; ?>">
											</use>
										</svg>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!--end card-->
						
					<?php
					if ( ( ( $pindex % EVENTS_CELLS_PER_ROW ) === EVENTS_CELLS_PER_ROW - 1 ) || ( $the_query->current_post + 1 === $the_query->post_count ) ) {
					?>
					</div>
					<!-- end row -->
					<?php
					}
					$pindex++;
					}
				} else {
					?>
					<div class="col-12 col-lg-8">
						<div clas="row pt-2">
							<?php echo __( 'Non è stato trovato alcun evento', 'design_laboratori_italia' ); ?>
						</div>
					</div>
						<?php
				}
				?>


				</div> 
				<!-- Fine elenco eventi-->

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
				'query'           => $the_query,
				'per_page'        => $per_page,
				'per_page_values' => $per_page_values,
			)
		);
	?>
</main>

<?php
get_footer();
