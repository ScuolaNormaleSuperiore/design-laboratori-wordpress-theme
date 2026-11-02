// The publications of the home page: the featured box and the list section, from the card to the detail page
// and from the type to the filtered archive. The options of the home are in the baseline
// (tests/e2e/README.md, "Contenuti di esempio").
const { test, expect } = require( '@playwright/test' );

const TITLE = 'Pubblicazione E2E completa';

test( 'the featured box and the list show the promoted publication with authors, type and year', async ( { page } ) => {
	await page.goto( '/' );

	for ( const scope of [ page.locator( '#blocco-card' ), page.locator( '#blocco-pubblicazioni' ) ] ) {
		const card = scope.locator( 'article', { hasText: TITLE } );
		await expect( card ).toHaveCount( 1 );
		await expect( card ).toContainText( 'Autore Uno E2E, Autore Due E2E' );
		await expect( card ).toContainText( 'Articolo in rivista' );
		// The list section shows the year; the box of the featured contents does not, like the playground.
		if ( 'blocco-pubblicazioni' === await scope.getAttribute( 'id' ) ) {
			await expect( card.locator( 'time' ) ).toHaveText( '2024' );
		} else {
			await expect( card.locator( 'time' ) ).toHaveCount( 0 );
		}
		await expect( card ).not.toContainText( 'Descrizione di prova della pubblicazione E2E completa.' );
	}
	await expect( page.getByRole( 'heading', { level: 3, name: 'Pubblicazioni in evidenza' } ) ).toBeVisible();
} );

test( 'the type of a publication is underlined only when pointed at or reached with the keyboard', async ( { page } ) => {
	await page.goto( '/' );
	const link = page.locator( '#blocco-pubblicazioni' ).getByRole( 'link', { name: /Articolo in rivista/ } );
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

	// The same in the box of the featured contents, not only in the list section.
	const boxLink = page.locator( '#blocco-card' ).getByRole( 'link', { name: /Articolo in rivista/ } );
	await page.mouse.move( 0, 0 );
	await boxLink.evaluate( ( element ) => element.blur() );
	expect( await boxLink.evaluate( ( element ) => getComputedStyle( element ).textDecorationLine ) ).toBe( 'none' );
} );

test( 'from the home the title leads to the detail page', async ( { page } ) => {
	await page.goto( '/' );

	await page.locator( '#blocco-pubblicazioni' ).getByRole( 'link', { name: TITLE, exact: true } ).click();

	await expect( page ).toHaveURL( /\/pubblicazione\/pubblicazione-e2e-completa\/$/ );
	await expect( page.getByRole( 'heading', { level: 2, name: TITLE } ) ).toBeVisible();
} );

test( 'from the home the type leads to the archive filtered by that type, and the button to the archive', async ( { page } ) => {
	await page.goto( '/' );

	await page.locator( '#blocco-pubblicazioni' ).getByRole( 'link', { name: /Articolo in rivista/ } ).click();

	await expect( page ).toHaveURL( /\/pubblicazioni\/\?tipologia(%5B|\[)0(%5D|\])=articolo-in-rivista$/ );
	await expect( page.getByRole( 'link', { name: TITLE, exact: true } ) ).toBeVisible();
	await expect( page.getByText( 'Pubblicazione E2E minima', { exact: true } ) ).toHaveCount( 0 );

	await page.goto( '/' );
	await page.locator( '#blocco-pubblicazioni' ).getByRole( 'link', { name: 'Tutte le pubblicazioni' } ).click();
	await expect( page ).toHaveURL( /\/pubblicazioni\/$/ );
} );

test( 'on a narrow screen the home publications do not overflow horizontally', async ( { page } ) => {
	await page.setViewportSize( { width: 320, height: 800 } );
	await page.goto( '/' );

	await expect( page.locator( '#blocco-pubblicazioni' ) ).toBeVisible();
	const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
	expect( overflow ).toBeLessThanOrEqual( 0 );
} );
