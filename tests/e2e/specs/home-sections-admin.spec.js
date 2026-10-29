// Configurazione → Sezioni HP: the arrows to reorder the sections are in the header of each row, also when the
// row is closed, and swapping two rows keeps the values of their radio buttons. The two sections of the
// baseline are "Contenuti in evidenza" and "Pubblicazioni". The site goes back to the baseline after the run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin, loginAsEditor } = require( '../support/auth' );

const PAGE = '/wp-admin/admin.php?page=homepage_sections';
const ROWS = '#site_sections_repeat > .cmb-repeatable-grouping';
const TITLE = 'h3.cmb-group-title';

// The tests share the site until the end of the run: the one that saves goes after those that only read or change the page.

/**
 * Open a row (closed by default) by clicking on its title.
 *
 * @param {import('@playwright/test').Locator} row Row of the group.
 */
async function openRow( row ) {
	if ( await row.evaluate( ( element ) => element.classList.contains( 'closed' ) ) ) {
		await row.locator( TITLE ).click();
	}
}

/**
 * Choose Sì (true) or No (false) in a radio field of a row.
 *
 * @param {import('@playwright/test').Locator} row   Row of the group.
 * @param {string}                             field `enabled` or `show_title`.
 * @param {boolean}                            value The value to choose.
 */
async function choose( row, field, value ) {
	await row.locator( `input[type="radio"][name$="[${ field }]"][value="${ value }"]` ).check();
}

/**
 * Read the value chosen in a radio field of a row.
 *
 * @param {import('@playwright/test').Locator} row   Row of the group.
 * @param {string}                             field `enabled` or `show_title`.
 * @return {Promise<string|null>} `true`, `false` or null when nothing is chosen.
 */
async function chosen( row, field ) {
	return row.locator( `input[type="radio"][name$="[${ field }]"]:checked` ).evaluateAll( ( radios ) => ( radios.length ? radios[ 0 ].value : null ) );
}

/**
 * The values of the two radio fields of a row.
 *
 * @param {import('@playwright/test').Locator} row Row of the group.
 * @return {Promise<Array<string|null>>} enabled and show_title.
 */
async function values( row ) {
	return [ await chosen( row, 'enabled' ), await chosen( row, 'show_title' ) ];
}

async function save( page ) {
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
}

test( 'the rows show the name of the section and the arrows in the header, with the row closed', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( PAGE );

	const rows = page.locator( ROWS );
	await expect( rows ).toHaveCount( 2 );
	await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Sezione 1 (Contenuti in evidenza)' );
	await expect( rows.nth( 1 ).locator( TITLE ) ).toContainText( 'Sezione 2 (Pubblicazioni)' );

	for ( let index = 0; index < 2; index++ ) {
		const row = rows.nth( index );
		await expect( row ).toHaveClass( /closed/ );
		await expect( row.locator( '> .cmb-shift-rows.move-up' ) ).toBeVisible();
		await expect( row.locator( '> .cmb-shift-rows.move-down' ) ).toBeVisible();
		await expect( row.locator( '> .cmb-remove-group-row' ) ).toBeVisible();
	}

	// The arrows are links with a name that a screen reader can say (in the language of the admin).
	await expect( rows.nth( 0 ).getByRole( 'link', { name: /Sposta la sezione in alto|Move the section up/ } ) ).toBeVisible();
	await expect( rows.nth( 0 ).getByRole( 'link', { name: /Sposta la sezione in basso|Move the section down/ } ) ).toBeVisible();

	// Arrows on the left, then the "x": no overlap, so a click on an arrow cannot remove the row.
	const up = await rows.nth( 0 ).locator( '> .cmb-shift-rows.move-up' ).boundingBox();
	const down = await rows.nth( 0 ).locator( '> .cmb-shift-rows.move-down' ).boundingBox();
	const remove = await rows.nth( 0 ).locator( '> .cmb-remove-group-row' ).boundingBox();
	expect( down.x ).toBeGreaterThanOrEqual( up.x + up.width );
	expect( remove.x ).toBeGreaterThanOrEqual( down.x + down.width );

	// The border of the arrows stays inside the bar of the row: they do not stick out above or below it.
	const bar = await rows.nth( 0 ).locator( TITLE ).boundingBox();
	for ( const arrow of [ up, down ] ) {
		expect( arrow.y ).toBeGreaterThanOrEqual( bar.y );
		expect( arrow.y + arrow.height ).toBeLessThanOrEqual( bar.y + bar.height );
	}
} );

test( 'a new row has its arrows in the header and takes part in the reordering', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( PAGE );
	const rows = page.locator( ROWS );

	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();

	await expect( rows ).toHaveCount( 3 );
	await expect( rows.nth( 2 ).locator( '> .cmb-shift-rows.move-up' ) ).toBeVisible();
	await expect( rows.nth( 2 ).locator( '> .cmb-shift-rows.move-down' ) ).toBeVisible();

	// Choose the section of the new row and see its name in the title.
	await openRow( rows.nth( 2 ) );
	await rows.nth( 2 ).locator( 'select[name$="[id]"]' ).selectOption( 'news_section' );
	await expect( rows.nth( 2 ).locator( TITLE ) ).toContainText( 'Sezione 3 (Notizie)' );

	// It goes up and the titles are renumbered.
	await rows.nth( 2 ).locator( TITLE ).click();
	await rows.nth( 2 ).locator( '> .cmb-shift-rows.move-up' ).click();
	await expect( rows.nth( 1 ).locator( TITLE ) ).toContainText( 'Sezione 2 (Notizie)' );
	await expect( rows.nth( 2 ).locator( TITLE ) ).toContainText( 'Sezione 3 (Pubblicazioni)' );
} );

test( 'swapping two rows with opposite values keeps every value, also after saving and reloading', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( PAGE );
	const rows = page.locator( ROWS );

	// Opposite values: the first row off with no title, the second row on with the title.
	await openRow( rows.nth( 0 ) );
	await choose( rows.nth( 0 ), 'enabled', false );
	await choose( rows.nth( 0 ), 'show_title', false );
	await openRow( rows.nth( 1 ) );
	await choose( rows.nth( 1 ), 'enabled', true );
	await choose( rows.nth( 1 ), 'show_title', true );
	await save( page );

	await page.goto( PAGE );
	await openRow( rows.nth( 0 ) );
	await openRow( rows.nth( 1 ) );
	expect( await values( rows.nth( 0 ) ) ).toEqual( [ 'false', 'false' ] );
	expect( await values( rows.nth( 1 ) ) ).toEqual( [ 'true', 'true' ] );

	// Swap with the arrow of the row closed again: the second goes up.
	await rows.nth( 1 ).locator( TITLE ).click();
	await rows.nth( 1 ).locator( '> .cmb-shift-rows.move-up' ).click();

	// Numbers and names follow the new order, whatever the language of the admin.
	await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Sezione 1 (Pubblicazioni)' );
	await expect( rows.nth( 1 ).locator( TITLE ) ).toContainText( 'Sezione 2 (Contenuti in evidenza)' );
	await openRow( rows.nth( 0 ) );
	await openRow( rows.nth( 1 ) );
	expect( await values( rows.nth( 0 ) ) ).toEqual( [ 'true', 'true' ] );
	expect( await values( rows.nth( 1 ) ) ).toEqual( [ 'false', 'false' ] );

	// Save and reload: nothing was lost on the way.
	await save( page );
	await page.goto( PAGE );
	await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Sezione 1 (Pubblicazioni)' );
	await openRow( rows.nth( 0 ) );
	await openRow( rows.nth( 1 ) );
	expect( await values( rows.nth( 0 ) ) ).toEqual( [ 'true', 'true' ] );
	expect( await values( rows.nth( 1 ) ) ).toEqual( [ 'false', 'false' ] );

	// And swap them back with the arrow down of the first row.
	await rows.nth( 0 ).locator( TITLE ).click();
	await rows.nth( 0 ).locator( '> .cmb-shift-rows.move-down' ).click();
	await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Sezione 1 (Contenuti in evidenza)' );
} );

test( 'a user who cannot edit the site configuration does not reach the page', async ( { page } ) => {
	await loginAsEditor( page );

	const response = await page.goto( PAGE );

	expect( response.status() ).toBe( 403 );
} );
