<?php
/**
 * The template for displaying author
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#author
 *
 * @package Design_Laboratori_Italia
 */

get_header();

while ( have_posts() ) {
	the_post();
	$ID = get_the_ID();
	$foto = get_field( 'foto' );
	$title = get_the_title( $ID );
	$image_url = dsi_get_persona_avatar( $foto, $ID );
	$bio = get_the_content();
	$categoria_appartenenza = get_field( 'categoria_appartenenza' )[0]->nome;
	$allegato_cv = get_field( 'allegato_cv' );
	echo ' <br />ALTR ALLEGATI <br />';
	$altri_allegati = get_field( 'altri_allegati' );
	$telefono = get_field( 'telefono' );
	$email = get_field( 'email' );
	$sitoweb = get_field( 'sito_web' );
}

// recupero la lista dei progetti.
$progetti = new WP_Query(
	array(
	'posts_per_page' => -1,
	'post_type'      => 'progetto',
	'orderby'        => 'data_inizio',
	'order'          => 'DESC',
	'meta_query'     => array(
		'relation'  => 'OR',
		array(
			'key'     => 'responsabile_del_progetto',
			'compare' => 'LIKE',
			'value'   => '"' . $ID . '"',
		),
		array(
			'key'       => 'persone',
			'compare'   => 'LIKE',
			'value'     => '"' . $ID . '"',
		),
	),
	)
);

// recupero la lista delle pubblicazioni.
$pubblicazioni = new WP_Query(
	array(
		'posts_per_page' => -1,
		'post_type' => 'pubblicazione',
		'orderby' => 'anno',
		'order' => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'autori',
				'compare' => 'LIKE',
				'value' => $title,
			),
		),
	)
);

?>
		<main id="main-container" class="main-container petrol">
				<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>
				<section class="section bg-petrol py-3 py-lg-3 py-xl-5">
						<div class="container">
							<div class="row variable-gutters">
									<div class="col-12 col-sm-3 col-lg-3 d-none d-sm-block">
											<div class="section-thumb thumb-large mx-3">
													<?php
													if ( $image_url ) {
														echo "<img src='" . $image_url . "' alt=''/>";
													}
													?>
											</div><!-- /section-thumb -->
									</div><!-- /col-lg-2 -->
									<div class="col-12 col-sm-9 col-md-8 col-lg-8 offset-lg-1 d-flex align-items-center">
										<div class="section-title">
											<h2 class="mb-3"><?php echo dsi_get_persona_display_name( get_field( 'nome' ), get_field( 'cognome' ), $title ); ?></h2>
											<p><?php echo $categoria_appartenenza; ?></p>
										</div><!-- /title-section -->
									</div><!-- /col-lg-5 col-md-8 -->
							</div><!-- /row -->
						</div><!-- /container -->
				</section><!-- /section -->

				<section class="section bg-white">
					<div class="container container-border-top">
						<div class="row variable-gutters">
							<div class="col-lg-3 col-md-4 aside-border px-0">
								<aside class="aside-main aside-sticky">
									<div class="aside-title" id="people-detail" >
										<a class="toggle-link-list" data-toggle="collapse" href="#lista-paragrafi" role="button" aria-expanded="true" aria-controls="lista-paragrafi" aria-label="apri / chiudi dettagli della persona">
											<span><?php _e( "Dettagli della persona", "design_laboratori_italia" ); ?></span>
											<svg class="icon icon-toggle svg-arrow-down-small"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-arrow-down-small"></use></svg>
										</a>
									</div>
									<div id="lista-paragrafi" class="link-list-wrapper collapse show" role="region" aria-labelledby="people-detail">
										<ul class="link-list">
											<?php if ( $bio != "" ) { ?>
												<li>
													<a class="list-item scroll-anchor-offset" href="#art-par-bio" title="Vai al paragrafo <?php _e( "Biografia", "design_laboratori_italia" ); ?>"><?php _e( "Biografia", "design_laboratori_italia" ); ?></a>
												</li>
											<?php } ?>
											<?php if ( $progetti && $progetti->have_posts() ) { ?>
												<li>
													<a class="list-item scroll-anchor-offset" href="#art-par-progetti" title="Vai al paragrafo <?php _e( "Progetti", "design_laboratori_italia" ); ?>"><?php _e( "Progetti", "design_laboratori_italia" ); ?></a>
												</li>
												<?php
											}
											// controllo che ci siano indirizzi di ricerca da mostrare.
											$indirizzi_di_ricerca_ids = array();
											while ( $progetti->have_posts() ) {
												$progetti->the_post();
												$ID        = get_the_ID();
												$indirizzi = get_field( 'elenco_indirizzi_di_ricerca_correlati' );
												$indirizzi = $indirizzi ? $indirizzi : array();
												foreach ( $indirizzi as $indirizzo ) {
													array_push( $indirizzi_di_ricerca_ids, $indirizzo->ID );
												}
											}
											?>
											<?php
											if ( is_array( $indirizzi_di_ricerca_ids ) && count( $indirizzi_di_ricerca_ids ) > 0 ) {
												?>
												<li>
													<a class="list-item scroll-anchor-offset" href="#art-par-indirizzi-di-ricerca" title="Vai al paragrafo <?php _e( "Indirizzi di ricerca", "design_laboratori_italia" ); ?>"><?php _e( "Indirizzi di ricerca", "design_laboratori_italia" ); ?></a>
												</li>
												<?php
											}
											?>
											<?php if ( $pubblicazioni && $pubblicazioni->have_posts() ) { ?>
												<li>
													<a class="list-item scroll-anchor-offset" href="#art-par-pubblicazioni" title="Vai al paragrafo <?php _e( "Pubblicazioni", "design_laboratori_italia" ); ?>"><?php _e( "Pubblicazioni", "design_laboratori_italia" ); ?></a>
												</li>
												<?php } ?>
												<?php
												if ( ( is_array( $allegato_cv ) && count( $allegato_cv ) > 0 ) || ( is_array( $altri_allegati ) && count( $altri_allegati ) > 0 ) ) {
													?>
													<li>
														<a class="list-item scroll-anchor-offset" href="#art-par-altre-info"
														title="Vai al paragrafo <?php _e( "Ulteriori informazioni", "design_laboratori_italia" ); ?>"><?php _e( "Ulteriori informazioni", "design_laboratori_italia" ); ?></a>
													</li>
													<?php
												}

												if ( ( $telefono != "" ) || ( $email != "" ) || ( $sitoweb != "" ) ) {
													?>
													<li>
														<a class="list-item scroll-anchor-offset" href="#art-par-contatti" title="Vai al paragrafo <?php _e( "Contatti", "design_laboratori_italia" ); ?>"><?php _e( "Contatti", "design_laboratori_italia" ); ?></a>
													</li>
												<?php } ?>
										</ul>
									</div>
								</aside>
							</div>
							<div class="col-lg-8 col-md-8 offset-lg-1 pt84">
								<article class="article-wrapper">
									<?php if ( $bio != "" ) { ?>
										<h3 id="art-par-bio"><?php _e( "Biografia", "design_laboratori_italia" ); ?></h3>
										<div class="row variable-gutters">
											<div class="col-lg-9">
												<p><?php echo $bio; ?></p>
											</div><!-- /col-lg-9 -->
										</div><!-- /row -->
										<?php
									}
									if ( $progetti && $progetti->have_posts() ) {
										?>
										<h3 id="art-par-progetti"  class="mb-4"><?php _e( "Progetti", "design_laboratori_italia" ); ?></h3>
										<div class="row variable-gutters mb-4">
											<div class="col-lg-12">
												<div	iv class="card-deck card-deck-spaced">
												<?php
												while ( $progetti->have_posts() ) {
													$progetti->the_post();
													$ID        = get_the_ID();
													$title     = get_the_title( $ID );
													?>
													<div class="card card-bg card-icon rounded">
														<div class="card-body">
															<svg class="icon svg-project">
																<use xmlns:xlink="http://www.w3.org/1999/xlink"
																xlink:href="#svg-project"></use>
															</svg>
															<div class="card-icon-content">
																<p>
																	<strong><a href="<?php echo get_permalink(); ?>"><?php echo $title; ?></a></strong>
																</p>
																<small><?php echo get_field( 'descrizione_breve' ); ?></small>
																<small>
																<?php
																// recupero i nomi dei responsabili di progetto.
																$responsabili_di_progetto = get_field( 'responsabile_del_progetto' );
																$responsabli = array();
																foreach ( $responsabili_di_progetto as $responsabile_di_progetto ) {
																	array_push( $responsabli, $responsabile_di_progetto->post_title );
																}
																echo implode( ",", $responsabli );
																?>
																</small>
															</div><!-- /card-icon-content -->
														</div><!-- /card-body -->
													</div><!-- /card card-bg card-icon rounded -->
													<?php } ?>
												</div><!-- /card-deck card-deck-spaced -->
											</div><!-- /col-lg-12 -->
										</div><!-- /row -->
										<?php
									}
									if ( is_array( $indirizzi_di_ricerca_ids ) && count( $indirizzi_di_ricerca_ids ) > 0 ) {
										// recupero la lista degli indirizzi di ricerca.
										$indirizzi_di_ricerca = new WP_Query(
											array(
												'posts_per_page' => -1,
												'post_type'      => 'indirizzo-di-ricerca',
												'orderby'        => 'title',
												'order'          => 'ASC',
												'post__in'       => $indirizzi_di_ricerca_ids,
											)
										);
										?>
									<h3 id="art-par-indirizzi-di-ricerca"  class="mb-4"><?php _e( "Indirizzi di ricerca", "design_laboratori_italia" ); ?></h3>
									<div class="row variable-gutters mb-4">
										<div class="col-lg-12">
											<div	iv class="card-deck card-deck-spaced">
											<?php
											while ( $indirizzi_di_ricerca->have_posts() ) {
												$indirizzi_di_ricerca->the_post();
												$ID    = get_the_ID();
												$indirizzo_ricerca_title = get_the_title( $ID );
												?>
												<div class="card card-bg card-icon rounded">
													<div class="card-body">
														<svg class="icon svg-project">
															<use xmlns:xlink="http://www.w3.org/1999/xlink"
															xlink:href="#svg-project"></use>
														</svg>
														<div class="card-icon-content">
															<p>
																<strong><a href="<?php echo get_permalink(); ?>"><?php echo $indirizzo_ricerca_title; ?></a></strong>
															</p>
														</div><!-- /card-icon-content -->
													</div><!-- /card-body -->
												</div><!-- /card card-bg card-icon rounded -->
											<?php } ?>
											</div><!-- /card-deck card-deck-spaced -->
										</div><!-- /col-lg-12 -->
									</div><!-- /row -->
										<?php
									}
									if ( $pubblicazioni && $pubblicazioni->have_posts() ) {
										?>
										<h3 id="art-par-pubblicazioni"  class="mb-4"><?php _e( "Pubblicazioni", "design_laboratori_italia" ); ?></h3>
										<div class="row variable-gutters mb-4">
											<div class="col-lg-12">
												<div	iv class="card-deck card-deck-spaced">
												<?php
												while ( $pubblicazioni->have_posts() ) {
													$pubblicazioni->the_post();
													$ID = get_the_ID();
													$title = get_the_title( $ID );
													?>
													<div class="card card-bg card-icon rounded">
														<div class="card-body">
															<svg class="icon svg-project">
																<use xmlns:xlink="http://www.w3.org/1999/xlink"
																xlink:href="#svg-project"></use>
															</svg>
															<div class="card-icon-content">
																<p>
																	<strong><a href="<?php echo get_permalink(); ?>"><?php echo $title; ?></a></strong>
																</p>
																<small><?php echo get_field( 'descrizione_breve' ); ?></small>
																<small><?php echo get_field( 'autori' ); ?></small>
															</div><!-- /card-icon-content -->
														</div><!-- /card-body -->
													</div><!-- /card card-bg card-icon rounded -->
												<?php } ?>
												</div><!-- /card-deck card-deck-spaced -->
											</div><!-- /col-lg-12 -->
										</div><!-- /row -->
											<?php
									}

									if ( ( is_array( $allegato_cv ) && count( $allegato_cv ) > 0 ) || ( is_array( $altri_allegati ) && count( $altri_allegati ) > 0 ) ) {
										?>
										<h4 id="art-par-altre-info"  class="mb-4"><?php _e( "Ulteriori informazioni", "design_laboratori_italia" ); ?></h4>
										<div class="row variable-gutters mb-4">
											<div class="col-lg-12">
												<div class="card-deck card-deck-spaced">
													<div class="card card-bg card-icon rounded">
														<div class="card-body">
															<svg class="icon it-pdf-document">
																<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#it-pdf-document"></use>
															</svg>
															<div class="card-icon-content">
																<p>
																	<strong><a href="<?php echo $allegato_cv['url']; ?>" ><?php echo $allegato_cv['title']; ?></a></strong>
																</p>
															</div><!-- /card-icon-content -->
														</div><!-- /card-body -->
													</div><!-- /card card-bg card-icon rounded -->
													<div class="card card-bg card-icon rounded">
														<div class="card-body">
															<svg class="icon it-pdf-document">
																<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#it-pdf-document"></use>
															</svg>
															<div class="card-icon-content">
																<p>
																	<strong><a href="<?php echo $altri_allegati['url']; ?>" ><?php echo $altri_allegati['title']; ?></a></strong>
																</p>
															</div><!-- /card-icon-content -->
														</div><!-- /card-body -->
													</div><!-- /card card-bg card-icon rounded -->
												</div><!-- /card-deck card-deck-spaced -->
											</div><!-- /col-lg-12 -->
										</div><!-- /row -->
										<?php
									}
									?>

									<?php if ( ( $telefono != "" ) || ( $email != "" ) || ( $sitoweb != "" ) ) { ?>
										<h4 id="art-par-contatti"><?php _e( "Contatti", "design_laboratori_italia" ); ?></h4>
										<div class="row variable-gutters">
											<div class="col-lg-9">
												<ul>
													<?php if ( $telefono != "" ){ ?><li><strong><?php _e( "Telefono", "design_laboratori_italia" ) ; ?> : </strong> <?php echo $telefono; ?></li><?php } ?>
													<?php if ( $email != "" ){ ?><li><strong><?php _e( "Email", "design_laboratori_italia" ) ; ?> : </strong> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></li><?php } ?>
													<?php if ( $email != "" ){ ?><li><strong><?php _e( "Sito web", "design_laboratori_italia" ) ; ?> : </strong> <a href="<?php echo $sitoweb; ?>"><?php echo $sitoweb; ?></a></li><?php } ?>
												</ul>
											</div><!-- /col-lg-9 -->
										</div><!-- /row -->
									<?php } ?>
													<div class="row variable-gutters">
														<div class="col-lg-9">
															<?php get_template_part("template-parts/single/bottom"); ?>
														</div><!-- /col-lg-9 -->
													</div><!-- /row -->
							</article>
						</div><!-- /col-lg-8 -->
					</div><!-- /row -->
				</div><!-- /container -->
			</section>
		</main><!-- #main -->
<?php
get_footer();
