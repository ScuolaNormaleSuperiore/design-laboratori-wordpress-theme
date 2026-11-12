<?php
/**
 * Restricted content: a content that exists but that the visitor may not read answers 401 (not logged in) or 403
 * (logged in, without permission), not 404.
 *
 * Groups hides a reserved content by taking it out of the queries, so WordPress finds nothing and answers 404 by itself;
 * the plugin sets no status and offers no way to change the response. The theme does it, only while the reserved area
 * is on: on a request that is already a 404 and has the shape of a single content it looks the content up without the
 * filters of Groups and asks Groups whether the current user can read it. Every earlier check costs nothing, so the
 * pages of the site and a site with the reserved area off pay nothing.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The state of the response being given: the status and the address of the content that was asked for.
 *
 * @param array<string, mixed>|false|null $value The state to keep, `false` to forget it (for the tests), null to read it.
 * @return array<string, mixed>|null The state, or null while no restricted response is being given.
 */
function dli_restricted_content_state( $value = null ) {
	static $state = null;
	if ( false === $value ) {
		$state = null;
		return null;
	}
	if ( null !== $value ) {
		$state = $value;
	}
	return $state;
}

/**
 * The reserved content that the current request asked for, or null.
 *
 * The checks go from the cheapest to the dearest and stop at the first that fails: the request is already a 404, the
 * reserved area is on, Groups is there, the address has the shape of a single content. Only then the content is looked
 * up (without the filters of Groups, which would hide it from this search too), in the language of the address, among
 * the published contents of a public type. A detail page that the theme switches off on purpose stays a 404.
 *
 * @return WP_Post|null The content that Groups reserved and the current user cannot read.
 */
function dli_find_restricted_post_for_request() {
	if ( ! is_404() || ! dli_is_reserved_area_enabled() || ! class_exists( 'Groups_Post_Access' ) ) {
		return null;
	}

	global $wp_query;
	$post_id = absint( $wp_query->get( 'page_id' ) );
	if ( 0 === $post_id ) {
		$post_id = absint( $wp_query->get( 'p' ) );
	}
	// The path of a page as it was asked for: after a 404 WordPress shortens `pagename` to the last part of the path and
	// copies the name of a content of any type into it, so the original request is the one to read.
	$requested = (array) $wp_query->query;
	$page_path = isset( $requested['pagename'] ) ? (string) $requested['pagename'] : '';
	$name      = (string) $wp_query->get( 'name' );

	$post = null;
	if ( $post_id > 0 ) {
		$post = get_post( $post_id );
	}
	if ( ! $post instanceof WP_Post && '' !== $page_path ) {
		$post = get_page_by_path( $page_path, OBJECT, 'page' );
	}
	if ( ! $post instanceof WP_Post && '' !== $name ) {
		$post_type = $wp_query->get( 'post_type' );
		$found     = get_posts(
			array(
				'name'             => $name,
				'post_type'        => $post_type ? $post_type : 'post',
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'no_found_rows'    => true,
				'suppress_filters' => true,
				'lang'             => dli_current_language( 'slug' ),
			)
		);
		$post      = $found ? $found[0] : null;
	}

	if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
		return null;
	}
	$type = get_post_type_object( $post->post_type );
	if ( ! $type || ! $type->public || dli_is_detail_page_disabled( $post ) ) {
		return null;
	}
	if ( Groups_Post_Access::user_can_read_post( $post->ID ) ) {
		return null;
	}
	return $post;
}

/**
 * Answer 401 or 403 to a request for a content that Groups reserved, in place of the 404.
 *
 * A visitor who is not logged in gets 401 and the way to log in; a logged-in user, who has nothing to gain from logging
 * in again, gets 403. The response has its own template, is not indexed and is not cached, and never says what the
 * content is called.
 *
 * @return void
 */
function dli_handle_restricted_content() {
	$post = dli_find_restricted_post_for_request();
	if ( null === $post ) {
		return;
	}

	global $wp_query;
	$status           = is_user_logged_in() ? 403 : 401;
	$wp_query->is_404 = false;
	dli_restricted_content_state(
		array(
			'status' => $status,
			'url'    => (string) get_permalink( $post ),
		)
	);
	status_header( $status );
	nocache_headers();
	add_filter( 'template_include', 'dli_restricted_content_template', 99 );
	add_filter( 'wp_robots', 'wp_robots_no_robots' );
	add_filter( 'document_title_parts', 'dli_restricted_content_title_parts' );
}
add_action( 'template_redirect', 'dli_handle_restricted_content', 6 );

/**
 * The template of the response.
 *
 * @param string $template The template that WordPress chose.
 * @return string
 */
function dli_restricted_content_template( $template ) {
	return null === dli_restricted_content_state() ? $template : get_theme_file_path( 'restricted-content.php' );
}

/**
 * The title of the response, in place of the title of the content.
 *
 * @param array<string, string> $parts The parts of the title of the document.
 * @return array<string, string>
 */
function dli_restricted_content_title_parts( $parts ) {
	$state = dli_restricted_content_state();
	if ( null === $state ) {
		return $parts;
	}
	$parts['title'] = 403 === $state['status']
		? __( 'Non hai i permessi per vedere questo contenuto', 'design_laboratori_italia' )
		: __( 'Contenuto riservato', 'design_laboratori_italia' );
	return $parts;
}
