// People archive: the surname filter preserves a query and reloads with a new one.
const { test, expect } = require( '@playwright/test' );

const PEOPLE_PAGE = '/persone/';
const INITIAL_SURNAME = 'dli-e2e-no-person-9c1a';
const UPDATED_SURNAME = 'dli-e2e-no-person-9c1b';

test( 'the people surname filter keeps its value and updates the archive URL', async ( { page } ) => {
	const response = await page.goto( `${ PEOPLE_PAGE }?cognome=${ INITIAL_SURNAME }` );

	expect( response.status() ).toBe( 200 );
	await expect( page.locator( '#personeform' ) ).toBeVisible();
	await expect( page.locator( '#searchPeopleCognome' ) ).toHaveValue( INITIAL_SURNAME );
	await expect( page.locator( '#personeform' ) ).toContainText( 'Non è stata trovata nessuna persona' );

	await page.locator( '#searchPeopleCognome' ).fill( UPDATED_SURNAME );
	await Promise.all( [
		page.waitForURL( ( url ) => url.searchParams.get( 'cognome' ) === UPDATED_SURNAME ),
		page.locator( '#searchPeopleCognome' ).evaluate( ( input ) => input.dispatchEvent( new Event( 'search' ) ) ),
	] );
	await expect( page.locator( '#searchPeopleCognome' ) ).toHaveValue( UPDATED_SURNAME );
	await expect( page.locator( '#personeform' ) ).toContainText( 'Non è stata trovata nessuna persona' );
} );
