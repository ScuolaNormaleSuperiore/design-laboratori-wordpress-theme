// People archive: the search field preserves a query and reloads with a new one on Enter.
const { test, expect } = require( '@playwright/test' );

const PEOPLE_PAGE = '/persone/';
const INITIAL_SEARCH = 'dli-e2e-no-person-9c1a';
const UPDATED_SEARCH = 'dli-e2e-no-person-9c1b';

test( 'the people search keeps its value and updates the archive URL on Enter', async ( { page } ) => {
	const response = await page.goto( `${ PEOPLE_PAGE }?vista=chip&cerca=${ INITIAL_SEARCH }` );

	expect( response.status() ).toBe( 200 );
	await expect( page.locator( '#personeform' ) ).toBeVisible();
	await expect( page.locator( '#searchPeople' ) ).toHaveValue( INITIAL_SEARCH );
	await expect( page.locator( '#personeform' ) ).toContainText( 'Non è stata trovata nessuna persona' );

	await page.locator( '#searchPeople' ).fill( UPDATED_SEARCH );
	await Promise.all( [
		page.waitForURL( ( url ) => url.searchParams.get( 'cerca' ) === UPDATED_SEARCH ),
		page.locator( '#searchPeople' ).press( 'Enter' ),
	] );
	await expect( page.locator( '#searchPeople' ) ).toHaveValue( UPDATED_SEARCH );
	await expect( page.locator( '#personeform' ) ).toContainText( 'Non è stata trovata nessuna persona' );
} );
