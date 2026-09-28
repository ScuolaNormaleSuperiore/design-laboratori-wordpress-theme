<?php
/**
 * Site search autocomplete — configuration, REST suggestions endpoint and
 * front-end asset loading for the "Cerca" page template.
 *
 * Provides:
 *  - dli_search_autocomplete_default_post_types()  Default post types offered/enabled.
 *  - dli_search_autocomplete_post_type_options()   Label => value list for the options page.
 *  - dli_search_autocomplete_settings()            Validated settings, single source of truth.
 *  - dli_search_autocomplete_is_enabled()          Whether the feature is on right now.
 *  - dli_search_autocomplete_register_route()      Registers the dli/v1/suggest REST route.
 *  - dli_search_autocomplete_suggest()             REST callback returning suggestions.
 *  - dli_search_autocomplete_enqueue()             Enqueues the front-end script on the search page.
 *
 * Endpoint registered (only while the feature is enabled):
 *  GET /wp-json/dli/v1/suggest?q=<text>&lang=<lang> → up to N { title, type, url } suggestions.
 *
 * The feature is opt-in (disabled by default) and fully configurable from
 * *WP → Configurazione → Altro → Ricerca nel sito*. See
 * DEV/TODO/AutocompletePlan.md for the full design.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_search_autocomplete_default_post_types' ) ) {
	/**
	 * Post types offered as checkboxes on the options page and used as the
	 * default when the option has never been saved. Same set as the site
	 * search's own content-type filter, minus the people-type taxonomy
	 * marker (not a real content type).
	 *
	 * @return array
	 */
	function dli_search_autocomplete_default_post_types() {
		return array_values(
			array_filter(
				DLI_POST_TYPES_TO_SEARCH,
				function ( $post_type ) {
					return PEOPLE_TYPE_POST_TYPE !== $post_type;
				}
			)
		);
	}
}

if ( ! function_exists( 'dli_search_autocomplete_post_type_options' ) ) {
	/**
	 * Build the post type => translated label options for the multicheck
	 * field on the options page.
	 *
	 * @return array
	 */
	function dli_search_autocomplete_post_type_options() {
		$options = array();
		foreach ( dli_search_autocomplete_default_post_types() as $post_type ) {
			$options[ $post_type ] = dli_get_post_type_label( $post_type );
		}
		return $options;
	}
}

if ( ! function_exists( 'dli_search_autocomplete_settings' ) ) {
	/**
	 * Read and validate the autocomplete settings. Single source of truth:
	 * both the REST endpoint and the front-end script are built from this
	 * function's result, so they cannot diverge.
	 *
	 * @return array {
	 *     @type bool   $enabled       Whether the admin has turned the feature on.
	 *     @type int    $min_chars     Minimum characters before suggesting (1-10).
	 *     @type int    $max_results   Maximum number of suggestions (1-20).
	 *     @type int    $delay         Debounce delay in milliseconds (0-2000).
	 *     @type array  $post_types    Enabled post types (subset of the default list).
	 *     @type int    $cache_minutes Suggestion cache TTL in minutes (0 = no cache).
	 *     @type string $on_select     'open' or 'search'.
	 * }
	 */
	function dli_search_autocomplete_settings() {
		// dli_get_option()'s own $default argument already covers "option
		// never saved" (see below): this only has to clamp a stored value
		// into range, without treating a genuinely saved 0 (no delay / no
		// cache, both valid) as if it were missing.
		$clamp = function ( $value, $min, $max ) {
			return max( $min, min( $max, absint( $value ) ) );
		};

		$allowed_post_types = dli_search_autocomplete_default_post_types();

		// A multicheck saved with every box unchecked is stored identically to
		// "never saved" by CMB2 (the field's data is simply removed): there is
		// no way to distinguish the two from the stored value alone. In
		// practice this is not a problem, because the feature is off by
		// default and only starts mattering once an admin actively enables it
		// and saves the form — at that point the submitted checkboxes (pre-
		// ticked with all types by default) are what gets stored.
		$post_types = (array) dli_get_option( 'search_autocomplete_post_types', 'setup', $allowed_post_types );
		$post_types = array_values( array_intersect( $allowed_post_types, $post_types ) );

		$on_select = dli_get_option( 'search_autocomplete_on_select', 'setup', 'open' );
		if ( ! in_array( $on_select, array( 'open', 'search' ), true ) ) {
			$on_select = 'open';
		}

		return array(
			'enabled'       => 'true' === dli_get_option( 'search_autocomplete_enabled', 'setup', 'false' ),
			'min_chars'     => $clamp( dli_get_option( 'search_autocomplete_min_chars', 'setup', 3 ), 1, 10 ),
			'max_results'   => $clamp( dli_get_option( 'search_autocomplete_max_results', 'setup', 5 ), 1, 20 ),
			'delay'         => $clamp( dli_get_option( 'search_autocomplete_delay', 'setup', 300 ), 0, 2000 ),
			'post_types'    => $post_types,
			'cache_minutes' => $clamp( dli_get_option( 'search_autocomplete_cache_minutes', 'setup', 10 ), 0, 1440 ),
			'on_select'     => $on_select,
		);
	}
}

if ( ! function_exists( 'dli_search_autocomplete_is_enabled' ) ) {
	/**
	 * Whether suggestions should be active right now: the admin switch is on
	 * and at least one post type is selected. With zero post types the
	 * feature behaves as fully disabled (no route, no script).
	 *
	 * @return bool
	 */
	function dli_search_autocomplete_is_enabled() {
		$settings = dli_search_autocomplete_settings();
		return $settings['enabled'] && ! empty( $settings['post_types'] );
	}
}

if ( ! function_exists( 'dli_search_autocomplete_register_route' ) ) {
	/**
	 * Register the GET /dli/v1/suggest REST route, only while the feature is
	 * enabled. Public callback: it only ever returns already-published
	 * content, the same content a visitor can already reach via the normal
	 * site search.
	 *
	 * @return void
	 */
	function dli_search_autocomplete_register_route() {
		if ( ! dli_search_autocomplete_is_enabled() ) {
			return;
		}

		register_rest_route(
			'dli/v1',
			'/suggest',
			array(
				'methods'             => 'GET',
				'callback'            => 'dli_search_autocomplete_suggest',
				'permission_callback' => '__return_true',
				'args'                => array(
					'q'    => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'lang' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);
	}
}
add_action( 'rest_api_init', 'dli_search_autocomplete_register_route' );

if ( ! function_exists( 'dli_search_autocomplete_resolve_lang' ) ) {
	/**
	 * Validate the requested language against the site's active Polylang
	 * languages, falling back to the default language.
	 *
	 * @param string $requested_lang Raw 'lang' request param (already sanitize_key()'d).
	 * @return string
	 */
	function dli_search_autocomplete_resolve_lang( $requested_lang ) {
		$active_langs = dli_languages_list( array( 'fields' => 'slug' ) );
		if ( $requested_lang && in_array( $requested_lang, $active_langs, true ) ) {
			return $requested_lang;
		}
		return dli_default_language();
	}
}

if ( ! function_exists( 'dli_search_autocomplete_suggest' ) ) {
	/**
	 * REST callback: return up to N { title, type, url } suggestions matching
	 * the query, for the enabled post types, in the requested language.
	 *
	 * @param WP_REST_Request $request Incoming REST request.
	 * @return WP_REST_Response
	 */
	function dli_search_autocomplete_suggest( WP_REST_Request $request ) {
		$settings = dli_search_autocomplete_settings();

		$q    = trim( (string) $request->get_param( 'q' ) );
		$lang = dli_search_autocomplete_resolve_lang( (string) $request->get_param( 'lang' ) );

		if ( mb_strlen( $q ) < $settings['min_chars'] ) {
			return rest_ensure_response( array() );
		}

		$max       = $settings['max_results'];
		$cache_key = 'dli_suggest_' . md5( $lang . '|' . mb_strtolower( $q ) . '|' . $max . '|' . implode( ',', $settings['post_types'] ) );
		$cache_ttl = $settings['cache_minutes'] * MINUTE_IN_SECONDS;
		$use_cache = $cache_ttl > 0;

		if ( $use_cache ) {
			$cached = get_transient( $cache_key );
			if ( is_array( $cached ) ) {
				return rest_ensure_response( $cached );
			}
		}

		$query = new WP_Query(
			array(
				's'                      => $q,
				'post_type'              => $settings['post_types'],
				'post_status'            => 'publish',
				// Ask for more than needed: dli_get_post_wrapper() can return an
				// empty wrapper for a post type without an archive page, and the
				// people-specific rules below can drop a result too. Both are
				// filtered out below, before trimming to $max.
				'posts_per_page'         => $max * 2,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'lang'                   => $lang,
			)
		);

		$suggestions = array();

		foreach ( $query->posts as $result_post ) {
			$wrapper = dli_get_post_wrapper( $result_post, 'medium' );
			if ( empty( $wrapper['title'] ) ) {
				continue;
			}

			$url = $wrapper['link'];

			if ( PEOPLE_POST_TYPE === $result_post->post_type ) {
				if ( dli_get_field( 'escludi_da_elenco', $result_post->ID ) ) {
					continue;
				}
				if ( dli_get_field( 'disattiva_pagina_dettaglio', $result_post->ID ) ) {
					// The person's own page is not reachable: fall back to the
					// full search instead of linking a disabled detail page.
					$url = '';
				}
			}

			$title = html_entity_decode( (string) $wrapper['title'], ENT_QUOTES, 'UTF-8' );
			$title = wp_strip_all_tags( $title );
			// wp_strip_all_tags() only removes real tags; a title containing a
			// literal '<'/'>' without a matching closing tag would otherwise
			// survive and could still break out of the <script> the theme's
			// widgets print JSON into, so they are stripped explicitly too.
			$title = str_replace( array( '<', '>' ), '', $title );
			$title = trim( $title );
			if ( '' === $title ) {
				continue;
			}

			$suggestions[] = array(
				'title' => $title,
				'type'  => $result_post->post_type,
				'url'   => $url,
			);

			if ( count( $suggestions ) >= $max ) {
				break;
			}
		}

		if ( $use_cache ) {
			set_transient( $cache_key, $suggestions, $cache_ttl );
		}

		return rest_ensure_response( $suggestions );
	}
}

if ( ! function_exists( 'dli_search_autocomplete_enqueue' ) ) {
	/**
	 * Enqueue the autocomplete script and its inline configuration, only on
	 * the "Cerca" page template and only while the feature is enabled.
	 *
	 * @return void
	 */
	function dli_search_autocomplete_enqueue() {
		if ( ! dli_search_autocomplete_is_enabled() ) {
			return;
		}
		if ( ! is_page_template( 'page-templates/cerca.php' ) ) {
			return;
		}

		$settings       = dli_search_autocomplete_settings();
		$theme_version  = wp_get_theme()->get( 'Version' );
		$script_path    = get_template_directory() . '/assets/js/search-autocomplete.js';
		$script_version = file_exists( $script_path ) ? (string) filemtime( $script_path ) : $theme_version;

		wp_enqueue_script(
			'dli-search-autocomplete',
			get_template_directory_uri() . '/assets/js/search-autocomplete.js',
			array( 'dli-boostrap-italia-js' ),
			$script_version,
			true
		);

		$type_labels = array();
		foreach ( $settings['post_types'] as $post_type ) {
			$type_labels[ $post_type ] = dli_get_post_type_label( $post_type );
		}

		$config = array(
			'endpoint'   => rest_url( 'dli/v1/suggest' ),
			'lang'       => dli_current_language( 'slug' ),
			'minChars'   => $settings['min_chars'],
			'delay'      => $settings['delay'],
			'onSelect'   => $settings['on_select'],
			'typeLabels' => $type_labels,
			'messages'   => array(
				'assistiveHint'         => __( 'Quando i risultati sono disponibili, usa le frecce su e giù per scorrerli e Invio per selezionare.', 'design_laboratori_italia' ),
				'noResults'             => __( 'Nessun risultato trovato', 'design_laboratori_italia' ),
				'statusNoResults'       => __( 'Nessun risultato di ricerca', 'design_laboratori_italia' ),
				/* translators: %d: minimum number of characters. */
				'statusQueryTooShort'   => __( 'Digita almeno %d caratteri per vedere i suggerimenti', 'design_laboratori_italia' ),
				/* translators: %d: number of results. */
				'statusResultsSingular' => __( '%d risultato disponibile.', 'design_laboratori_italia' ),
				/* translators: %d: number of results. */
				'statusResultsPlural'   => __( '%d risultati disponibili.', 'design_laboratori_italia' ),
				/* translators: 1: selected option label, 2: total number of results, 3: position of the selected option. */
				'statusSelectedOption'  => __( '%1$s, risultato %3$d di %2$d', 'design_laboratori_italia' ),
			),
		);

		wp_add_inline_script(
			'dli-search-autocomplete',
			'window.DLI_SEARCH_AUTOCOMPLETE = ' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';',
			'before'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'dli_search_autocomplete_enqueue' );
