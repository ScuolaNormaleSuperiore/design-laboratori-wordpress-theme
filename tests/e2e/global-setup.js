// Runs once before all the specs: the site starts from the baseline, then the admin
// area is warmed up.
const { request } = require( '@playwright/test' );
const { resetSite } = require( './support/reset' );
const { getAdminCredentials } = require( './support/auth' );

/**
 * Load the admin area once, so that no spec pays for it.
 *
 * The baseline has no transients. The first request to wp-admin after a restore makes
 * WordPress check core, plugin and theme updates, translations, browser and PHP
 * versions on the remote servers, one after the other: it takes 15 s and more, more
 * than the assertions wait. Here the wait is long and not part of any test.
 *
 * @param {string} baseURL URL of the E2E site.
 */
async function warmUpAdmin( baseURL ) {
	const { user, password } = getAdminCredentials();
	const context = await request.newContext( { baseURL, ignoreHTTPSErrors: true, timeout: 180000 } );

	try {
		await context.get( '/wp-login.php' );
		const response = await context.post( '/wp-login.php', {
			form: { log: user, pwd: password, 'wp-submit': 'Log In', redirect_to: '/wp-admin/', testcookie: '1' },
		} );
		if ( ! response.url().includes( '/wp-admin/' ) ) {
			console.warn( '[e2e] warm-up: the login did not reach wp-admin (' + response.url() + ')' );
		}
	} catch ( error ) {
		console.warn( '[e2e] warm-up failed: ' + error.message );
	} finally {
		await context.dispose();
	}
}

module.exports = async ( config ) => {
	resetSite();

	// Without the restore the site is already warm.
	if ( ! process.env.DLI_E2E_SKIP_RESET ) {
		await warmUpAdmin( config.projects[ 0 ].use.baseURL );
	}
};
