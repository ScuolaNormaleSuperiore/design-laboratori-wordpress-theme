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

test( 'the closed row of an index names the chosen page, and the name follows the choice, a new row, a reordering and a reload', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( SECTIONS );

	// The title of a row is "Sezione N (name of the section)"; for an index the chosen page follows the name.
	const labelOf = ( row ) => row.locator( 'h3.cmb-group-title .dli-section-label' );
	const addRow = async () => {
		await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
		const row = page.locator( ROWS ).last();
		await row.locator( 'select[name$="[id]"]' ).selectOption( 'page_index_section' );
		return row;
	};

	try {
		const first = await addRow();
		const name = ( await first.locator( 'select[name$="[id]"] option:checked' ).textContent() ).trim();

		// Without a page the title is the name of the section only; with a page the name of the page follows.
		await expect( labelOf( first ) ).toHaveText( `(${ name })` );
		await first.locator( 'select[name$="[page_id]"]' ).selectOption( { label: PAGE_TITLE } );
		await expect( labelOf( first ) ).toHaveText( `(${ name } — ${ PAGE_TITLE })` );

		// A subpage is listed with an indent in the choices: the indent is not part of the title.
		const second = await addRow();
		await expect( labelOf( second ) ).toHaveText( `(${ name })` );
		await second.locator( 'select[name$="[page_id]"]' ).selectOption( { label: '— Sottopagina E2E uno' } );
		await expect( labelOf( second ) ).toHaveText( `(${ name } — Sottopagina E2E uno)` );

		// Reordering: the names follow their rows.
		await second.locator( '> .cmb-shift-rows.move-up' ).click();
		const rows = page.locator( ROWS );
		const count = await rows.count();
		await expect( labelOf( rows.nth( count - 2 ) ) ).toHaveText( `(${ name } — Sottopagina E2E uno)` );
		await expect( labelOf( rows.nth( count - 1 ) ) ).toHaveText( `(${ name } — ${ PAGE_TITLE })` );

		// Saved and loaded again, the names are still there.
		await page.locator( 'input[name="submit-cmb"]' ).click();
		await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
		await page.goto( SECTIONS );
		const saved = page.locator( ROWS );
		const savedCount = await saved.count();
		await expect( labelOf( saved.nth( savedCount - 2 ) ) ).toHaveText( `(${ name } — Sottopagina E2E uno)` );
		await expect( labelOf( saved.nth( savedCount - 1 ) ) ).toHaveText( `(${ name } — ${ PAGE_TITLE })` );

		// A section of another type never carries a page, even if the row had one.
		const last = saved.nth( savedCount - 1 );
		// After the page is loaded the rows are closed: the row is opened to change its section.
		if ( await last.evaluate( ( element ) => element.classList.contains( 'closed' ) ) ) {
			await last.locator( 'h3.cmb-group-title' ).click();
		}
		await last.locator( 'select[name$="[id]"]' ).selectOption( 'news_section' );
		const newsName = ( await last.locator( 'select[name$="[id]"] option:checked' ).textContent() ).trim();
		await expect( labelOf( last ) ).toHaveText( `(${ newsName })` );
	} finally {
		// The two rows of the test are the last two: both are taken away in one visit.
		await page.goto( SECTIONS );
		page.on( 'dialog', ( dialog ) => dialog.accept() );
		await page.locator( ROWS ).last().locator( '> .cmb-remove-group-row' ).click();
		await page.locator( ROWS ).last().locator( '> .cmb-remove-group-row' ).click();
		await page.locator( 'input[name="submit-cmb"]' ).click();
		await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
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

/**
 * Add a row for an index at the end of Sezioni HP and fill it, without saving.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {Object} options Choices of the row: enabled, showTitle, background (`white` or `muted`), maxItems, maxSubitems.
 * @return {Promise<import('@playwright/test').Locator>} The row.
 */
async function addFilledRow( page, { enabled, showTitle, background, maxItems, maxSubitems } ) {
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const row = page.locator( ROWS ).last();
	await row.locator( 'select[name$="[id]"]' ).selectOption( 'page_index_section' );
	await row.locator( 'select[name$="[page_id]"]' ).selectOption( { label: PAGE_TITLE } );
	await row.locator( `input[type="radio"][name$="[enabled]"][value="${ enabled }"]` ).check();
	await row.locator( `input[type="radio"][name$="[show_title]"][value="${ showTitle }"]` ).check();
	await row.locator( `input[type="radio"][name$="[background]"][value="${ background }"]` ).check();
	await row.locator( 'input[name$="[max_items]"]' ).fill( String( maxItems ) );
	await row.locator( 'input[name$="[max_subitems]"]' ).fill( String( maxSubitems ) );
	return row;
}

/**
 * What a row holds, for the comparisons; a closed row is opened first.
 *
 * @param {import('@playwright/test').Locator} row Row of the group.
 * @return {Promise<Object>} The values of the row.
 */
async function readRow( row ) {
	if ( await row.evaluate( ( element ) => element.classList.contains( 'closed' ) ) ) {
		await row.locator( 'h3.cmb-group-title' ).click();
	}
	const radio = ( field ) => row.locator( `input[type="radio"][name$="[${ field }]"]:checked` ).evaluateAll( ( radios ) => ( radios.length ? radios[ 0 ].value : null ) );
	return {
		enabled: await radio( 'enabled' ),
		showTitle: await radio( 'show_title' ),
		background: await radio( 'background' ),
		maxItems: await row.locator( 'input[name$="[max_items]"]' ).inputValue(),
		maxSubitems: await row.locator( 'input[name$="[max_subitems]"]' ).inputValue(),
	};
}

/**
 * Take the last rows away from Sezioni HP in one visit and save.
 *
 * @param {import('@playwright/test').Page} page  Page.
 * @param {number}                          count How many rows.
 */
async function removeLastSections( page, count ) {
	await page.goto( SECTIONS );
	const accept = ( dialog ) => dialog.accept();
	page.on( 'dialog', accept );
	for ( let removed = 0; removed < count; removed++ ) {
		await page.locator( ROWS ).last().locator( '> .cmb-remove-group-row' ).click();
	}
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
	page.off( 'dialog', accept );
}

const A = { enabled: 'true', showTitle: 'true', background: 'muted', maxItems: '3', maxSubitems: '4' };
const B = { enabled: 'true', showTitle: 'false', background: 'white', maxItems: '5', maxSubitems: '2' };

test( 'two indexes with opposite choices keep each its own choices, saved and loaded again, and are shown as chosen', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( SECTIONS );

	try {
		await addFilledRow( page, A );
		await addFilledRow( page, B );
		await page.locator( 'input[name="submit-cmb"]' ).click();
		await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();

		await page.goto( SECTIONS );
		const rows = page.locator( ROWS );
		const count = await rows.count();
		expect( await readRow( rows.nth( count - 2 ) ) ).toEqual( A );
		expect( await readRow( rows.nth( count - 1 ) ) ).toEqual( B );

		// In the home each section is as its row says: the first grey with the title seen, the second white without.
		await page.goto( '/' );
		const first = page.locator( `#blocco-page-index-${ count - 1 }` );
		const second = page.locator( `#blocco-page-index-${ count }` );
		await expect( first ).toHaveClass( /section-muted/ );
		await expect( first.getByRole( 'heading', { level: 2, name: PAGE_TITLE } ) ).toBeVisible();
		await expect( second ).not.toHaveClass( /section-muted/ );
		await expect( second.locator( 'h2.visually-hidden' ) ).toHaveText( PAGE_TITLE );
	} finally {
		await removeLastSections( page, 2 );
	}
} );

test( 'two reorderings before saving, with rows just added, and two savings: no value changes row', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( SECTIONS );

	try {
		await addFilledRow( page, A );
		const second = await addFilledRow( page, B );
		const rows = page.locator( ROWS );
		const count = await rows.count();

		// Three moves in a row, without saving: up, down, up; the second row ends before the first.
		await second.locator( '> .cmb-shift-rows.move-up' ).click();
		await rows.nth( count - 2 ).locator( '> .cmb-shift-rows.move-down' ).click();
		await rows.nth( count - 1 ).locator( '> .cmb-shift-rows.move-up' ).click();
		expect( await readRow( rows.nth( count - 2 ) ) ).toEqual( B );
		expect( await readRow( rows.nth( count - 1 ) ) ).toEqual( A );

		// Saved twice, loaded each time: the order and the values stay.
		for ( let time = 1; time <= 2; time++ ) {
			await page.locator( 'input[name="submit-cmb"]' ).click();
			await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
			await page.goto( SECTIONS );
			expect( await readRow( rows.nth( count - 2 ) ), `saving ${ time }, the first row` ).toEqual( B );
			expect( await readRow( rows.nth( count - 1 ) ), `saving ${ time }, the second row` ).toEqual( A );
		}
	} finally {
		await removeLastSections( page, 2 );
	}
} );

test( 'turning off one copy and taking it away leaves the other with its data and its section', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( SECTIONS );
	let removed = false;

	try {
		await addFilledRow( page, { ...A, enabled: 'false' } );
		await addFilledRow( page, B );
		await page.locator( 'input[name="submit-cmb"]' ).click();
		await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();

		await page.goto( SECTIONS );
		const count = await page.locator( ROWS ).count();
		await page.goto( '/' );
		await expect( page.locator( `#blocco-page-index-${ count - 1 }` ) ).toHaveCount( 0 );
		await expect( page.locator( `#blocco-page-index-${ count }` ) ).toBeVisible();

		// The copy that is off is taken away: the other becomes the last row, keeping its choices and its section.
		await page.goto( SECTIONS );
		page.once( 'dialog', ( dialog ) => dialog.accept() );
		await page.locator( ROWS ).nth( count - 2 ).locator( '> .cmb-remove-group-row' ).click();
		await page.locator( 'input[name="submit-cmb"]' ).click();
		await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
		removed = true;

		await page.goto( SECTIONS );
		await expect( page.locator( ROWS ) ).toHaveCount( count - 1 );
		expect( await readRow( page.locator( ROWS ).last() ) ).toEqual( B );
		await page.goto( '/' );
		await expect( page.locator( `#blocco-page-index-${ count - 1 } h2.visually-hidden` ) ).toHaveText( PAGE_TITLE );
	} finally {
		await removeLastSections( page, removed ? 1 : 2 );
	}
} );

test( 'on a phone the panel of the accordion is closed and opened again with the keyboard', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page );

	try {
		await page.setViewportSize( { width: 320, height: 800 } );
		await page.goto( '/' );
		const section = page.locator( '#blocco-page-index-3' );
		const button = section.locator( '.accordion-header button' ).first();
		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );

		// The button has a name and takes the focus; Enter closes the panel and Space opens it again.
		await expect( button ).toHaveAccessibleName( 'Sottopagina E2E uno' );
		await button.focus();
		await expect( button ).toBeFocused();
		await page.keyboard.press( 'Enter' );
		await expect( button ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( section.getByRole( 'link', { name: 'Nipote E2E' } ) ).toBeHidden();
		await page.keyboard.press( 'Space' );
		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( section.getByRole( 'link', { name: 'Nipote E2E' } ) ).toBeVisible();
	} finally {
		await page.setViewportSize( { width: 1280, height: 720 } );
		await removeLastSection( page );
	}
} );
