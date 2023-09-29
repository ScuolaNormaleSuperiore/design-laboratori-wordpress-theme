<?php
global $post;

// $language = dli_current_language( 'slug' );
$localestr = pll_current_language( 'locale' );
$nome_lab  = dli_get_option( 'nome_laboratorio' );
$tagline   = dli_get_option( 'tagline_laboratorio' );
$wrapper   = dli_get_post_wrapper( $post );

$copyright     = $nome_lab;
$resource_type = 'document';
$charset       = 'text/html; charset=US-ASCII';
$page_title    =  array_key_exists( 'title', $wrapper ) ? $wrapper['title']: '';
$page_desc     =  array_key_exists( 'description', $wrapper ) ? $wrapper['description']: '';
$keywords      = preg_replace( "/[^a-zA-Z0-9\s]/", '', $page_title . ' ' . $tagline );

$analytics_text = dli_get_option( 'analytics_code', 'setup' );

?>

<meta name="resource-type" content="<?php echo $resource_type; ?>" />
<meta name="description" content="<?php echo $page_desc; ?>" />
<meta name="copyright" content="<?php echo $nome_lab; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>"/>

<meta http-equiv="content-type" content="<?php echo $charset; ?>" />
<meta http-equiv="content-language" content="<?php echo $localestr; ?>" />

<?php echo $analytics_text; ?>
