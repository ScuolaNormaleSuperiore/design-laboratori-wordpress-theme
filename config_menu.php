<?php

define (
	'DLI_LAB_MENU_IT',
	array(
		'name'     => 'Il laboratorio',
		'lang'     => 'it',
		'location' => 'menu-lab',
		'items' => array(
			array(
				'slug'         => SLUG_PERSONE_IT,
				'title'        => 'Persone',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PROGETTI_IT,
				'title'        => 'Progetti',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_IT,
				'title'        => 'Attività di ricerca',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PUBBLICAZIONI_IT,
				'title'        => 'Pubblicazioni',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_BREVETTI_IT,
				'title'        => 'Brevetti',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);

define(
	'DLI_NEWS_MENU_IT',
	array(
		'name'     => 'Novità',
		'lang'     => 'it',
		'location' => 'menu-header-right',
		'items' => array(
			array(
				'slug'         => SLUG_NOTIZIE_IT,
				'title'        => 'Notizie',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_EVENTI_IT,
				'title'        => 'Eventi',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_CONTATTI_IT,
				'title'        => 'Contatti',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_LUOGHI_IT,
				'title'        => 'Dove siamo',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);

define(
	'DLI_FOOTER_MENU_IT',
	array(
		'name'     => 'Footer it',
		'lang'     => 'it',
		'location' => 'menu-footer',
		'items' => array(
			array(
				'slug'         => SLUG_PRIVACY_IT,
				'title'        => 'Privacy policy',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => '',
				'title'        => 'Cookies policy',
				'content_type' => '',
				'post_type'    => '',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => esc_url( site_url() ) . '/'. SLUG_PRIVACY_IT . '#cookies',
			),
			array(
				'slug'         => SLUG_NOTE_LEGALI_IT,
				'title'        => 'Note legali',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_MAPPA_SITO_IT,
				'title'        => 'Mappa del sito',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_SITO_IT,
				'title'        => 'Cerca',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_BLOG_IT,
				'title'        => 'Blog',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => '',
				'title'        => 'Crediti',
				'content_type' => '',
				'post_type'    => '',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => 'https://developers.italia.it/it/software/sns_pi-scuolanormalesuperiore-design-laboratori-wordpress-theme',
			),
		),
	)
);

define(
	'DLI_USEFUL_LINKS_MENU_IT',
	array(
		'name'     => 'Link utili',
		'lang'     => 'it',
		'location' => 'menu-links',
		'items' => array(
			array(
				'slug'         => SLUG_ACCESSIBILITA_IT,
				'title'        => 'Dichiarazione di accessibilità',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_ARCHIVIO_PROGETTI_IT,
				'title'        => 'Archivio progetti',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);

define(
	'DLI_PRESENTATION_MENU_IT',
	array(
		'name'     => 'Presentazione',
		'lang'     => 'it',
		'location' => 'menu-right',
		'items' => array(
			array(
				'slug'         => SLUG_PRESENTAZIONE_IT,
				'title'        => 'Presentazione',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);

define(
	'DLI_LAB_MENU_EN',
	array(
		'name'     => 'The Lab',
		'lang'     => 'en',
		'location' => 'menu-lab',
		'items' => array(
			array(
				'slug'         => SLUG_PERSONE_EN,
				'title'        => 'People',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PROGETTI_EN,
				'title'        => 'Projects',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_EN,
				'title'        => 'Research activities',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PUBBLICAZIONI_EN,
				'title'        => 'Publications',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_BREVETTI_EN,
				'title'        => 'Patents',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);

define(
	'DLI_PRESENTATION_MENU_EN',
	array(
		'name'     => 'Presentation',
		'lang'     => 'en',
		'location' => 'menu-right',
		'items' => array(
			array(
				'slug'         => SLUG_PRESENTAZIONE_EN,
				'title'        => 'Presentation',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);

define(
	'DLI_NEWS_MENU_EN',
	array(
		'name'     => 'News',
		'lang'     => 'en',
		'location' => 'menu-header-right',
		'items' => array(
			array(
				'slug'         => SLUG_NOTIZIE_EN,
				'title'        => 'News',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_EVENTI_EN,
				'title'        => 'Events',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_CONTATTI_EN,
				'title'        => 'Contacts',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_LUOGHI_EN,
				'title'        => 'Where we are',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);

define(
	'DLI_FOOTER_MENU_EN',
	array(
		'name'     => 'Footer en',
		'lang'     => 'en',
		'location' => 'menu-footer',
		'items' => array(
			array(
				'slug'         => SLUG_PRIVACY_EN,
				'title'        => 'Privacy policy',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => '',
				'title'        => 'Cookies policy',
				'content_type' => '',
				'post_type'    => '',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => esc_url( site_url() ) . '/en/'. SLUG_PRIVACY_EN . '#cookies',
			),
			array(
				'slug'         => SLUG_NOTE_LEGALI_EN,
				'title'        => 'Legal notes',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_MAPPA_SITO_EN,
				'title'        => 'Site map',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_SITO_EN,
				'title'        => 'Search',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_BLOG_EN,
				'title'        => 'Blog',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => '',
				'title'        => 'Credits',
				'content_type' => '',
				'post_type'    => '',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => 'https://developers.italia.it/it/software/sns_pi-scuolanormalesuperiore-design-laboratori-wordpress-theme',
			),
		),
	)
);

define(
	'DLI_USEFUL_LINKS_MENU_EN',
	array(
		'name'     => 'Useful link',
		'lang'     => 'en',
		'location' => 'menu-links',
		'items' => array(
			array(
				'slug'         => SLUG_ACCESSIBILITA_EN,
				'title'        => 'Accessibility declaration',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_ARCHIVIO_PROGETTI_EN,
				'title'        => 'Projects archive',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	)
);
