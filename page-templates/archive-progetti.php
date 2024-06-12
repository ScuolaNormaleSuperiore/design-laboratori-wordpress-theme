<?php
/* Template Name: Archivio progetti
*
* @package Design_Laboratori_Italia
*/
global $post;
get_header();

define( 'PROG_CELLS_PER_ROW', 3 );
$today = date( 'Ymd' );
$the_query = new WP_Query(
	array(
		'paged'          => get_query_var( 'paged', 1 ),
		'post_type'      => PROGETTO_POST_TYPE,
		'posts_per_page' => DLI_POSTS_PER_PAGE,
    'meta_query' => array(
			array(
					'key' => 'archiviato',
					'value' => true,
					'compare' => '=',
					'type' => 'BOOLEAN',
			),
	),
		)
);
$num_results = $the_query->found_posts;
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->	
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PROGETTI -->
	<?php get_template_part( 'template-parts/hero/progetti-archive' ); ?>

	<?php
	// The mani loop of the page.
	$pindex = 0;
		?>

	<!-- ELENCO PROGETTI -->
	<section id="progetti">
		<div class="container p-5">
			<?php
			if ( $num_results ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				if ( ( $pindex % PROG_CELLS_PER_ROW ) == 0 ) {
			?>
				<!-- begin row -->
				<div class="row">
				<?php
					}
					$image_metadata = dli_get_image_metadata( $post, 'full' );
				?>
						<!--start card-->
						<div class="col-12 col-lg-4">
							<div class="card-space pb-5">
								<div class="card card-bg card-big no-after">
								<?php
								if ( $image_metadata['image_url'] ) {
								?>
									<div class="img-responsive-wrapper">
										<div class="img-responsive img-responsive-panoramic">
											<figure class="img-wrapper">
												<img src="<?php echo $image_metadata['image_url']; ?>" title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
												alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
											</figure>
										</div>
									</div>
								<?php
								}
								?>
									<div class="card-body">
										<h3 class="card-title h5 "><?php echo get_the_title(); ?></h3>
										<p class="card-text">
											<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
										</p>
										<a class="read-more" href="<?php echo get_permalink(); ?>">
										<span class="text"><?php echo __( 'Vai al progetto', 'design_laboratori_italia' ); ?></span>
										<svg class="icon" role="img" aria-labelledby="Arrow right">
											<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right'; ?>"></use>
										</svg>
										</a>
									</div>
								</div>
							</div>
						</div>  
						<!--end card-->
				<?php
					if ( ( ( $pindex % PROG_CELLS_PER_ROW ) === PROG_CELLS_PER_ROW - 1 ) || ( $the_query->current_post + 1 === $the_query->post_count ) ) {
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
			<?php echo __( 'Non è stato trovato nessun progetto', 'design_laboratori_italia' ); ?>
		</div>
		<?php
			}
		 ?>

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



