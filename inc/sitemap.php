<?php
/**
 * Sitemap module — HTML rendering helpers and XML sitemap endpoints.
 *
 * Provides:
 *  - dli_render_sitemap_node()   Recursive HTML renderer for sitemap nodes.
 *  - dli_flatten_site_tree()     Flattens the tree to a list of internal URLs.
 *  - dli_output_sitemap_xml()    Outputs the XML <urlset> for one language.
 *  - dli_output_sitemap_index()  Outputs the XML <sitemapindex> for all languages.
 *
 * XML endpoints registered:
 *  /sitemap.xml       → sitemap index (IT + EN)
 *  /sitemap-it.xml    → Italian URL list
 *  /sitemap-en.xml    → English URL list
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_render_sitemap_node' ) ) {
	/**
	 * Render a sitemap node recursively as an HTML <li> element.
	 *
	 * @param array $dli_node Sitemap node array with keys: name, link, external, children.
	 * @return void
	 */
	function dli_render_sitemap_node( $dli_node ) {
		if ( ! is_array( $dli_node ) ) {
			return;
		}

		$dli_name     = isset( $dli_node['name'] ) ? esc_html( (string) $dli_node['name'] ) : '';
		$dli_link     = isset( $dli_node['link'] ) ? esc_url( (string) $dli_node['link'] ) : '';
		$dli_external = ! empty( $dli_node['external'] );

		echo '<li>';

		if ( '' !== $dli_link ) {
			echo '<a class="mappasitolink"';
			if ( $dli_external ) {
				echo ' target="_blank" rel="noopener noreferrer"';
			}
			echo ' href="' . esc_url( $dli_link ) . '">' . esc_html( $dli_name ) . '</a>';
		} else {
			echo '<span class="mappasitolink">' . esc_html( $dli_name ) . '</span>';
		}

		if ( ! empty( $dli_node['children'] ) && is_array( $dli_node['children'] ) ) {
			echo '<ul>';
			foreach ( $dli_node['children'] as $dli_child_node ) {
				dli_render_sitemap_node( $dli_child_node );
			}
			echo '</ul>';
		}

		echo '</li>';
	}
}

if ( ! function_exists( 'dli_flatten_site_tree' ) ) {
	/**
	 * Recursively flatten a site tree into a simple array of internal URLs.
	 *
	 * External links and nodes without a URL are excluded.
	 *
	 * @param array $tree  Sitemap tree (as returned by dli_get_site_tree()).
	 * @param array $urls  Accumulator — pass by reference.
	 * @return array Flat list of URL strings.
	 */
	function dli_flatten_site_tree( $tree, &$urls = array() ) {
		foreach ( $tree as $node ) {
			if ( ! empty( $node['link'] ) && empty( $node['external'] ) ) {
				$urls[] = $node['link'];
			}
			if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
				dli_flatten_site_tree( $node['children'], $urls );
			}
		}
		return $urls;
	}
}

if ( ! function_exists( 'dli_output_sitemap_xml' ) ) {
	/**
	 * Output a standard XML <urlset> sitemap for the given language.
	 *
	 * @param string $lang Language slug ('it' or 'en').
	 * @return void
	 */
	function dli_output_sitemap_xml( $lang ) {
		try {
			$tree = dli_get_site_tree( $lang );
		} catch ( Exception $e ) {
			$tree = array();
		}

		$urls = array();
		dli_flatten_site_tree( $tree, $urls );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( $urls as $url ) {
			echo "\t<url>\n";
			echo "\t\t<loc>" . esc_url( $url ) . "</loc>\n";
			echo "\t</url>\n";
		}
		echo '</urlset>';
	}
}

if ( ! function_exists( 'dli_output_sitemap_index' ) ) {
	/**
	 * Output a standard XML <sitemapindex>.
	 *
	 * When the language selector is disabled (selettore_lingua_visible != 'true'),
	 * only the default language sitemap is listed; otherwise both IT and EN are included.
	 *
	 * @return void
	 */
	function dli_output_sitemap_index() {
		$base             = trailingslashit( get_site_url() );
		$selector_visible = dli_get_option( 'selettore_lingua_visible', 'setup' );

		if ( 'true' === $selector_visible ) {
			$langs = array( 'it', 'en' );
		} else {
			$default_lang = function_exists( 'pll_default_language' ) ? pll_default_language( 'slug' ) : DLI_DEFAULT_LANGUAGE;
			$langs        = array( $default_lang );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( $langs as $lang ) {
			echo "\t<sitemap>\n";
			echo "\t\t<loc>" . esc_url( $base . 'sitemap-' . $lang . '.xml' ) . "</loc>\n";
			echo "\t</sitemap>\n";
		}
		echo '</sitemapindex>';
	}
}

/**
 * Disable the WordPress built-in sitemap system so our custom endpoints
 * at /sitemap.xml, /sitemap-it.xml and /sitemap-en.xml can take over.
 */
add_filter( 'wp_sitemaps_enabled', '__return_false' );

/**
 * Register rewrite rules for the XML sitemap endpoints.
 *
 * @return void
 */
function dli_sitemap_rewrite_rules() {
	add_rewrite_rule( '^sitemap\.xml$', 'index.php?dli_sitemap=index', 'top' );
	add_rewrite_rule( '^sitemap-it\.xml$', 'index.php?dli_sitemap=it', 'top' );
	add_rewrite_rule( '^sitemap-en\.xml$', 'index.php?dli_sitemap=en', 'top' );
}
add_action( 'init', 'dli_sitemap_rewrite_rules' );

/**
 * Register the dli_sitemap query variable.
 *
 * @param array $vars Existing public query variables.
 * @return array
 */
function dli_sitemap_query_vars( $vars ) {
	$vars[] = 'dli_sitemap';
	return $vars;
}
add_filter( 'query_vars', 'dli_sitemap_query_vars' );

/**
 * Intercept sitemap requests and output the appropriate XML.
 *
 * @return void
 */
function dli_sitemap_template_redirect() {
	$sitemap = get_query_var( 'dli_sitemap' );
	if ( ! $sitemap ) {
		return;
	}

	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex, follow' );

	if ( 'index' === $sitemap ) {
		dli_output_sitemap_index();
	} else {
		dli_output_sitemap_xml( $sitemap );
	}

	exit;
}
add_action( 'template_redirect', 'dli_sitemap_template_redirect' );
