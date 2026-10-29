// The cards of the subpages of a base page. The pages are in the baseline
// (tests/e2e/README.md, "Contenuti di esempio").
const { test, expect } = require( '@playwright/test' );

test( 'a page that asks for them shows the cards of its subpages before the text, under a title', async ( { page } ) => {
	const response = await page.goto( '/pagina-e2e-con-sottopagine/' );
	expect( response.status() ).toBe( 200 );

	const section = page.locator( '#sottosezioni' );
	await expect( section.getByRole( 'heading', { level: 2, name: 'In questa sezione' } ) ).toBeVisible();
	await expect( section.locator( 'article.card-nav' ) ).toHaveCount( 2 );

	// Menu order; the summary of the editor, or the cut text.
	await expect( section.locator( 'article.card-nav' ).first() ).toContainText( 'Sottopagina E2E uno' );
	await expect( section.locator( 'article.card-nav' ).first() ).toContainText( 'Riassunto della sottopagina uno.' );
	await expect( section.locator( 'article.card-nav' ).nth( 1 ) ).toContainText( 'parola30' );
	await expect( section.locator( 'article.card-nav' ).nth( 1 ) ).not.toContainText( 'parola31' );

	// Not the draft, not the protected one (and no «protected post» text), not the grandchild.
	await expect( section ).not.toContainText( 'Sottopagina E2E bozza' );
	await expect( section ).not.toContainText( 'Sottopagina E2E protetta' );
	await expect( section ).not.toContainText( 'protected post' );
	await expect( section ).not.toContainText( 'Nipote E2E' );

	// Before the text of the page.
	const textBox = await page.getByText( 'Testo della pagina E2E con sottopagine.' ).boundingBox();
	const sectionBox = await section.boundingBox();
	expect( sectionBox.y ).toBeLessThan( textBox.y );
} );

test( 'the link of a card leads to the subpage', async ( { page } ) => {
	await page.goto( '/pagina-e2e-con-sottopagine/' );

	await page.locator( '#sottosezioni' ).getByRole( 'link', { name: /Sottopagina E2E uno/ } ).click();

	await expect( page ).toHaveURL( /\/pagina-e2e-con-sottopagine\/sottopagina-e2e-uno\/$/ );
	await expect( page.getByText( 'Testo della sottopagina uno.' ) ).toBeVisible();
} );

test( 'no cards without subpages, or when the flag is off', async ( { page } ) => {
	await page.goto( '/pagina-e2e-senza-sottopagine/' );
	await expect( page.getByText( 'Testo della pagina senza sottopagine.' ) ).toBeVisible();
	await expect( page.locator( '#sottosezioni' ) ).toHaveCount( 0 );

	await page.goto( '/pagina-e2e-card-spente/' );
	await expect( page.getByText( 'Testo della pagina con card spente.' ) ).toBeVisible();
	await expect( page.locator( '#sottosezioni' ) ).toHaveCount( 0 );
	await expect( page.locator( 'article.card-nav' ) ).toHaveCount( 0 );
} );

test( 'on a narrow screen the cards do not overflow horizontally', async ( { page } ) => {
	await page.setViewportSize( { width: 320, height: 800 } );
	await page.goto( '/pagina-e2e-con-sottopagine/' );

	await expect( page.locator( '#sottosezioni' ) ).toBeVisible();
	const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
	expect( overflow ).toBeLessThanOrEqual( 0 );
} );
