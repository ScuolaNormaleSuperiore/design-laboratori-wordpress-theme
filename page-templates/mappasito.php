<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
get_header();

// Build the PAGE TREE.
$pt = dli_get_site_tree();
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER MAPPA DEL SITO -->
	<?php get_template_part( 'template-parts/hero/mappasito' ); ?>



</main>

<?php
get_footer();
