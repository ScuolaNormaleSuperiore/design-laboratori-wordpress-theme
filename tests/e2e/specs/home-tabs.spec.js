// The homepage section "Pannello tab": the tabs are written in Configurazione → Layout HP (group `home_tabs_items`, with
// a rich text description in Italian and English) and shown by the section added in Sezioni HP. The first test is about the
// admin: reordering rows must not move a description to another tab, also after saving twice. The others follow the
// section on the home page, with the keyboard, in English and on a phone. Each test leaves the site as it found it:
// the site is shared by the whole run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const LAYOUT = '/wp-admin/admin.php?page=homepage';
const SECTIONS = '/wp-admin/admin.php?page=homepage_sections';
const GROUP = '#home_tabs_items_repeat';
const ROWS = `${ GROUP } > .cmb-repeatable-grouping`;
const SECTION_ROWS = '#site_sections_repeat > .cmb-repeatable-grouping';
const TITLE = 'h3.cmb-group-title';
const SECTION = '#blocco-tabs-3';

/**
 * Open a row (closed by default) by clicking on its title.
 *
 * @param {import('@playwright/test').Locator} row Row of a group.
 */
async function openRow( row ) {
	if ( await row.evaluate( ( element ) => element.classList.contains( 'closed' ) ) ) {
		await row.locator( TITLE ).click();
	}
}

/**
 * Save the options page and wait for it to come back.
 *
 * @param {import('@playwright/test').Page} page  Page.
 * @param {string}                          group Selector of a group of the page, shown again after the save.
 */
async function save( page, group = GROUP ) {
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( group ) ).toBeVisible();
}

/**
 * The visible text of the editor of a description, read from the iframe of TinyMCE. The editor is built again when the
 * rows are swapped, so the id of its textarea is read every time.
 *
 * @param {import('@playwright/test').Page}    page  Page.
 * @param {import('@playwright/test').Locator} row   Row of the group.
 * @param {string}                             field `descrizione` or `descrizione_eng`.
 * @return {Promise<string|null>} The text, or null while the editor is not ready.
 */
async function readDescription( page, row, field = 'descrizione' ) {
	try {
		const id = await row.locator( `textarea[name$="[${ field }]"]` ).getAttribute( 'id', { timeout: 1000 } );
		return ( await page.frameLocator( `#${ id }_ifr` ).locator( 'body' ).innerText( { timeout: 1000 } ) ).trim();
	} catch ( error ) {
		return null;
	}
}

/**
 * Write the description of a row in its editor.
 *
 * @param {import('@playwright/test').Page}    page  Page.
 * @param {import('@playwright/test').Locator} row   Row of the group, open.
 * @param {string}                             text  Text.
 * @param {string}                             field `descrizione` or `descrizione_eng`.
 */
async function typeDescription( page, row, text, field = 'descrizione' ) {
	const id = await row.locator( `textarea[name$="[${ field }]"]` ).getAttribute( 'id' );
	await page.frameLocator( `#${ id }_ifr` ).locator( 'body' ).fill( text );
}

/**
 * Check that the rows hold these labels and descriptions, in this order.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {Array<[string, string]>}         pairs Label and description of each row.
 */
async function expectTabs( page, pairs ) {
	const rows = page.locator( ROWS );
	await expect( rows ).toHaveCount( pairs.length );
	for ( let index = 0; index < pairs.length; index++ ) {
		const row = rows.nth( index );
		await openRow( row );
		await expect( row.locator( 'input[name$="[etichetta]"]' ) ).toHaveValue( pairs[ index ][ 0 ] );
		await expect.poll( () => readDescription( page, row ), { message: `description of the row ${ index + 1 }` } ).toBe( pairs[ index ][ 1 ] );
	}
}

/**
 * Add the tabs in Layout HP and save. The group shows an empty row when nothing is saved: it is the first one.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {Array<Object>}                   tabs Tabs: label, labelEng, text, textEng.
 * @param {boolean}                         andSave Whether to save; without it the rows stay as they are in the page.
 */
async function writeTabs( page, tabs, andSave = true ) {
	await page.goto( LAYOUT );
	const rows = page.locator( ROWS );
	while ( ( await rows.count() ) < tabs.length ) {
		await page.locator( `${ GROUP } .cmb-add-group-row` ).click();
	}
	for ( let index = 0; index < tabs.length; index++ ) {
		const row = rows.nth( index );
		await openRow( row );
		await row.locator( 'input[name$="[etichetta]"]' ).fill( tabs[ index ].label );
		if ( tabs[ index ].labelEng ) {
			await row.locator( 'input[name$="[etichetta_eng]"]' ).fill( tabs[ index ].labelEng );
		}
		await typeDescription( page, row, tabs[ index ].text );
		if ( tabs[ index ].textEng ) {
			await typeDescription( page, row, tabs[ index ].textEng, 'descrizione_eng' );
		}
	}
	if ( andSave ) {
		await save( page );
	}
}

/**
 * Take every tab away from Layout HP and save. CMB2 empties the last row instead of deleting it.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function clearTabs( page ) {
	await page.goto( LAYOUT );
	const accept = ( dialog ) => dialog.accept();
	page.on( 'dialog', accept );
	try {
		const rows = page.locator( ROWS );
		while ( ( await rows.count() ) > 1 ) {
			await rows.last().locator( '> .cmb-remove-group-row' ).click();
		}
		await rows.first().locator( '> .cmb-remove-group-row' ).click();
		await page.locator( 'input[name="home_tabs_title"]' ).fill( '' );
		await save( page );
	} finally {
		page.off( 'dialog', accept );
	}
}

/**
 * Add the section as a new row of Sezioni HP and save.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function addSection( page ) {
	await page.goto( SECTIONS );
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const row = page.locator( SECTION_ROWS ).last();
	await row.locator( 'select[name$="[id]"]' ).selectOption( 'tabs_section' );
	await row.locator( 'input[type="radio"][name$="[enabled]"][value="true"]' ).check();
	await row.locator( 'input[type="radio"][name$="[show_title]"][value="true"]' ).check();
	await save( page, '#site_sections_repeat' );
}

/**
 * Take the last row away from Sezioni HP and save.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function removeLastSection( page ) {
	await page.goto( SECTIONS );
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.locator( SECTION_ROWS ).last().locator( '> .cmb-remove-group-row' ).click();
	await save( page, '#site_sections_repeat' );
}

test( 'swapping rows keeps every description with its tab, also after saving twice', async ( { page } ) => {
	await loginAsAdmin( page );
	await writeTabs( page, [
		{ label: 'Alfa', text: 'Descrizione di Alfa' },
		{ label: 'Beta', text: 'Descrizione di Beta' },
		{ label: 'Gamma', text: 'Descrizione di Gamma' },
	] );

	try {
		await page.goto( LAYOUT );
		await expectTabs( page, [
			[ 'Alfa', 'Descrizione di Alfa' ],
			[ 'Beta', 'Descrizione di Beta' ],
			[ 'Gamma', 'Descrizione di Gamma' ],
		] );

		// The arrows are in the header of the row and have a name that a screen reader can say.
		const rows = page.locator( ROWS );
		await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Alfa' );
		await expect( rows.nth( 1 ).getByRole( 'link', { name: 'Sposta il tab in alto' } ) ).toBeVisible();

		// The second goes up: the descriptions follow their labels, before saving…
		await rows.nth( 1 ).locator( '> .cmb-shift-rows.move-up' ).click();
		await expectTabs( page, [
			[ 'Beta', 'Descrizione di Beta' ],
			[ 'Alfa', 'Descrizione di Alfa' ],
			[ 'Gamma', 'Descrizione di Gamma' ],
		] );
		await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Tab 1 (Beta)' );

		// …after the first save…
		await save( page );
		await page.goto( LAYOUT );
		await expectTabs( page, [
			[ 'Beta', 'Descrizione di Beta' ],
			[ 'Alfa', 'Descrizione di Alfa' ],
			[ 'Gamma', 'Descrizione di Gamma' ],
		] );

		// …and after a second swap and a second save in a row.
		await rows.nth( 1 ).locator( '> .cmb-shift-rows.move-down' ).click();
		await expectTabs( page, [
			[ 'Beta', 'Descrizione di Beta' ],
			[ 'Gamma', 'Descrizione di Gamma' ],
			[ 'Alfa', 'Descrizione di Alfa' ],
		] );
		await save( page );
		await page.goto( LAYOUT );
		await expectTabs( page, [
			[ 'Beta', 'Descrizione di Beta' ],
			[ 'Gamma', 'Descrizione di Gamma' ],
			[ 'Alfa', 'Descrizione di Alfa' ],
		] );
	} finally {
		await clearTabs( page );
	}
} );

test( 'rows added in the page and swapped twice in a row, before saving, keep their descriptions', async ( { page } ) => {
	await loginAsAdmin( page );
	await writeTabs(
		page,
		[
			{ label: 'Alfa', text: 'Descrizione di Alfa' },
			{ label: 'Beta', text: 'Descrizione di Beta' },
			{ label: 'Gamma', text: 'Descrizione di Gamma' },
		],
		false
	);

	try {
		// Nothing is saved yet: the rows were added in the page and are moved at once, two swaps in a row.
		const rows = page.locator( ROWS );
		await rows.nth( 2 ).locator( '> .cmb-shift-rows.move-up' ).click();
		await expectTabs( page, [
			[ 'Alfa', 'Descrizione di Alfa' ],
			[ 'Gamma', 'Descrizione di Gamma' ],
			[ 'Beta', 'Descrizione di Beta' ],
		] );
		await rows.nth( 1 ).locator( '> .cmb-shift-rows.move-up' ).click();
		await expectTabs( page, [
			[ 'Gamma', 'Descrizione di Gamma' ],
			[ 'Alfa', 'Descrizione di Alfa' ],
			[ 'Beta', 'Descrizione di Beta' ],
		] );

		await save( page );
		await page.goto( LAYOUT );
		await expectTabs( page, [
			[ 'Gamma', 'Descrizione di Gamma' ],
			[ 'Alfa', 'Descrizione di Alfa' ],
			[ 'Beta', 'Descrizione di Beta' ],
		] );
	} finally {
		await clearTabs( page );
	}
} );

test( 'after a row is removed the other rows keep their label in the title', async ( { page } ) => {
	await loginAsAdmin( page );
	await writeTabs(
		page,
		[
			{ label: 'Alfa', text: 'Descrizione di Alfa' },
			{ label: 'Beta', text: 'Descrizione di Beta' },
			{ label: 'Gamma', text: 'Descrizione di Gamma' },
		],
		false
	);

	try {
		const rows = page.locator( ROWS );
		await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Tab 1 (Alfa)' );
		await expect( rows.nth( 2 ).locator( TITLE ) ).toContainText( 'Tab 3 (Gamma)' );

		// CMB2 writes again the title of every row when one is removed: the labels must come back.
		page.once( 'dialog', ( dialog ) => dialog.accept() );
		await rows.nth( 1 ).locator( '> .cmb-remove-group-row' ).click();
		await expect( rows ).toHaveCount( 2 );
		await expect( rows.nth( 0 ).locator( TITLE ) ).toContainText( 'Tab 1 (Alfa)' );
		await expect( rows.nth( 1 ).locator( TITLE ) ).toContainText( 'Tab 2 (Gamma)' );
	} finally {
		await clearTabs( page );
	}
} );

test( 'the section shows the tabs on the home page, with the keyboard and in English', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page );
	await writeTabs( page, [
		{ label: 'Uno', labelEng: 'One', text: 'Testo del primo', textEng: 'Text of the first' },
		{ label: 'Due', text: 'Testo del secondo' },
	] );
	await page.goto( LAYOUT );
	await page.locator( 'input[name="home_tabs_title"]' ).fill( 'Le nostre aree' );
	await save( page );

	try {
		await page.goto( '/' );
		const section = page.locator( SECTION );
		await expect( section ).toBeVisible();
		await expect( section.getByRole( 'heading', { level: 2, name: 'Le nostre aree' } ) ).toBeVisible();

		const tabs = section.getByRole( 'tab' );
		await expect( tabs ).toHaveText( [ 'Uno', 'Due' ] );
		await expect( tabs.nth( 0 ) ).toHaveAttribute( 'aria-selected', 'true' );
		await expect( section.getByRole( 'tabpanel' ) ).toHaveCount( 1 );
		await expect( section.getByRole( 'tabpanel' ) ).toContainText( 'Testo del primo' );

		// With the keyboard (manual activation of Bootstrap Italia): the arrow moves the focus to the next tab,
		// Enter opens its panel.
		await tabs.nth( 0 ).focus();
		await page.keyboard.press( 'ArrowRight' );
		await expect( tabs.nth( 1 ) ).toBeFocused();
		await expect( tabs.nth( 0 ) ).toHaveAttribute( 'aria-selected', 'true' );
		await page.keyboard.press( 'Enter' );
		await expect( tabs.nth( 1 ) ).toHaveAttribute( 'aria-selected', 'true' );
		await expect( tabs.nth( 0 ) ).toHaveAttribute( 'aria-selected', 'false' );
		await expect( section.getByRole( 'tabpanel' ) ).toContainText( 'Testo del secondo' );

		// In English the English label and text are shown; a tab without English values keeps the Italian ones.
		await page.goto( '/en/' );
		const english = page.locator( SECTION );
		await expect( english.getByRole( 'tab' ) ).toHaveText( [ 'One', 'Due' ] );
		await expect( english.getByRole( 'tabpanel' ) ).toContainText( 'Text of the first' );
		await english.getByRole( 'tab', { name: 'Due' } ).click();
		await expect( english.getByRole( 'tabpanel' ) ).toContainText( 'Testo del secondo' );
	} finally {
		await clearTabs( page );
		await removeLastSection( page );
	}
} );

test( 'on a phone the tabs do not make the page overflow', async ( { page } ) => {
	await loginAsAdmin( page );
	await addSection( page );
	await writeTabs( page, [
		{ label: 'Un\'etichetta abbastanza lunga', text: 'Testo del primo' },
		{ label: 'Un\'altra etichetta lunga', text: 'Testo del secondo' },
		{ label: 'Terza etichetta lunga', text: 'Testo del terzo' },
	] );

	try {
		await page.setViewportSize( { width: 320, height: 800 } );
		await page.goto( '/' );
		const section = page.locator( SECTION );
		await expect( section ).toBeVisible();
		await expect( section.getByRole( 'tab' ) ).toHaveCount( 3 );
		await section.getByRole( 'tab' ).nth( 2 ).click();
		await expect( section.getByRole( 'tabpanel' ) ).toContainText( 'Testo del terzo' );

		const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
		expect( overflow ).toBeLessThanOrEqual( 0 );
	} finally {
		await page.setViewportSize( { width: 1280, height: 720 } );
		await clearTabs( page );
		await removeLastSection( page );
	}
} );
