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
$category  = dli_get_post_main_category( $post, 'category' );
$date      = get_the_date( DLI_ACF_DATE_FORMAT, $post );
$news_date = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
$image_url = get_the_post_thumbnail_url( 0, 'item-carousel' );
$pg        = dli_get_page_by_post_type( $post->post_type );
$pg_link   = get_permalink( $pg->ID );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER NOTIZIA-->
	<section id="banner-news" aria-describedby="Testo introduttivo news" class="bg-banner-news">
		<div class="section-muted  primary-bg-c1">
			<div class="container">
				<div class="row">
					<div class="col-sm-5 align-middle">
						<div class="hero-title text-left ms-4 pb-3 pt-5 ">
							<p class="card-date">
								<?php echo intval( $news_date->format( 'd' ) ) ; ?>
								&nbsp;
								<?php echo __( dli_get_monthname( $news_date->format( 'm' ), 'design_laboratori_italia' ) ); ?>
							</p>
							<h2 class="p-0  "><?php echo esc_attr( get_the_title() ); ?></h2>
							<p class="font-weight-normal">
								<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
							</p>
							<div class="chip chip-primary chip-lg chip-simple">
								<span class="chip-label"><?php echo esc_attr( $category['title'] ); ?></span>
							</div>
						</div>
					</div>
					<div class="col-sm-7">
						<?php
						if( $image_url ) {
						?>
						<img src="<?php echo $image_url; ?>" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes"  loading="lazy">
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- DETAGLIO NOTIZIA -->
	<section id="news" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">
				<!-- SIDEBAR NEWS  -->
				<?php
					$link_msg =  __( 'Tutte le notizie', 'design_laboratori_italia' );
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
