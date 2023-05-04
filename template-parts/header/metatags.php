<?php
global $post;

$current_language = ( 'it' === dli_current_language( 'slug' ) ) ? 'it-IT' : 'en-US';
$nome_lab         = dli_get_option( 'nome_laboratorio' );
$tagline          = dli_get_option( 'tagline_laboratorio' );
$wrapper          = dli_get_post_wrapper( $post );

$copyright     = $nome_lab;
$resource_type = 'document';
$language      = $current_language;
$charset       = 'text/html; charset=US-ASCII';
$keywords      = $wrapper['title'];
$description   = $wrapper['description'];

?>

<meta name="resource-type" content="<?php echo $resource_type; ?>" />
<meta name="description" content="<?php echo $description; ?>" />
<meta name="copyright" content="<?php echo $copyright; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>"/>

<meta http-equiv="content-type" content="<?php echo $charset; ?>" />
<meta http-equiv="content-language" content="<?php echo $language; ?>" />

