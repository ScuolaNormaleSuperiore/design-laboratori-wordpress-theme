<?php
/**
 * KK Writer Theme: The analytics code of the site.
 *
 * @package KK_Writer_Theme
 */

	global $post;

	if ( dli_get_option( 'seo_internal_management_enabled', 'setup' ) === 'true') {
		$og_data      = DLI_ContentsManager::get_og_data();
		$url          = isset( $og_data->url ) ? esc_url_raw( (string) $og_data->url ) : '';
		$locale       = isset( $og_data->locale ) ? sanitize_text_field( (string) $og_data->locale ) : '';
		$title        = isset( $og_data->title ) ? wp_strip_all_tags( (string) $og_data->title ) : '';
		$description  = isset( $og_data->description ) ? wp_strip_all_tags( (string) $og_data->description ) : '';
		$site_title   = isset( $og_data->site_title ) ? wp_strip_all_tags( (string) $og_data->site_title ) : '';
		$image        = isset( $og_data->image ) ? esc_url_raw( (string) $og_data->image ) : '';
		$img_width    = isset( $og_data->img_width ) ? absint( $og_data->img_width ) : 0;
		$img_height   = isset( $og_data->img_height ) ? absint( $og_data->img_height ) : 0;
?>

	<!-- SEO optimization -->
	<link rel="profile" href="<?php echo esc_url( 'http://gmpg.org/xfn/11' ); ?>" />
	<?php if ( ! empty( $url ) ) { ?>
	<link rel="canonical" href="<?php echo esc_url( $url ); ?>" />
	<?php } ?>
	<!-- OG DATA for page sharing -->
	<meta property="og:locale" content="<?php echo esc_attr( $locale ); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
	<?php if ( ! empty( $url ) ) { ?>
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>"/>
	<?php } ?>
	<meta property="og:site_name" content="<?php echo esc_attr( $site_title ); ?>" />

	<?php
		if ( ! empty( $image ) ) {
	?>
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>"/>
	<?php if ( $img_width > 0 ) { ?>
	<meta property="og:image:width" content="<?php echo esc_attr( (string) $img_width ); ?>" />
	<?php } ?>
	<?php if ( $img_height > 0 ) { ?>
	<meta property="og:image:height" content="<?php echo esc_attr( (string) $img_height ); ?>" />
	<?php } ?>
	<meta property="og:image:type" content="image/png" />
	<?php
		}
	?>

	<!-- TWITTER CARD  -->
	<meta name="twitter:card" content="<?php echo ! empty( $image ) ? 'summary_large_image' : 'summary'; ?>" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<?php if ( ! empty( $image ) ) { ?>
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php } ?>
	<?php if ( ! empty( $url ) ) { ?>
	<meta name="twitter:url" content="<?php echo esc_url( $url ); ?>">
	<?php } ?>

<?php
}
