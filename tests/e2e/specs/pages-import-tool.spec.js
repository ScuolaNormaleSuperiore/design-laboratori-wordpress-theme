// The tool Strumenti DLI → Importa → Pagine: the form and the downloads of the examples, the simulation that writes
// nothing, the import that needs the confirmation and creates the pages once, the two languages linked, the lorem ipsum,
// and the errors of a file. The tests that write put the site back to the baseline when they end: the pages they create
// would be in the way of the others.
const path = require( 'path' );
const fs = require( 'fs' );
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );
const { resetSite } = require( '../support/reset' );

const IMPORT = '/wp-admin/admin.php?page=dli-tools-import&type=pagine';
const EXAMPLES = path.join( __dirname, '..', '..', '..', 'SETUP', 'CSV_Import' );
const EXAMPLE_IT = path.join( EXAMPLES, 'pages-example-it.csv' );
const EXAMPLE_EN = path.join( EXAMPLES, 'pages-example-en.csv' );

/**
 * Fill the form and send it.
 *
 * @param {import('@playwright/test').Page} page    Page.
 * @param {Object}                          options Choices of the form.
 * @param {string|Object} options.file     Path of the file, or `{ name, mimeType, buffer }`.
 * @param {string}        options.language `it` or `en`.
 * @param {string}        options.exec     `dry_run` or `commit`.
 * @param {boolean}       options.lorem    Whether the lorem ipsum box is ticked.
 * @param {boolean}       options.confirm  Whether the confirmation box is ticked.
 */
async function send( page, { file = EXAMPLE_IT, language = 'it', exec = 'dry_run', lorem = false, confirm = false } = {} ) {
	await page.goto( IMPORT );
	await page.locator( '#dli-pages-csv' ).setInputFiles( file );
	await page.locator( '#dli-pages-language' ).selectOption( language );
	await page.locator( '#dli-pages-exec' ).selectOption( exec );
	if ( lorem ) {
		await page.locator( '#dli-pages-lorem' ).check();
	}
	if ( confirm ) {
		await page.locator( '#dli-pages-confirm' ).check();
	}
	await page.getByRole( 'button', { name: 'Esegui importazione pagine' } ).click();
	await expect( page.locator( '#dli-report-section' ) ).toBeVisible();
}

/**
 * The counters of the report: pages, created, skipped, errors.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @return {Promise<string[]>} The four numbers as text.
 */
async function counters( page ) {
	return page.locator( '.dli-report-summary tbody td' ).allTextContents();
}

test( 'the form has its fields and the three downloads, works on a phone, and the examples can be downloaded', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( '/wp-admin/admin.php?page=dli-tools-import' );

	// The type is in the sub-navigation of the import.
	await page.locator( '.dli-import-subnav' ).getByRole( 'link', { name: 'Pagine' } ).click();
	await expect( page ).toHaveURL( /type=pagine/ );

	await expect( page.getByLabel( 'File CSV' ) ).toBeVisible();
	await expect( page.getByLabel( 'Lingua delle pagine' ) ).toHaveValue( 'it' );
	await expect( page.getByLabel( 'Modalità di esecuzione' ) ).toHaveValue( 'dry_run' );
	await expect( page.getByLabel( /Inserisci testo Lorem ipsum/ ) ).not.toBeChecked();
	await expect( page.getByLabel( /Confermo/ ) ).not.toBeChecked();

	// The keyboard goes through the fields in the order of the form.
	await page.locator( '#dli-pages-csv' ).focus();
	for ( const id of [ 'dli-pages-language', 'dli-pages-exec', 'dli-pages-lorem', 'dli-pages-confirm' ] ) {
		await page.keyboard.press( 'Tab' );
		await expect( page.locator( '#' + id ) ).toBeFocused();
	}

	// An example comes with the BOM, the header and the 15 pages.
	const [ download ] = await Promise.all( [
		page.waitForEvent( 'download' ),
		page.getByRole( 'link', { name: /italiano/i } ).click(),
	] );
	expect( download.suggestedFilename() ).toBe( 'pages-example-it.csv' );
	const content = fs.readFileSync( await download.path(), 'utf8' );
	expect( content.charCodeAt( 0 ), 'the file starts with the BOM' ).toBe( 0xfeff );
	expect( content ).toContain( 'level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards;page_key;excerpt;slug' );
	expect( content.trim().split( '\n' ) ).toHaveLength( 16 );

	// On a phone nothing overflows.
	await page.setViewportSize( { width: 320, height: 800 } );
	await page.goto( IMPORT );
	const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
	expect( overflow ).toBeLessThanOrEqual( 0 );
} );

test( 'the simulation shows the pages, their addresses and the counters, and writes nothing', async ( { page } ) => {
	await loginAsAdmin( page );

	await send( page );

	await expect( page.locator( '.dli-report-mode' ) ).toContainText( 'esecuzione simulata' );
	expect( await counters( page ) ).toEqual( [ '15', '15', '0', '0' ] );
	await expect( page.locator( '.dli-log-table tbody tr' ) ).toHaveCount( 15 );
	await expect( page.locator( '.dli-log-table tbody tr' ).first() ).toContainText( 'Open Access' );
	await expect( page.locator( '.dli-log-table' ) ).toContainText( '/dati-della-ricerca/pianificare/' );
	await expect( page.locator( '#dli-report-section' ) ).toBeFocused();

	expect( ( await page.goto( '/dati-della-ricerca/' ) ).status(), 'the simulation created nothing' ).toBe( 404 );
} );

test( 'the import needs the confirmation, then creates the pages, and sending the file again creates nothing', async ( { page } ) => {
	await loginAsAdmin( page );

	try {
		// Without the confirmation nothing is written and the reason is said.
		await send( page, { exec: 'commit' } );
		await expect( page.locator( '.notice-error' ) ).toContainText( 'Per importare serve la conferma della pubblicazione: non è stato scritto nulla.' );
		expect( ( await page.goto( '/dati-della-ricerca/' ) ).status() ).toBe( 404 );

		// With it the pages are created.
		await send( page, { exec: 'commit', confirm: true } );
		await expect( page.locator( '.dli-report-mode' ) ).toContainText( 'esecuzione reale' );
		expect( await counters( page ) ).toEqual( [ '15', '15', '0', '0' ] );
		const response = await page.goto( '/dati-della-ricerca/' );
		expect( response.status() ).toBe( 200 );
		await expect( page.getByRole( 'heading', { level: 2, name: 'Dati della ricerca' } ) ).toBeVisible();
		await expect( page.locator( '#sottosezioni article.card-nav' ) ).toHaveCount( 4 );

		// The same file again: all skipped.
		await send( page, { exec: 'commit', confirm: true } );
		expect( await counters( page ) ).toEqual( [ '15', '0', '15', '0' ] );
	} finally {
		resetSite();
	}
} );

test( 'the two languages are imported one after the other and linked, and the lorem ipsum is written when asked', async ( { page } ) => {
	await loginAsAdmin( page );

	try {
		await send( page, { exec: 'commit', confirm: true, lorem: true } );
		expect( await counters( page ) ).toEqual( [ '15', '15', '0', '0' ] );
		await send( page, { file: EXAMPLE_EN, language: 'en', exec: 'commit', confirm: true } );
		expect( await counters( page ) ).toEqual( [ '15', '15', '0', '0' ] );

		// The Italian page has a text of lorem ipsum, the English one (imported without it) has none.
		await page.goto( '/dati-della-ricerca/' );
		await expect( page.locator( 'main' ) ).toContainText( 'Lorem ipsum' );
		const english = await page.goto( '/en/research-data/' );
		expect( english.status() ).toBe( 200 );
		await expect( page.locator( 'main' ) ).not.toContainText( 'Lorem ipsum' );

		// The two pages are translations of each other: the Italian one points to the English one.
		await page.goto( '/dati-della-ricerca/' );
		await expect( page.locator( 'link[rel="alternate"][hreflang="en"]' ) ).toHaveAttribute( 'href', /\/en\/research-data\/$/ );
	} finally {
		resetSite();
	}
} );

test( 'a file with errors is refused with every error and its line, and nothing is written', async ( { page } ) => {
	await loginAsAdmin( page );
	const content = 'level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards\nBuona;;;1;0;0\n;Senza genitore;;x;0;0\n;;;1;0;0\n';

	await send( page, {
		file: { name: 'errori.csv', mimeType: 'text/csv', buffer: Buffer.from( content, 'utf8' ) },
		exec: 'commit',
		confirm: true,
	} );

	await expect( page.locator( '.notice-error' ) ).toContainText( 'Riga 3: menu_order deve essere un numero intero da 0 in su.' );
	await expect( page.locator( '.notice-error' ) ).toContainText( 'Riga 4: manca il titolo.' );
	expect( ( await page.goto( '/buona/' ) ).status(), 'the good page was not created either' ).toBe( 404 );

	// A file that is not a CSV is refused too.
	await send( page, { file: { name: 'pagine.txt', mimeType: 'text/plain', buffer: Buffer.from( 'x', 'utf8' ) } } );
	await expect( page.locator( '.notice-error' ) ).toContainText( 'Formato non valido: è richiesto un file .csv.' );
} );
