// The headings of the footer follow one order and none of them leads nowhere: the name of the laboratory is an h3, the
// tagline is a paragraph, the titles of the columns («Contatti», «Link utili», «Seguici su») are h4 in plain text, and
// «Newsletter» is an h3 with its link. No heading skips a level and no link of the footer is `#`. The titles that the
// baseline does not have (a menu of useful links, the social icons, the newsletter) are not asked for: those that exist
// are checked.
const { test, expect } = require( '@playwright/test' );

const PAGES = [ '/', '/en/' ];
const WIDTHS = [ 1280, 320 ];

for ( const path of PAGES ) {
	for ( const width of WIDTHS ) {
		test( `footer of ${ path } at ${ width }px: headings in order, in plain text, no link to #`, async ( { page } ) => {
			await page.setViewportSize( { width, height: 900 } );
			const response = await page.goto( path );
			expect( response.status() ).toBe( 200 );

			const footer = page.locator( '#footer-container' );
			await expect( footer ).toBeAttached();

			const headings = await footer.evaluate( ( element ) => [ ...element.querySelectorAll( 'h1, h2, h3, h4, h5, h6' ) ].map( ( heading ) => ( {
				level: Number( heading.tagName.slice( 1 ) ),
				text: heading.textContent.replace( /\s+/g, ' ' ).trim(),
				hasLink: null !== heading.querySelector( 'a' ),
				inBrand: null !== heading.closest( '.it-brand-wrapper' ),
			} ) ) );

			expect( headings.length, 'the footer has headings' ).toBeGreaterThan( 0 );

			// No heading skips a level.
			headings.forEach( ( heading, index ) => {
				if ( index > 0 ) {
					expect( heading.level, `"${ heading.text }" does not skip a level` ).toBeLessThanOrEqual( headings[ index - 1 ].level + 1 );
				}
			} );

			// The name of the laboratory is the first heading, an h3; the tagline is not a heading.
			expect( headings[ 0 ].inBrand ).toBe( true );
			expect( headings[ 0 ].level ).toBe( 3 );
			expect( headings.filter( ( heading ) => heading.inBrand ), 'the brand has one heading' ).toHaveLength( 1 );
			expect( await footer.locator( '.it-brand-text p' ).count(), 'the tagline is a paragraph' ).toBe( 1 );

			// The titles of the columns are h4 without a link; Newsletter is an h3 with its link.
			for ( const heading of headings.filter( ( item ) => ! item.inBrand && 4 === item.level ) ) {
				expect( heading.hasLink, `"${ heading.text }" is plain text` ).toBe( false );
			}
			for ( const heading of headings.filter( ( item ) => ! item.inBrand && /^newsletter$/i.test( item.text ) ) ) {
				expect( heading.level ).toBe( 3 );
				expect( heading.hasLink ).toBe( true );
			}

			// No link of the footer leads nowhere.
			await expect( footer.locator( 'a[href="#"]' ) ).toHaveCount( 0 );
		} );
	}
}
