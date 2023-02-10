<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
get_header();

?>

<main id="main-container" class="main-container bluelectric">
	<?php
		while ( have_posts() ) {
			the_post();
	?>

	<h2>Qui ci vanno le NOTIZIE:</h2>
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
