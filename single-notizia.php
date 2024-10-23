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
$categories     = dli_get_post_categories( $post, 'category' );
$current_lang   = dli_current_language();
$cat_page       = DLI_PAGE_PER_CT[NEWS_POST_TYPE][$current_lang];
$date           = get_the_date( DLI_ACF_DATE_FORMAT, $post );
$news_date      = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
$image_metadata = dli_get_image_metadata( $post );
$pg             = dli_get_page_by_post_type( $post->post_type );
$pg_link        = get_permalink( $pg->ID );
$tags           = get_the_tags( $post->ID );
$current_url    = get_permalink();
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER NOTIZIA-->
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
							<?php echo intval( $news_date->format( 'd' ) ) ; ?>
							&nbsp;
							<?php echo __( dli_get_monthname( $news_date->format( 'm' ), 'design_laboratori_italia' ) ); ?>
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
							<div class="chip chip-primary chip-lg chip-simple border-light mt-3">
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

	<!-- CORPO NEWS -->
	<section id="news-section" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">
				<!-- SIDEBAR NEWS  -->
				<div class="col-12 col-lg-2 border-end">
					<!-- Tutte le notizie -->
					<div class="customSpacingDiv">
						<?php
							$link_msg =  __( 'Tutte le notizie', 'design_laboratori_italia' );
						?>
						<a class="customSpacing" href="<?php echo esc_url( $pg_link); ?>" title="<?php echo $link_msg; ?>" alt="<?php echo $link_msg; ?>" ><?php echo $link_msg; ?></a>
						<br /><br />
					</div>

					<!-- Condividi -->
					<?php get_template_part( 'template-parts/common/social_sharing'); ?>

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
							<div class="chip chip-simple chip-primary">
								<span class="chip-label"><?php echo esc_attr( $tag->name ); ?></span>
							</div>
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
