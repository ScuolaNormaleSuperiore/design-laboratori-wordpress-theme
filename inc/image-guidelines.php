<?php
/**
 * Recommended image formats: the list of the places where an image is uploaded, with the ratio, the sizes and the
 * advice on the crop, and the short text that the upload fields show to the editor.
 *
 * The list is the single source of the sizes written in the administration. The guide `DOC/HowTo/Immagini.md` repeats
 * them for the readers of the documentation, and a unit test checks that every place of the theme that shows an
 * image is in the list. When a template, a field or an image size is created or changed, update the list and the guide.
 *
 * The sizes are a starting point and not an exact size: the box of the image changes shape with the screen and the
 * image is always cropped around its centre.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_image_guidelines' ) ) {
	/**
	 * The recommended format of every place where an image is uploaded.
	 *
	 * Every place has: `label`, `ratio` (words, translated), `aspect` (width and height of a fixed ratio, or null),
	 * `recommended` and `minimum` (width and height in pixels, the height of the minimum may be null, or null when the
	 * place has no size), `formats`, `note` on the crop and `safe_zone` (where to keep the subject, or an empty string).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function dli_get_image_guidelines() {
		$safe_zone_hero   = __( 'tieni il soggetto nel rettangolo centrale, circa il 55% della larghezza e il 65% dell\'altezza: sul telefono e con titoli lunghi i bordi vengono tagliati.', 'design_laboratori_italia' );
		$safe_zone_banner = __( 'tieni il soggetto nel rettangolo centrale, circa il 40% della larghezza e il 60% dell\'altezza: l\'altezza della fascia cambia con il testo e con lo schermo.', 'design_laboratori_italia' );
		$note_card        = __( 'Il riquadro è 16:9: un\'immagine con un\'altra proporzione viene tagliata al centro.', 'design_laboratori_italia' );
		$ratio_logo       = __( 'SVG, oppure PNG con fondo trasparente', 'design_laboratori_italia' );
		$note_logo        = __( 'Il logo non viene tagliato: è mostrato alto circa 80 px; un PNG deve essere alto almeno 160 px.', 'design_laboratori_italia' );

		return array(
			'hero_home'               => array(
				'label'       => __( 'Hero della home', 'design_laboratori_italia' ),
				'ratio'       => __( 'panoramico', 'design_laboratori_italia' ),
				'aspect'      => null,
				'recommended' => array( 1920, 600 ),
				'minimum'     => array( 1600, null ),
				'formats'     => 'JPG, PNG',
				'note'        => __( 'L\'immagine riempie una fascia a tutta larghezza e viene tagliata sopra, sotto e ai lati.', 'design_laboratori_italia' ),
				'safe_zone'   => $safe_zone_hero,
			),
			'banner_home'             => array(
				'label'       => __( 'Banner della home', 'design_laboratori_italia' ),
				'ratio'       => __( 'panoramico', 'design_laboratori_italia' ),
				'aspect'      => null,
				'recommended' => array( 1920, 480 ),
				'minimum'     => array( 1600, null ),
				'formats'     => 'JPG, PNG',
				'note'        => __( 'L\'immagine riempie una fascia a tutta larghezza e viene tagliata sopra, sotto e ai lati.', 'design_laboratori_italia' ),
				'safe_zone'   => $safe_zone_banner,
			),
			'card_elenco'             => array(
				'label'       => __( 'Elenchi di contenuti (progetti, eventi, brevetti, ricerca, spin-off)', 'design_laboratori_italia' ),
				'ratio'       => '16:9',
				'aspect'      => array( 16, 9 ),
				'recommended' => array( 1200, 675 ),
				'minimum'     => array( 600, 338 ),
				'formats'     => 'JPG, PNG, WebP',
				'note'        => $note_card,
				'safe_zone'   => '',
			),
			'carousel_correlati'      => array(
				'label'       => __( 'Carousel e contenuti correlati', 'design_laboratori_italia' ),
				'ratio'       => '16:9',
				'aspect'      => array( 16, 9 ),
				'recommended' => array( 1200, 675 ),
				'minimum'     => array( 600, 338 ),
				'formats'     => 'JPG, PNG, WebP',
				'note'        => $note_card,
				'safe_zone'   => '',
			),
			// Measured on a news page: 526 × 351 on screen from a 600 × 400 file, in its own proportions.
			'dettaglio'               => array(
				'label'       => __( 'Pagine di dettaglio (notizie, luoghi, risorse tecniche, pagine)', 'design_laboratori_italia' ),
				'ratio'       => __( 'libero', 'design_laboratori_italia' ),
				'aspect'      => null,
				'recommended' => null,
				'minimum'     => array( 800, null ),
				'formats'     => 'JPG, PNG, WebP',
				'note'        => __( 'L\'immagine non viene tagliata: è mostrata nelle sue proporzioni, in una colonna larga circa 530 px. Un file largo almeno 1200 px resta nitido sugli schermi ad alta densità.', 'design_laboratori_italia' ),
				'safe_zone'   => '',
			),
			// Measured on the home: logos shown 64 px high and 106 to 220 px wide, from files 181 to 373 px high.
			'sponsor'                 => array(
				'label'       => __( 'Sponsor', 'design_laboratori_italia' ),
				'ratio'       => __( 'logo orizzontale su fondo trasparente', 'design_laboratori_italia' ),
				'aspect'      => null,
				'recommended' => null,
				'minimum'     => null,
				'formats'     => 'SVG, PNG',
				'note'        => __( 'Il logo viene mostrato alto al massimo 64 px: un\'altezza di almeno 128 px resta nitida sugli schermi ad alta densità.', 'design_laboratori_italia' ),
				'safe_zone'   => '',
			),
			// Measured on a person page: 112 × 112 on screen, in the circle of the Bootstrap Italia avatar, from a 528 × 521 file.
			'persona'                 => array(
				'label'       => __( 'Persone', 'design_laboratori_italia' ),
				'ratio'       => __( 'quadrato', 'design_laboratori_italia' ),
				'aspect'      => array( 1, 1 ),
				'recommended' => array( 500, 500 ),
				'minimum'     => array( 224, 224 ),
				'formats'     => 'JPG, PNG, WebP',
				'note'        => __( 'La foto viene mostrata in un cerchio, al massimo di 112 px: inquadra il volto al centro, perché gli angoli vengono tagliati.', 'design_laboratori_italia' ),
				'safe_zone'   => '',
			),
			// Measured on a publication page: 305 × 404 on screen from a 390 × 517 file, in its own proportions.
			'pubblicazione_copertina' => array(
				'label'       => __( 'Copertina di una pubblicazione', 'design_laboratori_italia' ),
				'ratio'       => __( 'verticale', 'design_laboratori_italia' ),
				'aspect'      => null,
				'recommended' => null,
				'minimum'     => array( 610, null ),
				'formats'     => 'JPG, PNG, WebP',
				'note'        => __( 'La copertina non viene tagliata: è mostrata nel suo formato, in una colonna larga circa 305 px (sul telefono al massimo 192 px). Un file largo almeno 610 px resta nitido sugli schermi ad alta densità.', 'design_laboratori_italia' ),
				'safe_zone'   => '',
			),
			'logo_header'             => array(
				'label'       => __( 'Logo dell\'intestazione', 'design_laboratori_italia' ),
				'ratio'       => $ratio_logo,
				'aspect'      => null,
				'recommended' => null,
				'minimum'     => null,
				'formats'     => 'SVG, PNG',
				'note'        => $note_logo,
				'safe_zone'   => '',
			),
			'logo_footer'             => array(
				'label'       => __( 'Logo del piè di pagina', 'design_laboratori_italia' ),
				'ratio'       => $ratio_logo,
				'aspect'      => null,
				'recommended' => null,
				'minimum'     => null,
				'formats'     => 'SVG, PNG',
				'note'        => $note_logo,
				'safe_zone'   => '',
			),
			'logo_mobile'             => array(
				'label'       => __( 'Logo per il menu del telefono', 'design_laboratori_italia' ),
				'ratio'       => $ratio_logo,
				'aspect'      => null,
				'recommended' => null,
				'minimum'     => null,
				'formats'     => 'SVG, PNG',
				'note'        => $note_logo,
				'safe_zone'   => '',
			),
			// Measured on the list of the spin-offs: the logo fills a box 403 px wide and 238 to 277 px high (the height follows
			// the text of the card) with `object-fit: cover`: a 378 × 133 logo shows 60% of its width. The detail page of a
			// spin-off does not show the logo.
			'logo_spinoff'            => array(
				'label'       => __( 'Logo di uno spin-off (elenco degli spin-off)', 'design_laboratori_italia' ),
				'ratio'       => __( 'quasi 3:2', 'design_laboratori_italia' ),
				'aspect'      => null,
				'recommended' => array( 800, 500 ),
				'minimum'     => array( 600, null ),
				'formats'     => 'PNG, JPG',
				'note'        => __( 'Il logo si vede solo nell\'elenco degli spin-off, non nella scheda. Nell\'elenco riempie un riquadro quasi 3:2 la cui altezza cambia con il testo della scheda, e un logo molto largo viene tagliato ai lati.', 'design_laboratori_italia' ),
				'safe_zone'   => __( 'metti il logo al centro di un\'immagine di proporzioni simili, con un margine di almeno il 15% su ogni lato.', 'design_laboratori_italia' ),
			),
			// Measured on a technical resource page: 280 × 280 on screen from a 512 × 512 file, in a figure at most 280 px wide
			// (`single-risorsa-tecnica.php`), in its own proportions. Without the photo, the featured image is used.
			'foto_risorsa_tecnica'    => array(
				'label'       => __( 'Foto di una risorsa tecnica', 'design_laboratori_italia' ),
				'ratio'       => __( 'libero', 'design_laboratori_italia' ),
				'aspect'      => null,
				'recommended' => null,
				'minimum'     => array( 560, null ),
				'formats'     => 'JPG, PNG, WebP',
				'note'        => __( 'La foto non viene tagliata: è mostrata nelle sue proporzioni, in uno spazio largo al massimo 280 px. Un file largo almeno 560 px resta nitido sugli schermi ad alta densità. Se manca, la scheda usa l\'immagine in evidenza.', 'design_laboratori_italia' ),
				'safe_zone'   => '',
			),
		);
	}
}

if ( ! function_exists( 'dli_get_image_guideline' ) ) {
	/**
	 * The recommended format of one place, or null when the place is not in the list.
	 *
	 * @param string $id Identifier of the place.
	 * @return array<string, mixed>|null
	 */
	function dli_get_image_guideline( $id ) {
		$guidelines = dli_get_image_guidelines();
		return $guidelines[ $id ] ?? null;
	}
}

if ( ! function_exists( 'dli_get_image_guideline_text' ) ) {
	/**
	 * The short text on the recommended format of one place, ready to be shown under an upload field.
	 *
	 * The text is plain: the caller escapes it where it prints it. An unknown place gives an empty string.
	 *
	 * @param string $id Identifier of the place.
	 * @return string
	 */
	function dli_get_image_guideline_text( $id ) {
		$guideline = dli_get_image_guideline( $id );
		if ( null === $guideline ) {
			return '';
		}

		if ( null !== $guideline['recommended'] ) {
			/* translators: 1: ratio of the image (for example 16:9), 2: width in pixels, 3: height in pixels. */
			$recommended = sprintf( __( 'Formato consigliato: %1$s, %2$d × %3$d px', 'design_laboratori_italia' ), $guideline['ratio'], $guideline['recommended'][0], $guideline['recommended'][1] );
		} else {
			/* translators: %s: ratio of the image, in words. */
			$recommended = sprintf( __( 'Formato consigliato: %s', 'design_laboratori_italia' ), $guideline['ratio'] );
		}

		if ( null !== $guideline['minimum'] ) {
			if ( null === $guideline['minimum'][1] ) {
				/* translators: %d: minimum width in pixels. */
				$minimum = sprintf( __( 'minimo %d px di larghezza', 'design_laboratori_italia' ), $guideline['minimum'][0] );
			} else {
				/* translators: 1: minimum width in pixels, 2: minimum height in pixels. */
				$minimum = sprintf( __( 'minimo %1$d × %2$d px', 'design_laboratori_italia' ), $guideline['minimum'][0], $guideline['minimum'][1] );
			}
			$recommended .= ' (' . $minimum . ')';
		}

		$parts = array( $recommended . '.', $guideline['note'] );
		if ( '' !== $guideline['safe_zone'] ) {
			/* translators: %s: where to keep the subject of the image. */
			$parts[] = sprintf( __( 'Zona sicura: %s', 'design_laboratori_italia' ), $guideline['safe_zone'] );
		}
		$parts[] = __( 'Le dimensioni sono indicative: il ritaglio dipende dallo schermo.', 'design_laboratori_italia' );
		/* translators: 1: file formats, 2: maximum weight in megabytes. */
		$parts[] = sprintf( __( 'Formati: %1$s, peso massimo %2$d MB.', 'design_laboratori_italia' ), $guideline['formats'], DLI_MAX_IMG_FILE_SIZE_MB );

		return implode( ' ', $parts );
	}
}

if ( ! function_exists( 'dli_set_acf_image_field_instructions' ) ) {
	/**
	 * Write the advice on the format into the instructions of the ACF image fields of the contents.
	 *
	 * The two fields are defined in `class-spinoffmanager.php` (logo of a spin-off) and
	 * `class-technicalresourcemanager.php` (photo of a technical resource); their instructions are set here so that
	 * the text comes from the list of formats and those files stay untouched. The exports in
	 * `SETUP/ACF_Custom_Fields/` carry the same text, and `DLI_Acf_Exports_Integration_Test` checks that they do.
	 *
	 * @param array<string, mixed> $field The field that ACF is loading.
	 * @return array<string, mixed> The field.
	 */
	function dli_set_acf_image_field_instructions( $field ) {
		$places = array(
			'field_67a9de5539f8f' => 'logo_spinoff',
			'field_682ed8f4bbd48' => 'foto_risorsa_tecnica',
		);
		if ( isset( $field['key'], $places[ $field['key'] ] ) ) {
			$field['instructions'] = dli_get_image_guideline_text( $places[ $field['key'] ] );
		}
		return $field;
	}
}
add_filter( 'acf/load_field', 'dli_set_acf_image_field_instructions' );

if ( ! function_exists( 'dli_add_featured_image_guideline' ) ) {
	/**
	 * Add the advice on the format under the box "Immagine in evidenza" of the banners and of the sponsors.
	 *
	 * Only these two types: they use the classic editor, where this filter acts. The other content types have the block
	 * editor, which draws the box with JavaScript, so they get the general line of the media window and the guide.
	 * The text is plain: it is escaped here and only one paragraph is added, after the box of WordPress.
	 *
	 * @param string   $content The box of WordPress.
	 * @param int|null $post_id The post that the box belongs to.
	 * @return string The box, with the advice for a banner or a sponsor.
	 */
	function dli_add_featured_image_guideline( $content, $post_id = null ) {
		$places = array(
			BANNER_POST_TYPE  => 'banner_home',
			SPONSOR_POST_TYPE => 'sponsor',
		);
		$type   = $post_id ? get_post_type( $post_id ) : false;
		if ( ! $type || ! isset( $places[ $type ] ) ) {
			return $content;
		}
		return $content . '<p class="description dli-image-guideline">' . esc_html( dli_get_image_guideline_text( $places[ $type ] ) ) . '</p>';
	}
}
add_filter( 'admin_post_thumbnail_html', 'dli_add_featured_image_guideline', 10, 2 );

if ( ! function_exists( 'dli_get_media_window_guideline_html' ) ) {
	/**
	 * The general advice on the format of the images, for the notice of the media window.
	 *
	 * It is the line that is true for most places (16:9, enough width, the subject at the centre) and it sends the
	 * reader to the text under each field. The link to the guide is added only when its address is known and is an
	 * http or https one. The result is HTML, already escaped.
	 *
	 * @param string $guide_url Address of the published guide, or an empty string.
	 * @return string
	 */
	function dli_get_media_window_guideline_html( $guide_url = DLI_IMAGE_GUIDE_URL ) {
		$html = esc_html__( 'Immagini: di norma formato 16:9, almeno 1200 px di larghezza e soggetto al centro. Le dimensioni sono indicative: il formato di ogni posizione è scritto sotto il campo.', 'design_laboratori_italia' );

		$url = '' === $guide_url ? '' : esc_url( $guide_url, array( 'http', 'https' ) );
		if ( '' !== $url ) {
			$html .= ' <a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Guida alle immagini', 'design_laboratori_italia' ) . '</a>';
		}
		return $html;
	}
}
