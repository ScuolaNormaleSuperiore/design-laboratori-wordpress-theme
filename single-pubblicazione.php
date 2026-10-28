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
// The theme prints the content with the_content(), which needs the post data of the current post.
setup_postdata( $post );

// A password-protected publication shows the title and the password form: the description, the
// authors and the external link are what the password protects. For such a post the_content()
// prints the password form, the same one that WordPress shows in every theme.
$dli_password_required = post_password_required( $post );

$dli_anno      = dli_get_field( 'anno' );
$dli_authors   = $dli_password_required ? array() : dli_get_publication_authors( $post );
$dli_url       = $dli_password_required ? '' : dli_get_field( 'url' );
$dli_file      = $dli_password_required ? null : dli_get_field( 'allegato' );
$dli_file_url  = is_array( $dli_file ) && ! empty( $dli_file['url'] ) ? $dli_file['url'] : '';
// Type and size of the file, when known: "PDF, 1.2 MB".
$dli_file_info = '' === $dli_file_url ? '' : implode(
	', ',
	array_filter(
		array(
			strtoupper( pathinfo( (string) wp_parse_url( $dli_file_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) ),
			! empty( $dli_file['filesize'] ) ? size_format( (int) $dli_file['filesize'] ) : '',
		)
	)
);
$dli_content   = trim( $post->post_content );
$dli_has_text  = ! $dli_password_required && '' !== $dli_content && '.' !== $dli_content;
$dli_tipologia = dli_get_post_main_category( $post, PUBLICATION_TYPE_TAXONOMY );
$dli_image_id  = get_post_thumbnail_id( $post );
$dli_image_url = $dli_image_id ? wp_get_attachment_image_url( $dli_image_id, 'full' ) : '';
$dli_image_alt = $dli_image_id ? trim( (string) get_post_meta( $dli_image_id, '_wp_attachment_image_alt', true ) ) : '';
if ( '' === $dli_image_alt ) {
	$dli_image_alt = __( 'Copertina della pubblicazione', 'design_laboratori_italia' );
}
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

	<!-- DETTAGLIO PUBBLICAZIONE: sidebar con copertina (o segnaposto verticale se manca
		una copertina reale) e condivisione; corpo con autori, descrizione e link esterno alla
		rivista o all'archivio istituzionale, se presenti. Stesse colonne delle altre schede
		con sidebar (col-lg-3 + col-lg-8 offset-lg-1), ma qui la sidebar non è un indice di
		navigazione: la pagina ha troppo poche sezioni per giustificarne uno. -->
	<section id="pubblicazione" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">
				<div class="col-12 col-lg-3 border-end">
					<div class="sticky-top pubblicazione-sidebar">
						<?php if ( $dli_image_url ) : ?>
							<figure class="figure mb-4 pubblicazione-copertina">
								<img class="figure-img img-fluid rounded shadow-sm mb-0" src="<?php echo esc_url( $dli_image_url ); ?>" alt="<?php echo esc_attr( $dli_image_alt ); ?>" />
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
						<?php the_content(); ?>
					<?php endif; ?>

					<?php if ( ! empty( $dli_authors ) ) : ?>
						<article id="autori" class="mb-4">
							<h3 class="h4"><?php echo esc_html__( 'Autori', 'design_laboratori_italia' ); ?></h3>
							<p>
								<?php foreach ( $dli_authors as $dli_index => $dli_author ) : ?>
									<?php echo $dli_index > 0 ? esc_html( ', ' ) : ''; ?>
									<?php if ( '' !== $dli_author['url'] ) : ?>
										<a href="<?php echo esc_url( $dli_author['url'] ); ?>"><?php echo esc_html( $dli_author['name'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $dli_author['name'] ); ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</p>
						</article>
					<?php endif; ?>

					<?php if ( $dli_has_text ) : ?>
						<article id="descrizione" class="mb-4">
							<h3 class="h4"><?php echo esc_html__( 'Descrizione', 'design_laboratori_italia' ); ?></h3>
							<?php the_content(); ?>
						</article>
					<?php endif; ?>

					<?php if ( $dli_url || $dli_file_url ) : ?>
						<article id="link" class="mb-4">
							<h3 class="h4 visually-hidden"><?php echo esc_html__( 'Link', 'design_laboratori_italia' ); ?></h3>
							<?php if ( $dli_url ) : ?>
								<a class="btn btn-primary btn-icon me-2 mb-2" href="<?php echo esc_url( $dli_url ); ?>" target="_blank" rel="noopener noreferrer">
									<span><?php echo esc_html__( 'Consulta la pubblicazione', 'design_laboratori_italia' ); ?></span>
									<span class="visually-hidden">(<?php echo esc_html__( 'si apre in una nuova finestra', 'design_laboratori_italia' ); ?>)</span>
									<svg class="icon icon-sm icon-inverse ms-1" aria-hidden="true" focusable="false">
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-external-link' ); ?>"></use>
									</svg>
								</a>
							<?php endif; ?>
							<?php if ( $dli_file_url ) : ?>
								<a class="btn btn-outline-primary btn-icon mb-2" href="<?php echo esc_url( $dli_file_url ); ?>" target="_blank" rel="noopener noreferrer">
									<span><?php echo esc_html__( 'Scarica la pubblicazione', 'design_laboratori_italia' ); ?></span>
									<?php if ( '' !== $dli_file_info ) : ?>
										<span>(<?php echo esc_html( $dli_file_info ); ?>)</span>
									<?php endif; ?>
									<span class="visually-hidden">(<?php echo esc_html__( 'si apre in una nuova finestra', 'design_laboratori_italia' ); ?>)</span>
									<svg class="icon icon-sm ms-1" aria-hidden="true" focusable="false">
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-download' ); ?>"></use>
									</svg>
								</a>
							<?php endif; ?>
						</article>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
