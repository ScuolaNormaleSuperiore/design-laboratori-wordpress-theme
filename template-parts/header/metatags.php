<?php
global $post;

$localestr = pll_current_language( 'locale' );
$nome_lab  = dli_get_option_by_lang( 'nome_laboratorio' );
$tagline   = dli_get_option_by_lang( 'tagline_laboratorio' );
$wrapper   = dli_get_post_wrapper( $post );

$copyright     = $nome_lab;
$resource_type = 'document';
$charset       = 'text/html; charset=US-ASCII';
$page_title    =  array_key_exists( 'title', $wrapper ) ? $wrapper['title'] : '';
$page_desc     =  array_key_exists( 'description', $wrapper ) ? $wrapper['description'] : '';
$keywords      = preg_replace( "/[^a-zA-Z0-9\s]/", '', $page_title . ' ' . $tagline );

?>

<meta name="resource-type" content="<?php echo esc_attr( $resource_type ); ?>" />
<meta name="description" content="<?php echo esc_attr( $page_desc ); ?>" />
<meta name="copyright" content="<?php echo esc_attr( $copyright ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( $keywords ); ?>"/>

<meta http-equiv="content-type" content="<?php echo esc_attr( $charset ); ?>" />
<meta http-equiv="content-language" content="<?php echo esc_attr( $localestr ); ?>" />
