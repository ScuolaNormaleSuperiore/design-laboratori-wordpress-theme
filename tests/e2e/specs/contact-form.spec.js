// The contact form (page template "Contatti"). The E2E site has no CAPTCHA plugin, so the form shows
// a warning and its submit button is disabled: the tests below check that behaviour and what the page
// answers when the form is posted anyway (a bot does not need the button). A successful send needs the
// CAPTCHA plugin and a mail server, so it is not covered here.
const { test, expect } = require( '@playwright/test' );

const CONTACT_PAGE = '/contatti/';
const FORM = '#formcontatti';

/**
 * Open the contact page and post the form even though its submit button is disabled.
 *
 * @param {import('@playwright/test').Page} page      Page.
 * @param {{nonce?: string}}                options   Optional replacement of the nonce value.
 */
async function postFormAnyway( page, options = {} ) {
	await page.goto( CONTACT_PAGE );
	await page.locator( FORM ).evaluate( ( form, nonce ) => {
		if ( undefined !== nonce ) {
			form.querySelector( 'input[name="contatti_nonce_field"]' ).value = nonce;
		}
		const submit = form.querySelector( 'button[type="submit"]' );
		submit.removeAttribute( 'disabled' );
		submit.removeAttribute( 'aria-disabled' );
	}, options.nonce );
	await Promise.all( [
		page.waitForLoadState( 'load' ),
		page.locator( `${ FORM } button[type="submit"]` ).click(),
	] );
}

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

test( 'a form posted with empty fields shows every validation message, in order', async ( { page } ) => {
	await postFormAnyway( page );

	const alert = page.locator( '#contatti_messaggi .alert-danger' );
	await expect( alert ).toBeVisible();
	const text = await alert.innerText();
	const unavailable = text.indexOf( 'Il servizio di verifica non è temporaneamente disponibile' );
	const required = text.indexOf( 'Compilare tutti i campi obbligatori' );
	const email = text.indexOf( 'Indicare un indirizzo email valido' );
	expect( unavailable ).toBeGreaterThanOrEqual( 0 );
	expect( required ).toBeGreaterThan( unavailable );
	expect( email ).toBeGreaterThan( required );
	// The messages are separated by a line break, and the first one has none before it.
	const html = await alert.evaluate( ( el ) => el.innerHTML );
	expect( ( html.match( /<br\s*\/?>/g ) || [] ).length ).toBe( 2 );
	await expect( page.locator( '#contatti_messaggi .alert-success' ) ).toHaveCount( 0 );
} );

test( 'a form posted with a wrong nonce is refused', async ( { page } ) => {
	await postFormAnyway( page, { nonce: 'wrong' } );

	const alert = page.locator( '#contatti_messaggi .alert-danger' );
	await expect( alert ).toContainText( 'Valore di Nonce errato.' );
	// The nonce is checked first: nothing else is validated and nothing is sent.
	await expect( alert ).not.toContainText( 'Compilare tutti i campi obbligatori' );
	await expect( page.locator( '#contatti_messaggi .alert-success' ) ).toHaveCount( 0 );
} );
