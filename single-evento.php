<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Design_Scuole_Italia
 */
global $post, $autore, $luogo, $c;
get_header();

$link_schede_luoghi = dsi_get_meta("link_schede_luoghi");
$link_schede_documenti = dsi_get_meta("link_schede_documenti");
$file_documenti = dsi_get_meta("file_documenti");

?>
    <main id="main-container" class="main-container greendark">
		<?php get_template_part("template-parts/common/breadcrumb"); ?>

		<?php while ( have_posts() ) :  the_post();
			$image_url = get_the_post_thumbnail_url($post, "item-gallery");
			$autore = get_user_by("ID", $post->post_author);
			?>
            <section class="section bg-white article-title">
				<?php if(has_post_thumbnail($post)){ ?>
                    <div class="title-img" style="background-image: url('<?php echo $image_url; ?>');"></div>
					<?php
					$colsize = 6;
				}else{
					$colsize = 12;
				} ?>
                <div class="container">
                    <div class="row variable-gutters">
                        <div class="col-md-<?php echo $colsize; ?> flex align-items-center">
                            <div class="title-content">
                                <h1 class="h2"><?php the_title(); ?></h1>
                                <h3 class="text-greendark mb-3"><?php echo dsi_get_date_evento($post); ?></h3>
                                <p class="mb-0"><?php echo dsi_get_meta("descrizione"); ?></p>
								<?php get_template_part("template-parts/common/badges-argomenti"); ?>
								<?php
								$link_schede_notizia = dsi_get_meta("link_schede_notizia");
								if(count($link_schede_notizia)){
									foreach ($link_schede_notizia as $id_notizia){
										$notizia = get_post($id_notizia);
										?>
                                        <div class="text-icon">
                                            <a href="<?php echo get_permalink($notizia); ?>">
                                                <svg class="icon svg-link"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-link"></use></svg>
                                                <p><?php echo $notizia->post_title; ?></p>
                                            </a>
                                        </div>
										<?php
									}
								}
								?>
                            </div><!-- /title-content -->
                        </div><!-- /col-md-6 -->
                    </div><!-- /row -->
                </div><!-- /container -->
            </section>

            <section class="section bg-white">
                <div class="container container-border-top">
                    <div class="row variable-gutters">
                        <div class="col-lg-3 aside-border px-0">
                            <aside class="aside-main aside-sticky">
                                <div class="aside-title">
                                    <a class="toggle-link-list" data-toggle="collapse" href="#lista-paragrafi" role="button" aria-expanded="true" aria-controls="lista-paragrafi">
                                        <span><?php _e("Indice dell'evento", "design_scuole_italia"); ?></span>
                                        <svg class="icon icon-toggle svg-arrow-down-small"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-arrow-down-small"></use></svg>
                                    </a>
                                </div>
                                <div id="lista-paragrafi" class="link-list-wrapper collapse show">
                                    <ul class="link-list">
                                        <li>
                                            <a class="list-item scroll-anchor-offset" href="#art-par-cosa" title="Vai al paragrafo <?php _e("Cos'è", "design_scuole_italia"); ?>"><?php _e("Cos'è", "design_scuole_italia"); ?></a>
                                        </li>
										<?php 	if(count($link_schede_luoghi)) { ?>
                                            <li>
                                                <a class="list-item scroll-anchor-offset" href="#art-par-luogo" title="Vai al paragrafo <?php _e("Luogo", "design_scuole_italia"); ?>"><?php _e("Luogo", "design_scuole_italia"); ?></a>
                                            </li>
										<?php } ?>
                                        <li>
                                            <a class="list-item scroll-anchor-offset" href="#art-par-date" title="Vai al paragrafo <?php _e("Date e Orari", "design_scuole_italia"); ?>"><?php _e("Date e Orari", "design_scuole_italia"); ?></a>
                                        </li>
                                        <li>
                                            <a class="list-item scroll-anchor-offset" href="#art-par-costi" title="Vai al paragrafo <?php _e("Costi", "design_scuole_italia"); ?>"><?php _e("Costi", "design_scuole_italia"); ?></a>
                                        </li>
                                        <li>
                                            <a class="list-item scroll-anchor-offset" href="#art-par-contatti" title="Vai al paragrafo <?php _e("Contatti", "design_scuole_italia"); ?>"><?php _e("Contatti", "design_scuole_italia"); ?></a>
                                        </li>
										<?php if((is_array($link_schede_documenti) && count($link_schede_documenti)>0) || (is_array($file_documenti) && count($file_documenti)>0)){ ?>
                                            <li>
                                                <a class="list-item scroll-anchor-offset" href="#art-par-altro" title="Vai al paragrafo <?php _e("Ulteriori informazioni", "design_scuole_italia"); ?>">Ulteriori informazioni<?php _e("", "design_scuole_italia"); ?></a>
                                            </li>
										<?php } ?>
                                        <li>
                                            <a class="list-item scroll-anchor-offset" href="#art-par-correlati" title="Vai al paragrafo <?php _e("Contenuti correlati", "design_scuole_italia"); ?>">Contenuti correlati<?php _e("", "design_scuole_italia"); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </aside>

                        </div>
                        <div class="col-lg-6">
                            <article class="article-wrapper pt-4 px-3">
                                <h4 id="art-par-cosa"><?php _e("Cos'è", "design_scuole_italia"); ?></h4>
								<?php the_content(); ?>
								<?php
								global $gallery;
								$gallery = dsi_get_meta("gallery");
								get_template_part("template-parts/single/gallery");

								$video = dsi_get_meta("video");
								if($video) { ?>
                                    <div class="video-container my-4">
										<?php echo wp_oembed_get ($video); ?>
                                    </div>
								<?php } ?>
                                <h6><?php _e("Destinatari", "design_scuole_italia"); ?></h6>
								<?php
								$descrizione_destinatari = dsi_get_meta("descrizione_destinatari");
								echo wpautop($descrizione_destinatari);
								?>

                                <h6><?php _e("Parteciperanno", "design_scuole_italia"); ?></h6>

                                <div class="card-deck card-deck-spaced mb-2">
									<?php
									$persone_amministrazione = dsi_get_meta("persone_amministrazione");
									foreach ($persone_amministrazione as $idutente) {
										$autore = get_user_by("ID", $idutente);
										?>
                                        <div class="card card-bg card-avatar rounded">
                                            <a href="<?php echo get_author_posts_url($idutente); ?>">
                                                <div class="card-body">
													<?php get_template_part("template-parts/autore/card"); ?>
                                                </div>
                                            </a>
                                        </div><!-- /card card-bg card-avatar rounded -->
										<?php
									}
									?>
                                </div><!-- /card-deck -->

                                <h4 id="art-par-luogo"><?php _e("Luogo", "design_scuole_italia"); ?></h4>

								<?php
								$c = 0;
								if(count($link_schede_luoghi)) {
									foreach ( $link_schede_luoghi as $idluogo ) {
										$c ++;
										$luogo = get_post( $idluogo );
										get_template_part( "template-parts/luogo/card", "large" );
									}
								}
								?>

                                <h4 id="art-par-date"><?php _e("Date e Orari", "design_scuole_italia"); ?></h4>
                                <div class="calendar-vertical mb-5">
									<?php
									$timestamp_inizio = dsi_get_meta("timestamp_inizio");
									$timestamp_fine= dsi_get_meta("timestamp_fine");
									$begin = new DateTime(date_i18n("c",$timestamp_inizio));
									$end = new DateTime(date_i18n("c",$timestamp_fine));
									$ora_inizio = date_i18n("H:i", $timestamp_inizio);
									$ora_fine = date_i18n("H:i", $timestamp_fine);
									for($i = $begin; $i <= $end; $i->modify('+1 day')){ ?>
                                        <div class="calendar-date">
                                            <div class="calendar-date-day">
                                                <p><?php  echo date_i18n("d", $i->getTimestamp()); ?></p>
                                                <small><?php  echo date_i18n("M", $i->getTimestamp()); ?></small>
                                            </div><!-- /calendar-date-day -->
                                            <div class="calendar-date-description rounded">
                                                <div class="calendar-date-description-content">
                                                    <p><?php echo $ora_inizio; ?><?php if($ora_fine != $ora_inizio) echo " - ".$ora_fine; ?></p>
                                                </div><!-- /calendar-date-description-content -->
                                            </div><!-- /calendar-date-description -->
                                        </div><!-- /calendar-date -->
									<?php } ?>
                                </div><!-- /calendar-vertical -->
								<?php

								?>
                                <h4 id="art-par-costi"><?php _e("Costi", "design_scuole_italia"); ?></h4>
								<?php
								$tipo_evento = dsi_get_meta("tipo_evento");
								$prezzo = dsi_get_meta("prezzo");
								if($tipo_evento == "gratis"){
									echo "<p>Evento Gratuito</p>";
								}else {
									foreach ($prezzo as $biglietto) {
										?>
                                        <div class="text-border-left">
                                            <div class="text-icon">
                                                <svg class="icon svg-ticket">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#svg-ticket"></use>
                                                </svg>
                                                <span><?php echo $biglietto["tipo_biglietto"]; ?></span>
                                            </div>
                                            <p class="price"><strong>€ <?php echo $biglietto["prezzo"]; ?></strong></p>
                                            <p><?php echo $biglietto["descrizione"]; ?></p>
                                        </div><!-- /text-border-left -->
										<?php
									}
								}
								?>


                                <h4 id="art-par-contatti"><?php _e("Contatti", "design_scuole_italia"); ?></h4>
								<?php
								$organizzato_da_scuola = dsi_get_meta("organizzato_da_scuola");
								$link_strutture= dsi_get_meta("link_struttura_organizzativa");

								if($organizzato_da_scuola == "si") {
									?>
                                    <h6><?php _e( "Organizzato da", "design_scuole_italia" ); ?></h6>
                                    <div class="card-deck card-deck-spaced">
										<?php
										global $icon_color, $second_icon_color;
										$icon_color        = "greendark";
										$second_icon_color = "#c8edc3";
										foreach ( $link_strutture as $id_struttura ) {
											$struttura = get_post( $id_struttura );

											get_template_part( "template-parts/struttura/card" );
										}
										?>
                                    </div><!-- /card-deck card-deck-spaced -->
									<?php
								}
								?>
                                <div class="in-evidence mb-5 py-4 pl-2 pr-2">
                                    <ul class="mb-0">
										<?php if(dsi_get_meta("website") != ""){ ?><li><strong class="mr-2"><?php _e("Sito web:", "design_scuole_italia"); ?></strong> <a href="<?php echo dsi_get_meta("website"); ?>"><?php echo dsi_get_meta("website"); ?></a></li><?php } ?>
										<?php if(dsi_get_meta("contatto_telefono") != ""){ ?><li><strong class="mr-2"><?php _e("Telefono:", "design_scuole_italia"); ?></strong> <?php echo dsi_get_meta("contatto_telefono"); ?></li><?php } ?>
										<?php if(dsi_get_meta("contatto_email") != ""){ ?><li><strong class="mr-2"><?php _e("Email:", "design_scuole_italia"); ?></strong> <a href="mailto:<?php echo dsi_get_meta("contatto_email"); ?>"><?php echo dsi_get_meta("contatto_email"); ?></a></li><?php } ?>
										<?php if(dsi_get_meta("patrocinato") != ""){ ?><li><strong class="mr-2"><?php _e("Patrocinato da:", "design_scuole_italia"); ?></strong> <?php echo dsi_get_meta("patrocinato"); ?></li><?php } ?>
										<?php if(dsi_get_meta("sponsor") != ""){ ?><li><strong class="mr-2"><?php _e("Sponsor:", "design_scuole_italia"); ?></strong> <?php echo dsi_get_meta("sponsor"); ?></li><?php } ?>
                                    </ul>
                                </div>
								<?php if((is_array($link_schede_documenti) && count($link_schede_documenti)>0) || (is_array($file_documenti) && count($file_documenti)>0)){ ?>
                                    <h4 id="art-par-altro"><?php _e("Ulteriori informazioni", "design_scuole_italia"); ?></h4>
                                    <h6><?php _e("Documenti", "design_scuole_italia"); ?></h6>
                                    <div class="card-deck card-deck-spaced">
										<?php
										if(is_array($link_schede_documenti) && count($link_schede_documenti)>0) {
											global $documento;
											foreach ( $link_schede_documenti as $link_scheda_documento ) {
												$documento = get_post( $link_scheda_documento );
												get_template_part( "template-parts/documento/card" );
											}
										}

										global $idfile, $nomefile;
										if(is_array($file_documenti) && count($file_documenti)>0) {

											foreach ( $file_documenti as $idfile => $nomefile ) {
												get_template_part( "template-parts/documento/file" );
											}
										}

										?>
                                    </div><!-- /card-deck card-deck-spaced -->
								<?php } ?>
								<?php get_template_part("template-parts/single/bottom"); ?>
                            </article>
                        </div><!-- /col-lg-6 -->
                        <div class="col-lg-3 aside-border-left px-0">
                            <div class="aside-sticky">
								<?php get_template_part("template-parts/evento/calendar"); ?>
                            </div>
                        </div><!-- /col-lg-3 -->
                    </div><!-- /row -->
                </div><!-- /container -->
            </section>

			<?php get_template_part("template-parts/single/more-posts"); ?>

		<?php  	endwhile; // End of the loop. ?>
    </main><!-- #main -->
<?php
get_footer();