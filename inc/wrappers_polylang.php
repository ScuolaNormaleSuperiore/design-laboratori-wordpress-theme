<?php
/**
 * Wrapper functions for POLYLANG.
 *
 * The plugin used to translate post types and taxonomies is Polylang.
 * In the code instead of using the Polylang functions (e.g. "pll_current_language" )
 * please use the corresponding wrapped functions (e.g. "dli_current_language" ).
 *
 * These wrappers are defensive: when Polylang is not active (deactivated, not yet
 * loaded, or missing on a fresh install) the theme degrades gracefully to a
 * single-language behaviour instead of triggering a fatal error on the undefined
 * `pll_*` functions. Read helpers fall back to the default language / empty data;
 * write helpers become no-ops.
 *
 * This command verifies if the second language is enabled:
 *
 *  $selettore_visibile = dli_get_option( 'selettore_lingua_visible', 'setup' );
 */


if ( ! function_exists( 'dli_polylang_active' ) ) {
	/**
	 * Check whether Polylang is available (active and loaded).
	 *
	 * Used by the wrappers below to decide between the real Polylang call and a
	 * safe single-language fallback. Testing a core read function is enough: if it
	 * exists the plugin is loaded.
	 *
	 * @return bool
	 */
	function dli_polylang_active() {
		return function_exists( 'pll_current_language' );
	}
}

if ( ! function_exists( 'dli_current_language' ) ) {
	/**
	 * Recupera la lingua corrente del sito.
	 *
	 * @param string $type
	 * @return string
	 */
	function dli_current_language( $type = 'slug' ) {
		if ( ! dli_polylang_active() ) {
			return DLI_DEFAULT_LANGUAGE;
		}
		$cl = pll_current_language( $type );
		return $cl ? $cl : DLI_DEFAULT_LANGUAGE;
	}
}

if ( ! function_exists( 'dli_default_language' ) ) {
	/**
	 * Recupera la lingua di default del sito.
	 *
	 * @param string $type
	 * @return string
	 */
	function dli_default_language( $type = 'slug' ) {
		if ( ! function_exists( 'pll_default_language' ) ) {
			return DLI_DEFAULT_LANGUAGE;
		}
		$dl = pll_default_language( $type );
		return $dl ? $dl : DLI_DEFAULT_LANGUAGE;
	}
}

if ( ! function_exists( 'dli_languages_list' ) ) {
	/**
	 * Recupera l'elenco delle lingue supportate dal sito.
	 *
	 * @param array $args
	 * @return array
	 */
	function dli_languages_list( $args ) {
		if ( ! function_exists( 'pll_languages_list' ) ) {
			return array( DLI_DEFAULT_LANGUAGE );
		}
		return pll_languages_list( $args );
	}
}

if ( ! function_exists( 'dli_set_term_language' ) ) {
	/**
	 * Imposta la lingua del termine di una tassonomia.
	 *
	 * @param [type] $term
	 * @param [type] $lang
	 * @return void
	 */
	function dli_set_term_language( $term, $lang ) {
		if ( ! function_exists( 'pll_set_term_language' ) ) {
			return;
		}
		return pll_set_term_language( $term, $lang );
	}
}

if ( ! function_exists( 'dli_save_term_translations' ) ) {
	/**
	 * Definisce un termine come la traduzione di un altro.
	 *
	 * @param [type] $related_taxonomies
	 * @return void
	 */
	function dli_save_term_translations( $related_taxonomies ) {
		if ( ! function_exists( 'pll_save_term_translations' ) ) {
			return;
		}
		return pll_save_term_translations( $related_taxonomies );
	}
}

if ( ! function_exists( 'dli_set_post_language' ) ) {
	/**
	 * Imposta la lingua di un post.
	 *
	 * @param [type] $post
	 * @param [type] $lang
	 * @return void
	 */
	function dli_set_post_language( $post, $lang ) {
		if ( ! function_exists( 'pll_set_post_language' ) ) {
			return;
		}
		return pll_set_post_language( $post, $lang );
	}
}

if ( ! function_exists( 'dli_save_post_translations' ) ) {
	/**
	 * Definisce un post come la traduzione di un altro.
	 *
	 * @param [type] $related_posts
	 * @return void
	 */
	function dli_save_post_translations( $related_posts ) {
		if ( ! function_exists( 'pll_save_post_translations' ) ) {
			return;
		}
		return pll_save_post_translations( $related_posts );
	}
}

if ( ! function_exists( 'dli_get_post_translations' ) ) {
	/**
	 * Recupera le traduzioni di un post nelle lingue del sito, se presenti.
	 *
	 * @param int $post_id
	 * @return array
	 */
	function dli_get_post_translations( $post_id ): array {
		if ( ! function_exists( 'pll_get_post_translations' ) ) {
			return array();
		}
		return pll_get_post_translations( $post_id );
	}
}

if ( ! function_exists( 'dli_get_term_translations' ) ) {
	/**
	 * Recupera le traduzioni di un termine nelle lingue del sito, se presenti.
	 *
	 * @param int $term_id
	 * @return array
	 */
	function dli_get_term_translations( $term_id ): array {
		if ( ! function_exists( 'pll_get_term_translations' ) ) {
			return array();
		}
		return pll_get_term_translations( $term_id );
	}
}

function dli_get_translated_page_url_by_slug( $slug ) {
	$page_url = '';
	$args     = array(
		'name'           => $slug,
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
	);
	$query    = new WP_Query( $args );
	if ( $query->have_posts() ) {
		$page         = $query->posts[0];
		$translations = dli_get_post_translations( $page->ID );
		$page_id      = array_key_exists( dli_current_language( 'slug' ), $translations ) ?
		$translations[ dli_current_language( 'slug' ) ] : null;
		$page_url     = $page_id ? get_permalink( $page_id ) : '';
	}
	return $page_url;
}


if ( ! function_exists( 'dli_homepage_url' ) ) {
	function dli_homepage_url() {
		$site_url         = get_site_url();
		$current_language = dli_current_language( 'slug' );
		$default_language = dli_default_language( 'slug' );
		if ( $current_language != $default_language ) {
			return $site_url . '/' . $current_language;
		} else {
			return $site_url;
		}
	}
}

if ( ! function_exists( 'dli_get_term' ) ) {
	/**
	 * Ritorna l'id del termine $term_id nella lingua $lang.
	 *
	 * @param int    $term_id
	 * @param string $lang
	 * @return int
	 */
	function dli_get_term( $term_id, $lang ) {
		if ( ! function_exists( 'pll_get_term' ) ) {
			return $term_id;
		}
		return pll_get_term( $term_id, $lang );
	}
}


if ( ! function_exists( 'dli_get_page_selectors' ) ) {
	/**
	 * Ritorna la lista di elementi del selettore in base alla lingua della pagina.
	 * Se la pagina non ha traduzione non c'è il selettore (richiesta esplicita, ma discutibile !!).
	 */
	function dli_get_page_selectors() {
		global $post;
		$selectors        = array();
		$site_url         = get_site_url();
		$languages_list   = dli_languages_list(
			array(
				'hide_empty' => 0,
				'fields'     => 'slug',
			)
		);
		$default_language = dli_default_language( 'slug' );
		$current_language = dli_current_language( 'slug' );

		// La home page è la stessa per tutte le lingue.
		if ( is_home() ) {

			// Home Page.
			foreach ( $languages_list as $lang_slug ) {
				if ( $lang_slug != $default_language ) {
					$url = $site_url . '/' . $lang_slug;
				} else {
					$url = $site_url;
				}
				array_push(
					$selectors,
					array(
						'slug' => $lang_slug,
						'url'  => $url,
					)
				);
			}
		} elseif ( $post ) {

				// Altre pagine del sito (non HP).
				$traduzioni = dli_get_post_translations( $post->ID );
				$selectors  = array(
					array(
						'slug' => $current_language,
						'url'  => get_permalink( $post ),
					),
				);
				foreach ( $languages_list as $lang_slug ) {
					if ( ( $lang_slug !== $current_language ) && array_key_exists( $lang_slug, $traduzioni ) ) {
						array_push(
							$selectors,
							array(
								'slug' => $lang_slug,
								'url'  => get_permalink( $traduzioni[ $lang_slug ] ),
							)
						);
					}
				}
		}
		return $selectors;
	}
}

if ( ! function_exists( 'dli_get_configuration_field_by_lang' ) ) {
	function dli_get_configuration_field_by_lang( $field_name, $field_type ) {
		$field_name_new = ( dli_current_language() === DLI_IT_SLUG ) ? $field_name : $field_name . DLI_ENG_SUFFIX_LANGUAGE;
		$field_value    = dli_get_option( $field_name_new, $field_type );
		if ( ! $field_value ) {
			$default_language = dli_default_language( 'slug' );
			$field_name_new   = ( DLI_IT_SLUG === $default_language ) ? $field_name : $field_name . DLI_ENG_SUFFIX_LANGUAGE;
		}

		return dli_get_option( $field_name_new, $field_type );
	}
}

if ( ! function_exists( 'dli_get_all_menus_by_lang' ) ) {
	/**
	 * Return all menu locations for the requested language.
	 *
	 * @param string $lang
	 * @return array
	 */
	function dli_get_all_menus_by_lang( $lang ) {
		$items   = array();
		$options = get_option( 'polylang' );
		if ( ! is_array( $options ) || empty( $options['nav_menus'] ) || empty( $options['nav_menus']['design-laboratori-wordpress-theme'] ) ) {
			return $items;
		}

		$menu_locations = $options['nav_menus']['design-laboratori-wordpress-theme'];
		if ( ! is_array( $menu_locations ) ) {
			return $items;
		}

		$ids = array();
		foreach ( $menu_locations as $name => $menulangs ) {
			if ( ! is_array( $menulangs ) ) {
				continue;
			}
			foreach ( $menulangs as $ml_lang => $ml_id ) {
				if ( ! in_array( $ml_id, $ids ) ) {
					if ( isset( $items[ $ml_lang ] ) ) {
						array_push( $items[ $ml_lang ], array( $name => $ml_id ) );
						array_push( $ids, $ml_id );
					} else {
						$items[ $ml_lang ] = array();
						array_push( $items[ $ml_lang ], array( $name => $ml_id ) );
						array_push( $ids, $ml_id );
					}
				}
			}
		}
		return isset( $items[ $lang ] ) ? $items[ $lang ] : array();
	}
}
