<?php
/**
 * Category archive template: the theme has no page for the archive of a category, so the request answers 404.
 *
 * The categories are labels that filter the list pages (`?cat[]=ID` on Notizie, Eventi and Blog) and no link of the
 * theme leads to their archive. WordPress picks this template only for a real category archive: a page, even when it is
 * filtered with `?cat[]=`, and the feeds of a category do not come here.
 *
 * @package Design_Laboratori_Italia
 */

global $wp_query;
$wp_query->set_404();
status_header( 404 );
nocache_headers();
get_template_part( '404' );
