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
$image_metadata = dli_get_image_metadata( $post, 'page-body' );
$related_items  = dli_get_field( 'pagine_collegate' );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PAGINA -->
	<section id="banner-paginabase" aria-describedby="Testo introduttivo paginabase" class="bg-banner-paginabase">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0"><?php echo get_the_title(); ?></h2>
					<p class="font-weight-normal">
						<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- CONTENUTO PAGINA-->
	<section class="section bg-white">
		<div class="container container-border-top">
			<div class="row">
				<?php
					//get top parent page id
					$top_parent = dli_get_page_anchestor_id( $post );
					$slugs      = array_reverse ( dli_get_page_slug_anchestors( $post ) );
					if ( count ( $slugs ) > 0 && ($slugs[0] === SLUG_LABORATORIO_IT || $slugs[0] === SLUG_LABORATORIO_EN ) ) {
						$top_parent = get_page_by_path( $slugs[0] . '/' . $slugs[1] )->ID;
					}
					//Retrieve till second level pages
						$pages = get_pages( array(
							'child_of'     => $top_parent,
							'offset'       => 0,
							'parent'       => $top_parent,
						));
					if($pages) {
						?>

				<!-- MENU LATERALE (INIZIO SIDEBAR) -->
				<div class="sidebar-wrapper border-end col-12 col-lg-3">
					<?php if ( $post->post_parent !== 0 ) {
					?>
					<a href="<?php echo get_permalink( $post->post_parent ); ?>" class="btn btn-primary btn-xs btn-me mb-5" role="button">
						<svg class="icon icon-sm icon-white me-2">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-left';?>"></use>
						</svg>
						<?php echo __( 'Torna indietro', 'design_laboratori_italia' ); ?>
					</a>
					<?php
					}
					?>
					<h3><?php echo __( 'Pagine collegate', 'design_laboratori_italia' ); ?></h3>
					<div class="progress">
						<div class="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<div class="sidebar-linklist-wrapper">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<?php
									foreach ( $pages as $pg ) {
										?>
										<li>
											<a class="list-item large medium right-icon <?php if ( $post->ID === $pg->ID || $pg->ID === $post->post_parent ) echo "active"; ?>" href="<?php echo get_permalink( $pg->ID ); ?>">
												<span class="list-item-title-icon-wrapper">
													<span><?php echo esc_attr( get_the_title( $pg ) ); ?></span>
												</span>
											</a>
											<?php
											//Show subpages of current branch page till second level.
											$subspg = get_pages( array(
												'child_of' => $pg->ID,
												'offset'   => 0,
												'parent'   => $pg->ID,
											));
											if ( $post->post_parent !== 0 && ($post->ID === $pg->ID || in_array( $post, $subspg ) ) ) {
												?>
												<ul class="link-sublist">
													<?php
													foreach( $subspg as $subpg ) {
														?>
														<li>
															<a class="list-item <?php if ( $post->ID === $subpg->ID ) echo "active"; ?>" href="<?php echo get_permalink( $subpg->ID ); ?>">
																<span><?php echo esc_attr( get_the_title( $subpg ) ); ?></span>
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
				}
				?>

				<!-- CORPO ARTICOLO-->
				<div class="col-lg-8 pt84">
					<div class="mb-4">
						<?php
						if ( $image_metadata['image_url'] ) {
						?>
							<figure class="figure">
								<img src="<?php echo $image_metadata['image_url']; ?>"
										alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>" 
										title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
										class="d-block mx-lg-auto img-fluid figure-img" loading="lazy">
								<?php
									if( $image_metadata['image_caption'] ) {
								?>
									<figcaption class="figure-caption"><?php echo esc_attr( $image_metadata['image_caption'] ); ?></figcaption>
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
						if ( $related_items ){
							get_template_part(
								'template-parts/common/sezione-related-items',
								null,
								array(
									'related_items' => $related_items,
								)
							);
						}
					?>

				</div><!-- /col-lg-8 -->

			</div><!-- /row -->
		</div><!-- /container -->
	</section>

		</div>   <!-- END container -->

</main>


<?php
get_footer();
