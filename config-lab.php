<?php
/**
 * Theme configuration constants.
 *
 * @package Design_Laboratori_Italia
 */

// Constants.
define( 'DLI_SHORT_DESCRIPTION_SIZE', 255 );

// Multilanguage options.
define( 'DLI_IT_SLUG', 'it' );
define( 'DLI_EN_SLUG', 'en' );
define( 'DLI_DEFAULT_LANGUAGE', DLI_IT_SLUG );
define( 'DLI_ENG_SUFFIX_LANGUAGE', '_eng' );
define( 'DLI_THEMA_PATH', plugin_dir_path( __FILE__ ) );
define( 'DLI_THEMA_URL', get_template_directory_uri() );

// Site Map.
define( 'DLI_HOMEPAGE_SLUG', 'homepage' );
define( 'DLI_HOMEPAGE_NAME', 'Home Page' );
define( 'DLI_ENTE_SLUG', 'enteappartenenza' );

// Define global lab data format.
define( 'DLI_ACF_DATE_FORMAT', 'd/m/Y' );
define( 'DLI_ACF_SHORT_DESC_LENGTH', 50 );

// Pagination.
define( 'TECHNICAL_RESOURCES_PER_ROW', 2 );
define( 'PERSONE_PER_ROW', 2 );
define( 'DLI_POSTS_PER_PAGE', 6 );
define( 'DLI_POST_PER_PAGE_VALUES', array( '3', '6', '9', '12', '24', '48', '96' ) );
define( 'DLI_POST_PER_PAGE_VALUES_COMBINED', array_combine( DLI_POST_PER_PAGE_VALUES, DLI_POST_PER_PAGE_VALUES ) );
define( 'DLI_PER_PAGE', 4 );
define( 'DLI_PER_PAGE_BIG', 10 );
define( 'DLI_PER_PAGE_VALUES', array( '4', '10', '20', '30', '40', '50' ) );

// Define roles and permissions.
define( 'DLI_SUPER_EDITOR_ROLE_SLUG', 'dli_super_editor' );
define( 'DLI_SUPER_EDITOR_ROLE_NAME', 'Super Editor' );
define( 'DLI_EDIT_CONFIG_PERMISSION', 'dli_edit_site_configuration' );
define( 'DLI_ADMIN_EDIT_CONFIG_PERMISSION', 'manage_options' );

// Active-template marker strings.
define( 'DLI_TEXT_TEMPLATE_ACTIVE_IT', ' [template attivo]' );
define( 'DLI_TEXT_TEMPLATE_ACTIVE_EN', ' [template active]' );

// CUSTOM CONTENT TYPES.
define( 'BANNER_POST_TYPE', 'banner' );
define( 'EVENT_POST_TYPE', 'evento' );
define( 'NEWS_POST_TYPE', 'notizia' );
define( 'PATENT_POST_TYPE', 'brevetto' );
define( 'PEOPLE_POST_TYPE', 'persona' );
define( 'PEOPLE_TYPE_POST_TYPE', 'tipologia-persona' );
define( 'PLACE_POST_TYPE', 'luogo' );
define( 'PROGETTO_POST_TYPE', 'progetto' );
define( 'PUBLICATION_POST_TYPE', 'pubblicazione' );
define( 'SPINOFF_POST_TYPE', 'spinoff' );
define( 'SPONSOR_POST_TYPE', 'sponsor' );
define( 'RESEARCH_ACTIVITY_POST_TYPE', 'indirizzo-di-ricerca' );
define( 'TECHNICAL_RESOURCE_POST_TYPE', 'risorsa-tecnica' );

// DEFAULT WP CONTENT TYPES.
define( 'WP_DEFAULT_POST', 'post' );
define( 'WP_DEFAULT_PAGE', 'page' );

// CUSTOM TAXONOMIES.
define( 'HOLDER_TAXONOMY', 'titolare-brevetto' );
define( 'PLACE_TYPE_TAXONOMY', 'tipologia-luogo' );
define( 'PUBLICATION_TYPE_TAXONOMY', 'tipo-pubblicazione' );
define( 'STRUCTURE_TAXONOMY', 'struttura' );
define( 'THEMATIC_AREA_TAXONOMY', 'area-tematica' );
define( 'BUSINESS_SECTOR_TAXONOMY', 'settore-attivita' );
define( 'RT_TYPE_TAXONOMY', 'tipo-risorsa-tecnica' );

// DEFAULT WP TAXONOMIES.
define( 'WP_DEFAULT_CATEGORY', 'category' );
define( 'WP_DEFAULT_TAGS', 'post_tag' );

// Archive page slugs.
define( 'SLUG_ARCHIVIO_PROGETTI_IT', 'archivio-progetti' );
define( 'SLUG_ARCHIVIO_PROGETTI_EN', 'projects-archive' );
define( 'SLUG_BLOG_IT', 'il-blog' );
define( 'SLUG_BLOG_EN', 'the-blog' );
define( 'SLUG_BREVETTI_IT', 'brevetti' );
define( 'SLUG_BREVETTI_EN', 'patents' );
define( 'SLUG_EVENTI_IT', 'eventi' );
define( 'SLUG_EVENTI_EN', 'events' );
define( 'SLUG_LABORATORIO_IT', 'il-laboratorio' );
define( 'SLUG_LABORATORIO_EN', 'the-lab' );
define( 'SLUG_LUOGHI_IT', 'luoghi' );
define( 'SLUG_LUOGHI_EN', 'places' );
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
define( 'SLUG_SPINOFF_IT', 'spinoff' );
define( 'SLUG_SPINOFF_EN', 'spinoff-en' );
define( 'SLUG_SPONSOR_IT', 'sponsor' );
define( 'SLUG_SPONSOR_EN', 'sponsor-en' );
define( 'SLUG_TECHNICAL_RESOURCE_IT', 'risorse-tecniche' );
define( 'SLUG_TECHNICAL_RESOURCE_EN', 'technical-resources' );

// Default static pages of the site.
define( 'SLUG_ACCESSIBILITA_IT', 'accessibilita' );
define( 'SLUG_ACCESSIBILITA_EN', 'accessibility' );
define( 'SLUG_PRESENTAZIONE_IT', 'presentazione' );
define( 'SLUG_PRESENTAZIONE_EN', 'presentation' );
define( 'SLUG_CONTATTI_IT', 'contatti' );
define( 'SLUG_CONTATTI_EN', 'contacts' );
define( 'SLUG_PRIVACY_IT', 'privacy-policy' );
define( 'SLUG_PRIVACY_EN', 'privacy-policy-en' );
define( 'SLUG_NOTE_LEGALI_IT', 'note-legali' );
define( 'SLUG_NOTE_LEGALI_EN', 'legal-notes' );
define( 'SLUG_MAPPA_SITO_IT', 'mappa-sito' );
define( 'SLUG_MAPPA_SITO_EN', 'site-map' );
define( 'SLUG_NEWSLETTER_IT', 'newsletter' );
define( 'SLUG_NEWSLETTER_EN', 'newsletter-en' );

// Archive page slug by post type.
define(
	'DLI_PAGE_PER_CT',
	array(
		EVENT_POST_TYPE              => array(
			'it' => SLUG_EVENTI_IT,
			'en' => SLUG_EVENTI_EN,
		),
		NEWS_POST_TYPE               => array(
			'it' => SLUG_NOTIZIE_IT,
			'en' => SLUG_NOTIZIE_EN,
		),
		PEOPLE_POST_TYPE             => array(
			'it' => SLUG_PERSONE_IT,
			'en' => SLUG_PERSONE_EN,
		),
		PEOPLE_TYPE_POST_TYPE        => array(
			'it' => SLUG_PERSONE_IT,
			'en' => SLUG_PERSONE_EN,
		),
		PLACE_POST_TYPE              => array(
			'it' => SLUG_LUOGHI_IT,
			'en' => SLUG_LUOGHI_EN,
		),
		PROGETTO_POST_TYPE           => array(
			'it' => SLUG_PROGETTI_IT,
			'en' => SLUG_PROGETTI_EN,
		),
		PUBLICATION_POST_TYPE        => array(
			'it' => SLUG_PUBBLICAZIONI_IT,
			'en' => SLUG_PUBBLICAZIONI_EN,
		),
		PATENT_POST_TYPE             => array(
			'it' => SLUG_BREVETTI_IT,
			'en' => SLUG_BREVETTI_EN,
		),
		RESEARCH_ACTIVITY_POST_TYPE  => array(
			'it' => SLUG_RICERCA_IT,
			'en' => SLUG_RICERCA_EN,
		),
		SPINOFF_POST_TYPE            => array(
			'it' => SLUG_SPINOFF_IT,
			'en' => SLUG_SPINOFF_EN,
		),
		SPONSOR_POST_TYPE            => array(
			'it' => SLUG_SPONSOR_IT,
			'en' => SLUG_SPONSOR_EN,
		),
		TECHNICAL_RESOURCE_POST_TYPE => array(
			'it' => SLUG_TECHNICAL_RESOURCE_IT,
			'en' => SLUG_TECHNICAL_RESOURCE_EN,
		),
		WP_DEFAULT_POST              => array(
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
		PATENT_POST_TYPE,
		RESEARCH_ACTIVITY_POST_TYPE,
		PEOPLE_TYPE_POST_TYPE,
		PLACE_POST_TYPE,
		WP_DEFAULT_PAGE,
		WP_DEFAULT_POST,
		BANNER_POST_TYPE,
		SPINOFF_POST_TYPE,
		SPONSOR_POST_TYPE,
		TECHNICAL_RESOURCE_POST_TYPE,
	)
);

define(
	'DLI_POST_TYPES_TO_SEARCH',
	array(
		PEOPLE_POST_TYPE,
		EVENT_POST_TYPE,
		NEWS_POST_TYPE,
		PROGETTO_POST_TYPE,
		PUBLICATION_POST_TYPE,
		PATENT_POST_TYPE,
		RESEARCH_ACTIVITY_POST_TYPE,
		PEOPLE_TYPE_POST_TYPE,
		WP_DEFAULT_PAGE,
		WP_DEFAULT_POST,
		SPINOFF_POST_TYPE,
		TECHNICAL_RESOURCE_POST_TYPE,
	)
);

define(
	'DLI_CAROUSEL_POST_TYPES',
	array(
		EVENT_POST_TYPE,
		NEWS_POST_TYPE,
		PUBLICATION_POST_TYPE,
		PATENT_POST_TYPE,
		PROGETTO_POST_TYPE,
		WP_DEFAULT_POST,
		SPINOFF_POST_TYPE,
		PATENT_POST_TYPE,
		TECHNICAL_RESOURCE_POST_TYPE,
	)
);


// Taxonomies.
define(
	'DLI_TAXONOMIES_TO_TRANSLATE',
	array(
		PLACE_TYPE_TAXONOMY,
		STRUCTURE_TAXONOMY,
		PUBLICATION_TYPE_TAXONOMY,
		THEMATIC_AREA_TAXONOMY,
		WP_DEFAULT_CATEGORY,
		WP_DEFAULT_TAGS,
		RT_TYPE_TAXONOMY,
	)
);

// Post wrapper used by carousel and featured content sections.
define(
	'DLI_POST_WRAPPER',
	array(
		'id'            => '',
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
define(
	'MANDATORY_PLUGINS',
	array(
		'polylang/polylang.php',
		'advanced-custom-fields/acf.php',
	)
);

define(
	'SUGGESTED_PLUGINS',
	array(
		array(
			'name'     => 'Advanced Custom Fields',
			'slug'     => 'advanced-custom-fields',
			'required' => true,
		),
		array(
			'name'     => 'ACF OpenStreetMap Field',
			'slug'     => 'acf-openstreetmap-field',
			'required' => true,
		),
		array(
			'name'     => 'Better aria label support',
			'slug'     => 'better-aria-label-support',
			'required' => true,
		),
		array(
			'name'     => 'Polylang - Multilanguage support',
			'slug'     => 'polylang',
			'required' => true,
		),
		array(
			'name'     => 'WP Mail SMTP',
			'slug'     => 'wp-mail-smtp',
			'required' => true,
		),
		array(
			'name'     => 'Really Simple CAPTCHA',
			'slug'     => 'really-simple-captcha',
			'required' => true,
		),
	)
);


// Item link modes for events/news (ACF select field: link_dettaglio).
define(
	'DLI_ITEM_LINK',
	array(
		'DETAIL_PAGE' => 'scheda',
		'WEBSITE'     => 'sitoweb',
		'ATTACHMENT'  => 'allegato',
	),
);
// Home page sections that can be enabled/ordered.
define(
	'DLI_HP_SECTIONS',
	array(
		'main_hero'            =>
			array(
				'id'       => 'main_hero',
				'name'     => 'Hero principale',
				'template' => 'template-parts/home/main-hero',
			),
		'site_description'     =>
				array(
					'id'       => 'site_description',
					'name'     => 'Descrizione del sito',
					'template' => 'template-parts/home/site-presentation',
				),
		'main_carousel'        =>
			array(
				'id'       => 'main_carousel',
				'name'     => 'Carousel principale',
				'template' => 'template-parts/home/carousel',
			),
		'featured_contents'    =>
			array(
				'id'       => 'featured_contents',
				'name'     => 'Contenuti in evidenza',
				'template' => 'template-parts/home/featured-contents',
			),
		'events_section'       =>
				array(
					'id'       => 'events_section',
					'name'     => 'Eventi',
					'template' => 'template-parts/home/hp-list-event',
				),
		'news_section'         =>
				array(
					'id'       => 'news_section',
					'name'     => 'Notizie',
					'template' => 'template-parts/home/hp-list-news',
				),
		'publications_section' =>
				array(
					'id'       => 'publications_section',
					'name'     => 'Pubblicazioni',
					'template' => 'template-parts/home/hp-list-publication',
				),
		'articles_section'     =>
				array(
					'id'       => 'articles_section',
					'name'     => 'Articoli',
					'template' => 'template-parts/home/hp-list-article',
				),
		'banners_section'      =>
				array(
					'id'       => 'banners_section',
					'name'     => 'Banner',
					'template' => 'template-parts/home/hp-banners-section',
				),
		'sponsors_section'     =>
				array(
					'id'       => 'sponsors_section',
					'name'     => 'Sponsor',
					'template' => 'template-parts/home/hp-sponsor-section',
				),
	)
);
