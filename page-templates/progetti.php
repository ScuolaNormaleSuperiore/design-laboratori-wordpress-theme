<?php
/* Template Name: I progetti.
*
* @package Design_Laboratori_Italia
*/
global $post;
get_header();

define( 'PROG_CELLS_PER_ROW', 3 );

$the_query = new WP_Query(
	array(
		'paged'           => get_query_var( 'paged', 1 ),
		'post_type'       => 'progetto',
		'posts_per_page'  => 6,

	)
);
$num_results = $the_query->found_posts;
?>

<main id="main-container">

	<!-- BREADCRUMB -->	
	<section id ="breadcrumb">breadcrumb</section>

	<!-- BANNER PROGETTI -->
	<?php get_template_part( 'template-parts/hero/progetti' ); ?>

	<?php
	// The mani loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		?>

	<!-- ELENCO PROGETTI -->
	<section id="progetti" class="p-1">
		<div class="container my-4">
			<?php
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				if ( ( $post_index % PROG_CELLS_PER_ROW ) == 0 ) {
			?>
				<!-- begin row -->
				<div class="row pt-5">
				<?php
					}
					$image_url   = get_the_post_thumbnail_url( get_the_ID(), 'full' );
					$image_id    = attachment_url_to_postid( $image_url );
					$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', TRUE );
					$image_title = get_the_title( $image_id );
				?>
						<!--start card-->
						<div class="col-12 col-lg-4">
							<div class="card-space">
								<div class="card card-bg card-big no-after">
								<?php
								if ( $image_url ) {
								?>
									<div class="img-responsive-wrapper">
										<div class="img-responsive img-responsive-panoramic">
											<figure class="img-wrapper">
												<img src="<?php echo $image_url; ?>" title="<?php echo $image_title; ?>" alt="<?php echo $image_url; ?>">
											</figure>
										</div>
									</div>
								<?php
								}
								?>
									<div class="card-body">
										<h3 class="card-title h5 "><?php echo get_the_title(); ?></h3>
										<p class="card-text"><?php echo wp_trim_words( get_field( 'descrizione_breve', $last_hero_news ), DLI_ACF_SHORT_DESC_LENGTH ); ?></p>
										<a class="read-more" href="<?php echo get_permalink(); ?>">
										<span class="text"><?php echo __( 'Vai al progetto', 'design_laboratori_italia' ); ?></span>
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
					if ( ( $post_index % PROG_CELLS_PER_ROW ) == PROG_CELLS_PER_ROW - 1 ) {
				?>
				</div>
				<!-- end row -->
			<?php
				}
				$post_index++;
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
	<nav class="pagination-wrapper justify-content-center" aria-label="Navigazione centrata">
	<div class="row pt-5" id='pagination_links'>
	<?php
		$prev_label = '<svg class="icon icon-primary"><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left"></use></svg>';
		$next_label = '<svg class="icon icon-primary"><use href="' . get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right"></use></svg>';
		echo paginate_links(
			array(
				'total'     => $the_query->max_num_pages,
				'prev_text' => $prev_label,
				'next_text' => $next_label,
				'type'      => 'list',
			)
		);
	?>
	</div>
	</nav>

	<br /><br />
	<nav class="pagination-wrapper justify-content-center" aria-label="Navigazione centrata">
		<ul class="pagination">
			<li class="page-item disabled">
				<a class="page-link" href="#" tabindex="-1" aria-hidden="true">
					<svg class="icon icon-primary"><use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-left'; ?>"></use></svg>
					<span class="visually-hidden">Pagina precedente</span>
				</a>
			</li>
			<li class="page-item">
				<a class="page-link" href="#" aria-current="page">
					<span class="d-inline-block d-sm-none">Pagina </span>1
				</a>
			</li>
			<li class="page-item"><a class="page-link" href="#">2</a></li>
			<li class="page-item"><a class="page-link" href="#">3</a></li>
			<li class="page-item">
				<a class="page-link" href="#">
					<span class="visually-hidden">Pagina successiva</span>
					<svg class="icon icon-primary"><use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right'; ?>"></use></svg>
				</a>
			</li>
		</ul>
	</nav>

	</main>

<?php
get_footer();



