<?php
/**
 * Search template: the theme has no page for the native results of WordPress (`/?s=term`), so the request goes to the
 * search page of the theme (`page-templates/cerca.php`), in the language of the visitor, with the term in `searchstring`.
 *
 * The search page fills its field with the term. It runs the search only when the address carries the field
 * `cercasito_nonce_field`, which its own form sends: the field marks a form that was sent and only its presence is
 * checked, its value is not validated (the search is read-only and its addresses can be shared). The visitor therefore
 * presses the button once. The term is cleaned, cut and encoded, and the address is built from the address of the
 * site: the redirect never leaves it.
 *
 * @package Design_Laboratori_Italia
 */

// Longest term carried in the address.
$dli_search_term_max_length = 100;

// WordPress has already removed the escaping of the request from this variable: unslashing it again would remove the
// literal backslashes of the term.
$dli_search_term = sanitize_text_field( (string) get_query_var( 's' ) );
$dli_search_term = mb_substr( $dli_search_term, 0, $dli_search_term_max_length );

$dli_search_url = trailingslashit( dli_get_search_link( dli_current_language( 'slug' ) ) );
if ( '' !== $dli_search_term ) {
	$dli_search_url = add_query_arg( 'searchstring', rawurlencode( $dli_search_term ), $dli_search_url );
}

if ( wp_safe_redirect( $dli_search_url ) ) {
	exit;
}
