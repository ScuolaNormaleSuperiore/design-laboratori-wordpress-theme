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
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER BLOG-->
	<section id="banner-news" aria-describedby="Testo introduttivo news" class="bg-banner-news">
		<div class="section-muted  primary-bg-c1">
			<div class="container">
				<div class="row">
					<div class="col-sm-5 align-middle">
						<div class="hero-title text-left ms-4 pb-3 pt-5 ">
							<p class="card-date">
								<?php echo intval( $post_date->format( 'd' ) ) ; ?>
								&nbsp;
								<?php echo __( dli_get_monthname( $post_date->format( 'm' ), 'design_laboratori_italia' ) ); ?>
							</p>
							<h2 class="p-0  "><?php echo esc_attr( get_the_title() ); ?></h2>
							<p class="font-weight-normal">
								<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
							</p>
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
						</div>
					</div>
					<div class="col-sm-7">
					<?php
						if( $image_metadata['image_url'] ) {
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
				</div>
			</div>
		</div>
	</section>

	<!-- DETAGLIO BLOG -->
	<section id="news" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">
				<!-- SIDEBAR NEWS  -->
				<?php
					$link_msg =  __( 'Tutti gli articoli', 'design_laboratori_italia' );
				?>
				<div class="col-12 col-lg-2 border-end">
						<a href="<?php echo esc_url( $pg_link); ?>" title="<?php echo $link_msg; ?>" alt="<?php echo $link_msg; ?>" ><?php echo $link_msg; ?></a>
				</div>
				<!-- NEWS -->
				<div class="col-12 col-lg-10 it-page-sections-container">
					<div class="row p-4 pt-0">
						<p>
							<?php echo the_content(); ?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>

</main>


<?php
get_footer();
