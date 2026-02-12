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
$paged = absint( get_query_var( 'paged' ) );
if ( 0 === $paged ) {
	$paged = absint( get_query_var( 'page' ) );
}
if ( 0 === $paged ) {
	$paged = 1;
}
// Gestione del parametro: TAG livello.
$selected_level = isset( $_GET['level'] ) ? sanitize_text_field( $_GET['level'] ) : '';

// Recupero dati da visualizzare.
$params = array(
	'today'     => $today,
	'per_page'  => $per_page,
	'paged'     => $paged,
	'tag_level' => $selected_level,
);
$the_query   = DLI_ContentsManager::get_projects_data_query( $params );
$num_results = $the_query->found_posts;

// Get the tag used into published projects.
$tags = DLI_ContentsManager::get_tags_by_post_type( PROGETTO_POST_TYPE );

// Etichette per la gestione dei tag.
$label_select_level = dli_get_configuration_field_by_lang( 'seleziona_livello_progetti', 'progetti' );
$label_all_levels   = dli_get_configuration_field_by_lang( 'tutti_i_livelli_progetti', 'progetti' );
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
		 <?php
		 if ( count( $tags ) > 0 ) {
		?>
		<div class="row text-center pb-5">
			<div class="col-12 col-lg-12">
				<div class="title-section">
					<?php
						foreach( $tags as $tag ) {
					?>
					<div class="chip chip-primary chip-lg chip-simple <?php if ( $selected_level === $tag->slug ) echo " chip-selected" ?>">
						<span class="chip-label customSpacing">
							<a class="hover-text-white"
								href="#"
           			onclick="addParameterAndReloadPage('level', '<?php echo esc_attr( $tag->slug ); ?>'); return false;"
								title="<?php _e( 'Filtra per', "design_laboratori_italia" ); ?>: <?php echo esc_attr( $tag->name ); ?>"
								data-focus-mouse="false"><?php echo esc_html( $tag->name ); ?></a>
						</span>
					</div>
					<?php
						}
					?>
					<div class="chip chip-primary chip-lg chip-simple <?php if ( $selected_level === '' ) echo " chip-selected" ?>">
						<span class="chip-label customSpacing">
							<a class="hover-text-white"
								href="#"
								onclick="addParameterAndReloadPage('level', ''); return false;"
								title="<?php echo esc_attr( $label_all_levels ); ?>">
								<?php echo esc_html( $label_all_levels ); ?>
							</a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<?php
		 }
		?>
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
					$responsabili   = dli_get_field( 'responsabile_del_progetto', $post_id );
					$levels         = wp_get_post_terms( $post_id, 'post_tag' );
					$nomi_resp      = ( $responsabili && count( $responsabili ) >0 ) ? implode(', ', array_map(function($persona) {
						return $persona->post_title;
					}, $responsabili)) : '' ;

				?>
						<!--start card-->
						<div class="col-12 col-lg-4">
							<div class="card-space pb-5">
								<div class="card card-bg card-big no-after dli_card_progetti">
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
										<h3 class="card-title h5 "><?php echo esc_html( get_the_title() ); ?></h3>
										<p class="card-text font-serif">
											<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
										</p>
										<span class="card-signature"><?php echo esc_html( $nomi_resp ); ?></span>

										<div class="it-card-footer">
											<div class="head-tags">
												<?php
												foreach ( $levels as $level ) {
												?>
												<a class="card-tag text-decoration-none" href="#"
													onclick="addParameterAndReloadPage('level', '<?php echo esc_attr( $level->slug ); ?>'); return false;">
													<?php echo esc_html( $level->name ); ?>
												</a>
												<?php
												}
												?>
											</div>
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
