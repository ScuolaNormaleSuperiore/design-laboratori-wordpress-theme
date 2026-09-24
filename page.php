<?php
/**
 * Page template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$dli_image_metadata = dli_get_image_metadata( $post, 'full' );
$dli_image_metadata = is_array( $dli_image_metadata ) ? $dli_image_metadata : array();
$dli_related_items  = dli_get_field( 'pagine_collegate' );
?>

<main id="main-container" role="main">

	<!-- BANNER PAGINA: hero a due colonne con breadcrumb integrato; l'eventuale
		immagine in evidenza della pagina resta nel corpo (vedi sotto), non qui
		— stesso criterio già usato per Risorse tecniche. -->
	<section id="banner-paginabase" class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-paginabase-title">
		<div class="container">
			<div class="row align-items-stretch">
				<div class="col-12 col-lg-7">
					<section class="pt-2">
						<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
					</section>
					<div class="it-hero-text-wrapper px-lg-2">
						<h2 id="dli-hero-paginabase-title"><?php echo esc_html( get_the_title() ); ?></h2>
						<?php
						$dli_summary = dli_get_field( 'descrizione_breve' );
						if ( $dli_summary ) :
							?>
							<p class="fs-5"><?php echo esc_html( wp_trim_words( $dli_summary, DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-12 col-lg-5 d-none d-lg-block">
					<?php if ( dli_show_hero_decorative_image() ) : ?>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- CONTENUTO PAGINA-->
	<section id="paginabase" class="pb-3">
		<div class="container pt-5">
			<div class="row">
				<?php
					// Get top parent page ID.
					$dli_top_parent = dli_get_page_anchestor_id( $post );
					$dli_slugs      = dli_get_page_slug_anchestors( $post );
					$dli_slugs      = is_array( $dli_slugs ) ? array_reverse( $dli_slugs ) : array();
				if ( count( $dli_slugs ) > 1 && ( SLUG_LABORATORIO_IT === $dli_slugs[0] || SLUG_LABORATORIO_EN === $dli_slugs[0] ) ) {
					$dli_top_parent_page = get_page_by_path( $dli_slugs[0] . '/' . $dli_slugs[1] );
					if ( $dli_top_parent_page instanceof WP_Post ) {
						$dli_top_parent = $dli_top_parent_page->ID;
					}
				}
					// Retrieve second level pages.
					$dli_pages = get_pages(
						array(
							'child_of'    => $dli_top_parent,
							'offset'      => 0,
							'parent'      => $dli_top_parent,
							'sort_order'  => 'ASC',
							'sort_column' => 'menu_order',
						)
					);
					?>

				<!-- MENU LATERALE (SIDEBAR "Pagine collegate"): albero completo dei
					fratelli della pagina (e di tutti i loro discendenti, a qualunque
					profondità), non solo dei figli diretti della pagina corrente —
					come nel prototipo (sf-pagina-base-voce1.html). Nessun bottone
					"Torna indietro": con l'albero sempre visibile e la pagina padre
					già raggiungibile da breadcrumb non serve, come nel prototipo. -->
				<div class="sidebar-wrapper col-12 col-lg-3 border-end pb-3 mb-4 mb-lg-0">
					<?php if ( $dli_pages ) : ?>
						<div class="sticky-top" style="top: 1rem;">
							<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Pagine collegate', 'design_laboratori_italia' ); ?></h3>
							<div class="sidebar-linklist-wrapper">
								<div class="link-list-wrapper">
									<?php
									get_template_part(
										'template-parts/common/lista-pagine-collegate',
										null,
										array(
											'pages'     => $dli_pages,
											'current_post_id' => $post->ID,
											'top_level' => true,
										)
									);
									?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<!-- CORPO ARTICOLO: col-lg-8 offset-lg-1, stesso schema di colonne delle
					schede di dettaglio con sidebar. -->
				<div class="col-12 col-lg-8 offset-lg-1">
					<?php if ( ! empty( $dli_image_metadata['image_url'] ) ) : ?>
						<figure class="mb-4">
							<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
								alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ?? '' ); ?>"
								title="<?php echo esc_attr( $dli_image_metadata['image_title'] ?? '' ); ?>"
								class="img-fluid rounded">
							<?php if ( ! empty( $dli_image_metadata['image_caption'] ) ) : ?>
								<figcaption class="figure-caption mt-2">
									<?php echo esc_html( $dli_image_metadata['image_caption'] ); ?>
								</figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<?php the_content(); ?>

					<!-- NEWS ED EVENTI (pagine_collegate) -->
					<?php
					if ( $dli_related_items ) {
						get_template_part(
							'template-parts/common/sezione-related-items',
							null,
							array(
								'items' => $dli_related_items,
							)
						);
					}
					?>
				</div>

			</div>
		</div>
	</section>

	</main>


<?php
get_footer();
