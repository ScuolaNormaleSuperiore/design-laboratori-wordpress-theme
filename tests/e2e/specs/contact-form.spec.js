// The contact form (page template "Contatti"). The E2E site has no CAPTCHA plugin, so the form shows
// a warning and its submit button is disabled: the test below checks that behaviour. The answers to a form posted
// anyway (messages, their order, the nonce checked first) are checked by DLI_Contact_Form_Integration_Test. A
// successful send needs the CAPTCHA plugin and a mail server, so it is not covered here.
const { test, expect } = require( '@playwright/test' );

const CONTACT_PAGE = '/contatti/';
const FORM = '#formcontatti';

test( 'the contact form is shown and cannot be sent without the CAPTCHA plugin', async ( { page } ) => {
	const response = await page.goto( CONTACT_PAGE );

	expect( response.status() ).toBe( 200 );
	await expect( page.locator( `${ FORM } #nomecognome` ) ).toBeVisible();
	await expect( page.locator( `${ FORM } #indirizzoemail` ) ).toBeVisible();
	await expect( page.locator( `${ FORM } #testomessaggio` ) ).toBeVisible();
	await expect( page.locator( `${ FORM } input[name="contatti_nonce_field"]` ) ).toHaveCount( 1 );
	await expect( page.locator( `${ FORM } .alert-warning` ) ).toContainText( 'Il servizio di verifica non è temporaneamente disponibile' );
	await expect( page.locator( `${ FORM } button[type="submit"]` ) ).toBeDisabled();
} );
