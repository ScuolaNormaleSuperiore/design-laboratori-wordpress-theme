<?php
/**
 * Header meta tags output.
 *
 * @package Design_Laboratori_Italia
 */

$dli_locale_str = pll_current_language( 'locale' );
$dli_nome_lab   = dli_get_option_by_lang( 'nome_laboratorio' );
$dli_tagline    = dli_get_option_by_lang( 'tagline_laboratorio' );
$dli_wrapper    = dli_get_post_wrapper( get_post() );

$dli_copyright     = $dli_nome_lab;
$dli_resource_type = 'document';
$dli_charset       = 'text/html; charset=US-ASCII';
$dli_page_title    = array_key_exists( 'title', $dli_wrapper ) ? $dli_wrapper['title'] : '';
$dli_page_desc     = array_key_exists( 'description', $dli_wrapper ) ? $dli_wrapper['description'] : '';
$dli_keywords      = preg_replace( '/[^a-zA-Z0-9\s]/', '', $dli_page_title . ' ' . $dli_tagline );

?>

<meta name="resource-type" content="<?php echo esc_attr( $dli_resource_type ); ?>" />
<meta name="description" content="<?php echo esc_attr( $dli_page_desc ); ?>" />
<meta name="copyright" content="<?php echo esc_attr( $dli_copyright ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( $dli_keywords ); ?>"/>

<meta http-equiv="content-type" content="<?php echo esc_attr( $dli_charset ); ?>" />
<meta http-equiv="content-language" content="<?php echo esc_attr( $dli_locale_str ); ?>" />
