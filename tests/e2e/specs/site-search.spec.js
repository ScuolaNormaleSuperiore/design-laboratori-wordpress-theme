// Public site search: a user can submit a query and clear it again.
const { test, expect } = require( '@playwright/test' );

const SEARCH_PAGE = '/ricerca-sito/';
const SEARCH_TERM = 'dli-e2e-no-result-9c1a';

test( 'the site search submits a query and shows its empty state', async ( { page } ) => {
	const response = await page.goto( SEARCH_PAGE );

	expect( response.status() ).toBe( 200 );
	await expect( page.locator( '#ricercasitoform' ) ).toBeVisible();
	await expect( page.locator( '#searchstring' ) ).toBeVisible();

	await page.locator( '#searchstring' ).fill( SEARCH_TERM );
	await Promise.all( [
		page.waitForURL( ( url ) => url.searchParams.get( 'searchstring' ) === SEARCH_TERM ),
		page.getByRole( 'button', { name: 'Cerca', exact: true } ).click(),
	] );

	await expect( page.locator( '#risultati [role="status"]' ) ).toContainText( `0 risultati per «${ SEARCH_TERM }»` );
	await expect( page.locator( '#risultati' ) ).toContainText( 'Nessun risultato per i filtri selezionati.' );
	await Promise.all( [
		page.waitForURL( SEARCH_PAGE ),
		page.getByRole( 'link', { name: 'Annulla ricerca' } ).click(),
	] );
	await expect( page.locator( '#risultati' ) ).toHaveCount( 0 );
} );
