// Publication detail page: the password-protected publication and the English one. The complete and the minimal page are checked by DLI_Publication_Detail_Integration_Test.
// The publications are in the baseline (tests/e2e/README.md, "Contenuti di esempio").
const { test, expect } = require( '@playwright/test' );

const PROTECTED = '/pubblicazione/pubblicazione-e2e-protetta/';
const COMPLETE_EN = '/en/pubblicazione/e2e-complete-publication/';

test( 'a password-protected publication shows only the password form until the password is entered', async ( { page } ) => {
	const response = await page.goto( PROTECTED );

	expect( response.status() ).toBe( 200 );
	await expect( page.getByRole( 'heading', { level: 2, name: 'Pubblicazione E2E protetta' } ) ).toBeVisible();
	await expect( page.locator( 'input[name="post_password"]' ) ).toBeVisible();
	await expect( page.locator( 'body' ) ).not.toContainText( 'Testo riservato della pubblicazione E2E protetta.' );
	await expect( page.locator( 'body' ) ).not.toContainText( 'Autore Uno E2E' );
	await expect( page.locator( 'a[href="https://example.org/e2e-pubblicazione-protetta"]' ) ).toHaveCount( 0 );

	await page.locator( 'input[name="post_password"]' ).fill( 'e2e-password' );
	await Promise.all( [
		page.waitForURL( ( url ) => url.pathname === PROTECTED ),
		page.locator( 'input[name="post_password"]' ).press( 'Enter' ),
	] );
	await expect( page.locator( '#descrizione' ) ).toContainText( 'Testo riservato della pubblicazione E2E protetta.' );
	await expect( page.locator( '#autori' ) ).toContainText( 'Autore Uno E2E' );
} );

test( 'the English translation of a publication opens in English', async ( { page } ) => {
	const response = await page.goto( COMPLETE_EN );

	expect( response.status() ).toBe( 200 );
	await expect( page.locator( 'html' ) ).toHaveAttribute( 'lang', /^en/ );
	await expect( page.getByRole( 'heading', { level: 2, name: 'E2E complete publication' } ) ).toBeVisible();
	await expect( page.locator( '.it-category' ) ).toContainText( 'Article in journal' );
	await expect( page.locator( '#descrizione' ) ).toContainText( 'Sample description of the complete E2E publication.' );
} );
