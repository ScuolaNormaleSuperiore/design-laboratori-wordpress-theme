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

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PAGINA -->
	<section id="banner-paginabase" aria-describedby="dli-privacy-intro-desc" class="bg-banner-paginabase">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0"><?php echo esc_html( get_the_title() ); ?></h2>
					<p id="dli-privacy-intro-desc" class="font-weight-normal">
						<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- CONTENUTO PAGINA -->
	<section id="paginabase" class="pb-3">
		<div class="container container-border-top pt-5">
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

				<?php if ( $dli_pages ) : ?>
					<div class="sidebar-wrapper border-end col-12 col-lg-3">
						<?php if ( 0 !== $post->post_parent ) : ?>
							<a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" class="btn btn-primary btn-xs btn-me mb-5" role="button">
								<svg class="icon icon-sm icon-white me-2" role="img" aria-labelledby="go-back-title">
									<title id="go-back-title"><?php echo esc_html__( 'Torna indietro', 'design_laboratori_italia' ); ?></title>
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-left' ); ?>"></use>
								</svg>
								<?php echo esc_html__( 'Torna indietro', 'design_laboratori_italia' ); ?>
							</a>
						<?php endif; ?>

						<h3><?php echo esc_html__( 'Pagine collegate', 'design_laboratori_italia' ); ?></h3>
						<div class="progress">
							<div class="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="sidebar-linklist-wrapper">
							<div class="link-list-wrapper">
								<ul class="link-list">
									<?php foreach ( $dli_pages as $dli_page ) : ?>
										<li>
											<a class="list-item large medium right-icon <?php echo ( $post->ID === $dli_page->ID || $dli_page->ID === $post->post_parent ) ? 'active' : ''; ?>" href="<?php echo esc_url( get_permalink( $dli_page->ID ) ); ?>">
												<span class="list-item-title-icon-wrapper">
													<span><?php echo esc_html( get_the_title( $dli_page ) ); ?></span>
												</span>
											</a>
											<?php
											$dli_subpages = get_pages(
												array(
													'child_of'    => $dli_page->ID,
													'offset'      => 0,
													'parent'      => $dli_page->ID,
													'sort_column' => 'menu_order',
												)
											);
											?>
											<?php if ( 0 !== $post->post_parent && ( $post->ID === $dli_page->ID || in_array( $post, $dli_subpages, true ) ) ) : ?>
												<ul class="link-sublist">
													<?php foreach ( $dli_subpages as $dli_subpage ) : ?>
														<li>
															<a class="list-item <?php echo ( $post->ID === $dli_subpage->ID ) ? 'active' : ''; ?>" href="<?php echo esc_url( get_permalink( $dli_subpage->ID ) ); ?>">
																<span><?php echo esc_html( get_the_title( $dli_subpage ) ); ?></span>
															</a>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>
				<?php else : ?>
					<div class="sidebar-wrapper border-end col-12 col-lg-3"></div>
				<?php endif; ?>

				<div class="col-lg-8 pt84">
					<div class="mb-4">
						<?php if ( $dli_image_metadata['image_url'] ) : ?>
							<img
								src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
								alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
								title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
								class="img-fluid"
							>
							<?php if ( $dli_image_metadata['image_caption'] ) : ?>
								<figcaption class="figure-caption">
									<?php echo esc_html( $dli_image_metadata['image_caption'] ); ?>
								</figcaption>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<article class="article-wrapper">
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

					</article>

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
