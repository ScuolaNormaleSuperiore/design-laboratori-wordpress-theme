// The homepage section "Card di navigazione": from the configuration (Layout HP, Sezioni HP) to the home page.
// The baseline has three promoted pages (in the order: «Pagina E2E con sottopagine», «Pagina E2E senza
// sottopagine», «Sottopagina E2E uno»), a promoted page protected by a password and a promoted draft. By default
// the section lists the first level only, so «Sottopagina E2E uno» (a subpage) is not a card; "Tutti i livelli"
// in the row brings it back.
// Title and subtitle (Italian and English) are already in the option of Layout HP. The test adds the section to
// the home and takes it away at the end: the site is shared by the whole run. Layout HP is not saved here,
// because saving that page also saves its other fields (the boxes of the featured contents).
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const SECTIONS = '/wp-admin/admin.php?page=homepage_sections';
const LAYOUT = '/wp-admin/admin.php?page=homepage';
const ROWS = '#site_sections_repeat > .cmb-repeatable-grouping';
const TITLE = 'h3.cmb-group-title';

/**
 * Add the section as a new row of Sezioni HP and save.
 *
 * @param {import('@playwright/test').Page} page       Page.
 * @param {Object}                          options    Radio choices.
 * @param {boolean}                         options.showTitle Whether to show the title.
 * @param {string}                          options.background `white` or `muted`, or empty for the default.
 * @param {string}                          options.maxItems   Number of items, or empty for the default.
 * @param {string}                          options.maxLevel   Level of the pages (`1`, `2`, `3`, `0`), or empty for the default.
 */
async function addSection( page, { showTitle, background, maxItems, maxLevel } ) {
	await page.goto( SECTIONS );
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const row = page.locator( ROWS ).last();
	await row.locator( 'select[name$="[id]"]' ).selectOption( 'card_navigation_section' );
	await row.locator( `input[type="radio"][name$="[enabled]"][value="true"]` ).check();
	await row.locator( `input[type="radio"][name$="[show_title]"][value="${ showTitle }"]` ).check();
	if ( background ) {
		await row.locator( `input[type="radio"][name$="[background]"][value="${ background }"]` ).check();
	}
	if ( maxItems ) {
		await row.locator( 'input[name$="[max_items]"]' ).fill( maxItems );
	}
	if ( maxLevel ) {
		await row.locator( 'select[name$="[max_level]"]' ).selectOption( maxLevel );
	}
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
}

/**
 * Take the last row away from Sezioni HP and save.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function removeLastSection( page ) {
	await page.goto( SECTIONS );
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.locator( ROWS ).last().locator( '> .cmb-remove-group-row' ).click();
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
}

test( 'the section goes from the configuration to the home page, with the pages in order', async ( { page } ) => {
	await loginAsAdmin( page );

	// Layout HP has the fields of the section, in Italian and in English, with the values of the baseline.
	await page.goto( LAYOUT );
	await expect( page.locator( 'input[name="home_card_navigation_title"]' ) ).toHaveValue( 'Cosa puoi fare' );
	await expect( page.locator( 'textarea[name="home_card_navigation_text"]' ) ).toHaveValue( 'I servizi più richiesti.' );
	await expect( page.locator( 'input[name="home_card_navigation_title_eng"]' ) ).toHaveValue( 'What you can do' );
	await expect( page.locator( 'textarea[name="home_card_navigation_text_eng"]' ) ).toHaveValue( '' );

	// Sezioni HP: the background field is there for this section, and the title is shown.
	await page.goto( SECTIONS );
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const newRow = page.locator( ROWS ).last();
	await newRow.locator( 'select[name$="[id]"]' ).selectOption( 'news_section' );
	await expect( newRow.locator( 'input[type="radio"][name$="[background]"]' ).first() ).toBeHidden();
	await expect( newRow.locator( 'input[name$="[max_items]"]' ) ).toBeHidden();
	await expect( newRow.locator( 'select[name$="[max_level]"]' ) ).toBeHidden();
	await newRow.locator( 'select[name$="[id]"]' ).selectOption( 'card_navigation_section' );
	await expect( newRow.locator( 'input[type="radio"][name$="[background]"]' ).first() ).toBeVisible();
	await expect( newRow.locator( 'input[name$="[max_items]"]' ) ).toBeVisible();
	// The level of the pages: four choices, the first level by default.
	const level = newRow.locator( 'select[name$="[max_level]"]' );
	await expect( level ).toBeVisible();
	await expect( level ).toHaveValue( '1' );
	await expect( level.locator( 'option' ) ).toHaveCount( 4 );
	await newRow.locator( 'input[type="radio"][name$="[enabled]"][value="true"]' ).check();
	await newRow.locator( 'input[type="radio"][name$="[show_title]"][value="true"]' ).check();
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();

	try {
		// The home page: title, subtitle, the pages of the first level in order, not the subpage, the protected one nor the draft.
		await page.goto( '/' );
		const section = page.locator( '#blocco-card-navigation-3' );
		await expect( section ).toBeVisible();
		await expect( section.getByRole( 'heading', { level: 2, name: 'Cosa puoi fare' } ) ).toBeVisible();
		await expect( section ).toContainText( 'I servizi più richiesti.' );
		await expect( section ).toHaveClass( /section-muted/ );

		const titles = section.locator( 'article.card-nav h3' );
		await expect( titles ).toHaveText( [ 'Pagina E2E con sottopagine', 'Pagina E2E senza sottopagine' ] );
		await expect( section ).not.toContainText( 'Pagina E2E promossa protetta' );
		await expect( section ).not.toContainText( 'Pagina E2E promossa bozza' );
		await expect( section ).not.toContainText( 'protected post' );
		await expect( section ).toContainText( 'Una pagina promossa senza sottopagine.' );

		// A card leads to its page, with a link that says where.
		await section.getByRole( 'link', { name: /Pagina E2E senza sottopagine/ } ).click();
		await expect( page ).toHaveURL( /\/pagina-e2e-senza-sottopagine\/$/ );

		// In English no page is promoted: the section does not appear, not even empty.
		await page.goto( '/en/' );
		await expect( page.locator( '#blocco-card-navigation-3' ) ).toHaveCount( 0 );
	} finally {
		await removeLastSection( page );
	}
} );

test( 'the section with a limit of two cards on a narrow screen: one card per row, no horizontal overflow', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page, { showTitle: 'false', background: 'white', maxItems: '2' } );

	try {
		await page.setViewportSize( { width: 320, height: 800 } );
		await page.goto( '/' );
		const section = page.locator( '#blocco-card-navigation-3' );
		await expect( section ).toBeVisible();
		await expect( section ).not.toHaveClass( /section-muted/ );
		await expect( section.locator( 'h2' ) ).toHaveCount( 0 );
		await expect( section ).toHaveAttribute( 'aria-label', /.+/ );
		await expect( section.locator( 'article.card-nav' ) ).toHaveCount( 2 );

		const boxes = await section.locator( 'li' ).evaluateAll( ( items ) => items.map( ( item ) => item.getBoundingClientRect().left ) );
		expect( new Set( boxes.map( Math.round ) ).size ).toBe( 1 );

		const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
		expect( overflow ).toBeLessThanOrEqual( 0 );
	} finally {
		await page.setViewportSize( { width: 1280, height: 720 } );
		await removeLastSection( page );
	}
} );

test( 'the level of the pages chosen in the row brings the promoted subpages back as cards', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page, { showTitle: 'true', background: '', maxItems: '', maxLevel: '0' } );

	try {
		await page.goto( '/' );
		const section = page.locator( '#blocco-card-navigation-3' );
		await expect( section ).toBeVisible();

		// All the levels: the promoted subpage is a card too, in the order of the field Ordine.
		const titles = section.locator( 'article.card-nav h3' );
		await expect( titles ).toHaveText( [ 'Pagina E2E con sottopagine', 'Pagina E2E senza sottopagine', 'Sottopagina E2E uno' ] );
	} finally {
		await removeLastSection( page );
	}
} );
