<?php
/* Template Name: Il Laboratorio.
 *
 * la scuola template file
 *
 * @package Design_Laboratori_Italia
 */
global $post, $gallery, $struttura;
get_header();

?>
	<main id="main-container" class="main-container redbrown">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/home/hero' );
			get_template_part( 'template-parts/home/citazione' );
			get_template_part( 'template-parts/home/timeline' );
			get_template_part( 'template-parts/home/strutture' );
			get_template_part( 'template-parts/home/le-carte' );
			get_template_part( 'template-parts/home/gallery-luoghi' );
			get_template_part( 'template-parts/home/i-numeri' );

		endwhile;
		?>
	</main>

<?php
get_footer();
