<?php
/**
 * Detail page for the post-type: post.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_categories     = dli_get_post_categories( $post, 'category' );
$dli_current_lang   = dli_current_language();
$dli_cat_page       = DLI_PAGE_PER_CT[ WP_DEFAULT_POST ][ $dli_current_lang ];
$dli_date           = get_the_date( DLI_ACF_DATE_FORMAT, $post );
$dli_post_date      = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
$dli_post_day       = $dli_post_date ? intval( $dli_post_date->format( 'd' ) ) : '';
$dli_post_month     = $dli_post_date ? dli_get_monthname( $dli_post_date->format( 'm' ) ) : '';
$dli_image_metadata = dli_get_image_metadata( $post );
$dli_pg             = dli_get_page_by_post_type( $post->post_type );
$dli_pg_link        = $dli_pg ? get_permalink( $dli_pg->ID ) : '';
$dli_tags           = get_the_tags( $post->ID );
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER BLOG-->
	<section class="it-hero-wrapper it-dark it-overlay it-hero-small-size"> 
		<!-- - img-->
		<div class="img-responsive-wrapper">
			<div class="img-responsive">
				<figure class="img-wrapper">
					<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
						alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
						title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
					>
					<?php
					if ( $dli_image_metadata['image_caption'] ) {
						?>
						<figcaption class="figure-caption"><?php echo esc_html( $dli_image_metadata['image_caption'] ); ?></figcaption>
						<?php
					}
					?>
				</figure>
			</div>
		</div>
		<!-- - texts-->
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="it-hero-text-wrapper bg-dark">
						<?php if ( $dli_post_date ) { ?>
						<span class="it-Categoria">
							<?php echo esc_html( $dli_post_day ); ?>
							&nbsp;
							<?php echo esc_html( $dli_post_month ); ?>
						</span>
						<?php } ?>
						<h2><?php echo esc_html( get_the_title() ); ?></h2>
						<p class="d-none d-lg-block">
							<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
						</p>
						<!-- categorie -->
							<?php
							foreach ( $dli_categories as $dli_category ) {
								$dli_cat_url = add_query_arg( 'cat', array( $dli_category['id'] ), get_site_url() . '/' . $dli_cat_page );
								?>
							<div class="chip chip-primary chip-lg chip-simple">
								<a class="text-decoration-none" href="<?php echo esc_url( $dli_cat_url ); ?>">
									<span class="chip-label"><?php echo esc_html( $dli_category['title'] ); ?></span>
								</a>
							</div>
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
							$dli_link_msg = __( 'Tutti gli articoli', 'design_laboratori_italia' );
						?>
						<a href="<?php echo esc_url( $dli_pg_link ); ?>" title="<?php echo esc_attr( $dli_link_msg ); ?>"><?php echo esc_html( $dli_link_msg ); ?></a>
						<br /><br />
					</div>

					<!-- Condividi -->
					<?php get_template_part( 'template-parts/common/social-sharing' ); ?>

					<?php
					if ( $dli_tags ) {
						?>
					<div class="mt-4 mb-4">
						<h3 class="mb-0 h6">
							<small><?php echo esc_html__( 'Argomenti', 'design_laboratori_italia' ); ?></small>
						</h3>
						<?php
						foreach ( $dli_tags as $dli_tag ) {
							?>
							<div class="chip chip-simple chip-primary">
								<span class="chip-label"><?php echo esc_html( $dli_tag->name ); ?></span>
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
						<article id="news-body">
							<?php the_content(); ?>
						</article>
					</div>
				</div>
			</div>
		</div>
	</section>

</main>


<?php
get_footer();
