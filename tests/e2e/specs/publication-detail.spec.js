// Publication detail page: complete, minimal and password-protected publications, and the English one.
// The publications are in the baseline (tests/e2e/README.md, "Contenuti di esempio").
const { test, expect } = require( '@playwright/test' );

const COMPLETE = '/pubblicazione/pubblicazione-e2e-completa/';
const MINIMAL = '/pubblicazione/pubblicazione-e2e-minima/';
const PROTECTED = '/pubblicazione/pubblicazione-e2e-protetta/';
const COMPLETE_EN = '/en/pubblicazione/e2e-complete-publication/';

test( 'a complete publication shows its type, year, authors, description and external link', async ( { page } ) => {
	const response = await page.goto( COMPLETE );

	expect( response.status() ).toBe( 200 );
	await expect( page.getByRole( 'heading', { level: 2, name: 'Pubblicazione E2E completa' } ) ).toBeVisible();
	await expect( page.locator( '.it-category' ) ).toContainText( 'Articolo in rivista' );
	await expect( page.locator( 'time[datetime="2024"]' ) ).toHaveText( '2024' );

	const authors = page.locator( '#autori' );
	await expect( authors.getByRole( 'heading', { name: 'Autori' } ) ).toBeVisible();
	await expect( authors.getByRole( 'link', { name: 'Autore Uno E2E' } ) ).toHaveAttribute( 'href', /\/persone\/autore-uno-e2e\/$/ );
	// The second author has the detail page disabled: the name is there, without a link.
	await expect( authors ).toContainText( 'Autore Due E2E' );
	await expect( authors.getByRole( 'link', { name: 'Autore Due E2E' } ) ).toHaveCount( 0 );

	await expect( page.locator( '#descrizione' ) ).toContainText( 'Descrizione di prova della pubblicazione E2E completa.' );

	const link = page.getByRole( 'link', { name: /Consulta la pubblicazione/ } );
	await expect( link ).toHaveAttribute( 'href', 'https://example.org/e2e-pubblicazione-completa' );
	await expect( link ).toHaveAttribute( 'target', '_blank' );
	await expect( link ).toHaveAttribute( 'rel', /noopener/ );
	await expect( link ).toContainText( 'si apre in una nuova finestra' );
} );

test( 'a publication with only title and year shows the cover placeholder and no empty blocks', async ( { page } ) => {
	const response = await page.goto( MINIMAL );

	expect( response.status() ).toBe( 200 );
	await expect( page.getByRole( 'heading', { level: 2, name: 'Pubblicazione E2E minima' } ) ).toBeVisible();
	await expect( page.locator( 'time[datetime="2023"]' ) ).toBeVisible();
	await expect( page.locator( '.pubblicazione-copertina-placeholder' ) ).toBeVisible();
	await expect( page.locator( '#autori' ) ).toHaveCount( 0 );
	await expect( page.locator( '#descrizione' ) ).toHaveCount( 0 );
	await expect( page.getByRole( 'link', { name: /Consulta la pubblicazione/ } ) ).toHaveCount( 0 );
} );

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
