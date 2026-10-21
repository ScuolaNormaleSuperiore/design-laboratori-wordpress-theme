// The home page of the site loads with the theme active.
const { test, expect } = require( '@playwright/test' );

test( 'the home page loads', async ( { page } ) => {
	const response = await page.goto( '/' );

	expect( response.status() ).toBeLessThan( 400 );
	await expect( page.locator( 'html' ) ).toHaveAttribute( 'lang', /.+/ );
	await expect( page.locator( 'header' ).first() ).toBeVisible();
	// ACF and Polylang are active: the theme did not stop with its own error.
	await expect( page.locator( 'body' ) ).not.toContainText( 'devono essere installati e attivati' );
} );
