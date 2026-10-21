// The administrator can log in and reach the dashboard.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

test( 'the administrator logs in', async ( { page } ) => {
	await loginAsAdmin( page );

	await expect( page.locator( '#wpadminbar' ) ).toBeVisible();
	await expect( page.locator( '#adminmenu' ) ).toBeVisible();
} );
