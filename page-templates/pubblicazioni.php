<?php
define( 'PREFIX_CAT_FILTER', 'checkBoxCat' );

/* Template Name: Le pubblicazioni
 *
 * @package Design_Laboratori_Italia
 */
get_header();

$anni_pubblicazioni      = $wpdb->get_results( "SELECT DISTINCT meta_value FROM $wpdb->postmeta pm, $wpdb->posts p WHERE meta_key  = 'anno' and pm.post_id=p.ID  and p.post_type='pubblicazione' ORDER BY meta_value DESC " );
$anno_filter_array       = null;
$tipi_pubbl_filter_array = null;
$anno_select             = null;

if ( isset( $_GET['annoSelect'] ) && $_GET['annoSelect'] != '' ) {
	$anno_select = $_GET['annoSelect'];
	$anno_filter_array = 
		array(
			'key'     => 'anno',
			'compare' => 'LIKE',
			'value'   => $anno_select,
		);
}

// Estraggo i parametri per il tipo pubblicazione.
$tipi_pubblicazione_params = array();
foreach ( $_GET as $parameter ) {
	if ( str_starts_with( $parameter, PREFIX_CAT_FILTER ) ) {
		$tipo = str_replace( PREFIX_CAT_FILTER, '', $parameter );
		array_push( $tipi_pubblicazione_params, $tipo );
	}
}
if ( count( $tipi_pubblicazione_params ) > 0 ) {
	$tipi_pubbl_filter_array =
		array(
			'taxonomy' => 'tipo-pubblicazione',
			'field'    => 'slug',
			'operator' => 'IN',
			'terms'    => $tipi_pubblicazione_params,
		);
}

if ( ! isset( $anno_filter_array ) && ! isset( $tipi_pubbl_filter_array ) ) {
	$pubblicazioni = new WP_Query(
		array(
			'posts_per_page' => DLI_POSTS_PER_PAGE,
			'paged'          => get_query_var( 'paged', 1 ),
			'post_type'      => 'pubblicazione',
			'orderby'        => 'anno',
			'order'          => 'ASC',
			'meta_query'     => array(
				$anno_filter_array,
			),
			'tax_query'   => array(
				$tipi_pubbl_filter_array,
			),
		)
	);
}

if ( isset( $anno_filter_array ) && isset( $tipi_pubbl_filter_array ) ) {
	$pubblicazioni = new WP_Query(
		array(
			'posts_per_page' => DLI_POSTS_PER_PAGE,
			'paged'          => get_query_var( 'paged', 1 ),
			'post_type'      => 'pubblicazione',
			'orderby'        => 'anno',
			'order'          => 'ASC',
			'meta_query'     => array(
				$anno_filter_array,
			),
			'tax_query'   => array(
				$tipi_pubbl_filter_array,
			),
		)
	);
}

if ( isset( $anno_filter_array ) && !isset( $tipi_pubbl_filter_array ) ) {
	$pubblicazioni = new WP_Query(
		array(
			'posts_per_page' => DLI_POSTS_PER_PAGE,
			'paged'          => get_query_var( 'paged', 1 ),
			'post_type'      => 'pubblicazione',
			'orderby'        => 'anno',
			'order'          => 'ASC',
			'meta_query'     => array(
				$anno_filter_array,
			),
		)
	);
}

if ( !isset( $anno_filter_array ) && isset( $tipi_pubbl_filter_array ) ) {
	$pubblicazioni = new WP_Query(
		array(
			'posts_per_page' => DLI_POSTS_PER_PAGE,
			'paged'          => get_query_var( 'paged', 1 ),
			'post_type'      => 'pubblicazione',
			'orderby'        => 'anno',
			'order'          => 'ASC',
			'tax_query'   => array(
				$tipi_pubbl_filter_array,
			)
		)
	);
}
$num_results = $pubblicazioni->found_posts;


?>

<form action="<?php $_SERVER['PHP_SELF']; ?>" id="pubblicazioniform" method="GET">
	<main id="main-container" role="main">

		<!-- BREADCRUMB -->
		<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

		<!-- BANNER PUBBLICAZIONI -->
		<?php get_template_part( 'template-parts/hero/pubblicazioni' ); ?>

		<!-- ELENCO PUBBLICAZIONI -->
		<section id="pubblicazioni">
			<div class="container p-5"> 
				<div class="row"> <!-- SPAZIATURA ridotta in alto solo sulla prima riga riga pt-0 le card NON uniformate in altezza -->
					<div class="col-12 col-lg-3 border-end pb-3">
						<!--COLONNA FILTRI -->
						<!-- FILTRO PER ANNO -->
						<div class="row pt-3">
							<h3 class="h6 text-uppercase border-bottom"><?php _e( 'Anno', 'design_laboratori_italia' ); ?></h3>
							<div class="select-wrapper">
								<label for="annoSelect" class="visually-hidden"><?php _e( 'Anno', 'design_laboratori_italia' ); ?></label>
								<select id="annoSelect" name="annoSelect" onChange="this.form.submit()">
									<option <?php if (!isset( $_GET['annoSelect'] ) || $_GET['annoSelect'] == '') echo " selected "; ?> value=""><?php _e( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
									<?php
									foreach ( $anni_pubblicazioni as $anno ) { ?>
										<option <?php if ($anno_select == $anno->meta_value ) echo " selected "; ?> value="<?php echo $anno->meta_value; ?>"><?php echo $anno->meta_value; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<?php
						// recupero i termini della tassonomia tipo pubblicazione.
						$tipi_pubblicazione = get_terms(
							[
								'taxonomy'   => 'tipo-pubblicazione',
								'hide_empty' => false,
							]
						);
						// visualizzo i filtri sul tipo doi pubblicazione solo se ne esistono almeno 1.
						if ( count( $tipi_pubblicazione ) >= 1 ) {
						?>
						<!-- FILTRO PER CATEGORIA - Se esiste -->
						<div class="row pt-5">
							<h3 class="h6 text-uppercase border-bottom"><?php _e( 'Tipologia', 'design_laboratori_italia' ); ?></h3>
							<div>
								<?php foreach ( $tipi_pubblicazione as $tipo_pubblicazione ) { ?>
								<div class="form-check">
									<?php
									$checked = false;
									if  ( isset ( $_GET[ PREFIX_CAT_FILTER . esc_attr( $tipo_pubblicazione->slug ) ] ) )  {
										$checked = true;
									}
									?>
								<input id="<?php echo PREFIX_CAT_FILTER . esc_attr( $tipo_pubblicazione->slug ); ?>" name="<?php echo PREFIX_CAT_FILTER . esc_attr( $tipo_pubblicazione->slug ); ?>" value="<?php echo PREFIX_CAT_FILTER . esc_attr( $tipo_pubblicazione->slug ); ?>" type="checkbox" <?php if($checked) {echo " checked ";}; ?> onChange="this.form.submit()">
								<label for="<?php echo PREFIX_CAT_FILTER . esc_attr( $tipo_pubblicazione->slug ); ?>"><?php echo esc_attr( $tipo_pubblicazione->name ); ?></label>
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

					<!-- PUBBLICAZIONI -->
					<?php

					if ( $num_results ) {
					?>

						<div class="col-12 col-lg-8 pt-3">
							<div class="row">
								<?php
								while ( $pubblicazioni->have_posts() ) {
									$pubblicazioni->the_post();
									$ID       = get_the_ID();
									$title    = get_the_title( $ID );
									$url      = get_field( 'url' );
									?>
									<!--start card-->
									<div class="card-wrapper pt-3">
										<div class="card card-teaser rounded shadow">
											<div class="card-body">
												<?php
												if ( $url ) {
													?>
													<h3 class="card-title h5">
														<svg class="icon" role="img" aria-labelledby="Note">
															<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note'; ?>"></use>
														</svg>
														<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_attr( $title ); ?></a>
													</h3>
													<?php
												}
												else {
													?>
													<h3 class="card-title h5">
														<svg class="icon" role="img" aria-labelledby="Note">
															<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note'; ?>"></use>
														</svg>
														<?php echo esc_attr( $title ); ?>
													</h3>
												<?php } ?>
												<p class="card-text"><?php echo esc_attr( the_content() ); ?></p>
											</div>
										</div>
									</div>
									<!--end card-->
								<?php } 
								?>
							</div>
						</div>
						<?php
							wp_reset_postdata();
					}
					else {
						?>
						<div class="col-12 col-lg-8">
							<div class="row pt-2">
							<?php echo __( 'Non è stata trovata nessuna pubblicazione', 'design_laboratori_italia' ); ?>
							</div>
					</div>
					<?php
					}
					?>
				</div>
			</div>
		</section>


		<!-- PAGINAZIONE -->
	<?php
		get_template_part(
			'template-parts/common/paginazione',
			null,
			array(
				'query' => $pubblicazioni,
			)
		);
	?>

	</main>
</form>
<!-- END CONTENT -->

<?php
get_footer();
