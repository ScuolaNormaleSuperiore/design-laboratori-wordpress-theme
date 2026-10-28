<?php
/**
 * Helpers of the publications, shared by the detail page, the archive and the home cards.
 *
 * Provides:
 *  - dli_get_publication_authors()  The authors of a publication that can be shown, with their link.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_publication_authors' ) ) {
	/**
	 * The authors of a publication that can be shown to a visitor.
	 *
	 * An author is a person related to the publication by the `autori` field. Only published persons
	 * with a name are returned: an unpublished person is not public, and a name can be empty when
	 * both first and last name are missing and the person has no title. The link is the person page,
	 * or an empty string when the person has the detail page disabled.
	 *
	 * @param WP_Post|int $publication The publication.
	 * @return array<int, array{id: int, name: string, url: string}> Authors, in the order of the field.
	 */
	function dli_get_publication_authors( $publication ) {
		$publication = get_post( $publication );
		if ( ! $publication instanceof WP_Post ) {
			return array();
		}

		$related = dli_get_field( 'autori', $publication->ID );
		if ( ! is_array( $related ) ) {
			return array();
		}

		$authors = array();
		foreach ( $related as $person ) {
			if ( ! $person instanceof WP_Post || 'publish' !== get_post_status( $person ) ) {
				continue;
			}

			$name = trim(
				dli_get_persona_display_name(
					(string) dli_get_field( 'nome', $person->ID ),
					(string) dli_get_field( 'cognome', $person->ID ),
					get_the_title( $person )
				)
			);
			if ( '' === $name ) {
				continue;
			}

			$authors[] = array(
				'id'   => $person->ID,
				'name' => $name,
				'url'  => dli_get_field( 'disattiva_pagina_dettaglio', $person->ID ) ? '' : get_permalink( $person ),
			);
		}

		return $authors;
	}
}
