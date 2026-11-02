// The page size asked for in the address (`per_page`) is input from the visitor: the archives keep it in range and never
// pass a huge, negative or non numeric value to the query. The page prints the size it uses in the button of the
// pagination ("12/pagina"), which is what these tests read. Projects, blog and places accept any size up to the largest
// offered value (96) and use the default for anything else; the archive of the projects accepts only the offered values.
// The default is 6 in all of them on the baseline.
const { test, expect } = require( '@playwright/test' );

const PAGES = [
	{ name: 'projects', path: '/progetti/', clamped: true },
	{ name: 'blog', path: '/il-blog/', clamped: true },
	{ name: 'places', path: '/luoghi/', clamped: true },
	{ name: 'author archive', path: '/author/e2e-admin/', clamped: true },
	{ name: 'archive of the projects', path: '/archivio-progetti/', clamped: false },
];

/**
 * The page size that the page uses, read from the button of the pagination.
 *
 * @param {import('@playwright/test').Page} page  Page.
 * @param {string}                          path  Path of the page.
 * @param {string}                          query Query string, with no question mark.
 * @return {Promise<string>} The text of the button, for example "12/pagina".
 */
async function usedSize( page, path, query ) {
	const response = await page.goto( `${ path }?${ query }` );
	expect( response.status() ).toBe( 200 );
	return ( await page.locator( '#pagerChanger' ).innerText() ).replace( /\s+/g, '' );
}

for ( const { name, path, clamped } of PAGES ) {
	test( `${ name }: a huge, negative or non numeric size is never used`, async ( { page } ) => {
		const largest = clamped ? '96/' : '6/';
		expect( await usedSize( page, path, 'per_page=99999999' ), 'a huge size' ).toMatch( new RegExp( `^${ largest }` ) );
		expect( await usedSize( page, path, 'per_page=99999999999999999999999' ), 'larger than an integer' ).toMatch( new RegExp( `^${ largest }` ) );
		expect( await usedSize( page, path, 'per_page=-1' ), 'negative' ).toMatch( /^6\// );
		expect( await usedSize( page, path, 'per_page=0' ), 'zero' ).toMatch( /^6\// );
		expect( await usedSize( page, path, 'per_page=abc' ), 'text' ).toMatch( /^6\// );
		expect( await usedSize( page, path, 'per_page=12abc' ), 'a number with text' ).toMatch( /^6\// );
		expect( await usedSize( page, path, 'per_page=1e3' ), 'an exponent' ).toMatch( /^6\// );
		expect( await usedSize( page, path, 'per_page[]=99999' ), 'a list' ).toMatch( /^6\// );
	} );

	test( `${ name }: an offered size is used`, async ( { page } ) => {
		expect( await usedSize( page, path, 'per_page=12' ) ).toMatch( /^12\// );
		expect( await usedSize( page, path, 'per_page=96' ) ).toMatch( /^96\// );
	} );
}

test( 'the archive of the projects uses only the offered sizes, the other pages keep an old size that is in range', async ( { page } ) => {
	expect( await usedSize( page, '/archivio-progetti/', 'per_page=7' ), 'not offered, in the archive' ).toMatch( /^6\// );
	expect( await usedSize( page, '/progetti/', 'per_page=7' ), 'not offered, in the projects' ).toMatch( /^7\// );
} );
