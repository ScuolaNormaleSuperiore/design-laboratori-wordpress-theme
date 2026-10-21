// Configuration of the functional (E2E) tests. See tests/e2e/README.md.
const { defineConfig } = require( '@playwright/test' );

module.exports = defineConfig( {
	testDir: './tests/e2e/specs',
	outputDir: './tests/e2e/test-results',
	// The tests share one WordPress site: they run one at a time.
	workers: 1,
	fullyParallel: false,
	reporter: [
		[ 'list' ],
		[ 'html', { outputFolder: 'tests/e2e/playwright-report', open: 'never' } ],
	],
	use: {
		baseURL: process.env.DLI_E2E_BASE_URL || 'https://dli-wp-e2e.local',
		// Local sites use a certificate that the browser does not trust.
		ignoreHTTPSErrors: true,
		trace: 'retain-on-failure',
		screenshot: 'only-on-failure',
		video: 'retain-on-failure',
	},
} );
