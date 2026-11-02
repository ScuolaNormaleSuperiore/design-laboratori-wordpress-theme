// The homepage section "Contenuti in evidenza": the boxes are configured in Configurazione → Layout HP with a content type
// and a title in Italian and English (no template to choose), and the section shows the boxes that have contents,
// sharing the row. The baseline has the publications and no news or events (tests/e2e/README.md, "Contenuti di
// esempio"), so the boxes of the test are publications and events: the first show the publications, the others are
// left out because they have nothing in them. Saving Layout HP writes the defaults of the boxes that were never
// saved, so the test puts the boxes back as they were: the site is shared by the whole run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const LAYOUT = '/wp-admin/admin.php?page=homepage';
const SECTION = '#blocco-card';

/**
 * Open the group of a box (closed by default) by clicking on its title.
 *
 * @param {import('@playwright/test').Page} page   Page.
 * @param {number}                          number Number of the box, from 1.
 */
async function openBox( page, number ) {
	const row = page.locator( `#featured_contents_${ number }_repeat > .cmb-repeatable-grouping` );
	if ( await row.evaluate( ( element ) => element.classList.contains( 'closed' ) ) ) {
		await row.locator( 'h3.cmb-group-title' ).click();
	}
}

/**
 * The input or select of a field of a box.
 *
 * @param {import('@playwright/test').Page} page   Page.
 * @param {number}                          number Number of the box, from 1.
 * @param {string}                          field  `label_box_<n>`, `label_box_<n>_eng` or `type_box_<n>`.
 * @return {import('@playwright/test').Locator} The field.
 */
function boxField( page, number, field ) {
	return page.locator( `[name$="[featured_contents_${ field.replace( '<n>', number ) }]"]` );
}

/**
 * Write the boxes in Layout HP and save.
 *
 * @param {import('@playwright/test').Page} page  Page.
 * @param {Array<Object>}                   boxes Boxes by position: type, title, titleEng.
 */
async function writeBoxes( page, boxes ) {
	await page.goto( LAYOUT );
	for ( let index = 0; index < boxes.length; index++ ) {
		const number = index + 1;
		await openBox( page, number );
		await boxField( page, number, `type_box_${ number }` ).selectOption( boxes[ index ].type );
		await boxField( page, number, `label_box_${ number }` ).fill( boxes[ index ].title );
		await boxField( page, number, `label_box_${ number }_eng` ).fill( boxes[ index ].titleEng );
	}
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#featured_contents_1_repeat' ) ).toBeVisible();
}

const BASELINE_BOXES = [
	{ type: 'pubblicazione', title: 'Pubblicazioni in evidenza', titleEng: '' },
	{ type: 'evento', title: '', titleEng: '' },
	{ type: 'evento', title: '', titleEng: '' },
];

test( 'a box has a content type and a title in both languages, and no template to choose', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( LAYOUT );

	for ( const number of [ 1, 2, 3 ] ) {
		await openBox( page, number );
		await expect( boxField( page, number, `label_box_${ number }` ) ).toBeVisible();
		await expect( boxField( page, number, `label_box_${ number }_eng` ) ).toBeVisible();
		await expect( boxField( page, number, `type_box_${ number }` ) ).toBeVisible();
		await expect( page.locator( `[name$="[featured_contents_template_box_${ number }]"]` ) ).toHaveCount( 0 );
	}

	// The types that can fill a box: news, events and publications are there; the research activities are not.
	const options = await boxField( page, 1, 'type_box_1' ).locator( 'option' ).evaluateAll( ( items ) => items.map( ( item ) => item.value ) );
	expect( options ).toEqual( expect.arrayContaining( [ 'notizia', 'evento', 'pubblicazione' ] ) );
	expect( options ).not.toContain( 'indirizzo-di-ricerca' );
} );

test( 'the boxes with contents share the row, with the title of the language and no empty column', async ( { page } ) => {
	await loginAsAdmin( page );
	await writeBoxes( page, [
		{ type: 'pubblicazione', title: 'Pubblicazioni in evidenza', titleEng: 'Featured publications' },
		{ type: 'pubblicazione', title: 'Seconda colonna', titleEng: '' },
		{ type: 'evento', title: 'Eventi vuoti', titleEng: '' },
	] );

	try {
		await page.goto( '/' );
		const section = page.locator( SECTION );
		await expect( section ).toBeVisible();

		// Two boxes have contents, so each one takes half the row; the third has no event and is not there.
		await expect( section.getByRole( 'heading', { level: 3 } ) ).toHaveText( [ 'Pubblicazioni in evidenza', 'Seconda colonna' ] );
		await expect( section.locator( '.row > div.col-lg-6' ) ).toHaveCount( 2 );
		await expect( section ).not.toContainText( 'Eventi vuoti' );

		// The section keeps a hidden h2, each box has an h3 and each item an h4: no level is skipped.
		await expect( section.getByRole( 'heading', { level: 2 } ) ).toHaveCount( 1 );
		await expect( section.locator( 'h2.visually-hidden' ) ).toHaveCount( 1 );
		await expect( section.getByRole( 'heading', { level: 4 } ).first() ).toContainText( 'Pubblicazione E2E completa' );

		// The button leads to the publications page.
		await expect( section.getByRole( 'link', { name: 'Tutte le pubblicazioni' } ).first() ).toHaveAttribute( 'href', /\/pubblicazioni\/$/ );

		// In English the title of the language is the English one; a box without it keeps the Italian title.
		await page.goto( '/en/' );
		const english = page.locator( SECTION );
		await expect( english.getByRole( 'heading', { level: 3 } ) ).toHaveText( [ 'Featured publications', 'Seconda colonna' ] );
	} finally {
		await writeBoxes( page, BASELINE_BOXES );
	}
} );

test( 'on a phone the boxes are one under the other and nothing overflows', async ( { page } ) => {
	await loginAsAdmin( page );
	await writeBoxes( page, [
		{ type: 'pubblicazione', title: 'Pubblicazioni in evidenza', titleEng: '' },
		{ type: 'pubblicazione', title: 'Seconda colonna', titleEng: '' },
		{ type: 'pubblicazione', title: 'Terza colonna', titleEng: '' },
	] );

	try {
		await page.setViewportSize( { width: 320, height: 800 } );
		await page.goto( '/' );
		const section = page.locator( SECTION );
		await expect( section.getByRole( 'heading', { level: 3 } ) ).toHaveCount( 3 );

		const boxes = await section.locator( '.row > div' ).evaluateAll( ( items ) => items.map( ( item ) => Math.round( item.getBoundingClientRect().left ) ) );
		expect( new Set( boxes ).size, 'every box starts at the same left edge: one column' ).toBe( 1 );

		const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
		expect( overflow ).toBeLessThanOrEqual( 0 );
	} finally {
		await page.setViewportSize( { width: 1280, height: 720 } );
		await writeBoxes( page, BASELINE_BOXES );
	}
} );
