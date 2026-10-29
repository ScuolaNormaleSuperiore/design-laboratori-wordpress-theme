// The tool "Pagine di esempio" of Strumenti DLI → Utilità: two separate boxes, creation of the example
// section in both languages, and its removal. The site goes back to the baseline after the run.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const UTILS = '/wp-admin/admin.php?page=dli-tools-utils';

test( 'the tool creates the example section in both languages and removes it', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( UTILS );

	// Two separate boxes, the example pages under the reload tool.
	const boxes = page.locator( '.postbox.dli-tool-panel' );
	await expect( boxes ).toHaveCount( 2 );
	await expect( boxes.nth( 1 ).getByRole( 'heading', { name: 'Pagine di esempio' } ) ).toBeVisible();
	await expect( page.locator( '#dli-example-lang' ) ).toHaveValue( 'iten' );

	// Create: the browser asks for confirmation.
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.getByRole( 'button', { name: 'Crea le pagine di esempio' } ).click();
	await expect( page.locator( '.notice-success' ) ).toContainText( 'Pagine create: 24' );

	// Both top pages show the four cards under the title.
	for ( const url of [ '/gestione-dei-dati-della-ricerca/', '/en/research-data-management/' ] ) {
		const response = await page.goto( url );
		expect( response.status() ).toBe( 200 );
		await expect( page.locator( '#sottosezioni article.card-nav' ) ).toHaveCount( 4 );
	}

	// Again: nothing is duplicated.
	await page.goto( UTILS );
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.getByRole( 'button', { name: 'Crea le pagine di esempio' } ).click();
	await expect( page.locator( '.notice-success' ) ).toContainText( 'Pagine create: 0. Già presenti: 24' );

	// Remove.
	page.once( 'dialog', ( dialog ) => dialog.accept() );
	await page.getByRole( 'button', { name: 'Rimuovi le pagine di esempio' } ).click();
	await expect( page.locator( '.notice-success' ) ).toContainText( 'Rimosse 24 pagine di esempio' );
	const gone = await page.goto( '/gestione-dei-dati-della-ricerca/' );
	expect( gone.status() ).toBe( 404 );
} );

test( 'declining the confirmation creates nothing', async ( { page } ) => {
	await loginAsAdmin( page );
	await page.goto( UTILS );

	page.once( 'dialog', ( dialog ) => dialog.dismiss() );
	await page.getByRole( 'button', { name: 'Crea le pagine di esempio' } ).click();

	await expect( page.locator( '.notice-success' ) ).toHaveCount( 0 );
	const response = await page.goto( '/gestione-dei-dati-della-ricerca/' );
	expect( response.status() ).toBe( 404 );
} );
