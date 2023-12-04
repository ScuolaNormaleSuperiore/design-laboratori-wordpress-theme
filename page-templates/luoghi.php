<?php
/* Template Name: I Luoghi.
 *
 * @package Design_Laboratori_Italia
 */
get_header();

// estraggo i parametri per il tipo luoho
$tipi_luogo_params = array();
foreach ( $_GET as $parameter ) {
	array_push( $tipi_luogo_params, $parameter );
}

if ( count( $tipi_luogo_params ) > 0 ) {
	$tipi_luogo_filter_array =
		array(
			'taxonomy' => PLACE_TYPE_TAXONOMY,
			'field'    => 'slug',
			'operator' => 'IN',
			'terms'    => $tipi_luogo_params,
		);
}

if ( isset( $tipi_luogo_filter_array ) ) {
	$luoghi = new WP_Query(
		array(
			'posts_per_page' => DLI_POSTS_PER_PAGE,
			'paged'          => get_query_var( 'paged', 1 ),
			'post_type'      => PLACE_POST_TYPE,
			'orderby'        => 'title',
			'tax_query'   => array(
				$tipi_luogo_filter_array,
			),
		)
	);
}
else {
	$luoghi = new WP_Query(
		array(
			'posts_per_page' => DLI_POSTS_PER_PAGE,
			'paged'          => get_query_var( 'paged', 1 ),
			'post_type'      => PLACE_POST_TYPE,
			'orderby'        => 'title',
		)
	);
}

$num_results = $luoghi->found_posts;
?>

<form action="<?php $_SERVER['PHP_SELF']; ?>" id="luoghiform" method="GET">
<!-- START CONTENT -->
	<main id="main-container" role="main">
		<!-- BREADCRUMB -->
		<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

		<!-- BANNER PERSONE -->
		<?php get_template_part( 'template-parts/hero/luoghi' ); ?>

		<!-- ELENCO LUOGHI -->
		<section id="luoghi" class="p-4">   
			<div class="container my-4"> 
				<div class="row pt-0"> <!-- SPAZIATURA ridotta in alto solo sulla prima riga riga pt-0 le card NON uniformate in altezza -->
					<div class="col-12 col-lg-3 border-end">
						<!--COLONNA FILTRI -->
						<?php
						// recupero i termini della tassonomia tipologia luogo che hanno dei risultati.
						$tipi_luogo = dli_get_all_place_types_with_results();

						// visualizzo i filtri sul tipo di luogo solo se ne esistono almeno 1.
						if ( count( $tipi_luogo ) >= 1 ) {
						?>
						<!-- FILTRO PER CATEGORIA - Se esiste -->
							<div class="row pt-4">
								<h3 class="h6 text-uppercase border-bottom"><?php _e( 'Tipologia', 'design_laboratori_italia' ); ?></h3>
								<div>
								<?php foreach ( $tipi_luogo as $tipo_luogo ) { ?>
									<div class="form-check">
									<?php
									$checked = false;
									if  ( isset ( $_GET[esc_attr( $tipo_luogo->slug )] ) ) {
										$checked = true;
									}
									?>
										<input id="<?php echo esc_attr( $tipo_luogo->slug ); ?>" name="<?php echo esc_attr( $tipo_luogo->slug ); ?>" value="<?php echo esc_attr( $tipo_luogo->slug ); ?>" type="checkbox" <?php if($checked) {echo " checked ";}; ?> onChange="this.form.submit()">
										<label for="<?php echo esc_attr( $tipo_luogo->slug ); ?>"><?php echo esc_attr( $tipo_luogo->name ); ?></label>
									</div>
									<?php
								}
								?>
								</div>
							</div>
							<?php
						}
						?>
						<!--fine filtri -->
					</div>
					<!-- LUOGHI -->
					<?php

					if ( $num_results ) {
					?>
						<div class="col-12 col-lg-8">
							<div class="row">
							<?php
								while ( $luoghi->have_posts() ) {
									$luoghi->the_post();
									$ID        = get_the_ID();
									$title     = get_the_title( $ID );
									$desc      = get_field( 'descrizione_breve' );
									$indirizzo = get_field( 'indirizzo' );
									$terms     = get_the_terms( $ID, PLACE_TYPE_TAXONOMY );
									$tipo      = $terms[0]->name;
									?>
									<!--start card-->
									<div class="card-wrapper ">
									<div class="card card-teaser rounded shadow">
										<div class="card-body">
										<h3 class="card-title h5 ">
											<svg class="icon" role="img" aria-labelledby="Map marker">
												<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-map-marker'; ?>"></use>
											</svg>
											<a href="<?php the_permalink(); ?>"><?php echo esc_attr( $title ); ?></a>
										</h3>
										<div class="card-text">
											<p><?php echo esc_attr( $desc ); ?><br>
											<?php echo esc_attr( $indirizzo ); ?><br>
											<?php echo esc_attr( $tipo ); ?><br>
											</p>
										</div>
									</div>
								</div>
							</div>
							<!--end card-->
							<?php }
								?>
							</div>
						</div>
						<?php
					}
					else {
						?>
						<div class="col-12 col-lg-8">
							<div class="row pt-2">
								<?php echo __( 'Non è stata trovato nessun luogo', 'design_laboratori_italia' ); ?>
							</div>
						</div>
						<?php
					}
					?>
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
				'query' => $luoghi,
			)
		);
	?>
	</main>
</form>
	<!-- END CONTENT -->
<?php
get_footer();