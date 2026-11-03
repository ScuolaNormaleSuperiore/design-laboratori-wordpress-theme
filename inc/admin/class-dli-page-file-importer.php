<?php
/**
 * Import of pages from a CSV file (Strumenti DLI → Importa → Pagine).
 *
 * For now the class holds the lorem ipsum of the new pages; the reader of the file, the plan and the writer of the
 * pages are added by the next steps of the plan of the import.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The pages of the import: the rows of the file become a tree of pages, the plan says what the import would do, and the
 * import creates the pages. Also the text of the new pages, when the operator asks for lorem ipsum.
 */
class DLI_Page_File_Importer {

	/**
	 * The most pages of a file: the import of more takes too long for one request (about 45 ms a page).
	 */
	const MAX_PAGES = 200;

	/**
	 * Meta where a page created by the import keeps its identity (the key of the file, or the path of the titles).
	 */
	const META_KEY = '_dli_page_import_key';

	/**
	 * ACF key of the field `promuovi_in_home` of the base page.
	 */
	const PROMOTE_FIELD_KEY = 'field_670e717b3bedc';

	/**
	 * ACF key of the field `mostra_sottopagine_a_card` of the base page.
	 */
	const CARDS_FIELD_KEY = 'field_670e717b3bedd';

	/**
	 * The format of a key (`page_key`).
	 */
	const KEY_PATTERN = '/^[a-z0-9][a-z0-9_-]{0,63}$/';

	/**
	 * The format of a slug that a row asks for: already a slug.
	 */
	const SLUG_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

	/**
	 * Turn the rows of the file into the pages of a tree, or into the list of what is wrong.
	 *
	 * The rows are those of `DLI_CSV_Reader` (the line of the file and the cells by the name of the column). A row has one
	 * title in `level_1`, `level_2` or `level_3`: a main page opens a branch, a second level belongs to the last main page
	 * and a third level to the last second level of the branch. Pages are given back even when there are errors, so that
	 * every error of the file is reported at once; the pages must not be used when there is one.
	 *
	 * Each page has: `line`, `level`, `title`, `parent` (index in the list, null for a main page), `order`, `promote`,
	 * `cards`, `key`, `excerpt`, `slug`, `path` (the titles from the main page, in lower case) and `identity` (the key, or
	 * the path when the page has no key). Each error has a `code`, the `line` and a `detail`: `no_title`, `several_titles`,
	 * `level_jump`, `plain_text` (title or excerpt), `invalid_order`, `invalid_flag` (the column), `invalid_key`,
	 * `duplicate_key`, `invalid_slug` and `duplicate_path`.
	 *
	 * @param array<int, array{line: int, cells: array<string, string>}> $rows Rows of the file.
	 * @return array{pages: array<int, array<string, mixed>>, errors: array<int, array{code: string, line: int, detail: string}>} The pages and the errors.
	 */
	public static function parse_rows( array $rows ) {
		$pages   = array();
		$errors  = array();
		$current = array(
			1 => null,
			2 => null,
		);
		$keys    = array();
		$paths   = array();

		foreach ( $rows as $row ) {
			$cells = $row['cells'];
			$line  = $row['line'];

			$titles = array();
			foreach ( array( 1, 2, 3 ) as $level ) {
				$title = trim( (string) ( $cells[ 'level_' . $level ] ?? '' ) );
				if ( '' !== $title ) {
					$titles[ $level ] = $title;
				}
			}
			if ( 1 !== count( $titles ) ) {
				$errors[] = self::parse_error( empty( $titles ) ? 'no_title' : 'several_titles', $line );
				continue;
			}
			$level = (int) key( $titles );
			$title = $titles[ $level ];

			$parent = null;
			if ( $level > 1 ) {
				$parent = $current[ $level - 1 ];
				if ( null === $parent ) {
					$errors[] = self::parse_error( 'level_jump', $line );
					continue;
				}
			}

			$index = count( $pages );
			$path  = ( null === $parent ? '' : $pages[ $parent ]['path'] . ' / ' ) . self::normalize_title( $title );
			if ( 1 === $level ) {
				$current = array(
					1 => $index,
					2 => null,
				);
			} elseif ( 2 === $level ) {
				$current[2] = $index;
			}

			if ( isset( $paths[ $path ] ) ) {
				$errors[] = self::parse_error( 'duplicate_path', $line, $title );
			}
			$paths[ $path ] = true;

			$excerpt = trim( (string) ( $cells['excerpt'] ?? '' ) );
			foreach ( array(
				'title'   => $title,
				'excerpt' => $excerpt,
			) as $field => $text ) {
				if ( preg_match( '/[<>]/', $text ) ) {
					$errors[] = self::parse_error( 'plain_text', $line, $field );
				}
			}

			$order = trim( (string) ( $cells['menu_order'] ?? '' ) );
			if ( 1 !== preg_match( '/^\d+$/', $order ) ) {
				$errors[] = self::parse_error( 'invalid_order', $line );
			}

			$flags = array();
			foreach ( array( 'promote_in_home', 'show_subpages_as_cards' ) as $column ) {
				$value = (string) ( $cells[ $column ] ?? '' );
				if ( '0' !== $value && '1' !== $value ) {
					$errors[] = self::parse_error( 'invalid_flag', $line, $column );
				}
				$flags[ $column ] = '1' === $value;
			}

			$key = trim( (string) ( $cells['page_key'] ?? '' ) );
			if ( '' !== $key ) {
				if ( 1 !== preg_match( self::KEY_PATTERN, $key ) ) {
					$errors[] = self::parse_error( 'invalid_key', $line, $key );
				} elseif ( isset( $keys[ $key ] ) ) {
					$errors[] = self::parse_error( 'duplicate_key', $line, $key );
				}
				$keys[ $key ] = true;
			}

			$slug = trim( (string) ( $cells['slug'] ?? '' ) );
			if ( '' !== $slug && 1 !== preg_match( self::SLUG_PATTERN, $slug ) ) {
				$errors[] = self::parse_error( 'invalid_slug', $line, $slug );
			}

			$pages[] = array(
				'line'     => $line,
				'level'    => $level,
				'title'    => $title,
				'parent'   => $parent,
				'order'    => (int) $order,
				'promote'  => $flags['promote_in_home'],
				'cards'    => $flags['show_subpages_as_cards'],
				'key'      => $key,
				'excerpt'  => $excerpt,
				'slug'     => $slug,
				'path'     => $path,
				'identity' => '' !== $key ? $key : $path,
			);
		}

		return array(
			'pages'  => $pages,
			'errors' => $errors,
		);
	}

	/**
	 * Why the import cannot run on this site, before looking at the pages.
	 *
	 * @param string $language Slug of the language of the pages.
	 * @return string `plugins` (ACF or Polylang are not active), `language` (the site does not have it) or an empty string.
	 */
	public static function prerequisites_error( $language ) {
		if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'update_field' ) || ! function_exists( 'pll_set_post_language' ) ) {
			return 'plugins';
		}
		if ( ! in_array( $language, (array) dli_languages_list( array( 'fields' => 'slug' ) ), true ) ) {
			return 'language';
		}
		return '';
	}

	/**
	 * What the import would do with each page, writing nothing.
	 *
	 * A page that was imported before (same identity in the same language) is `skipped`; one that is not there is
	 * `created`, unless something stops the import: `error`. The errors, which stop the whole import, are: `plugins` and
	 * `language` (prerequisites, line 0), `empty_slug`, `ambiguous` (two pages with the identity, in the language),
	 * `trashed` (imported once, now in the bin), `path_in_use` (a page of the site, of any status or language, has the
	 * address; the detail is its ID) and `translation_conflict` (the page that this one would be linked to is already
	 * in a group that has a page in this language; the detail is the ID of that page). A page that is in the bin
	 * because it was moved there has already given its address back, so it does not block.
	 *
	 * Each row of the plan has: `line`, `title`, `level`, `parent` (index), `order`, `promote`, `cards`, `slug`, `url`,
	 * `action`, `id` (of the page that exists), `message` (the code of the error), `detail` and `translations` (the pages
	 * of other languages with the same key, by language, that the new page is linked to).
	 *
	 * @param array<int, array<string, mixed>> $pages    The pages given by `parse_rows()`, without errors.
	 * @param string                           $language Slug of the language of the pages.
	 * @return array{rows: array<int, array<string, mixed>>, errors: array<int, array{code: string, line: int, detail: string}>, counts: array{created: int, skipped: int, errors: int}} The plan.
	 */
	public static function plan( array $pages, $language ) {
		$result = array(
			'rows'   => array(),
			'errors' => array(),
			'counts' => array(
				'created' => 0,
				'skipped' => 0,
				'errors'  => 0,
			),
		);

		$blocked = self::prerequisites_error( $language );
		if ( '' !== $blocked ) {
			$result['errors'][]         = self::parse_error( $blocked, 0 );
			$result['counts']['errors'] = 1;
			return $result;
		}

		$home       = trailingslashit( dli_get_language_home_url( $language ) );
		$ids        = array();
		$slug_paths = array();

		foreach ( $pages as $index => $page ) {
			$slug      = '' !== $page['slug'] ? $page['slug'] : sanitize_title( $page['title'] );
			$parent_id = null === $page['parent'] ? 0 : ( $ids[ $page['parent'] ] ?? null );
			$slug_path = ( null === $page['parent'] ? '' : ( $slug_paths[ $page['parent'] ] ?? '' ) . '/' ) . $slug;

			$row = array(
				'line'         => $page['line'],
				'title'        => $page['title'],
				'level'        => $page['level'],
				'parent'       => $page['parent'],
				'order'        => $page['order'],
				'promote'      => $page['promote'],
				'cards'        => $page['cards'],
				'slug'         => $slug,
				'url'          => $home . $slug_path . '/',
				'action'       => 'created',
				'id'           => 0,
				'message'      => '',
				'detail'       => '',
				'translations' => array(),
			);

			$found = self::find_by_identity( $page['identity'], $language );

			if ( '' === $slug ) {
				$row = self::refuse( $row, 'empty_slug' );
			} elseif ( count( $found['same'] ) > 1 ) {
				$row = self::refuse( $row, 'ambiguous' );
			} elseif ( 1 === count( $found['same'] ) ) {
				$existing_id = $found['same'][0];
				if ( 'trash' === get_post_status( $existing_id ) ) {
					$row = self::refuse( $row, 'trashed', (string) $existing_id );
				} else {
					$row['action'] = 'skipped';
					$row['id']     = $existing_id;
					$row['url']    = (string) get_permalink( $existing_id );
					$ids[ $index ] = $existing_id;
					$slug_path     = get_page_uri( $existing_id );
				}
			} else {
				$used = null === $parent_id ? 0 : self::page_with_address( $slug, $parent_id );
				if ( $used > 0 ) {
					$row = self::refuse( $row, 'path_in_use', (string) $used );
				} elseif ( '' !== $page['key'] ) {
					foreach ( $found['others'] as $other_language => $other_id ) {
						$group = dli_get_post_translations( $other_id );
						if ( isset( $group[ $language ] ) ) {
							$row = self::refuse( $row, 'translation_conflict', (string) $other_id );
							break;
						}
						$row['translations'][ $other_language ] = $other_id;
					}
				}
			}

			$slug_paths[ $index ] = $slug_path;
			$result['rows'][]     = $row;
			if ( 'error' === $row['action'] ) {
				$result['errors'][] = self::parse_error( $row['message'], $row['line'], $row['detail'] );
			}
			$key                       = 'error' === $row['action'] ? 'errors' : $row['action'];
			$result['counts'][ $key ] += 1;
		}

		return $result;
	}

	/**
	 * Create the pages of the file, if the plan has no error: otherwise nothing is written.
	 *
	 * The pages are published, with the author given, the language, the order, the summary and the two flags; the text
	 * is lorem ipsum only for the new pages and only when asked. Each new page carries its identity in a private meta,
	 * written as soon as the page exists, so a second run recognises the pages of the first one. A page that was imported
	 * before is not touched. The new page is linked as a translation to the pages with the same key in other languages.
	 * Parents come before their subpages. A failure while writing stops the import: the pages that were created stay, are
	 * reported, and the rows that were not reached are `not_created`.
	 *
	 * @param array<int, array<string, mixed>> $pages      The pages given by `parse_rows()`, without errors.
	 * @param string                           $language   Slug of the language of the pages.
	 * @param bool                             $with_lorem Whether the new pages have a text of lorem ipsum.
	 * @param int                              $author     User who is the author of the new pages.
	 * @return array{rows: array<int, array<string, mixed>>, errors: array<int, array{code: string, line: int, detail: string}>, counts: array{created: int, skipped: int, errors: int}, written: bool} The plan, with what was done.
	 */
	public static function import( array $pages, $language, $with_lorem, $author ) {
		$result            = self::plan( $pages, $language );
		$result['written'] = false;
		if ( ! empty( $result['errors'] ) ) {
			return $result;
		}

		$result['counts']['created'] = 0;
		$ids                         = array();
		$stopped                     = false;

		foreach ( $result['rows'] as $index => $row ) {
			if ( $stopped ) {
				$result['rows'][ $index ]['action'] = 'not_created';
				continue;
			}
			if ( 'skipped' === $row['action'] ) {
				$ids[ $index ] = $row['id'];
				continue;
			}

			$parent_id = null === $row['parent'] ? 0 : (int) ( $ids[ $row['parent'] ] ?? 0 );
			$created   = self::create_page( $pages[ $index ], $row, $parent_id, $language, (bool) $with_lorem, (int) $author );

			if ( $created['id'] > 0 ) {
				$ids[ $index ]                  = $created['id'];
				$result['rows'][ $index ]['id'] = $created['id'];
				$result['written']              = true;
				$result['counts']['created']   += 1;
			}
			if ( '' !== $created['code'] ) {
				$result['rows'][ $index ]['action']  = 'error';
				$result['rows'][ $index ]['message'] = $created['code'];
				$result['rows'][ $index ]['detail']  = $created['detail'];
				$result['errors'][]                  = self::parse_error( $created['code'], $row['line'], $created['detail'] );
				$result['counts']['errors']         += 1;
				$stopped                             = true;
			}
		}

		return $result;
	}

	/**
	 * Create one page and what goes with it.
	 *
	 * @param array<string, mixed> $page       The page as the parser gave it.
	 * @param array<string, mixed> $row        Its row of the plan.
	 * @param int                  $parent_id  The parent page, 0 for a main page.
	 * @param string               $language   Slug of the language.
	 * @param bool                 $with_lorem Whether the page has a text of lorem ipsum.
	 * @param int                  $author     The author.
	 * @return array{id: int, code: string, detail: string} The ID of the page (0 when it was not created) and the error, if any.
	 */
	private static function create_page( array $page, array $row, $parent_id, $language, $with_lorem, $author ) {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_author'  => $author,
				'post_title'   => $page['title'],
				'post_name'    => $row['slug'],
				'post_parent'  => $parent_id,
				'menu_order'   => $page['order'],
				'post_excerpt' => $page['excerpt'],
				'post_content' => $with_lorem ? self::lorem_html( $page['identity'] ) : '',
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			return array(
				'id'     => 0,
				'code'   => 'insert_failed',
				'detail' => $page_id->get_error_message(),
			);
		}

		update_post_meta( $page_id, self::META_KEY, $page['identity'] );
		dli_set_post_language( $page_id, $language );
		if ( $page['promote'] ) {
			update_field( self::PROMOTE_FIELD_KEY, 1, $page_id );
		}
		if ( $page['cards'] ) {
			update_field( self::CARDS_FIELD_KEY, 1, $page_id );
		}

		if ( get_post_field( 'post_name', $page_id ) !== $row['slug'] ) {
			return array(
				'id'     => $page_id,
				'code'   => 'slug_changed',
				'detail' => (string) get_post_field( 'post_name', $page_id ),
			);
		}

		if ( ! empty( $row['translations'] ) ) {
			$current = dli_get_post_translations( (int) reset( $row['translations'] ) );
			dli_save_post_translations( array_merge( $current, $row['translations'], array( $language => $page_id ) ) );
		}

		return array(
			'id'     => $page_id,
			'code'   => '',
			'detail' => '',
		);
	}

	/**
	 * The pages that carry an identity, by language: those of this language and one of each other language.
	 *
	 * @param string $identity Identity of the page (its key, or the path of its titles).
	 * @param string $language Slug of the language.
	 * @return array{same: int[], others: array<string, int>} The pages in this language, and the pages in the others by language.
	 */
	private static function find_by_identity( $identity, $language ) {
		$found = array(
			'same'   => array(),
			'others' => array(),
		);

		$ids = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => array_keys( get_post_stati() ),
				'numberposts'      => -1,
				'meta_key'         => self::META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- The identity of an imported page; the import is a one-off administrative action.
				'meta_value'       => $identity, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Same lookup.
				'suppress_filters' => true,
				'fields'           => 'ids',
				'no_found_rows'    => true,
			)
		);
		foreach ( $ids as $page_id ) {
			$page_language = dli_get_post_language( $page_id );
			if ( $page_language === $language ) {
				$found['same'][] = (int) $page_id;
			} elseif ( '' !== $page_language && ! isset( $found['others'][ $page_language ] ) ) {
				$found['others'][ $page_language ] = (int) $page_id;
			}
		}

		return $found;
	}

	/**
	 * The page of the site that has an address under a parent, in any language and status.
	 *
	 * @param string $slug      The last part of the address.
	 * @param int    $parent_id The parent page, 0 for a main page.
	 * @return int ID of the page, 0 when the address is free.
	 */
	private static function page_with_address( $slug, $parent_id ) {
		$found = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => array_keys( get_post_stati() ),
				'name'             => $slug,
				'post_parent'      => $parent_id,
				'numberposts'      => 1,
				'suppress_filters' => true,
				'fields'           => 'ids',
				'no_found_rows'    => true,
			)
		);
		return empty( $found ) ? 0 : (int) $found[0];
	}

	/**
	 * A row of the plan that stops the import.
	 *
	 * @param array<string, mixed> $row    The row.
	 * @param string               $code   Code of the error.
	 * @param string               $detail Detail of the error.
	 * @return array<string, mixed> The row.
	 */
	private static function refuse( array $row, $code, $detail = '' ) {
		$row['action']  = 'error';
		$row['message'] = $code;
		$row['detail']  = $detail;
		return $row;
	}

	/**
	 * A title as it is compared: the spaces around and inside are one, in lower case.
	 *
	 * @param string $title Title.
	 * @return string The title.
	 */
	private static function normalize_title( $title ) {
		$title = trim( (string) preg_replace( '/\s+/u', ' ', $title ) );
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
	}

	/**
	 * An error of the file.
	 *
	 * @param string $code   Code.
	 * @param int    $line   Line of the file.
	 * @param string $detail The column, the key or the title the error is about.
	 * @return array{code: string, line: int, detail: string} The error.
	 */
	private static function parse_error( $code, $line, $detail = '' ) {
		return array(
			'code'   => $code,
			'line'   => $line,
			'detail' => $detail,
		);
	}

	/**
	 * The pool of lorem ipsum: six paragraphs of different length.
	 *
	 * @return string[] The paragraphs.
	 */
	public static function lorem_pool() {
		return array(
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus tincidunt justo a placerat congue. Pellentesque mattis sollicitudin lectus, non tristique lectus convallis a. Aenean vitae libero in neque viverra pulvinar. Proin vestibulum nunc in mollis dapibus. Etiam finibus sem eget nisl ullamcorper, accumsan viverra magna eleifend. Sed bibendum elit libero, eget tincidunt neque sodales nec. Integer sagittis felis vel ipsum imperdiet ultrices. In mauris ante, tempor ornare sodales in, eleifend et magna. Sed ut tincidunt nibh. Vestibulum et mauris molestie, suscipit tortor vel, interdum odio. Cras bibendum lacus porttitor, tempus leo id, consequat dui. Aliquam sit amet leo fringilla, rutrum leo vel, accumsan leo. Sed eros lacus, pulvinar ut dapibus quis, tristique vitae erat. Suspendisse vulputate nisi ut elit ultricies cursus at nec augue. Pellentesque hendrerit, augue tempor scelerisque mattis, sapien massa bibendum mauris, vitae gravida ex diam et purus. Nullam ipsum lacus, laoreet non venenatis non, rutrum at tellus.',
			'Nunc eget elit urna. Duis luctus, ante eu ultrices sollicitudin, ex turpis maximus enim, vitae porttitor erat dolor at risus. Maecenas vel elit turpis. Aenean pretium erat ex, sit amet tempus lectus convallis sed. Aliquam quis est et dolor elementum dignissim. Maecenas nibh nisl, porta eu porta ut, suscipit vel ex. Morbi pretium sit amet ante fringilla consectetur. Duis pretium nisl a nibh ultrices, ut hendrerit sapien elementum. Morbi mauris ante, pulvinar placerat tristique vitae, pulvinar et dui. Fusce convallis, elit sed dignissim mollis, elit libero dapibus ante, a fermentum tellus justo et justo. Donec placerat convallis mattis. In mollis ac erat a sodales. Praesent vestibulum orci risus, et varius leo ornare a.',
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus tincidunt justo a placerat congue. Pellentesque mattis sollicitudin lectus, non tristique lectus convallis a. Aenean vitae libero in neque viverra pulvinar.',
			'Curabitur at justo non mauris luctus tempor. Vivamus ac ligula vel nunc consequat pharetra. Quisque non dolor id arcu feugiat interdum. Donec vitae nulla sit amet lorem aliquam posuere. Praesent eget ipsum a erat pulvinar tincidunt. Nam sed lacus at nisl dictum sollicitudin, vel porta augue ornare.',
			'Sed bibendum elit libero, eget tincidunt neque sodales nec. Integer sagittis felis vel ipsum imperdiet ultrices. In mauris ante, tempor ornare sodales in, eleifend et magna. Sed ut tincidunt nibh. Vestibulum et mauris molestie, suscipit tortor vel, interdum odio. Cras bibendum lacus porttitor, tempus leo id, consequat dui.',
			'Morbi pretium sit amet ante fringilla consectetur. Duis pretium nisl a nibh ultrices, ut hendrerit sapien elementum.',
		);
	}

	/**
	 * The paragraphs of a page, chosen by its key: from one to three, consecutive in the pool, always the same for the
	 * same key (so a page and its translation have the same text) and not the same for all the keys.
	 *
	 * @param string $seed The key of the page (`page_key`), or its path when it has no key.
	 * @return string[] The paragraphs.
	 */
	public static function lorem_paragraphs( $seed ) {
		$pool  = self::lorem_pool();
		$hash  = (int) sprintf( '%u', crc32( (string) $seed ) );
		$count = 1 + ( $hash % 3 );
		$first = intdiv( $hash, 3 ) % count( $pool );

		$paragraphs = array();
		for ( $offset = 0; $offset < $count; $offset++ ) {
			$paragraphs[] = $pool[ ( $first + $offset ) % count( $pool ) ];
		}

		return $paragraphs;
	}

	/**
	 * The content of a page: one paragraph tag for each paragraph.
	 *
	 * @param string $seed The key of the page.
	 * @return string The HTML.
	 */
	public static function lorem_html( $seed ) {
		return '<p>' . implode( '</p><p>', self::lorem_paragraphs( $seed ) ) . '</p>';
	}
}
