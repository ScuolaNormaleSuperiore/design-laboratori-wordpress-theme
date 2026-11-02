// The breadcrumb of the hero stays on one line: with a long path the current item is cut with an ellipsis, the earlier
// items do not wrap or shrink, the page does not scroll sideways, and the whole text stays in the page for the screen
// readers. The baseline has a page three levels deep: «Pagina E2E con sottopagine» > «Sottopagina E2E uno» > «Nipote E2E».
const { test, expect } = require( '@playwright/test' );

const DEEP_PAGE = '/pagina-e2e-con-sottopagine/sottopagina-e2e-uno/nipote-e2e/';

test( 'on a phone the breadcrumb of a deep page is one line with the current item cut by an ellipsis', async ( { page } ) => {
	await page.setViewportSize( { width: 320, height: 800 } );
	const response = await page.goto( DEEP_PAGE );
	expect( response.status() ).toBe( 200 );

	const list = page.locator( '.it-hero-wrapper .breadcrumb' ).first();
	await expect( list ).toBeVisible();
	const items = list.locator( '> li' );
	await expect( items ).toHaveCount( 4 );

	// One line: every item starts at the same height, and the list does not wrap.
	const tops = await items.evaluateAll( ( elements ) => elements.map( ( element ) => Math.round( element.getBoundingClientRect().top ) ) );
	expect( new Set( tops ).size, 'all the items are on the same line' ).toBe( 1 );
	expect( await list.evaluate( ( element ) => getComputedStyle( element ).flexWrap ) ).toBe( 'nowrap' );

	// The current item is the one that is cut; the earlier ones keep their size.
	const current = items.last();
	await expect( current ).toHaveAttribute( 'aria-current', 'page' );
	expect( await current.evaluate( ( element ) => getComputedStyle( element ).textOverflow ) ).toBe( 'ellipsis' );
	expect( await current.evaluate( ( element ) => getComputedStyle( element ).whiteSpace ) ).toBe( 'nowrap' );
	expect( await items.first().evaluate( ( element ) => getComputedStyle( element ).flexShrink ) ).toBe( '0' );

	// The text is whole in the page, whatever is seen.
	await expect( current ).toHaveText( /Nipote E2E/ );

	const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
	expect( overflow ).toBeLessThanOrEqual( 0 );
} );

test( 'on a wide screen the breadcrumb is not cut', async ( { page } ) => {
	await page.setViewportSize( { width: 1280, height: 800 } );
	await page.goto( DEEP_PAGE );

	const current = page.locator( '.it-hero-wrapper .breadcrumb > li' ).last();
	await expect( current ).toBeVisible();
	const cut = await current.evaluate( ( element ) => element.scrollWidth > element.clientWidth );
	expect( cut, 'the whole path fits in the line' ).toBe( false );
} );
