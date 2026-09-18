<?php
/**
 * Template Name: Privacy
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

$dli_image_metadata = dli_get_image_metadata( $post, 'full' );
$dli_related_items  = dli_get_field( 'pagine_collegate' );
?>

<main id="main-container" role="main">

	<script>
		function checkLocalStorage() {
			const keyExists = localStorage.getItem('bs-ck3') !== null;
			const noDenyCookiesMsg = document.getElementById('dli_no_accepted_cookies_msg');
			const denyCookiesButton = document.getElementById('dli_deny_cookies_button');

			if (!noDenyCookiesMsg || !denyCookiesButton) {
				return;
			}

			if (keyExists) {
				noDenyCookiesMsg.style.display = 'none';
				denyCookiesButton.style.display = 'block';
			} else {
				noDenyCookiesMsg.style.display = 'block';
				denyCookiesButton.style.display = 'none';
			}
		}

		window.addEventListener('DOMContentLoaded', checkLocalStorage);

		function deleteYouTubeCookies() {
			const domain = 'youtube.com';
			document.cookie.split(';').forEach(function (cookie) {
				const cookieName = cookie.split('=')[0].trim();
				document.cookie = cookieName + '=; path=/; domain=' + domain + '; expires=Thu, 01 Jan 1970 00:00:00 GMT';
			});
		}

		function removeThirdPartiesCookies() {
			const keyExists = localStorage.getItem('bs-ck3') !== null;
			const noDenyCookiesMsg = document.getElementById('dli_no_accepted_cookies_msg');
			const denyCookiesButton = document.getElementById('dli_deny_cookies_button');

			if (keyExists) {
				localStorage.removeItem('bs-ck3');

				if (noDenyCookiesMsg && denyCookiesButton) {
					noDenyCookiesMsg.style.display = 'block';
					denyCookiesButton.style.display = 'none';
				}

				deleteYouTubeCookies();
			}
		}
	</script>

	<!-- BANNER PAGINA: hero a due colonne con breadcrumb integrato; l'eventuale
	     immagine in evidenza della pagina resta nel corpo (vedi sotto), non qui
	     — stesso criterio già usato per Risorse tecniche e per page.php. -->
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
							<p class="fs-5"><?php echo wp_kses_post( wp_trim_words( $dli_summary, DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
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

	<!-- CONTENUTO PAGINA -->
	<section id="paginabase" class="pb-3">
		<div class="container pt-5">
			<div class="row">
				<?php
				$dli_top_parent = dli_get_page_anchestor_id( $post );
				$dli_slugs      = array_reverse( dli_get_page_slug_anchestors( $post ) );

				if (
					count( $dli_slugs ) > 0 &&
					isset( $dli_slugs[1] ) &&
					( SLUG_LABORATORIO_IT === $dli_slugs[0] || SLUG_LABORATORIO_EN === $dli_slugs[0] )
				) {
					$dli_parent_page = get_page_by_path( $dli_slugs[0] . '/' . $dli_slugs[1] );
					if ( $dli_parent_page ) {
						$dli_top_parent = $dli_parent_page->ID;
					}
				}

				$dli_pages = get_pages(
					array(
						'child_of'    => $dli_top_parent,
						'offset'      => 0,
						'parent'      => $dli_top_parent,
						'sort_column' => 'menu_order',
					)
				);
				?>

				<!-- MENU LATERALE (SIDEBAR "Pagine collegate"): stesso pattern di page.php
				     (albero completo, sempre aperto, nessun bottone "Torna indietro"). -->
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
											'pages'           => $dli_pages,
											'current_post_id' => $post->ID,
											'top_level'       => true,
										)
									);
									?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<div class="col-12 col-lg-8 offset-lg-1">
					<?php if ( ! empty( $dli_image_metadata['image_url'] ) ) : ?>
						<figure class="mb-4">
							<img
								src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
								alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
								title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
								class="img-fluid rounded"
							>
							<?php if ( ! empty( $dli_image_metadata['image_caption'] ) ) : ?>
								<figcaption class="figure-caption mt-2">
									<?php echo esc_html( $dli_image_metadata['image_caption'] ); ?>
								</figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<?php the_content(); ?>

						<div>
							<div class="text-image-cta d-flex mb-0">
								<div class="content w-100">
									<h5 class="mb-3"><?php echo esc_html__( 'Gestione cookies', 'design_laboratori_italia' ); ?></h5>
								</div>
							</div>
							<div class="text-image-cta d-flex mb-5">
								<div class="content w-100">
									<div id="dli_no_accepted_cookies_msg">
										<p><?php echo esc_html__( 'Non hai installato cookie di terze parti', 'design_laboratori_italia' ); ?>.</p>
									</div>
									<div id="dli_deny_cookies_button">
										<p><?php echo esc_html__( 'Hai installato i seguenti cookies di terze parti:', 'design_laboratori_italia' ); ?></p>
										<p>
											<strong><?php echo esc_html__( 'Youtube per la visualizzazione di video', 'design_laboratori_italia' ); ?></strong>
											&nbsp;&nbsp;&nbsp;
											<button type="button" class="btn btn-primary" onclick="removeThirdPartiesCookies();">
												<?php echo esc_html__( 'Revoca', 'design_laboratori_italia' ); ?>
											</button>
										</p>
									</div>
								</div>
							</div>
						</div>

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

<?php get_footer(); ?>
