// Login helpers of the E2E tests. Credentials come from environment variables
// and are never stored in the repository.
const { expect } = require( '@playwright/test' );

/**
 * Read a mandatory environment variable.
 *
 * @param {string} name Variable name.
 * @return {string} Value.
 */
function requireEnv( name ) {
	const value = process.env[ name ];
	if ( ! value ) {
		throw new Error( `Environment variable ${ name } is not set (see tests/e2e/README.md).` );
	}
	return value;
}

/**
 * Log in through wp-login.php and wait for the dashboard.
 *
 * @param {import('@playwright/test').Page} page     Page.
 * @param {string}                          user     User name.
 * @param {string}                          password Password.
 */
async function login( page, user, password ) {
	await page.goto( '/wp-login.php' );
	await page.locator( '#user_login' ).fill( user );
	await page.locator( '#user_pass' ).fill( password );
	await page.locator( '#wp-submit' ).click();
	await expect( page ).toHaveURL( /\/wp-admin\// );
}

/**
 * Log in as the site administrator (DLI_E2E_ADMIN_USER, DLI_E2E_ADMIN_PASSWORD).
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function loginAsAdmin( page ) {
	await login( page, requireEnv( 'DLI_E2E_ADMIN_USER' ), requireEnv( 'DLI_E2E_ADMIN_PASSWORD' ) );
}

module.exports = { login, loginAsAdmin };
