<?php

// Multilanguage options.
define( 'DLI_IT_SLUG', 'it' );
define( 'DLI_EN_SLUG', 'en' );
define( 'DLI_DEFAULT_LANGUAGE', DLI_IT_SLUG );
define( 'DLI_ENG_SUFFIX_LANGUAGE', '_eng' );
define( 'DLI_THEMA_PATH', dirname( plugin_basename( __FILE__ ) ) );
define( 'DLI_THEMA_URL', get_template_directory_uri() );

// Site Map.
define( 'DLI_HOMEPAGE_SLUG', 'homepage' );
define( 'DLI_HOMEPAGE_NAME', 'Home Page' );
define( 'DLI_ENTE_SLUG', 'enteappartenenza' );

// Define global lab data format.
define( 'DLI_ACF_DATE_FORMAT', 'd/m/Y' );
define( 'DLI_ACF_SHORT_DESC_LENGTH', 50 );
define( 'DLI_POSTS_PER_PAGE', 6 );
define( 'PERSONE_PER_ROW', 2 );

// Define template active sting.
define( 'DLI_TEXT_TEMPLATE_ACTIVE_IT', ' [template attivo]' );
define( 'DLI_TEXT_TEMPLATE_ACTIVE_EN', ' [template active]' );

// CUSTOM CONTENT TYPES.
define( 'EVENT_POST_TYPE', 'evento' );
define( 'NEWS_POST_TYPE', 'notizia' );
define( 'PEOPLE_POST_TYPE', 'persona' );
define( 'PEOPLE_TYPE_POST_TYPE', 'tipologia-persona' );
define( 'PLACE_POST_TYPE', 'luogo' );
define( 'PROGETTO_POST_TYPE', 'progetto' );
define( 'PUBLICATION_POST_TYPE', 'pubblicazione' );
define( 'RESEARCHACTIVITY_POST_TYPE', 'indirizzo-di-ricerca' );
define( 'WP_DEFAULT_POST', 'post' );
define( 'WP_DEFAULT_PAGE', 'page' );

// CUSTOM TAXONOMIES.
define( 'STRUCTURE_TAXONOMY', 'struttura' );
define( 'PLACE_TYPE_TAXONOMY', 'tipologia-luogo' );
define( 'PUBLICATION_TYPE_TAXONOMY', 'tipo-pubblicazione' );
define( 'WP_DEFAULT_CATEGORY', 'category' );

// The slug is the name of the post, that is the name that appears in the url.
// Archive pages of the site.
define( 'SLUG_LABORATORIO_IT', 'il-laboratorio' );
define( 'SLUG_LABORATORIO_EN', 'the-lab' );
define( 'SLUG_LUOGHI_IT', 'luoghi' );
define( 'SLUG_LUOGHI_EN', 'places' );
define( 'SLUG_EVENTI_IT', 'eventi' );
define( 'SLUG_EVENTI_EN', 'events' );
define( 'SLUG_NOTIZIE_IT', 'notizie' );
define( 'SLUG_NOTIZIE_EN', 'news' );
define( 'SLUG_PUBBLICAZIONI_IT', 'pubblicazioni' );
define( 'SLUG_PUBBLICAZIONI_EN', 'publications' );
define( 'SLUG_RICERCA_IT', 'ricerca' );
define( 'SLUG_RICERCA_EN', 'research' );
define( 'SLUG_PROGETTI_IT', 'progetti' );
define( 'SLUG_PROGETTI_EN', 'projects' );
define( 'SLUG_PERSONE_IT', 'persone' );
define( 'SLUG_PERSONE_EN', 'people' );
define( 'SLUG_RICERCA_SITO_IT', 'ricerca-sito' );
define( 'SLUG_RICERCA_SITO_EN', 'search-site' );
define( 'SLUG_BLOG_IT', 'il-blog' );
define( 'SLUG_BLOG_EN', 'the-blog' );
define( 'SLUG_ARCHIVIO_PROGETTI_IT', 'archivio-progetti' );
define( 'SLUG_ARCHIVIO_PROGETTI_EN', 'projects-archive' );


// Default static pages of the site.
define( 'SLUG_ACCESSIBILITA_IT', 'accessibilita' );
define( 'SLUG_ACCESSIBILITA_EN', 'accessibility' );
define( 'SLUG_PRESENTAZIONE_IT', 'presentazione' );
define( 'SLUG_PRESENTAZIONE_EN', 'presentation' );
define( 'SLUG_CONTATTI_IT', 'contatti' );
define( 'SLUG_CONTATTI_EN', 'contacts' );
define( 'SLUG_PRIVACY_IT', 'privacy-policy' );
define( 'SLUG_PRIVACY_EN', 'privacy-policy-en' );
define( 'SLUG_MEDIA_POLICY_IT', 'media-policy' );
define( 'SLUG_MEDIA_POLICY_EN', 'media-policy-en' );
define( 'SLUG_NOTE_LEGALI_IT', 'note-legali' );
define( 'SLUG_NOTE_LEGALI_EN', 'legal-notes' );
define( 'SLUG_MAPPA_SITO_IT', 'mappa-sito' );
define( 'SLUG_MAPPA_SITO_EN', 'site-map' );

// ARCHIVE PAGE PER POST TYPE.
define(
	'DLI_PAGE_PER_CT',
	array(
		EVENT_POST_TYPE => array(
			'it' => SLUG_EVENTI_IT,
			'en' => SLUG_EVENTI_EN,
		),
		NEWS_POST_TYPE => array(
			'it' => SLUG_NOTIZIE_IT,
			'en' => SLUG_NOTIZIE_EN,
		),
		PEOPLE_POST_TYPE => array(
			'it' => SLUG_PERSONE_IT,
			'en' => SLUG_PERSONE_EN,
		),
		PEOPLE_TYPE_POST_TYPE => array(
			'it' => SLUG_PERSONE_IT,
			'en' => SLUG_PERSONE_EN,
		),
		PLACE_POST_TYPE => array(
			'it' => SLUG_LUOGHI_IT,
			'en' => SLUG_LUOGHI_EN,
		),
		PROGETTO_POST_TYPE => array(
			'it' => SLUG_PROGETTI_IT,
			'en' => SLUG_PROGETTI_EN,
		),
		PUBLICATION_POST_TYPE => array(
			'it' => SLUG_PUBBLICAZIONI_IT,
			'en' => SLUG_PUBBLICAZIONI_EN,
		),
		RESEARCHACTIVITY_POST_TYPE => array(
			'it' => SLUG_RICERCA_IT,
			'en' => SLUG_RICERCA_EN,
		),
		WP_DEFAULT_POST => array(
			'it' => SLUG_BLOG_IT,
			'en' => SLUG_BLOG_EN,
		),
	)
);

// Post Types for PolyLang.
define(
	'DLI_POST_TYPES_TO_TRANSLATE',
	array(
		PEOPLE_POST_TYPE,
		EVENT_POST_TYPE,
		NEWS_POST_TYPE,
		PROGETTO_POST_TYPE,
		PUBLICATION_POST_TYPE,
		RESEARCHACTIVITY_POST_TYPE,
		PEOPLE_TYPE_POST_TYPE,
		PLACE_POST_TYPE,
		WP_DEFAULT_PAGE,
		WP_DEFAULT_POST,
	)
);

// Taxonomies.
define(
	'DLI_TAXONOMIES_TO_TRANSLATE',
	array(
		PLACE_TYPE_TAXONOMY,
		STRUCTURE_TAXONOMY,
		PUBLICATION_TYPE_TAXONOMY,
		WP_DEFAULT_CATEGORY,
	)
);

// Post Wrapper for Carousel and Featured Contents.
define(
	'DLI_POST_WRAPPER',
	array(
		'type'          => '',
		'category'      => '',
		'category_link' => '',
		'date'          => '',
		'title'         => '',
		'description'   => '',
		'full_content'  => '',
		'link'          => '',
		'image_url'     => '',
		'image_alt'     => '',
		'image_title'   => '',
	)
);