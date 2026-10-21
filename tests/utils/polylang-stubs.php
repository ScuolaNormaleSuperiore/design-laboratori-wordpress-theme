<?php
/**
 * Polylang API declarations used by the theme, for PHPStan only.
 *
 * Polylang is an optional runtime dependency and is not installed in the
 * local analysis environment. Keep this list restricted to APIs called by
 * first-party theme code.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'pll_current_language' ) ) {
    /**
     * @param string $value Requested language field.
     * @return string|int
     */
    function pll_current_language( $value = 'slug' ) {
        return '';
    }
}

if ( ! function_exists( 'pll_default_language' ) ) {
    /**
     * @param string $value Requested language field.
     * @return string|int
     */
    function pll_default_language( $value = 'slug' ) {
        return '';
    }
}

if ( ! function_exists( 'pll_languages_list' ) ) {
    /**
     * @param array<string, mixed> $args Query arguments.
     * @return array<int, string|int>
     */
    function pll_languages_list( $args = array() ) {
        return array();
    }
}

if ( ! function_exists( 'pll_set_term_language' ) ) {
    /**
     * @param int    $term Term ID.
     * @param string $lang Language slug.
     * @return void
     */
    function pll_set_term_language( $term, $lang ) {}
}

if ( ! function_exists( 'pll_save_term_translations' ) ) {
    /**
     * @param array<string, int> $translations Term IDs keyed by language slug.
     * @return void
     */
    function pll_save_term_translations( $translations ) {}
}

if ( ! function_exists( 'pll_set_post_language' ) ) {
    /**
     * @param int    $post Post ID.
     * @param string $lang Language slug.
     * @return void
     */
    function pll_set_post_language( $post, $lang ) {}
}

if ( ! function_exists( 'pll_save_post_translations' ) ) {
    /**
     * @param array<string, int> $translations Post IDs keyed by language slug.
     * @return void
     */
    function pll_save_post_translations( $translations ) {}
}

if ( ! function_exists( 'pll_get_post_translations' ) ) {
    /**
     * @param int $post_id Post ID.
     * @return array<string, int>
     */
    function pll_get_post_translations( $post_id ) {
        return array();
    }
}

if ( ! function_exists( 'pll_get_term_translations' ) ) {
    /**
     * @param int $term_id Term ID.
     * @return array<string, int>
     */
    function pll_get_term_translations( $term_id ) {
        return array();
    }
}

if ( ! function_exists( 'pll_get_term' ) ) {
    /**
     * @param int    $term_id Term ID.
     * @param string $lang    Language slug.
     * @return int
     */
    function pll_get_term( $term_id, $lang ) {
        return 0;
    }
}
