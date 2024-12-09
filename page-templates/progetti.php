<?php
/* Template Name: I progetti
*
* @package Design_Laboratori_Italia
*/
global $post;
get_header();

define( 'PROG_CELLS_PER_ROW', 3 );
$pagination_mode = dli_get_option('pagination_mode', 'progetti' );
$custom_per_page = dli_get_option('pagination_number', 'progetti' );
$per_page        = ( $pagination_mode === 'show_all' ) ? 999 : DLI_POSTS_PER_PAGE;
$per_page_values = DLI_POST_PER_PAGE_VALUES;
$today           = date( 'Ymd' );

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	$per_page = sanitize_text_field( $_GET['per_page'] );
}
if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ) {
	$paged = 1;
} else {
	$paged = get_query_var( 'paged', 1 );
}

$params = array(
	'today'    => $today,
	'per_page' => $per_page,
	'paged'    => $paged,
);
$the_query   = DLI_ContentsManager::dli_get_projects_data_query( $params );
$num_results = $the_query->found_posts;

// Get the tag used into published projects.
$tags = DLI_ContentsManager::dli_get_tags_by_post_type( PROGETTO_POST_TYPE );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->	
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PROGETTI -->
	<?php get_template_part( 'template-parts/hero/progetti' ); ?>

	<?php
	// The mani loop of the page.
	$pindex = 0;
		?>

	<!-- ELENCO PROGETTI -->
	<section id="progetti">
		<div class="container p-5">

		<!-- Filtro per TAG --> 
		<div class="row text-center pb-5">
			<div class="col-12 col-lg-12">
				<div class="title-section">
					<?php
						foreach( $tags as $tag ) {
					?>
					<div class="chip chip-primary chip-lg chip-simple ">
						<span class="chip-label customSpacing">
							<a class="hover-text-white" 
								href="?struttura=prima-struttura"
								title="Filtra per: Prima struttura"
								data-focus-mouse="false"><?php echo esc_attr( $tag->name ); ?></a>
						</span>
					</div>
					<?php
						}
					?>
					<div class="chip chip-primary chip-lg chip-simple  chip-selected">
						<span class="chip-label customSpacing">
							<a class="hover-text-white" 
							href="https://sitofederato-dev.sns.it/il-laboratorio/persone/" 
							title="Tutte le strutture">Tutte le strutture</a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<!-- FINE FILTRO PER TAG -->


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
					$post_id        = get_the_ID();
					$progetto       = get_post( $post_id );
					$image_metadata = dli_get_image_metadata( $progetto, 'item-card-list' );
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
												<img src="<?php echo $image_metadata['image_url']; ?>" 
												title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
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
										<span class="text customSpacing"><?php echo __( 'Vai al progetto', 'design_laboratori_italia' ); ?></span>
										<svg class="icon" role="img" aria-labelledby="Arrow right">
											<title><?php echo __( 'Vai al progetto', 'design_laboratori_italia' ); ?></title>
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
				'mode'            => $pagination_mode,
				'query'           => $the_query,
				'per_page'        => $per_page,
				'per_page_values' => $per_page_values,
			)
		);
	?>

	</main>

<?php
get_footer();