<?php
/* Template Name: I progetti.
*
* @package Design_Laboratori_Italia
*/
global $post;
get_header();

?>

<main id="main-container" class="main-container bluelectric">
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>
	<?php
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/hero/progetti' );
	?>

	<h2>Qui ci vanno i PROGETTI:</h2>
	<ul>
		<li>Uno</li>
		<li>Due</li>
		<li>Tre</li>
	</ul>

	<?php 
			}
	?>

<?php
get_footer();



