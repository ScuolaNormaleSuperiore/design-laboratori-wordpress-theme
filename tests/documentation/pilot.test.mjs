import assert from 'node:assert/strict';
import { once } from 'node:events';
import { createRequire } from 'node:module';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import { createPreviewServer } from '../../SETUP/Documentazione/scripts/preview.mjs';

const { chromium } = createRequire(import.meta.url)('playwright');
const output = fileURLToPath(new URL('../../SETUP/Documentazione/.build/pilot/', import.meta.url));

for (const basePath of ['/', '/design-laboratori-wordpress-theme/']) {
	test(`generated Daux pilot: navigation, assets and search under ${basePath}`, async (t) => {
		const server = createPreviewServer(output, basePath);
		server.listen(0, '127.0.0.1');
		await once(server, 'listening');
		t.after(() => new Promise((resolve) => server.close(resolve)));
		const origin = `http://127.0.0.1:${server.address().port}`;
		const browser = await chromium.launch({ headless: true });
		t.after(() => browser.close());
		const page = await browser.newPage();
		const failures = [];
		page.on('pageerror', (error) => failures.push(error.message));
		page.on('requestfailed', (request) => failures.push(request.url()));
		page.on('response', (response) => { if (response.status() >= 400) failures.push(`${response.status()} ${response.url()}`); });
		await page.route('**/*', (route) => {
			if (!route.request().url().startsWith(origin + '/')) {
				failures.push(`Risorsa esterna: ${route.request().url()}`);
				return route.abort();
			}
			return route.continue();
		});
		await page.goto(origin + basePath, { waitUntil: 'networkidle' });
		await page.locator('a', { hasText: 'guida di prova' }).click();
		assert.equal(new URL(page.url()).pathname, basePath + 'guida.html');
		assert.equal(new URL(page.url()).hash, '#esempio');
		assert.equal(await page.locator('#esempio').count(), 1);
		assert.equal(await page.locator('img').evaluate((element) => element.complete && element.naturalWidth > 0), true);
		await page.locator('#search_input').fill('documentazionepilota');
		await page.locator('#search_input').press('Enter');
		await page.waitForFunction(() => [...document.querySelectorAll('a')].some((a) => /guida\.html/.test(a.href) && a.closest('.SearchResults, .SearchResults__entry, #search_results')));
		assert.deepEqual(failures, []);
	});
}
