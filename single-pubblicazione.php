<?php
/**
 * Detail page for the post-type: pubblicazione.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_password_required = post_password_required( $post );

$dli_anno        = dli_get_field( 'anno' );
$dli_autori      = ( $dli_password_required ) ? array() : dli_get_field( 'autori' );
$dli_autori      = is_array( $dli_autori ) ? $dli_autori : array();
$dli_url         = ( $dli_password_required ) ? '' : dli_get_field( 'url' );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
$dli_description = ( $dli_password_required || '.' === $post->post_content ) ? '' : apply_filters( 'the_content', $post->post_content );
$dli_tipologia   = dli_get_post_main_category( $post, PUBLICATION_TYPE_TAXONOMY );
$dli_image_id    = get_post_thumbnail_id( $post );
$dli_image_url   = $dli_image_id ? wp_get_attachment_image_url( $dli_image_id, 'full' ) : '';
?>

<main id="main-container" role="main">

	<!-- BANNER PUBBLICAZIONE: nessuna foto di copertina nell'hero (la copertina
		del volume sta nella sidebar del corpo, non a piena larghezza), stesso
		pattern "senza foto" delle altre schede: hero a sfondo pieno con
		breadcrumb integrato. Campi: tipologia (tassonomia, la stessa dei
		filtri dell'elenco), titolo, anno. -->
	<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-pubblicazione-title">
		<div class="container">
			<div class="row align-items-stretch">
				<div class="col-12 col-lg-7">
					<section class="pt-2">
						<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
					</section>
					<div class="it-hero-text-wrapper px-lg-2">
						<?php if ( $dli_tipologia['id'] ) : ?>
							<span class="it-category">
								<span class="visually-hidden"><?php echo esc_html__( 'Tipologia:', 'design_laboratori_italia' ); ?></span>
								<?php echo esc_html( $dli_tipologia['title'] ); ?>
							</span>
						<?php endif; ?>
						<h2 id="dli-hero-pubblicazione-title"><?php echo esc_html( get_the_title() ); ?></h2>
						<?php if ( $dli_anno ) : ?>
							<p class="text-white mt-3">
								<span class="visually-hidden"><?php echo esc_html__( 'Anno di pubblicazione:', 'design_laboratori_italia' ); ?></span>
								<time datetime="<?php echo esc_attr( $dli_anno ); ?>"><?php echo esc_html( $dli_anno ); ?></time>
							</p>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-12 col-lg-5 d-none d-lg-block">
					<?php if ( dli_show_hero_decorative_image() ) : ?>
					<img class="hero-placeholder-scala-fissa" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- DETTAGLIO PUBBLICAZIONE: sidebar con copertina (o placeholder verticale
		se manca una copertina reale) e condivisione; corpo con autori,
		descrizione e link esterno alla rivista/archivio istituzionale, se
		presente. Stesse colonne delle altre schede con sidebar
		(col-lg-3 + col-lg-8 offset-lg-1), ma qui la sidebar non è un indice di
		navigazione: la pagina ha troppo poche sezioni per giustificarne uno. -->
	<section id="pubblicazione" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">
				<div class="col-12 col-lg-3 border-end">
					<div class="sticky-top" style="top: 1rem">
						<?php if ( $dli_image_url ) : ?>
							<figure class="figure mb-4 pubblicazione-copertina">
								<img class="figure-img img-fluid rounded shadow-sm mb-0" src="<?php echo esc_url( $dli_image_url ); ?>" alt="<?php echo esc_attr__( 'Copertina della pubblicazione', 'design_laboratori_italia' ); ?>" />
							</figure>
						<?php else : ?>
							<figure class="figure mb-4 pubblicazione-copertina pubblicazione-copertina-placeholder">
								<img class="figure-img img-fluid rounded shadow-sm mb-0" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-copertina-sns.png' ); ?>" alt="" />
							</figure>
						<?php endif; ?>
						<?php get_template_part( 'template-parts/common/social-sharing' ); ?>
					</div>
				</div>

				<div class="col-12 col-lg-8 offset-lg-1">
					<?php if ( $dli_password_required ) : ?>
						<?php echo get_the_password_form( $post ); ?>
					<?php endif; ?>

					<?php if ( ! empty( $dli_autori ) ) : ?>
						<article id="autori" class="mb-4">
							<h3 class="h4"><?php echo esc_html__( 'Autori', 'design_laboratori_italia' ); ?></h3>
							<p>
								<?php
								$dli_autori_html = array();
								foreach ( $dli_autori as $dli_autore ) {
									$dli_autore_id   = $dli_autore->ID;
									$dli_autore_nome = dli_get_persona_display_name( dli_get_field( 'nome', $dli_autore_id ), dli_get_field( 'cognome', $dli_autore_id ), get_the_title( $dli_autore_id ) );
									if ( dli_get_field( 'disattiva_pagina_dettaglio', $dli_autore_id ) ) {
										$dli_autori_html[] = esc_html( $dli_autore_nome );
									} else {
										$dli_autori_html[] = '<a href="' . esc_url( get_permalink( $dli_autore_id ) ) . '">' . esc_html( $dli_autore_nome ) . '</a>';
									}
								}
								echo wp_kses_post( implode( ', ', $dli_autori_html ) );
								?>
							</p>
						</article>
					<?php endif; ?>

					<?php if ( $dli_description ) : ?>
						<article id="descrizione" class="mb-4">
							<h3 class="h4"><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></h3>
							<?php echo wp_kses_post( $dli_description ); ?>
						</article>
					<?php endif; ?>

					<?php if ( $dli_url ) : ?>
						<article id="link" class="mb-4">
							<h3 class="h4 visually-hidden"><?php echo esc_html__( 'Link', 'design_laboratori_italia' ); ?></h3>
							<a class="btn btn-primary btn-icon" href="<?php echo esc_url( $dli_url ); ?>" target="_blank" rel="noopener noreferrer">
								<span><?php echo esc_html__( 'Consulta la pubblicazione', 'design_laboratori_italia' ); ?></span>
								<span class="visually-hidden">(<?php echo esc_html__( 'si apre in una nuova finestra', 'design_laboratori_italia' ); ?>)</span>
								<svg class="icon icon-sm icon-inverse ms-1" aria-hidden="true" focusable="false">
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-external-link' ); ?>"></use>
								</svg>
							</a>
						</article>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
