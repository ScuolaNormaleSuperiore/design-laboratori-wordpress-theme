// Login helpers of the E2E tests. The users exist in tests/e2e/fixtures/baseline.sql
// with the well-known password "password": the site is a local test site. Each value
// can be overridden with an environment variable (see tests/e2e/README.md).
const { expect } = require( '@playwright/test' );

/**
 * Read an environment variable, with a default.
 *
 * @param {string} name     Variable name.
 * @param {string} fallback Value used when the variable is not set.
 * @return {string} Value.
 */
function env( name, fallback ) {
	return process.env[ name ] || fallback;
}

/**
 * Credentials of the site administrator (DLI_E2E_ADMIN_USER, DLI_E2E_ADMIN_PASSWORD).
 *
 * @return {{user: string, password: string}} User name and password.
 */
function getAdminCredentials() {
	return { user: env( 'DLI_E2E_ADMIN_USER', 'e2e-admin' ), password: env( 'DLI_E2E_ADMIN_PASSWORD', 'password' ) };
}

/**
 * Log in through wp-login.php and wait for the dashboard.
 *
 * @param {import('@playwright/test').Page} page     Page.
 * @param {string}                          user     User name.
 * @param {string}                          password Password.
 */
async function login( page, user, password ) {
	// Kept only to explain a failure: the requests of the login and their status.
	const requests = [];
	const started = Date.now();
	page.on( 'request', ( request ) => {
		if ( 'POST' === request.method() ) {
			requests.push( `POST started ${ request.url() } at +${ Date.now() - started } ms` );
		}
	} );
	page.on( 'requestfailed', ( request ) => {
		requests.push( `FAILED ${ request.method() } ${ request.url() } (${ ( request.failure() || {} ).errorText }) at +${ Date.now() - started } ms` );
	} );
	page.on( 'response', ( response ) => {
		const request = response.request();
		if ( 'POST' === request.method() || /wp-login\.php|\/wp-admin\/$/.test( response.url() ) ) {
			requests.push( `${ request.method() } ${ response.status() } ${ response.url() } at +${ Date.now() - started } ms` );
		}
	} );

	await page.goto( '/wp-login.php' );
	await page.locator( '#user_login' ).fill( user );
	await page.locator( '#user_pass' ).fill( password );
	await page.locator( '#wp-submit' ).click();

	try {
		await expect( page ).toHaveURL( /\/wp-admin\// );
	} catch ( error ) {
		const wordpressMessage = await page.locator( '#login_error' ).innerText().catch( () => '(no #login_error on the page)' );
		throw new Error(
			`Login of "${ user }" did not reach wp-admin after ${ Date.now() - started } ms. ` +
			`URL: ${ page.url() }. WordPress says: ${ wordpressMessage.replace( /\s+/g, ' ' ) }. ` +
			`Requests: ${ requests.join( ' | ' ) }\n${ error.message }`
		);
	}
}

/**
 * Log in as the site administrator (DLI_E2E_ADMIN_USER, DLI_E2E_ADMIN_PASSWORD).
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function loginAsAdmin( page ) {
	const { user, password } = getAdminCredentials();
	await login( page, user, password );
}

/**
 * Log in as the Super Editor (DLI_E2E_SUPER_EDITOR_USER, DLI_E2E_SUPER_EDITOR_PASSWORD).
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function loginAsSuperEditor( page ) {
	await login(
		page,
		env( 'DLI_E2E_SUPER_EDITOR_USER', 'e2e-super-editor' ),
		env( 'DLI_E2E_SUPER_EDITOR_PASSWORD', 'password' )
	);
}

/**
 * Log in as the Editor (DLI_E2E_EDITOR_USER, DLI_E2E_EDITOR_PASSWORD).
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function loginAsEditor( page ) {
	await login( page, env( 'DLI_E2E_EDITOR_USER', 'e2e-editor' ), env( 'DLI_E2E_EDITOR_PASSWORD', 'password' ) );
}

module.exports = { getAdminCredentials, login, loginAsAdmin, loginAsSuperEditor, loginAsEditor };
