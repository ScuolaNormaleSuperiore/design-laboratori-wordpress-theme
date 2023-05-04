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

// define(
// 	'DLI_STATIC_PAGES',
// 	array(
// 		array(
// 			'content_slug_it'    => SLUG_PRESENTAZIONE_IT,
// 			'content_slug_en'    => SLUG_PRESENTAZIONE_EN,
// 			'content_title_it'   => 'Presentazione',
// 			'content_title_en'   => 'Presentation',
// 			'content_it'         => 'La nostra storia...',
// 			'content_en'         => 'Our history...',
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => '',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_PRIVACY_IT,
// 			'content_slug_en'    => SLUG_PRIVACY_EN,
// 			'content_title_it'   => 'Privacy policy',
// 			'content_title_en'   => 'Privacy policy',
// 			'content_it'         => 'La nostra Privacy Policy...',
// 			'content_en'         => 'Our Privacy Policy...',
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => '',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_CONTATTI_IT,
// 			'content_slug_en'    => SLUG_CONTATTI_EN,
// 			'content_title_it'   => 'Contatti',
// 			'content_title_en'   => 'Contacts',
// 			'content_it'         => 'I nostri contatti...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'Our contacts...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/contatti.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_ACCESSIBILITA_IT,
// 			'content_slug_en'    => SLUG_ACCESSIBILITA_EN,
// 			'content_title_it'   => 'Dichiarazione',
// 			'content_title_en'   => 'Accessibility',
// 			'content_it'         => 'La di chiarazione di accessibilità...',
// 			'content_en'         => 'The accessibility declaration...',
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => '',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_MEDIA_POLICY_IT,
// 			'content_slug_en'    => SLUG_MEDIA_POLICY_EN,
// 			'content_title_it'   => 'Media policy',
// 			'content_title_en'   => 'Media policy',
// 			'content_it'         => 'La Media policy ( italiano )...',
// 			'content_en'         => 'The Media policy ( english )...',
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => '',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_NOTE_LEGALI_IT,
// 			'content_slug_en'    => SLUG_NOTE_LEGALI_EN,
// 			'content_title_it'   => 'Note legali',
// 			'content_title_en'   => 'Legal notes',
// 			'content_it'         => 'Le note legali...',
// 			'content_en'         => 'The legal notes...',
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => '',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_MAPPA_SITO_IT,
// 			'content_slug_en'    => SLUG_MAPPA_SITO_EN,
// 			'content_title_it'   => 'Mappa del sito',
// 			'content_title_en'   => 'Site map',
// 			'content_it'         => 'La mappa del sito...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'The map of the site...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/mappasito.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_LABORATORIO_IT,
// 			'content_slug_en'    => SLUG_LABORATORIO_EN,
// 			'content_title_it'   => 'Il Laboratorio',
// 			'content_title_en'   => 'The Lab',
// 			'content_it'         => 'Descrizione del laboratorio...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'Lab description...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/il-laboratorio.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_PERSONE_IT,
// 			'content_slug_en'    => SLUG_PERSONE_EN,
// 			'content_title_it'   => 'Persone',
// 			'content_title_en'   => 'People',
// 			'content_it'         => 'Descrizione dello staff del laboratorio...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'Description of the Lab staff...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/persone.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_PROGETTI_IT,
// 			'content_slug_en'    => SLUG_PROGETTI_EN,
// 			'content_title_it'   => 'Progetti',
// 			'content_title_en'   => 'Projects',
// 			'content_it'         => 'Descrizione dei progetti del laboratorio...',
// 			'content_en'         => 'Description of the Lab projects...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/progetti.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_ARCHIVIO_PROGETTI_IT,
// 			'content_slug_en'    => SLUG_ARCHIVIO_PROGETTI_EN,
// 			'content_title_it'   => 'Archivio progetti',
// 			'content_title_en'   => 'Projects archive',
// 			'content_it'         => 'Archivio dei progetti del laboratorio...',
// 			'content_en'         => 'Projects archive of the Lab projects...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/archive-progetti.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_RICERCA_IT,
// 			'content_slug_en'    => SLUG_RICERCA_EN,
// 			'content_title_it'   => 'Attività di ricerca',
// 			'content_title_en'   => 'Research activities',
// 			'content_it'         => 'Descrizione delle attività di ricerca...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'Description of the research activities...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/ricerca.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_PUBBLICAZIONI_IT,
// 			'content_slug_en'    => SLUG_PUBBLICAZIONI_EN,
// 			'content_title_it'   => 'Pubblicazioni',
// 			'content_title_en'   => 'Publications',
// 			'content_it'         => 'Le nostre pubblicazioni ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'Our publications...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/pubblicazioni.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_NOTIZIE_IT,
// 			'content_slug_en'    => SLUG_NOTIZIE_EN,
// 			'content_title_it'   => 'Le notizie',
// 			'content_title_en'   => 'News',
// 			'content_it'         => 'Le notizie del laboratorio ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'Lab publications...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/notizie.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_EVENTI_IT,
// 			'content_slug_en'    => SLUG_EVENTI_EN,
// 			'content_title_it'   => 'Gli eventi',
// 			'content_title_en'   => 'Events',
// 			'content_it'         => 'Gli eventi del laboratorio ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'The events of the lab...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/eventi.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_LUOGHI_IT,
// 			'content_slug_en'    => SLUG_LUOGHI_EN,
// 			'content_title_it'   => 'I luoghi',
// 			'content_title_en'   => 'Places',
// 			'content_it'         => 'I luoghi del laboratorio ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'The places of the lab...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/luoghi.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_RICERCA_SITO_IT,
// 			'content_slug_en'    => SLUG_RICERCA_SITO_EN,
// 			'content_title_it'   => 'Ricerca',
// 			'content_title_en'   => 'Search',
// 			'content_it'         => 'Ricerca cose nel sito ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'Search things in the site...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/cerca.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => null,
// 		),
// 		array(
// 			'content_slug_it'    => SLUG_BLOG_IT,
// 			'content_slug_en'    => SLUG_BLOG_EN,
// 			'content_title_it'   => 'Blog',
// 			'content_title_en'   => 'Blog',
// 			'content_it'         => 'Il blog ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
// 			'content_en'         => 'The blog...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
// 			'content_status'     => 'publish',
// 			'content_author'     => 1,
// 			'content_template'   => 'page-templates/blog.php',
// 			'content_type'       => 'page',
// 			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
// 		),
// 	)
// );
