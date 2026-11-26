// Restores the E2E site to the baseline (fixtures/baseline.sql) in the wp-env E2E
// environment (.wp-env.e2etest.json). Used by the global setup and teardown. See
// tests/e2e/README.md.
const { wp, wpSucceeds, THEME_DIR } = require( './wp' );

// Address of the E2E environment: also the siteurl stored in the baseline.
const SITE_URL = 'http://localhost:8892';

// Italian translations used by the specs. ACF OpenStreetMap Field has none.
const LANGUAGE_PLUGINS = [ 'advanced-custom-fields', 'polylang' ];

/**
 * Restore the database of the E2E site and make Italian its locale.
 *
 * Skipped when DLI_E2E_SKIP_RESET is set (useful while debugging a spec).
 * Needs the E2E environment running (tests/bin/e2e.mjs starts it).
 */
function resetSite() {
	if ( process.env.DLI_E2E_SKIP_RESET ) {
		console.log( '[e2e] reset skipped (DLI_E2E_SKIP_RESET is set)' );
		return;
	}

	try {
		const siteUrl = wp( [ 'option', 'get', 'siteurl' ] );
		if ( SITE_URL !== siteUrl ) {
			throw new Error( 'the site answers with siteurl ' + siteUrl + ', not ' + SITE_URL + '.' );
		}

		wp( [ 'db', 'import', THEME_DIR + '/tests/e2e/fixtures/baseline.sql' ] );
		installItalian();
		wp( [ 'eval-file', THEME_DIR + '/tests/e2e/support/force-italian.php' ] );
		console.log( '[e2e] E2E site restored to the baseline' );
	} catch ( error ) {
		throw new Error( 'Reset of the E2E site failed: ' + error.message );
	}
}

/**
 * Install the Italian language packs that are missing. They are files, not database
 * rows: once installed they survive the restore and the next runs.
 */
function installItalian() {
	if ( ! wpSucceeds( [ 'language', 'core', 'is-installed', 'it_IT' ] ) ) {
		wp( [ 'language', 'core', 'install', 'it_IT' ] );
	}
	for ( const plugin of LANGUAGE_PLUGINS ) {
		if ( ! wpSucceeds( [ 'language', 'plugin', 'is-installed', plugin, 'it_IT' ] ) ) {
			wp( [ 'language', 'plugin', 'install', plugin, 'it_IT' ] );
		}
	}
}

module.exports = { resetSite };
