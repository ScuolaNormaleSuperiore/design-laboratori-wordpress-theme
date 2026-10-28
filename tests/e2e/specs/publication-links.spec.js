// Where the title of a publication leads, in the list and in the site search, and the attachment button.
// The publications are in the baseline (tests/e2e/README.md, "Contenuti di esempio").
const { test, expect } = require( '@playwright/test' );

const LIST = '/pubblicazioni/?per_page=20';
const NEW_WINDOW = /si apre in una nuova finestra/;

test( 'in the list a title leads to the detail page, the external site or the file, or is plain text', async ( { page } ) => {
	const response = await page.goto( LIST );
	expect( response.status() ).toBe( 200 );

	// Default choice: the detail page, same window.
	const detail = page.getByRole( 'link', { name: 'Pubblicazione E2E completa', exact: true } );
	await expect( detail ).toHaveAttribute( 'href', /\/pubblicazione\/pubblicazione-e2e-completa\/$/ );
	await expect( detail ).not.toHaveAttribute( 'target', '_blank' );

	// «Sito web»: the Url, new window, announced to screen readers.
	const external = page.getByRole( 'link', { name: /Pubblicazione E2E sito esterno/ } );
	await expect( external ).toHaveAttribute( 'href', 'https://example.org/e2e-sito-esterno' );
	await expect( external ).toHaveAttribute( 'target', '_blank' );
	await expect( external ).toHaveAccessibleName( NEW_WINDOW );

	// «Link ad allegato»: the PDF.
	const attached = page.getByRole( 'link', { name: /Pubblicazione E2E con allegato/ } );
	await expect( attached ).toHaveAttribute( 'href', /\/2026\/10\/e2e-articolo\.pdf$/ );
	await expect( attached ).toHaveAttribute( 'target', '_blank' );

	// «Sito web» without Url: the title is there, but it is not a link.
	await expect( page.getByText( 'Pubblicazione E2E senza link', { exact: true } ) ).toBeVisible();
	await expect( page.getByRole( 'link', { name: /Pubblicazione E2E senza link/ } ) ).toHaveCount( 0 );
} );

test( 'the list card shows the authors, not the description, and not those of a protected publication', async ( { page } ) => {
	await page.goto( LIST );

	// «Autore Uno E2E» is author of the complete publication and of the protected one: only the first card lists it.
	await expect( page.locator( '.it-card-text', { hasText: 'Autore Uno E2E' } ) ).toHaveCount( 1 );
	await expect( page.locator( 'body' ) ).not.toContainText( 'Descrizione di prova della pubblicazione E2E completa.' );
	await expect( page.locator( 'body' ) ).not.toContainText( 'Testo riservato della pubblicazione E2E protetta.' );
} );

test( 'the site search lists a publication without link as plain text and marks external links', async ( { page } ) => {
	const response = await page.goto( '/ricerca-sito/?searchstring=Pubblicazione+E2E&cercasito_nonce_field=e2e&selected_contents%5B%5D=pubblicazione' );
	expect( response.status() ).toBe( 200 );

	const results = page.locator( '#risultati' );
	await expect( results.getByRole( 'link', { name: /Pubblicazione E2E sito esterno/ } ) ).toHaveAttribute( 'href', 'https://example.org/e2e-sito-esterno' );
	await expect( results.getByRole( 'link', { name: /Pubblicazione E2E sito esterno/ } ) ).toHaveAttribute( 'target', '_blank' );
	await expect( results ).toContainText( 'Pubblicazione E2E senza link' );
	await expect( results.getByRole( 'link', { name: /Pubblicazione E2E senza link/ } ) ).toHaveCount( 0 );
} );

test( 'the detail page of a publication with a file offers the download, with its type', async ( { page } ) => {
	const response = await page.goto( '/pubblicazione/pubblicazione-e2e-con-allegato/' );
	expect( response.status() ).toBe( 200 );

	const download = page.getByRole( 'link', { name: /Scarica la pubblicazione/ } );
	await expect( download ).toHaveAttribute( 'href', /\/2026\/10\/e2e-articolo\.pdf$/ );
	await expect( download ).toHaveAttribute( 'target', '_blank' );
	await expect( download ).toContainText( '(PDF' );
	// No external link was filled in: only the download button.
	await expect( page.getByRole( 'link', { name: /Consulta la pubblicazione/ } ) ).toHaveCount( 0 );
} );

test( 'a publication that leads to its site still has a detail page', async ( { page } ) => {
	const response = await page.goto( '/pubblicazione/pubblicazione-e2e-sito-esterno/' );

	expect( response.status() ).toBe( 200 );
	await expect( page.getByRole( 'heading', { level: 2, name: 'Pubblicazione E2E sito esterno' } ) ).toBeVisible();
	await expect( page.getByRole( 'link', { name: /Consulta la pubblicazione/ } ) ).toHaveAttribute( 'href', 'https://example.org/e2e-sito-esterno' );
} );
