<?php
/* Template Name: Blog
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'BLOG_CELLS_PER_ROW', 3 );

if ( isset( $_GET['cat'] ) ){
	$selected_categories = $_GET['cat'];
} else {
	$selected_categories = array();
}


$the_query = new WP_Query(
	array(
		'paged'          => get_query_var( 'paged', 1 ),
		'post_type'      => WP_DEFAULT_POST,
		'posts_per_page' => DLI_POSTS_PER_PAGE,
		'category__in'   => $selected_categories,
	)
);
$num_results    = $the_query->found_posts;
$all_categories = dli_get_all_categories_by_ct( 'category', WP_DEFAULT_POST );
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER ARTICOLI -->
	<?php get_template_part( 'template-parts/hero/blog' ); ?>

	<!-- SEZIONE ARTICOLI -->
	<section id="news" class="p-4">   
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
							<form action="." id="notizieform" method="GET">
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
				<!-- Inizio ELENCO NEWS -->
				<div class="col-12 col-lg-8">
				<?php
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						if ( ( $pindex % BLOG_CELLS_PER_ROW ) == 0 ) {
					?>
					<!-- begin row -->
					<div class="row pt-5">
					<?php
					}
					$post_id  = get_the_ID();
					$termitem = dli_get_post_main_category( $post, 'category' );
					?>
						<!-- start card-->
						<div class="col-12 col-lg-4">
							<div class="card-wrapper">
								<div class="card card-bg">
									<div class="card-body">
										<div class="category-top">
											<?php
											if ( $termitem['title'] ) {
											?>
											<a class="category" href="#"><?php echo $termitem['title']; ?></a>
											<?php
											}
											?>
											<span class="data"><?php echo get_the_date( 'd/m/Y' ); ?></span>
										</div>
										<h3 class="card-title h4"><?php echo get_the_title(); ?></h3>
										<p class="card-text">
											<?php echo wp_trim_words( get_the_content(), DLI_ACF_SHORT_DESC_LENGTH ); ?>
										</p>
										<a class="read-more" href="<?php echo get_permalink(); ?>">
										<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
										<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right'; ?>"></use>
										</svg>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!--end card-->
					<?php
					if ( ( ( $pindex % BLOG_CELLS_PER_ROW ) === BLOG_CELLS_PER_ROW - 1 ) || ( $the_query->current_post + 1 === $the_query->post_count ) ) {
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
							<?php echo __( 'Non è stato trovato nessun articolo', 'design_laboratori_italia' ); ?>
						</div>
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
