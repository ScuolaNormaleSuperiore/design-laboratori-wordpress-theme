// The homepage section "Navigazione per pagine": from Sezioni HP to the home page, as columns on a desktop and as an
// accordion on a phone. The promoted pages of the baseline that have published subpages are two groups:
// «Pagina E2E con sottopagine» (links «Sottopagina E2E uno» and «Sottopagina E2E due»; the protected one and the draft
// are left out) and «Sottopagina E2E uno» (link «Nipote E2E»). «Pagina E2E senza sottopagine» has none, so it is no group.
// Title and subtitle are in the option of Layout HP of the baseline. Each test adds the section to the home and takes it
// away at the end: the site is shared by the whole run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const SECTIONS = '/wp-admin/admin.php?page=homepage_sections';
const ROWS = '#site_sections_repeat > .cmb-repeatable-grouping';
const SECTION = '#blocco-page-navigation-3';

/**
 * Add the section as a new row of Sezioni HP and save.
 *
 * @param {import('@playwright/test').Page} page    Page.
 * @param {Object}                          options Choices of the row.
 * @param {string}                          options.maxItems    Groups, or empty for the default.
 * @param {string}                          options.maxSubitems Subpages per group, or empty for the default.
 */
async function addSection( page, { maxItems, maxSubitems } = {} ) {
	await page.goto( SECTIONS );
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const row = page.locator( ROWS ).last();
	await row.locator( 'select[name$="[id]"]' ).selectOption( 'page_navigation_section' );
	await row.locator( 'input[type="radio"][name$="[enabled]"][value="true"]' ).check();
	await row.locator( 'input[type="radio"][name$="[show_title]"][value="true"]' ).check();
	if ( maxItems ) {
		await row.locator( 'input[name$="[max_items]"]' ).fill( maxItems );
	}
	if ( maxSubitems ) {
		await row.locator( 'input[name$="[max_subitems]"]' ).fill( maxSubitems );
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

test( 'the section shows the groups as columns on a desktop, with the title as text and the subpages as links', async ( { page } ) => {
	await loginAsAdmin( page );

	// The fields of the row are there for this section only.
	await page.goto( SECTIONS );
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const newRow = page.locator( ROWS ).last();
	await newRow.locator( 'select[name$="[id]"]' ).selectOption( 'news_section' );
	await expect( newRow.locator( 'input[name$="[max_subitems]"]' ) ).toBeHidden();
	await newRow.locator( 'select[name$="[id]"]' ).selectOption( 'page_navigation_section' );
	await expect( newRow.locator( 'input[name$="[max_items]"]' ) ).toBeVisible();
	await expect( newRow.locator( 'input[name$="[max_subitems]"]' ) ).toBeVisible();
	await expect( newRow.locator( 'input[type="radio"][name$="[background]"]' ).first() ).toBeVisible();
	await newRow.locator( 'input[type="radio"][name$="[enabled]"][value="true"]' ).check();
	await newRow.locator( 'input[type="radio"][name$="[show_title]"][value="true"]' ).check();
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();

	try {
		await page.goto( '/' );
		const section = page.locator( SECTION );
		await expect( section ).toBeVisible();
		await expect( section.getByRole( 'heading', { level: 2, name: 'Le sezioni del sito' } ) ).toBeVisible();
		await expect( section ).toContainText( 'Trova la pagina partendo da ciò che devi fare.' );
		await expect( section ).toHaveClass( /section-muted/ );

		// Two groups, in order; the title of a group is text, not a link.
		const columns = section.locator( '.row.d-md-flex > div' );
		await expect( columns ).toHaveCount( 2 );
		await expect( columns.nth( 0 ).locator( 'h3' ) ).toHaveText( 'Pagina E2E con sottopagine' );
		await expect( columns.nth( 1 ).locator( 'h3' ) ).toHaveText( 'Sottopagina E2E uno' );
		await expect( columns.nth( 0 ).locator( 'h3 a' ) ).toHaveCount( 0 );

		// The links of the first group: the published subpages, in order; not the protected one nor the draft.
		await expect( columns.nth( 0 ).locator( 'a.list-item .text' ) ).toHaveText( [ 'Sottopagina E2E uno', 'Sottopagina E2E due' ] );
		await expect( columns.nth( 1 ).locator( 'a.list-item .text' ) ).toHaveText( [ 'Nipote E2E' ] );
		await expect( section ).not.toContainText( 'Pagina E2E senza sottopagine' );
		await expect( section ).not.toContainText( 'Sottopagina E2E protetta' );
		await expect( section ).not.toContainText( 'Sottopagina E2E bozza' );

		// On a desktop the accordion of the phone is not there to be reached.
		await expect( section.locator( '.accordion' ) ).toBeHidden();

		// A link leads to its page.
		await columns.nth( 0 ).getByRole( 'link', { name: 'Sottopagina E2E due' } ).click();
		await expect( page ).toHaveURL( /\/pagina-e2e-con-sottopagine\/sottopagina-e2e-due\/$/ );

		// In English no page is promoted: the section does not appear.
		await page.goto( '/en/' );
		await expect( page.locator( SECTION ) ).toHaveCount( 0 );
	} finally {
		await removeLastSection( page );
	}
} );

test( 'on a phone the section is an accordion with the first panel open, and nothing overflows', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page );

	try {
		await page.setViewportSize( { width: 320, height: 800 } );
		await page.goto( '/' );
		const section = page.locator( SECTION );
		await expect( section ).toBeVisible();

		// The columns of the desktop are hidden; the accordion is shown.
		await expect( section.locator( '.row.d-md-flex' ) ).toBeHidden();
		const headers = section.locator( '.accordion-header button' );
		await expect( headers ).toHaveCount( 2 );
		await expect( headers.nth( 0 ) ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( headers.nth( 1 ) ).toHaveAttribute( 'aria-expanded', 'false' );

		// The links of the open panel are visible, those of the closed one are not (so they cannot be reached with the keyboard).
		await expect( section.getByRole( 'link', { name: 'Sottopagina E2E uno' } ) ).toBeVisible();
		await expect( section.getByRole( 'link', { name: 'Nipote E2E' } ) ).toBeHidden();

		// Opening the second panel closes the first.
		await headers.nth( 1 ).click();
		await expect( headers.nth( 1 ) ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( section.getByRole( 'link', { name: 'Nipote E2E' } ) ).toBeVisible();
		await expect( headers.nth( 0 ) ).toHaveAttribute( 'aria-expanded', 'false' );

		const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
		expect( overflow ).toBeLessThanOrEqual( 0 );
	} finally {
		await page.setViewportSize( { width: 1280, height: 720 } );
		await removeLastSection( page );
	}
} );

test( 'the numbers chosen in the row limit the groups and the subpages of each group', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page, { maxItems: '1', maxSubitems: '1' } );

	try {
		await page.goto( '/' );
		const section = page.locator( SECTION );
		const columns = section.locator( '.row.d-md-flex > div' );

		await expect( columns ).toHaveCount( 1 );
		await expect( columns.first().locator( 'h3' ) ).toHaveText( 'Pagina E2E con sottopagine' );
		await expect( columns.first().locator( 'a.list-item' ) ).toHaveCount( 1 );
		await expect( columns.first().locator( 'a.list-item .text' ) ).toHaveText( 'Sottopagina E2E uno' );
	} finally {
		await removeLastSection( page );
	}
} );
