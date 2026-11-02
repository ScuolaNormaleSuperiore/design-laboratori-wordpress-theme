// The homepage section "Indice di una pagina": the page is chosen in the row of Sezioni HP, its subpages that have
// subpages of their own are the columns and those subpages the links. On the baseline «Pagina E2E con sottopagine» has
// the subpages «Sottopagina E2E uno» (with the child «Nipote E2E») and «Sottopagina E2E due» (no children), a protected
// one and a draft: the index has one column, «Sottopagina E2E uno», with one link. The pages have no English
// translation, so on the English site the section does not appear. Each test adds the section to the home and takes it
// away at the end: the site is shared by the whole run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const SECTIONS = '/wp-admin/admin.php?page=homepage_sections';
const ROWS = '#site_sections_repeat > .cmb-repeatable-grouping';
const PAGE_TITLE = 'Pagina E2E con sottopagine';

/**
 * Add the section as a new row of Sezioni HP and save.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {Object}                          options Choices of the row.
 * @param {boolean}                         options.showTitle Whether the title is shown.
 */
async function addSection( page, { showTitle = true } = {} ) {
	await page.goto( SECTIONS );
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const row = page.locator( ROWS ).last();
	await row.locator( 'select[name$="[id]"]' ).selectOption( 'page_index_section' );
	await row.locator( 'select[name$="[page_id]"]' ).selectOption( { label: PAGE_TITLE } );
	await row.locator( 'input[type="radio"][name$="[enabled]"][value="true"]' ).check();
	await row.locator( `input[type="radio"][name$="[show_title]"][value="${ showTitle }"]` ).check();
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

test( 'the field of the page, and the numbers, are there for this section only, and the choice is kept', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( SECTIONS );
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const row = page.locator( ROWS ).last();

	await row.locator( 'select[name$="[id]"]' ).selectOption( 'news_section' );
	await expect( row.locator( 'select[name$="[page_id]"]' ) ).toBeHidden();

	await row.locator( 'select[name$="[id]"]' ).selectOption( 'page_index_section' );
	await expect( row.locator( 'select[name$="[page_id]"]' ) ).toBeVisible();
	await expect( row.locator( 'input[name$="[max_items]"]' ) ).toBeVisible();
	await expect( row.locator( 'input[name$="[max_subitems]"]' ) ).toBeVisible();

	// The choices are the pages of the default language, the subpages indented; the protected one and the draft are not there.
	const labels = await row.locator( 'select[name$="[page_id]"] option' ).allTextContents();
	expect( labels ).toContain( PAGE_TITLE );
	expect( labels.some( ( label ) => /Sottopagina E2E uno/.test( label ) && label.startsWith( '—' ) ) ).toBe( true );
	expect( labels.join( '|' ) ).not.toContain( 'Sottopagina E2E protetta' );
	expect( labels.join( '|' ) ).not.toContain( 'Sottopagina E2E bozza' );

	await row.locator( 'select[name$="[page_id]"]' ).selectOption( { label: PAGE_TITLE } );
	await row.locator( 'input[type="radio"][name$="[enabled]"][value="true"]' ).check();
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();

	try {
		// After saving the choice is still there.
		await page.goto( SECTIONS );
		const saved = page.locator( ROWS ).last();
		await expect( saved.locator( 'select[name$="[page_id]"] option:checked' ) ).toHaveText( PAGE_TITLE );
	} finally {
		await removeLastSection( page );
	}
} );

test( 'the section shows the page as the title and the subpages with children as columns, with the links', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page );

	try {
		await page.goto( '/' );
		const section = page.locator( '#blocco-page-index-3' );
		await expect( section ).toBeVisible();
		await expect( section.getByRole( 'heading', { level: 2, name: PAGE_TITLE } ) ).toBeVisible();
		await expect( section ).not.toHaveClass( /section-muted/ );

		// One column, with the title as text; the subpage without children, the protected one and the draft are not there.
		const columns = section.locator( '.row.d-md-flex > div' );
		await expect( columns ).toHaveCount( 1 );
		await expect( columns.first().locator( 'h3' ) ).toHaveText( 'Sottopagina E2E uno' );
		await expect( columns.first().locator( 'h3 a' ) ).toHaveCount( 0 );
		await expect( columns.first().locator( 'a.list-item .text' ) ).toHaveText( [ 'Nipote E2E' ] );
		await expect( section ).not.toContainText( 'Sottopagina E2E due' );
		await expect( section ).not.toContainText( 'Sottopagina E2E protetta' );
		await expect( section ).not.toContainText( 'Sottopagina E2E bozza' );
		await expect( section.locator( '.accordion' ) ).toBeHidden();

		// The links are not underlined at rest, like in the playground; they are when pointed at or reached with the keyboard.
		const link = columns.first().getByRole( 'link', { name: 'Nipote E2E' } );
		const underline = () => link.locator( '.text' ).evaluate( ( element ) => getComputedStyle( element ).textDecorationLine );
		expect( await underline(), 'at rest the link is not underlined' ).toBe( 'none' );
		await link.hover();
		expect( await underline(), 'pointed at, it is underlined' ).toBe( 'underline' );
		await page.mouse.move( 0, 0 );
		expect( await underline() ).toBe( 'none' );
		await page.keyboard.press( 'Tab' );
		await link.focus();
		await expect( link ).toBeFocused();
		expect( await underline(), 'with the focus, it is underlined' ).toBe( 'underline' );

		// The link leads to its page.
		await link.click();
		await expect( page ).toHaveURL( /\/nipote-e2e\/$/ );

		// The page has no English translation: the section does not appear.
		await page.goto( '/en/' );
		await expect( page.locator( '[id^="blocco-page-index-"]' ) ).toHaveCount( 0 );
	} finally {
		await removeLastSection( page );
	}
} );

test( 'without a visible title the section keeps its h2, and two copies of the same page share no identifier', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page, { showTitle: false } );
	await addSection( page, { showTitle: true } );

	try {
		await page.goto( '/' );
		const first = page.locator( '#blocco-page-index-3' );
		const second = page.locator( '#blocco-page-index-4' );
		await expect( first ).toBeVisible();
		await expect( second ).toBeVisible();

		// The title is there for the assistive technologies, not seen: the h3 of the columns never skip a level.
		const hiddenTitle = first.locator( 'h2.visually-hidden' );
		await expect( hiddenTitle ).toHaveText( PAGE_TITLE );
		await expect( first ).toHaveAttribute( 'aria-labelledby', await hiddenTitle.getAttribute( 'id' ) );
		await expect( second.getByRole( 'heading', { level: 2, name: PAGE_TITLE } ) ).toBeVisible();

		// No identifier is repeated in the whole page: not even for the same page.
		const duplicates = await page.evaluate( () => {
			const seen = new Set();
			const repeated = [];
			document.querySelectorAll( '[id]' ).forEach( ( element ) => {
				if ( seen.has( element.id ) ) {
					repeated.push( element.id );
				}
				seen.add( element.id );
			} );
			return repeated;
		} );
		expect( duplicates ).toEqual( [] );
	} finally {
		await removeLastSection( page );
		await removeLastSection( page );
	}
} );

test( 'on a phone the section is an accordion with the first panel open, and nothing overflows', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page );

	try {
		await page.setViewportSize( { width: 320, height: 800 } );
		await page.goto( '/' );
		const section = page.locator( '#blocco-page-index-3' );
		await expect( section ).toBeVisible();

		await expect( section.locator( '.row.d-md-flex' ) ).toBeHidden();
		const headers = section.locator( '.accordion-header button' );
		await expect( headers ).toHaveCount( 1 );
		await expect( headers.first() ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( section.getByRole( 'link', { name: 'Nipote E2E' } ) ).toBeVisible();

		const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
		expect( overflow ).toBeLessThanOrEqual( 0 );
	} finally {
		await page.setViewportSize( { width: 1280, height: 720 } );
		await removeLastSection( page );
	}
} );
