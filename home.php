<?php
/**
 * The template for displaying home
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();
$carousel_after_pres = dli_get_option( 'home_carousel_after_presentation_enabled', 'homepage' );
?>
<main id="main-container" class="main-container redbrown" role="main">

<!-- Section MAIN HERO  -->
<?php get_template_part( 'template-parts/home/main-hero' ); ?>

<!-- Section CAROUSEL -->
<?php
 if ( 'false' === $carousel_after_pres ){
?>
<?php get_template_part( 'template-parts/home/carousel' ); ?>
<?php
	}
?>

<!-- PRESENTAZIONE -->
<?php get_template_part( 'template-parts/home/site-presentation' ); ?>

<!-- Section CAROUSEL -->
<?php
 if ( 'false' !== $carousel_after_pres ){
?>
<?php get_template_part( 'template-parts/home/carousel' ); ?>
<?php
	}
?>

<!-- Section Featured Contents (Contenuti in evidenza) -->
<?php get_template_part( 'template-parts/home/featured-contents' ); ?>

<!-- Section Content List (elenco contenuti) -->
<?php get_template_part( 'template-parts/home/hp-list-event' ); ?>
<?php get_template_part( 'template-parts/home/hp-list-news' ); ?>
<?php get_template_part( 'template-parts/home/hp-list-publication' ); ?>
<?php get_template_part( 'template-parts/home/hp-list-article' ); ?>

<!-- Section banners -->
<?php get_template_part( 'template-parts/home/hp-banners-section' ); ?>

</main>
<?php
get_footer();
