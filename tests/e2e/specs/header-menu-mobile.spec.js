// The button that opens the main menu on a narrow screen: it is seen, inside the screen, reachable with the mouse and
// with the keyboard, and the open menu fits the screen. Bootstrap Italia puts the block with the logo and the name and the
// block with the button side by side in a row that does not wrap: when the first needs more than the width left, the
// button used to be pushed past the left edge of the screen (assets/css/main.css, ".it-nav-wrapper"). The widths are
// those of the narrowest phones; the test checks what must hold whatever the logo and the name of the site are.
const { test, expect } = require( '@playwright/test' );

const TOGGLER = 'button.custom-navbar-toggler';

for ( const width of [ 320, 360, 400 ] ) {
	test( `at ${ width }px the menu button is inside the screen and opens and closes the menu`, async ( { page } ) => {
		await page.setViewportSize( { width, height: 800 } );
		await page.goto( '/' );

		const toggler = page.locator( TOGGLER ).first();
		await expect( toggler ).toBeVisible();

		const box = await toggler.boundingBox();
		expect( box.x, 'the button does not start before the left edge' ).toBeGreaterThanOrEqual( 0 );
		expect( box.x + box.width, 'the button does not end after the right edge' ).toBeLessThanOrEqual( width );

		// The mouse reaches it: the click is not taken by another element.
		await toggler.click();
		await expect( toggler ).toHaveAttribute( 'aria-expanded', 'true' );
		const menu = page.locator( '#nav1' );
		await expect( menu ).toBeVisible();
		const menuBox = await menu.boundingBox();
		expect( menuBox.x ).toBeGreaterThanOrEqual( 0 );
		expect( menuBox.x + menuBox.width ).toBeLessThanOrEqual( width + 1 );

		// Every link of the open menu is inside the screen.
		const past = await page.evaluate( ( viewport ) => [ ...document.querySelectorAll( '#nav1 a.nav-link' ) ].filter( ( link ) => link.getBoundingClientRect().width > 0 && link.getBoundingClientRect().right > viewport + 1 ).length, width );
		expect( past, 'links of the menu past the right edge' ).toBe( 0 );

		// It closes with Escape. Bootstrap Italia starts to listen to Escape only after the opening animation (pressed at
		// once it does nothing, on any site, and a trial click does not wait for it), so Escape is pressed again until
		// the menu closes: what is checked is that it does close, not how fast the library is ready.
		await expect( async () => {
			await page.keyboard.press( 'Escape' );
			await expect( toggler ).toHaveAttribute( 'aria-expanded', 'false', { timeout: 1000 } );
		} ).toPass( { timeout: 10000 } );
	} );
}

test( 'the menu button can be reached and used with the keyboard', async ( { page } ) => {
	await page.setViewportSize( { width: 320, height: 800 } );
	await page.goto( '/' );

	const toggler = page.locator( TOGGLER ).first();
	await toggler.focus();
	await expect( toggler ).toBeFocused();
	await page.keyboard.press( 'Enter' );
	await expect( toggler ).toHaveAttribute( 'aria-expanded', 'true' );
} );
