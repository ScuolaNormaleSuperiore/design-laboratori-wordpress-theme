// The tool Strumenti DLI → Importa → Persone, as it is: the form, the simulation that writes nothing, the real import,
// the modes that skip people that exist, the errors of a row and the refusals of a file (wrong extension, header
// without the email, too large). These checks describe the behaviour before the code that reads the file is shared
// with other tools. The person created by the test is taken away at the end: the site is shared by the whole run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const IMPORT = '/wp-admin/admin.php?page=dli-tools-import';
const PEOPLE = '/wp-admin/edit.php?post_type=persona';
const SURNAME = 'ImportE2E';
const HEADER = 'nome;cognome;email;stato';

/**
 * Send the form of the import with a file made on the spot.
 *
 * @param {import('@playwright/test').Page} page    Page.
 * @param {Object}                          options Choices of the form.
 * @param {string}                          options.content Content of the file.
 * @param {string}                          options.name    Name of the file.
 * @param {string}                          options.mode    Mode of the import.
 * @param {string}                          options.exec    `dry_run` or `commit`.
 */
async function sendFile( page, { content, name = 'persone.csv', mode = 'upsert', exec = 'dry_run' } ) {
	await page.goto( IMPORT );
	await page.locator( '#dli-csv-file' ).setInputFiles( { name, mimeType: 'text/csv', buffer: Buffer.from( content, 'utf8' ) } );
	await page.locator( '#dli-import-mode' ).selectOption( mode );
	await page.locator( '#dli-exec-mode' ).selectOption( exec );
	await page.getByRole( 'button', { name: 'Esegui import persone' } ).click();
	await expect( page.locator( '#dli-report-section' ) ).toBeVisible();
}

/**
 * The counters of the report: processed, created, updated, skipped, errors.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @return {Promise<string[]>} The five numbers as text.
 */
async function counters( page ) {
	return page.locator( '.dli-report-summary tbody td' ).allTextContents();
}

/**
 * How many people with the surname of the test are in the list.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @return {Promise<number>} The people.
 */
async function peopleOfTheTest( page ) {
	await page.goto( `${ PEOPLE }&s=${ SURNAME }` );
	return page.locator( '#the-list tr.iedit' ).count();
}

/**
 * Take away the people of the test: to the bin and then for good.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function removePeopleOfTheTest( page ) {
	if ( 0 === ( await peopleOfTheTest( page ) ) ) {
		return;
	}
	await page.locator( '#cb-select-all-1' ).check();
	await page.locator( '#bulk-action-selector-top' ).selectOption( 'trash' );
	await page.locator( '#doaction' ).click();
	await page.goto( `${ PEOPLE }&post_status=trash` );
	await page.locator( '#delete_all' ).first().click();
}

test( 'the form has the file, the two modes with the simulation as the default, and the sample file', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( IMPORT );

	await expect( page.locator( '#dli-csv-file' ) ).toHaveAttribute( 'accept', '.csv' );
	await expect( page.locator( '#dli-import-mode option' ) ).toHaveCount( 4 );
	await expect( page.locator( '#dli-import-mode' ) ).toHaveValue( 'upsert' );
	await expect( page.locator( '#dli-exec-mode' ) ).toHaveValue( 'dry_run' );
	await expect( page.getByRole( 'link', { name: 'Scarica CSV di esempio' } ) ).toBeVisible();
} );

test( 'the simulation writes nothing, the import creates the person, and the modes that skip leave it as it is', async ( { page } ) => {
	await loginAsAdmin( page );
	const file = `${ HEADER }\nProva;${ SURNAME };prova.import.e2e@example.org;publish\n`;

	try {
		// Simulation: the report says what would happen and no person is created.
		await sendFile( page, { content: file } );
		await expect( page.locator( '.dli-report-mode' ) ).toContainText( 'esecuzione simulata' );
		expect( await counters( page ) ).toEqual( [ '1', '1', '0', '0', '0' ] );
		await expect( page.locator( '.dli-log-table tbody tr' ) ).toHaveCount( 1 );
		await expect( page.locator( '.dli-log-table' ) ).toContainText( 'prova.import.e2e@example.org' );
		expect( await peopleOfTheTest( page ), 'the simulation created nobody' ).toBe( 0 );

		// Real import: one person is created.
		await sendFile( page, { content: file, exec: 'commit' } );
		await expect( page.locator( '.dli-report-mode' ) ).toContainText( 'esecuzione reale' );
		expect( await counters( page ) ).toEqual( [ '1', '1', '0', '0', '0' ] );
		await expect( page.locator( '.dli-log-status-ok' ) ).toHaveCount( 1 );
		expect( await peopleOfTheTest( page ) ).toBe( 1 );

		// The same file again, in the two modes that do not update a person who exists: skipped, still one person.
		for ( const mode of [ 'solo_nuovi', 'ignora_esistenti' ] ) {
			await sendFile( page, { content: file, mode, exec: 'commit' } );
			expect( await counters( page ), mode ).toEqual( [ '1', '0', '0', '1', '0' ] );
		}
		expect( await peopleOfTheTest( page ) ).toBe( 1 );

		// Upsert updates the person who exists.
		await sendFile( page, { content: file, mode: 'upsert', exec: 'commit' } );
		expect( await counters( page ) ).toEqual( [ '1', '0', '1', '0', '0' ] );
		expect( await peopleOfTheTest( page ) ).toBe( 1 );
	} finally {
		await removePeopleOfTheTest( page );
	}
} );

test( 'a row without the email is an error of that row, and the others are processed', async ( { page } ) => {
	await loginAsAdmin( page );

	await sendFile( page, { content: `${ HEADER }\nProva;${ SURNAME };;publish\nAltra;${ SURNAME };non-una-email;publish\n` } );

	expect( await counters( page ) ).toEqual( [ '2', '0', '0', '0', '2' ] );
	await expect( page.locator( '.dli-log-status-errore' ) ).toHaveCount( 2 );
	expect( await peopleOfTheTest( page ) ).toBe( 0 );
} );

test( 'a file that cannot be used is refused with a message and nothing is processed', async ( { page } ) => {
	await loginAsAdmin( page );

	// A file that is not a .csv.
	await sendFile( page, { content: `${ HEADER }\n`, name: 'persone.txt' } );
	await expect( page.locator( '.notice-error' ) ).toContainText( 'Formato non valido: è richiesto un file .csv.' );

	// A header without a required column.
	await sendFile( page, { content: 'nome;cognome\nProva;Prova\n' } );
	await expect( page.locator( '.notice-error' ) ).toContainText( 'colonna obbligatoria "email" non trovata' );
	await expect( page.locator( '.dli-report-summary tbody td' ).first() ).toHaveText( '0' );

	// A file over 2 MB.
	await sendFile( page, { content: `${ HEADER }\n${ 'a'.repeat( 2 * 1024 * 1024 + 1024 ) }\n` } );
	await expect( page.locator( '.notice-error' ) ).toContainText( 'File troppo grande. Dimensione massima: 2 MB.' );
} );
