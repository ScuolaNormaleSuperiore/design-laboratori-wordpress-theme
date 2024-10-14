<?php
/* Template Name: Brevetti
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

if ( isset( $_GET['area'] ) ){
	$selected_areas = $_GET['area'];
} else {
	$selected_areas = array();
}

$params = array(
	'search_string' => '',
	'areas'         => array(),
	'deposit_year'  => '',
);

$the_query = DLI_ContentsManager::get_patent_data_query( $params );

$num_results    = $the_query->found_posts;
// $all_categories = dli_get_all_categories_by_ct( 'category', NEWS_POST_TYPE );
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER BREVETTI -->
	<?php get_template_part( 'template-parts/hero/brevetti' ); ?>


</main>

<?php
get_footer();
