// The tool Strumenti DLI → Importa → Persone in a real browser: the file is chosen, the form is sent and the report is
// shown. This is the only check that needs a browser; everything else is covered at a lower level:
// - the form (file field, the four modes, the defaults, the link to the example), the simulation that writes nothing,
//   a row with an error, the files that are refused, the reading of the file and the report:
//   DLI_People_Import_Admin_Integration_Test;
// - what one row does in each mode: DLI_People_File_Importer_Integration_Test;
// - the example file: DLI_Example_People_Csv_Unit_Test.
// The person created by the test is taken away at the end: the site is shared by the whole run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const IMPORT = '/wp-admin/admin.php?page=dli-tools-import';
const PEOPLE = '/wp-admin/edit.php?post_type=persona';
const SURNAME = 'ImportE2E';

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

test( 'a file chosen in the browser is imported and the report is shown', async ( { page } ) => {
	await loginAsAdmin( page );
	const file = `nome;cognome;email;stato\nProva;${ SURNAME };prova.import.e2e@example.org;publish\n`;

	try {
		await page.goto( IMPORT );
		await page.locator( '#dli-csv-file' ).setInputFiles( { name: 'persone.csv', mimeType: 'text/csv', buffer: Buffer.from( file, 'utf8' ) } );
		await page.locator( '#dli-exec-mode' ).selectOption( 'commit' );
		await page.getByRole( 'button', { name: 'Esegui import persone' } ).click();

		await expect( page.locator( '#dli-report-section' ) ).toBeVisible();
		await expect( page.locator( '.dli-report-mode' ) ).toContainText( 'esecuzione reale' );
		expect( await page.locator( '.dli-report-summary tbody td' ).allTextContents() ).toEqual( [ '1', '1', '0', '0', '0' ] );
		await expect( page.locator( '.dli-log-table' ) ).toContainText( 'prova.import.e2e@example.org' );
		expect( await peopleOfTheTest( page ) ).toBe( 1 );
	} finally {
		await removePeopleOfTheTest( page );
	}
} );
