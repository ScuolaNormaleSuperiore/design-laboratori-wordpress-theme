// The cards of the subpages of a base page. The pages are in the baseline
// (tests/e2e/README.md, "Contenuti di esempio").
const { test, expect } = require( '@playwright/test' );

test( 'the link of a card leads to the subpage', async ( { page } ) => {
	await page.goto( '/pagina-e2e-con-sottopagine/' );

	await page.locator( '#sottosezioni' ).getByRole( 'link', { name: /Sottopagina E2E uno/ } ).click();

	await expect( page ).toHaveURL( /\/pagina-e2e-con-sottopagine\/sottopagina-e2e-uno\/$/ );
	await expect( page.getByText( 'Testo della sottopagina uno.' ) ).toBeVisible();
} );

test( 'the link of a navigation card is underlined only when pointed at or reached with the keyboard', async ( { page } ) => {
	await page.goto( '/pagina-e2e-con-sottopagine/' );
	const link = page.locator( '#sottosezioni article.card-nav' ).first().getByRole( 'link' );
	const underline = () => link.evaluate( ( element ) => getComputedStyle( element ).textDecorationLine );

	await expect( link ).toBeVisible();
	expect( await underline(), 'at rest the link is not underlined' ).toBe( 'none' );

	await link.hover();
	expect( await underline(), 'pointed at, it is underlined' ).toBe( 'underline' );
	await page.mouse.move( 0, 0 );
	expect( await underline() ).toBe( 'none' );

	// Reached with the keyboard it is underlined too: the change does not depend on the color.
	await page.keyboard.press( 'Tab' );
	await link.focus();
	await expect( link ).toBeFocused();
	expect( await underline(), 'with the focus, it is underlined' ).toBe( 'underline' );
} );
