// Newsletter subscription form: browser-visible controls and native email validation.
const { test, expect } = require( '@playwright/test' );

test( 'the newsletter page shows the subscription form', async ( { page } ) => {
	const response = await page.goto( '/newsletter/' );

	expect( response.status() ).toBeLessThan( 400 );
	await expect( page.getByRole( 'heading', { name: 'Newsletter' } ) ).toBeVisible();
	await expect( page.locator( '#formnewsletter' ) ).toBeVisible();
	await expect( page.locator( '#user_name' ) ).toBeVisible();
	await expect( page.locator( '#user_surname' ) ).toBeVisible();
	await expect( page.locator( '#user_mail' ) ).toBeVisible();
	await expect( page.getByRole( 'button', { name: 'Iscrivimi alla newsletter' } ) ).toBeVisible();
} );

test( 'the newsletter form rejects a malformed email in the browser', async ( { page } ) => {
	await page.goto( '/newsletter/' );

	await page.locator( '#user_mail' ).fill( 'not-an-email' );

	expect( await page.locator( '#user_mail' ).evaluate( ( input ) => input.validity.typeMismatch ) ).toBe( true );
} );
