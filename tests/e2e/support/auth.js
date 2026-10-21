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
	await login( page, env( 'DLI_E2E_ADMIN_USER', 'e2e-admin' ), env( 'DLI_E2E_ADMIN_PASSWORD', 'password' ) );
}

module.exports = { login, loginAsAdmin };
