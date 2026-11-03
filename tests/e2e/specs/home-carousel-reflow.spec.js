// The baseline has no events. Add a browser-only fixture with the same container
// and carousel variant as hp-list-event.php, before Bootstrap Italia initializes.
// The real theme assets must keep both arrows on screen and the next button usable.
const { test, expect } = require( '@playwright/test' );

for ( const path of [ '/', '/en/' ] ) {
	test( `event carousel of ${ path } fits narrow screens and its arrows work`, async ( { page } ) => {
		await page.route( ( url ) => url.pathname === path, async ( route ) => {
			const response = await route.fetch();
			const slides = [ 'First', 'Second', 'Third', 'Fourth' ].map( ( title ) =>
				`<li class="splide__slide"><article class="it-card"><h3>${ title }</h3><p>Event fixture</p></article></li>`
			).join( '' );
			const fixture = `<section id="carousel-reflow-fixture" class="section pt-5 pb-3"><div class="section-content"><div class="container"><div class="it-carousel-wrapper splide it-carousel-landscape-abstract-three-cols-arrow-visible" data-bs-carousel-splide role="region" aria-label="Events fixture"><div class="splide__track"><ul class="splide__list">${ slides }</ul></div></div></div></div></section>`;
			const html = ( await response.text() ).replace( /(<main\b[^>]*>)/i, `$1${ fixture }` );
			await route.fulfill( { response, body: html } );
		} );

		await page.setViewportSize( { width: 1280, height: 900 } );
		await page.goto( path );
		const carousel = page.locator( '#carousel-reflow-fixture .splide' );
		await expect( carousel ).toHaveClass( /is-initialized/ );

		for ( const width of [ 320, 360, 400, 576, 768, 992, 1024, 1280 ] ) {
			await page.setViewportSize( { width, height: 800 } );
			await expect( async () => {
				const bounds = await carousel.locator( '.splide__arrow' ).evaluateAll( ( arrows ) => arrows.map( ( arrow ) => {
					const rect = arrow.getBoundingClientRect();
					return { left: rect.left, right: rect.right, width: rect.width };
				} ) );
				expect( bounds ).toHaveLength( 2 );
				for ( const box of bounds ) {
					expect( box.width ).toBeGreaterThan( 0 );
					expect( box.left, `left edge at ${ width }px` ).toBeGreaterThanOrEqual( 0 );
					expect( box.right, `right edge at ${ width }px` ).toBeLessThanOrEqual( width );
				}
				const size = await page.evaluate( () => ( { scroll: document.documentElement.scrollWidth, viewport: document.documentElement.clientWidth } ) );
				expect( size.scroll, `page width at ${ width }px` ).toBeLessThanOrEqual( size.viewport + 1 );
			} ).toPass( { timeout: 5000 } );

			if ( 320 === width ) {
				const active = carousel.locator( '.splide__slide.is-active' );
				const firstId = await active.getAttribute( 'id' );
				await carousel.locator( '.splide__arrow--next' ).click();
				await expect( active ).not.toHaveAttribute( 'id', firstId );
				await carousel.locator( '.splide__arrow--prev' ).focus();
				await page.keyboard.press( 'Enter' );
				await expect( active ).toHaveAttribute( 'id', firstId );
			}
		}
	} );
}
