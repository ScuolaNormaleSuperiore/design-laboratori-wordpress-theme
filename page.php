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
$image_url = get_the_post_thumbnail_url( 0, 'item-carousel' );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PAGINA -->
	<section id="banner-news" aria-describedby="Testo introduttivo eventi" class="bg-banner-eventi">
		<div class="section-muted  primary-bg-c1">
			<div class="container">
				<div class="row">
					<div class="col-sm-5 align-middle">
						<div class="hero-title text-left ms-4 pb-3 pt-5 ">
							<h2 class="p-0  "><?php echo get_the_title(); ?></h2>
							<p class="font-weight-normal">
								<?php echo wp_trim_words( get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
							</p>
						</div>
					</div>
					<div class="col-sm-7">
					<?php
					if ( $image_url ) {
					?>
					<img src="<?php echo $image_url; ?>"
							alt="<?php echo esc_attr( get_the_title() ); ?>" 
							title="<?php echo esc_attr( get_the_title() ); ?>" 
							alt="<?php echo esc_attr( get_the_title() ); ?>" 
							class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes"  loading="lazy">
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- CONTENUTO PAGINA-->
	<section class="section bg-white">
		<div class="container container-border-top">
			<div class="row">
				<?php
					//Retrieve till second level pages
					$pages = get_pages( array(
						'child_of'     => $post->ID,
						'offset'       => 0,
						'parent'       => $post->ID,
					));
					if($pages) {
						?>
				<!-- SIDEBAR -->
				<div class="sidebar-wrapper col-12 col-lg-3">
					<h3><?php echo __( 'Pagine collegate', 'design_laboratori_italia' ); ?></h3>
					<div class="sidebar-linklist-wrapper">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<?php
								//Retrieve till second level pages
									$pages = get_pages( array(
										'child_of'     => $post->ID,
										'offset'       => 0,
										'parent'       => $post->ID,
									));

									foreach ( $pages as $pg ) {
										?>
										<li>
											<a class="list-item large medium right-icon active" href="#collapseOne" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseOne">
												<span class="list-item-title-icon-wrapper">
													<span><?php echo esc_attr( get_the_title( $pg ) ); ?></span>
													<svg class="icon icon-sm icon-primary right" aria-hidden="true">
														<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
													</svg>
												</span>
											</a>
											<?php
											$subspg = get_pages( array(
												'child_of'     => $pg->ID,
												'offset'       => 0,
												'parent'       => $pg->ID,
											));
											?>
											<ul class="link-sublist collapse show" id="collapseOne">
												<?php
												foreach( $subspg as $subpg ) {
													?>
													<li>
														<a class="list-item active" href="<?php echo get_permalink( $subpg->ID ); ?>">
															<span><?php echo esc_attr( get_the_title( $subpg ) ); ?></span>
														</a>
													</li>
													<?php
												}
												?>
											</ul>
											<?php
									}
								?>
							</ul>
						</div>
					</div>
				</div>
				<?php
					}
					?>
				<!-- FINE SIDEBAR -->
				<div class="col-lg-8 pt84">
					<article class="article-wrapper"><?php the_content(); ?></article>
				</div><!-- /col-lg-8 -->
			</div><!-- /row -->
		</div><!-- /container -->
	</section>

		</div>   <!-- END container -->

</main>


<?php
get_footer();