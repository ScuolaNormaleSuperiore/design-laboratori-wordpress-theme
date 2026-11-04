<?php
/**
 * Banners of the homepage section "Banner".
 *
 * Each row of Sezioni HP chooses how its banners are picked: automatic (the published banners by priority, the way
 * it has always been) or manual (a list chosen by the editor, in order). It also has its own number of banners.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_home_banners' ) ) {
	/**
	 * The banners that a row of the section Banner shows, in the order they are shown.
	 *
	 * Automatic: the published banners of the current language by the field `priorita`, the lowest first, as many as
	 * the number of the row (`DLI_HP_BANNERS_DEFAULT_ITEMS` when the row does not say). Manual: the banners that the
	 * editor chose, in the order chosen, at most `DLI_HP_ITEMS_MAX`; the number of the row does not count. The choices are the banners of the default language, so each one is replaced by its translation in the current
	 * language and a banner that is not published, is not a banner, does not exist or has no translation is left out.
	 * A manual row with nothing chosen shows nothing: it is not filled with the automatic ones.
	 *
	 * The media of the banners are read with one query for all of them, not one for each.
	 *
	 * @param array<string, mixed> $row The row of Sezioni HP: `banner_mode`, `banner_ids` and `max_items`.
	 * @return WP_Post[] The banners.
	 */
	function dli_get_home_banners( array $row ) {
		$manual = 'manual' === ( $row['banner_mode'] ?? '' );
		$limit  = $manual ? DLI_HP_ITEMS_MAX : dli_normalize_hp_max_items( $row['max_items'] ?? null, DLI_HP_BANNERS_DEFAULT_ITEMS );

		$query_args = array(
			'post_type'              => array( BANNER_POST_TYPE ),
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		);

		if ( $manual ) {
			$ids = array();
			foreach ( dli_sanitize_hp_id_list( $row['banner_ids'] ?? array() ) as $chosen_id ) {
				$banner_id = dli_get_banner_id_in_current_language( $chosen_id );
				if ( $banner_id && ! in_array( $banner_id, $ids, true ) ) {
					$ids[] = $banner_id;
				}
			}
			if ( empty( $ids ) ) {
				return array();
			}
			// The IDs are already of the current language: the filter of Polylang on the query is not needed.
			$query_args['post__in'] = $ids;
			$query_args['orderby']  = 'post__in';
			$query_args['lang']     = '';
		} else {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'priorita'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Few banners, ordered by their priority as it has always been.
			$query_args['order']    = 'ASC';
		}

		$banners = ( new WP_Query( $query_args ) )->posts;
		if ( empty( $banners ) ) {
			return array();
		}

		// One query for the images of all the banners.
		$thumb_ids = array_filter( array_map( 'get_post_thumbnail_id', wp_list_pluck( $banners, 'ID' ) ) );
		if ( ! empty( $thumb_ids ) ) {
			$thumb_ids = array_map( 'absint', array_unique( $thumb_ids ) );
			update_meta_cache( 'post', $thumb_ids );
			get_posts(
				array(
					'post_type'              => 'attachment',
					'post_status'            => 'inherit',
					'post__in'               => $thumb_ids,
					'posts_per_page'         => count( $thumb_ids ),
					'orderby'                => 'post__in',
					'no_found_rows'          => true,
					'update_post_meta_cache' => true,
					'update_post_term_cache' => false,
				)
			);
		}

		return $banners;
	}
}

if ( ! function_exists( 'dli_get_banner_id_in_current_language' ) ) {
	/**
	 * The ID of a banner in the current language.
	 *
	 * A banner with translations gives the one of the current language, or nothing when there is none; a banner
	 * without translations is its own. Whether it is published is checked by the query that reads it.
	 *
	 * @param int $banner_id The ID chosen by the editor.
	 * @return int The ID to show, or 0.
	 */
	function dli_get_banner_id_in_current_language( $banner_id ) {
		$banner_id    = (int) $banner_id;
		$translations = dli_get_post_translations( $banner_id );
		if ( empty( $translations ) ) {
			return $banner_id;
		}

		$current = dli_current_language();
		return empty( $translations[ $current ] ) ? 0 : (int) $translations[ $current ];
	}
}

if ( ! function_exists( 'dli_get_banners_for_select' ) ) {
	/**
	 * The choices of the field "Banner scelti" of Sezioni HP.
	 *
	 * The published banners of the default language, by title, plus the banners that a row already has and that can no
	 * longer be shown (a draft, a trashed banner), marked "(non visibile)": a choice field drops from the saved value
	 * what it does not list, so without them saving the form would lose the choice for good.
	 *
	 * @return array<int, string> Banner ID => label.
	 */
	function dli_get_banners_for_select() {
		$options = array();
		foreach ( get_posts(
			array(
				'post_type'      => BANNER_POST_TYPE,
				'post_status'    => 'publish',
				'lang'           => (string) dli_default_language( 'slug' ),
				'orderby'        => 'title',
				'order'          => 'ASC',
				'posts_per_page' => -1,
			)
		) as $banner ) {
			$options[ $banner->ID ] = get_the_title( $banner );
		}

		$chosen = array();
		foreach ( DLI_ContentsManager::get_hp_section_options() as $row ) {
			if ( 'banners_section' === ( $row['id'] ?? '' ) ) {
				$chosen = array_merge( $chosen, dli_sanitize_hp_id_list( $row['banner_ids'] ?? array() ) );
			}
		}
		$missing = array_diff( array_unique( $chosen ), array_keys( $options ) );
		if ( empty( $missing ) ) {
			return $options;
		}

		foreach ( get_posts(
			array(
				'post_type'      => BANNER_POST_TYPE,
				'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private', 'trash' ),
				'lang'           => '',
				'post__in'       => $missing,
				'orderby'        => 'post__in',
				'posts_per_page' => -1,
			)
		) as $banner ) {
			$options[ $banner->ID ] = 'publish' === $banner->post_status ? get_the_title( $banner ) : sprintf(
				/* translators: %s: the title of a banner that is a draft, private or in the trash */
				__( '%s (non visibile)', 'design_laboratori_italia' ),
				get_the_title( $banner )
			);
		}

		return $options;
	}
}
