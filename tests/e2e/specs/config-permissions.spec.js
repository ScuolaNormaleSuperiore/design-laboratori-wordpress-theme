// Who can open the Configurazione page of the theme (capability dli_edit_site_configuration):
// the administrator and the Super Editor can, the Editor cannot.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin, loginAsSuperEditor, loginAsEditor } = require( '../support/auth' );

const CONFIG_PAGE = '/wp-admin/admin.php?page=dli_options';

test( 'the administrator opens the Configurazione page', async ( { page } ) => {
	await loginAsAdmin( page );
	const response = await page.goto( CONFIG_PAGE );

	expect( response.status() ).toBe( 200 );
	await expect( page.locator( '#wpbody-content' ) ).toContainText( 'Configurazione' );
} );

test( 'the Super Editor opens the Configurazione page', async ( { page } ) => {
	await loginAsSuperEditor( page );
	const response = await page.goto( CONFIG_PAGE );

	expect( response.status() ).toBe( 200 );
	await expect( page.locator( '#wpbody-content' ) ).toContainText( 'Configurazione' );
} );

test( 'the Editor cannot open the Configurazione page', async ( { page } ) => {
	await loginAsEditor( page );
	const response = await page.goto( CONFIG_PAGE );

	expect( response.status() ).toBe( 403 );
	await expect( page.locator( '.wp-die-message' ) ).toBeVisible();
	// The refusal page is a bare page: the options form is not there.
	await expect( page.locator( 'body' ) ).not.toContainText( 'Opzioni di base' );
} );
