// The tool "Pagine di esempio" of Strumenti DLI → Utilità: two separate boxes, creation of the example
// pages (four main pages, 15 pages per language) in both languages, and their removal. The site goes back
// to the baseline after the run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const UTILS = '/wp-admin/admin.php?page=dli-tools-utils';

test( 'the tool creates the example section in both languages and removes it', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( UTILS );

	// Two separate boxes, the example pages under the reload tool.
	const boxes = page.locator( '.postbox.dli-tool-panel' );
	await expect( boxes ).toHaveCount( 2 );
	await expect( boxes.nth( 1 ).getByRole( 'heading', { name: 'Example pages' } ) ).toBeVisible();
	await expect( page.locator( '#dli-example-lang' ) ).toHaveValue( 'iten' );

	// Create: the browser asks for confirmation.
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.getByRole( 'button', { name: 'Create the example pages' } ).click();
	await expect( page.locator( '.notice-success' ) ).toContainText( 'Pages created: 30' );
	// The outcome leads to the four main pages of each language.
	await expect( page.locator( '.notice-success a' ) ).toHaveCount( 8 );

	// Both top pages show the four cards under the title.
	for ( const url of [ '/dati-della-ricerca/', '/en/research-data/' ] ) {
		const response = await page.goto( url );
		expect( response.status() ).toBe( 200 );
		await expect( page.locator( '#sottosezioni article.card-nav' ) ).toHaveCount( 4 );
	}

	// Again: nothing is duplicated.
	await page.goto( UTILS );
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.getByRole( 'button', { name: 'Create the example pages' } ).click();
	await expect( page.locator( '.notice-success' ) ).toContainText( 'Pages created: 0. Already there: 30' );

	// Remove.
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.getByRole( 'button', { name: 'Remove the example pages' } ).click();
	await expect( page.locator( '.notice-success' ) ).toContainText( '30 example pages removed' );
	const gone = await page.goto( '/dati-della-ricerca/' );
	expect( gone.status() ).toBe( 404 );
} );

test( 'declining the confirmation creates nothing', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( UTILS );

	page.once( 'dialog', ( dialog ) => dialog.dismiss() );
	await page.getByRole( 'button', { name: 'Create the example pages' } ).click();

	await expect( page.locator( '.notice-success' ) ).toHaveCount( 0 );
	const response = await page.goto( '/dati-della-ricerca/' );
	expect( response.status() ).toBe( 404 );
} );
