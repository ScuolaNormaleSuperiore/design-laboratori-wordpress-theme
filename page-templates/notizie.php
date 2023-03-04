<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
global $post;
get_header();
define( 'NEWS_CELLS_PER_ROW', 3 );

$the_query = new WP_Query(
	array(
		'paged'          => get_query_var( 'paged', 1 ),
		'post_type'      => NEWS_POST_TYPE,
		'posts_per_page' => DLI_POSTS_PER_PAGE,
	)
);
$num_results = $the_query->found_posts;
?>

<main id="main-container" class="main-container bluelectric">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PROGETTI -->
	<?php get_template_part( 'template-parts/hero/notizie' ); ?>

	<!-- SEZIONE NOTIZIE -->
	<section id="news" class="p-4">   
		<div class="container my-4"> 
			<div class="row pt-0">

				<!--COLONNA FILTRI -->
				<div class="col-12 col-lg-3 border-end">
					<div class="row pt-4">
						<h3 class="h6 text-uppercase border-bottom">Categoria</h3>
						<div>
							<div class="form-check">
								<input id="checkbox4" type="checkbox" checked>
								<label for="checkbox4">Categoria</label>
							</div>
							<div class="form-check">
								<input id="checkbox5" type="checkbox">
								<label for="checkbox5" class="disabled">Altra categoria</label>
							</div>
							<div class="form-check">
								<input id="checkbox5" type="checkbox">
								<label for="checkbox5" class="disabled">Altra categoria 2</label>
							</div>
						</div>
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
						if ( ( $pindex % NEWS_CELLS_PER_ROW ) == 0 ) {
					?>
					<!-- begin row -->
					<div class="row pt-5">
					<?php
					}
					$post_id  = get_the_ID();
					$termitem = dli_get_main_taxonomy_termitem( $post_id, 'category' );
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
											<span class="data">10/12/2023</span>
										</div>
										<h3 class="card-title h4"><?php echo get_the_title(); ?></h3>
										<p class="card-text">
											<?php echo wp_trim_words( get_field( 'descrizione_breve', $last_hero_news ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
										</p>
										<a class="read-more" href="<?php echo get_permalink(); ?>">
										<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
										<svg class="icon">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right'; ?>"></use>
										</svg>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!--end card-->
					<?php
					if ( ( ( $pindex % NEWS_CELLS_PER_ROW ) === NEWS_CELLS_PER_ROW - 1 ) || ( $the_query->current_post + 1 === $the_query->post_count ) ) {
					?>
					</div>
					<!-- end row -->
					<?php
					}
					$pindex++;
					}
				} else {
					?>
				<div clas="row pt-2">
					<?php echo __( 'Non è stata trovata nessuna notizia', 'design_laboratori_italia' ); ?>
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
