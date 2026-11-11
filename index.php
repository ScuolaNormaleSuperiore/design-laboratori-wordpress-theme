<?php
/**
 * Fallback template: WordPress requires it in every theme and uses it only when no other template of the theme matches
 * the request (for example the page of an attachment).
 *
 * The theme has no page for such a request, so it answers 404 with the 404 page of the theme, as `category.php` does.
 * Pages, posts, the content types, the archives, the search and the 404 page itself have their own templates and never
 * come here. This file must not define `WP_USE_THEMES`: WordPress defines it before it loads any template.
 *
 * @package Design_Laboratori_Italia
 */

global $wp_query;
$wp_query->set_404();
status_header( 404 );
nocache_headers();
get_template_part( '404' );
