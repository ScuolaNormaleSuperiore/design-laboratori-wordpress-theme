// An iframe pasted in the editor with a fixed width (a chart, a map, a form) must not make the page scroll
// sideways on a narrow screen. The baseline has no such content: the test adds, only in the browser response,
// the content column of page.php with a 500 px iframe, and uses the real CSS of the theme.
const { test, expect } = require( '@playwright/test' );

test( 'an iframe with a fixed width in the content fits narrow screens', async ( { page } ) => {
	await page.route( ( url ) => '/' === url.pathname, async ( route ) => {
		const response = await route.fetch();
		const fixture = '<section id="iframe-reflow-fixture" class="container"><div class="row"><div class="col-12 col-lg-8 offset-lg-1">' +
			'<p><iframe width="500" height="300" title="Iframe fixture" src="about:blank"></iframe></p>' +
			'</div></div></section>';
		const html = ( await response.text() ).replace( /(<main\b[^>]*>)/i, `$1${ fixture }` );
		await route.fulfill( { response, body: html } );
	} );

	await page.goto( '/' );
	const iframe = page.locator( '#iframe-reflow-fixture iframe' );
	await expect( iframe ).toBeAttached();

	for ( const width of [ 320, 360, 400 ] ) {
		await page.setViewportSize( { width, height: 800 } );
		await expect( async () => {
			const size = await page.evaluate( () => ( { scroll: document.documentElement.scrollWidth, viewport: document.documentElement.clientWidth } ) );
			expect( size.scroll, `page width at ${ width }px` ).toBeLessThanOrEqual( size.viewport + 1 );

			const box = await iframe.evaluate( ( element ) => {
				const frame = element.getBoundingClientRect();
				const column = element.closest( '.col-12' ).getBoundingClientRect();
				return { frameRight: frame.right, columnRight: column.right };
			} );
			expect( box.frameRight, `iframe inside its column at ${ width }px` ).toBeLessThanOrEqual( box.columnRight + 1 );
		} ).toPass( { timeout: 5000 } );
	}

	// On a wide screen the iframe keeps the width chosen in the editor.
	await page.setViewportSize( { width: 1280, height: 900 } );
	await expect( async () => {
		expect( Math.round( ( await iframe.boundingBox() ).width ) ).toBe( 500 );
	} ).toPass( { timeout: 5000 } );
} );
