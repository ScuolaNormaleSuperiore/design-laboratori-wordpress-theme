<?php
/**
 * Wrapper functions for POLYLANG.
 * 
 * The plugin used to translate post types and taxonomies is Polylang.
 * In the code instead of using the Polylang funcyions (e.g. "pll_current_language" )
 * please use the corresponding wrapped functions (e.g. "dli_current_language" ).
 * 
 * This command verifies if the second language is enabled:
 * 
 * 	$selettore_visibile = dli_get_option( 'selettore_lingua_visible', 'setup' );
 * 
 */


if ( ! function_exists( 'dli_current_language' ) ) {
	function dli_current_language( $type = 'slug' ) {
		$cl = pll_current_language( $type );
		return $cl ? $cl : DLI_DEFAULT_LANGUAGE;
	}
}

if ( ! function_exists( 'dli_languages_list' ) ) {
	function dli_languages_list( $args ) {
		return pll_languages_list( $args );
	}
}

if ( ! function_exists( 'dli_set_term_language' ) ) {
	function dli_set_term_language( $term, $lang ) {
		return pll_set_term_language( $term, $lang );
	}
}

if ( ! function_exists( 'dli_save_term_translations' ) ) {
	function dli_save_term_translations( $related_taxonomies ) {
		return pll_save_term_translations( $related_taxonomies );
	}
}

if ( ! function_exists( 'dli_set_post_language' ) ) {
	function dli_set_post_language( $post, $lang ) {
		return pll_set_post_language( $post, $lang );
	}
}

if ( ! function_exists( 'dli_save_post_translations' ) ) {
	function dli_save_post_translations( $related_posts ) {
		return pll_save_post_translations( $related_posts );
	}
}
