<?php
/**
 * Articolo blog template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$categories     = dli_get_post_categories( $post, 'category' );
$current_lang   = dli_current_language();
$cat_page       = DLI_PAGE_PER_CT[WP_DEFAULT_POST][$current_lang];
$date           = get_the_date( DLI_ACF_DATE_FORMAT, $post );
$post_date      = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
$image_metadata = dli_get_image_metadata( $post );
$pg             = dli_get_page_by_post_type( $post->post_type );
$pg_link        = get_permalink( $pg->ID );
$tags           = get_the_tags( $post->ID );
$current_url    = get_permalink();
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER BLOG-->
	<section class="it-hero-wrapper it-dark it-overlay it-hero-small-size"> 
		<!-- - img-->
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<div class="img-wrapper">
					<img src="<?php echo $image_metadata['image_url']; ?>"
						alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>"
						title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>"
					>
				</div>
			</div>
		</div>
		<!-- - texts-->
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<span class="it-Categoria">
							<?php echo intval( $post_date->format( 'd' ) ) ; ?>
							&nbsp;
							<?php echo __( dli_get_monthname( $post_date->format( 'm' ), 'design_laboratori_italia' ) ); ?>
						</span>
						<h2><?php echo esc_attr( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block">
							<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
						</p>
						<!-- categorie -->
							<?php
								foreach( $categories as $category ) {
									$cat_url = add_query_arg( 'cat', array( $category['id'] ), get_site_url() . '/' . $cat_page );
							?>
							<div class="chip chip-primary chip-lg chip-simple">
								<a class="text-decoration-none" href="<?php echo $cat_url ?>">
									<span class="chip-label"><?php echo esc_attr( $category['title'] ); ?></span>
								</a>
							</div>
							<?php
								}
							?>
						<?php
							if( $image_metadata['image_caption'] ) {
							?>
								<figcaption class="figure-caption"><?php echo esc_attr( $image_metadata['image_caption'] ); ?></figcaption>
							<?php
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETAGLIO BLOG -->
	<section id="news" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">
				<!-- SIDEBAR BLOG  -->
				<div class="col-12 col-lg-2 border-end">
					<!-- Tutte le notizie -->
					<div>
						<?php
							$link_msg =  __( 'Tutti gli articoli', 'design_laboratori_italia' );
						?>
						<a href="<?php echo esc_url( $pg_link); ?>" title="<?php echo $link_msg; ?>" alt="<?php echo $link_msg; ?>" ><?php echo $link_msg; ?></a>
						<br /><br />
					</div>
					<!-- Condividi -->
					<div class="dropdown d-inline">
						<button class="btn btn-dropdown dropdown-toggle" type="button" id="shareActions" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<svg class="icon" aria-hidden="true" focusable="false">
								<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-share' ?>"></use>
							</svg>
							<small>
								<?php echo __( 'Condividi', 'design_laboratori_italia' ); ?>
							</small>
						</button>
						<div class="dropdown-menu shadow-lg" aria-labelledby="shareActions">
							<div class="link-list-wrapper">
								<ul class="link-list">
									<li>
										<a class="list-item" href="https://facebook.com/sharer/sharer.php?u=<?php echo esc_url( $current_url ) ; ?>" target="_blank" rel="noopener" aria-label="Share on Facebook">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'?>"></use>
											</svg>
											<span>Facebook</span>
										</a>
									</li>
									<li>
										<a class="list-item" href="https://twitter.com/share?url=<?php echo esc_url( $current_url ) ; ?>">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'?>"></use>
											</svg>
											<br/>
											<span>Twitter</span>
										</a>
									</li>
									<li>
										<a class="list-item" href="http://www.linkedin.com/shareArticle?mini=true&amp;url#=">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-linkedin'?>"></use>
											</svg>
											<span>Linkedin</span>
										</a>
									</li>
									<li>
										<a class="list-item" href="whatsapp://send?text==url" target="_blank" rel="noopener" aria-label="Share on Whatsapp">
											<svg class="icon" aria-hidden="true" focusable="false">
												<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-whatsapp'?>"></use>
											</svg>
											<span>Whatsapp</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<?php
					if ( $tags ){
					?>
					<div class="mt-4 mb-4">
						<h6 class="mb-0">
							<small><?php echo __( 'Argomenti', 'design_laboratori_italia' ); ?></small>
						</h6>
						<?php
						foreach( $tags as $tag ) {
						?>
							<!-- <a class="text-decoration-none" href="#"> -->
							<div class="chip chip-simple chip-primary">
								<span class="chip-label"><?php echo esc_attr( $tag->name ); ?></span>
							</div>
							<!-- </a>-->
						<?php
						}
						?>
					</div>
					<?php
					}
					?>
				</div>
				
				<!-- CORPO DELLA NEWS -->
				<div class="col-12 col-lg-9 it-page-sections-container">
					<div class="row p-4 pt-0">
						<article id="news">
							<?php echo the_content(); ?>
						</article>
					</div>
				</div>
			</div>
		</div>
	</section>

</main>


<?php
get_footer();
