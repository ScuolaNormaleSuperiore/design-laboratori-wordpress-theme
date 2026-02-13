<?php
/**
 * KK Writer Theme: The analytics code of the site.
 *
 * @package KK_Writer_Theme
 */

global $post;

if ( dli_get_option( 'seo_internal_management_enabled', 'setup' ) === 'true') {
	$og_data = DLI_ContentsManager::get_og_data();
	$shared_title = isset( $og_data->shared_title ) ? $og_data->shared_title : '';
	$url          = isset( $og_data->url ) ? $og_data->url : '';
	$locale       = isset( $og_data->locale ) ? $og_data->locale : '';
	$title        = isset( $og_data->title ) ? $og_data->title : '';
	$description  = isset( $og_data->description ) ? $og_data->description : '';
	$site_title   = isset( $og_data->site_title ) ? $og_data->site_title : '';
	$image        = isset( $og_data->image ) ? $og_data->image : '';
	$img_width    = isset( $og_data->img_width ) ? $og_data->img_width : '';
	$img_height   = isset( $og_data->img_height ) ? $og_data->img_height : '';
?>

	<!-- SEO optimization -->
	<link rel="profile" href="<?php echo esc_url( 'http://gmpg.org/xfn/11' ); ?>" />
	<title><?php echo esc_html( $shared_title ); ?></title>
	<link rel="canonical" href="<?php echo esc_url( $url ); ?>" />
	<!-- OG DATA for page sharing -->
	<meta property="og:locale" content="<?php echo esc_attr( $locale ); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>"/>
	<meta property="og:site_name" content="<?php echo esc_attr( $site_title ); ?>" />

	<?php
		if ( $image ) {
	?>
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>"/>
	<meta property="og:image:width" content="<?php echo esc_attr( $img_width ); ?>" />
	<meta property="og:image:height" content="<?php echo esc_attr( $img_height ); ?>" />
	<meta property="og:image:type" content="image/png" />
	<?php
		}
	?>

	<!-- TWITTER CARD  -->
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<meta name="twitter:url" content="<?php echo esc_url( $url ); ?>">

<?php
}
