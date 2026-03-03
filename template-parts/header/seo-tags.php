<?php
/**
 * KK Writer Theme: The analytics code of the site.
 *
 * @package KK_Writer_Theme
 */

	global $post;

if ( dli_get_option( 'seo_internal_management_enabled', 'setup' ) === 'true' ) {
	$dli_og_data     = DLI_ContentsManager::get_og_data();
	$dli_url         = isset( $dli_og_data->url ) ? esc_url_raw( (string) $dli_og_data->url ) : '';
	$dli_locale          = isset( $dli_og_data->locale ) ? sanitize_text_field( (string) $dli_og_data->locale ) : '';
	$dli_title           = isset( $dli_og_data->title ) ? wp_strip_all_tags( (string) $dli_og_data->title ) : '';
	$dli_description = isset( $dli_og_data->description ) ? wp_strip_all_tags( (string) $dli_og_data->description ) : '';
	$dli_site_title  = isset( $dli_og_data->site_title ) ? wp_strip_all_tags( (string) $dli_og_data->site_title ) : '';
	$dli_image       = isset( $dli_og_data->image ) ? esc_url_raw( (string) $dli_og_data->image ) : '';
	$dli_img_width   = isset( $dli_og_data->img_width ) ? absint( $dli_og_data->img_width ) : 0;
	$dli_img_height  = isset( $dli_og_data->img_height ) ? absint( $dli_og_data->img_height ) : 0;
	?>

	<!-- SEO optimization -->
	<link rel="profile" href="<?php echo esc_url( 'http://gmpg.org/xfn/11' ); ?>" />
	<?php if ( ! empty( $dli_url ) ) { ?>
	<link rel="canonical" href="<?php echo esc_url( $dli_url ); ?>" />
	<?php } ?>
	<!-- OG DATA for page sharing -->
	<meta property="og:locale" content="<?php echo esc_attr( $dli_locale ); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo esc_attr( $dli_title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $dli_description ); ?>" />
	<?php if ( ! empty( $dli_url ) ) { ?>
	<meta property="og:url" content="<?php echo esc_url( $dli_url ); ?>"/>
	<?php } ?>
	<meta property="og:site_name" content="<?php echo esc_attr( $dli_site_title ); ?>" />

	<?php
	if ( ! empty( $dli_image ) ) {
		?>
	<meta property="og:image" content="<?php echo esc_url( $dli_image ); ?>"/>
			<?php if ( $dli_img_width > 0 ) { ?>
	<meta property="og:image:width" content="<?php echo esc_attr( (string) $dli_img_width ); ?>" />
	<?php } ?>
			<?php if ( $dli_img_height > 0 ) { ?>
	<meta property="og:image:height" content="<?php echo esc_attr( (string) $dli_img_height ); ?>" />
	<?php } ?>
	<meta property="og:image:type" content="image/png" />
			<?php
	}
	?>

	<!-- TWITTER CARD  -->
	<meta name="twitter:card" content="<?php echo ! empty( $dli_image ) ? 'summary_large_image' : 'summary'; ?>" />
	<meta name="twitter:title" content="<?php echo esc_attr( $dli_title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $dli_description ); ?>">
	<?php if ( ! empty( $dli_image ) ) { ?>
	<meta name="twitter:image" content="<?php echo esc_url( $dli_image ); ?>">
	<?php } ?>
	<?php if ( ! empty( $dli_url ) ) { ?>
	<meta name="twitter:url" content="<?php echo esc_url( $dli_url ); ?>">
	<?php } ?>

	<?php
}
