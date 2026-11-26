// Configuration of the functional (E2E) tests. See tests/e2e/README.md.
const { defineConfig } = require( '@playwright/test' );

module.exports = defineConfig( {
	testDir: './tests/e2e/specs',
	outputDir: './tests/e2e/test-results',
	// The site is restored to the baseline before and after the run.
	globalSetup: './tests/e2e/global-setup.js',
	globalTeardown: './tests/e2e/global-teardown.js',
	// The tests share one WordPress site: they run one at a time.
	workers: 1,
	// One retry absorbs rare browser or network hiccups (e.g. ERR_NETWORK_CHANGED).
	retries: 1,
	fullyParallel: false,
	// Fail CI when a focused test would exclude the rest of the suite.
	forbidOnly: !! process.env.CI,
	// Login and an admin page take several seconds on the test site: the default 5 s of the
	// assertions is too tight. (The much slower first load after a restore is absorbed by
	// the warm-up of the global setup.)
	timeout: 60000,
	expect: { timeout: 15000 },
	reporter: [
		[ 'list' ],
		[ 'html', { outputFolder: 'tests/e2e/playwright-report', open: 'never' } ],
	],
	use: {
		// The wp-env E2E environment (.wp-env.e2etest.json): the baseline stores this address.
		baseURL: 'http://localhost:8892',
		// Recording video and trace on every test slows the browser down and made the login
		// fail now and then: record only when a test is retried.
		trace: 'on-first-retry',
		screenshot: 'only-on-failure',
		video: 'off',
	},
} );
