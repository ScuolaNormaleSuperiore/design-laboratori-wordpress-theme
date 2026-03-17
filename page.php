<?php
/**
 * Notizia template file
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

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PAGINA -->
	<section id="banner-paginabase" aria-describedby="dli-page-intro-desc" class="bg-banner-paginabase">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0"><?php echo esc_html( get_the_title() ); ?></h2>
					<p id="dli-page-intro-desc" class="font-weight-normal">
						<?php echo esc_html( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- CONTENUTO PAGINA-->
	<section id="paginabase" class="pb-3">
		<div class="container container-border-top pt-5">
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
					if ( $dli_pages ) {
						?>

				<!-- MENU LATERALE (INIZIO SIDEBAR) -->
				<div class="sidebar-wrapper border-end col-12 col-lg-3">
						<?php
						if ( 0 !== $post->post_parent ) {
							?>
						<a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" class="btn btn-primary btn-xs btn-me mb-5" role="button">
							<svg class="icon icon-sm icon-white me-2" role="img" aria-hidden="true" focusable="false">
								<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-left' ); ?>"></use>
							</svg>
							<?php echo esc_html__( 'Torna indietro', 'design_laboratori_italia' ); ?>
						</a>
							<?php
						}
						?>
						<h3><?php echo esc_html__( 'Pagine collegate', 'design_laboratori_italia' ); ?></h3>
					<div class="progress">
						<div class="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<div class="sidebar-linklist-wrapper">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<?php
								foreach ( $dli_pages as $dli_page ) {
									?>
											<li>
													<a class="list-item large medium right-icon <?php echo esc_attr( ( $post->ID === $dli_page->ID || $dli_page->ID === $post->post_parent ) ? 'active' : '' ); ?>" href="<?php echo esc_url( get_permalink( $dli_page->ID ) ); ?>">
													<span class="list-item-title-icon-wrapper">
														<span><?php echo esc_html( get_the_title( $dli_page ) ); ?></span>
													</span>
												</a>
											<?php
											// Show subpages of current branch page till second level.
											$dli_subpages        = get_pages(
												array(
													'child_of' => $dli_page->ID,
													'offset'   => 0,
													'parent'   => $dli_page->ID,
													'sort_column' => 'menu_order',
												)
											);
												$dli_subpage_ids = wp_list_pluck( $dli_subpages, 'ID' );
											if ( 0 !== $post->post_parent && ( $post->ID === $dli_page->ID || in_array( $post->ID, $dli_subpage_ids, true ) ) ) {
												?>
													<ul class="link-sublist">
													<?php
													foreach ( $dli_subpages as $dli_subpage ) {
														?>
															<li>
																<a class="list-item <?php echo esc_attr( ( $post->ID === $dli_subpage->ID ) ? 'active' : '' ); ?>" href="<?php echo esc_url( get_permalink( $dli_subpage->ID ) ); ?>">
																	<span><?php echo esc_html( get_the_title( $dli_subpage ) ); ?></span>
																</a>
															</li>
															<?php
													}
													?>
													</ul>
													<?php
											}
								}
								?>
							</ul>
						</div>
					</div>
				</div>
				<!-- FINE SIDEBAR -->
						<?php
					} else {
						?>
				<!-- La pagina non ha genitori e figli -->
				<div class="sidebar-wrapper border-end col-12 col-lg-3"></div>
						<?php
					}
					?>

				<!-- CORPO ARTICOLO-->
				<div class="col-lg-8 pt84">
					<div class="mb-4">
						<?php
						if ( ! empty( $dli_image_metadata['image_url'] ) ) {
							?>
								<figure class="mb-0">
									<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
										alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ?? '' ); ?>" 
										title="<?php echo esc_attr( $dli_image_metadata['image_title'] ?? '' ); ?>" 
										class="img-fluid">
									<?php
									if ( ! empty( $dli_image_metadata['image_caption'] ) ) {
										?>
										<figcaption class="figure-caption">
											<?php echo esc_html( $dli_image_metadata['image_caption'] ); ?>
										</figcaption>
										<?php
									}
									?>
								</figure>
								<?php
						}
						?>
					</div>
					<article class="article-wrapper"><?php the_content(); ?></article>

					<!-- NEWS ED EVENTI (related_items) -->
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

				</div><!-- /col-lg-8 -->

			</div><!-- /row -->
			</div><!-- /container -->
			</section>

	</main>


<?php
get_footer();
