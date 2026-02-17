<?php
/**
 * The code to enable analytics.
 *
 * @package Design_Laboratori_Italia
 */

$analytics_text = (string) dli_get_option( 'analytics_code', 'setup' );

$allowed_analytics_html = array(
	'script'   => array(
		'src'             => true,
		'type'            => true,
		'async'           => true,
		'defer'           => true,
		'id'              => true,
		'crossorigin'     => true,
		'integrity'       => true,
		'referrerpolicy'  => true,
		'nonce'           => true,
	),
	'noscript' => array(),
	'iframe'   => array(
		'src'             => true,
		'height'          => true,
		'width'           => true,
		'style'           => true,
		'title'           => true,
		'loading'         => true,
		'referrerpolicy'  => true,
	),
	'img'      => array(
		'src'             => true,
		'alt'             => true,
		'height'          => true,
		'width'           => true,
		'style'           => true,
		'loading'         => true,
		'decoding'        => true,
		'referrerpolicy'  => true,
	),
);

if ( '' !== $analytics_text ) {
	echo wp_kses( $analytics_text, $allowed_analytics_html );
}
