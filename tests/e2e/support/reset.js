// Restores the E2E site to the baseline (fixtures/baseline.sql). Used by the
// global setup and teardown. See tests/e2e/README.md.
const { execFileSync } = require( 'child_process' );
const path = require( 'path' );

/**
 * Restore the database of the E2E site.
 *
 * Skipped when DLI_E2E_SKIP_RESET is set (useful while debugging a spec).
 * Needs DLI_E2E_SITE_PATH (folder of the WordPress installation) and `php` on the PATH.
 */
function resetSite() {
	if ( process.env.DLI_E2E_SKIP_RESET ) {
		console.log( '[e2e] reset skipped (DLI_E2E_SKIP_RESET is set)' );
		return;
	}

	const sitePath = process.env.DLI_E2E_SITE_PATH;
	if ( ! sitePath ) {
		throw new Error(
			'DLI_E2E_SITE_PATH is not set: it must be the folder of the WordPress installation of the E2E site ' +
			'(or set DLI_E2E_SKIP_RESET=1 to skip the reset). See tests/e2e/README.md.'
		);
	}

	const script = path.join( __dirname, 'reset-baseline.php' );
	const baseline = path.join( __dirname, '..', 'fixtures', 'baseline.sql' );

	try {
		const output = execFileSync( 'php', [ script, sitePath, baseline ], {
			encoding: 'utf8',
			stdio: [ 'ignore', 'pipe', 'pipe' ],
		} );
		console.log( '[e2e] ' + output.trim() );
	} catch ( error ) {
		const detail = ( error.stderr || error.message || '' ).toString().trim();
		throw new Error( 'Reset of the E2E site failed. ' + detail );
	}
}

module.exports = { resetSite };
