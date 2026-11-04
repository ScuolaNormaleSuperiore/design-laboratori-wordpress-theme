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

