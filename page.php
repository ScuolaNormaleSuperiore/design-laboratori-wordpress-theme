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

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PAGINA -->
	<section id="banner-news" aria-describedby="Testo introduttivo eventi" class="bg-banner-eventi">
		<div class="section-muted  primary-bg-c1">
			<div class="container">
				<div class="row">
					<div class="col-sm-5 align-middle">
						<div class="hero-title text-left ms-4 pb-3 pt-5 ">
							<h2 class="p-0  "><?php echo get_the_title( ); ?></h2>
							<p class="font-weight-normal">
								<?php echo wp_trim_words( get_field( 'descrizione_breve', $last_hero_event ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
							</p>
						</div>
					</div>
					<div class="col-sm-7">
					<?php
					if ( $image_url ) {
					?>
					<img src="<?php echo $image_url; ?>"
							alt="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>" 
							title="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>" 
							alt="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>" 
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
			<div class="row variable-gutters d-flex justify-content-center">
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