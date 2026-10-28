<?php
/**
 * Open Graph data of the current page.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Value object holding the Open Graph data of the current page.
 */
class DLI_OG_Wrapper {
	/**
	 * Current item's post ID.
	 *
	 * @var string
	 */
	public string $id = '';

	/**
	 * Current item's title.
	 *
	 * @var string
	 */
	public string $title = '';

	/**
	 * Title including the laboratory name, for the og:title tag.
	 *
	 * @var string
	 */
	public string $shared_title = '';

	/**
	 * Current item's post type.
	 *
	 * @var string
	 */
	public string $type = '';

	/**
	 * Current item's description/excerpt.
	 *
	 * @var string
	 */
	public string $description = '';

	/**
	 * Current item's permalink.
	 *
	 * @var string
	 */
	public string $url = '';

	/**
	 * Site locale.
	 *
	 * @var string
	 */
	public string $locale = '';

	/**
	 * Laboratory name.
	 *
	 * @var string
	 */
	public string $site_title = '';

	/**
	 * Laboratory tagline.
	 *
	 * @var string
	 */
	public string $site_tagline = '';

	/**
	 * Featured image URL.
	 *
	 * @var string
	 */
	public string $image = '';

	/**
	 * Featured image width in pixels.
	 *
	 * @var string
	 */
	public string $img_width = '';

	/**
	 * Featured image height in pixels.
	 *
	 * @var string
	 */
	public string $img_height = '';

	/**
	 * Featured image MIME type.
	 *
	 * @var string
	 */
	public string $img_type = '';

	/**
	 * Site URL.
	 *
	 * @var string
	 */
	public string $site_url = '';

	/**
	 * Site domain, for the og:site_name context.
	 *
	 * @var string
	 */
	public string $domain = '';
}
