<?php
/**
 * Detail page for the post-type: notizia.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_main_category  = dli_get_post_main_category( $post, 'category' );
$dli_summary        = dli_get_field( 'descrizione_breve' );
$dli_date           = get_the_date( DLI_ACF_DATE_FORMAT, $post );
$dli_news_date      = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
$dli_news_day       = $dli_news_date ? intval( $dli_news_date->format( 'd' ) ) : '';
$dli_news_month     = $dli_news_date ? dli_get_monthname( $dli_news_date->format( 'm' ) ) : '';
$dli_image_metadata = dli_get_image_metadata( $post );
$dli_has_photo      = ! empty( $dli_image_metadata['image_url'] );
$dli_tags           = get_the_tags( $post->ID );
?>

<main id="main-container" role="main">

	<!-- BANNER NOTIZIA: hero a due colonne, con la foto reale in evidenza della news
		(formato naturale, nessun object-fit forzato) quando esiste, altrimenti il
		frammento decorativo placeholder-sns.png come nelle altre pagine senza foto:
		vedi sf-scheda-news.html, l'immagine in evidenza di una news arriva dal
		backoffice con formato/orientamento molto variabili e un crop forzato a piena
		larghezza la penalizzerebbe. -->
	<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-news-title">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-7">
					<section class="pt-2">
						<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
					</section>
					<div class="it-hero-text-wrapper px-lg-2">
						<?php if ( $dli_main_category && ! empty( $dli_main_category['title'] ) ) : ?>
							<span class="it-category"><?php echo esc_html( $dli_main_category['title'] ); ?></span>
						<?php endif; ?>
						<h2 id="dli-hero-news-title"><?php echo esc_html( get_the_title() ); ?></h2>
						<?php if ( $dli_summary ) : ?>
							<p class="fs-5"><?php echo wp_kses_post( wp_trim_words( $dli_summary, DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
						<?php endif; ?>
						<?php if ( $dli_news_date ) : ?>
							<time class="d-block text-white mt-3" datetime="<?php echo esc_attr( $dli_news_date->format( 'Y-m-d' ) ); ?>">
								<?php echo esc_html( trim( $dli_news_day . ' ' . $dli_news_month . ' ' . $dli_news_date->format( 'Y' ) ) ); ?>
							</time>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-12 col-lg-5 d-none d-lg-block">
					<?php if ( $dli_has_photo ) : ?>
						<figure class="figure mb-0">
							<img class="figure-img img-fluid rounded mb-0"
								src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
								alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>">
							<?php if ( $dli_image_metadata['image_caption'] ) : ?>
								<figcaption class="figure-caption mt-2 text-light"><?php echo esc_html( $dli_image_metadata['image_caption'] ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php elseif ( dli_show_hero_decorative_image() ) : ?>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- CORPO NEWS -->
	<section id="news-section" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">
				<!-- SIDEBAR NEWS  -->
				<div class="col-12 col-lg-3 border-end">
					<div class="sticky-top" style="top: 1rem;">
						<!-- Condividi -->
						<?php get_template_part( 'template-parts/common/social-sharing' ); ?>

						<?php if ( $dli_tags ) : ?>
							<div class="mt-4 mb-4">
								<h3 class="mb-0 h6">
									<small><?php echo esc_html__( 'Argomenti', 'design_laboratori_italia' ); ?></small>
								</h3>
								<?php foreach ( $dli_tags as $dli_tag ) : ?>
									<a class="chip chip-primary text-decoration-none mt-2" href="<?php echo esc_url( get_tag_link( $dli_tag ) ); ?>">
										<span class="chip-label"><?php echo esc_html( $dli_tag->name ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- CORPO DELLA NEWS -->
				<div class="col-12 col-lg-8 offset-lg-1">
					<div class="p-4 pt-0">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>
	</section>

</main>


<?php
get_footer();
