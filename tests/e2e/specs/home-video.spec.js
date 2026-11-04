// The homepage section "Video": each row of Sezioni HP has its own address, so two copies show two players. The
// content of the player (identifiers, button, label) is checked by DLI_Home_Video_Integration_Test; here is what only
// the browser shows: the field of the row, the name of the closed row (the title, or the code of the video), the page without
// script errors, one shared function for the two buttons and the player on a phone. The site is shared by the whole
// run: the test adds the rows and takes them away at the end.
const { test, expect } = require( '@playwright/test' );
const { loginAsAdmin } = require( '../support/auth' );

const SECTIONS = '/wp-admin/admin.php?page=homepage_sections';
const ROWS = '#site_sections_repeat > .cmb-repeatable-grouping';
const URL_A = 'https://www.youtube.com/watch?v=aaaaaaaaaaa';
const URL_B = 'https://youtu.be/bbbbbbbbbbb';

/**
 * Add a row of the section Video at the end of Sezioni HP, without saving.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {string}                          url   Address of the video.
 * @param {string}                          title Title in Italian, or an empty string.
 * @return {Promise<import('@playwright/test').Locator>} The row.
 */
async function addFilledRow( page, url, title = '' ) {
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const row = page.locator( ROWS ).last();
	await row.locator( 'select[name$="[id]"]' ).selectOption( 'video_section' );
	await row.locator( 'input[type="radio"][name$="[enabled]"][value="true"]' ).check();
	await row.locator( 'input[type="radio"][name$="[show_title]"][value="true"]' ).check();
	await row.locator( 'input[name$="[video_url]"]' ).fill( url );
	if ( title ) {
		await row.locator( 'input[name$="[title]"]' ).fill( title );
	}
	return row;
}

/**
 * Take the last rows away from Sezioni HP in one visit and save.
 *
 * @param {import('@playwright/test').Page} page  Page.
 * @param {number}                          count How many rows.
 */
async function removeLastSections( page, count ) {
	await page.goto( SECTIONS );
	const accept = ( dialog ) => dialog.accept();
	page.on( 'dialog', accept );
	for ( let removed = 0; removed < count; removed++ ) {
		await page.locator( ROWS ).last().locator( '> .cmb-remove-group-row' ).click();
	}
	await page.locator( 'input[name="submit-cmb"]' ).click();
	await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();
	page.off( 'dialog', accept );
}

test( 'two video sections show two players with their own address, and the closed row names the host', async ( { page } ) => {
	const errors = [];
	page.on( 'pageerror', ( error ) => errors.push( error.message ) );
	await loginAsAdmin( page );
	await page.goto( SECTIONS );

	// The address is a field of this section only. The probe row is only on the page: it is never saved.
	await page.locator( '#site_sections_repeat .cmb-add-group-row' ).click();
	const probe = page.locator( ROWS ).last();
	await probe.locator( 'select[name$="[id]"]' ).selectOption( 'news_section' );
	await expect( probe.locator( 'input[name$="[video_url]"]' ) ).toBeHidden();
	await probe.locator( 'select[name$="[id]"]' ).selectOption( 'video_section' );
	await expect( probe.locator( 'input[name$="[video_url]"]' ) ).toBeVisible();
	await page.goto( SECTIONS );

	try {
		await addFilledRow( page, URL_A );
		await addFilledRow( page, URL_B, 'Intervista' );
		await page.locator( 'input[name="submit-cmb"]' ).click();
		await expect( page.locator( '#site_sections_repeat' ) ).toBeVisible();

		// The name of a closed row says which video it is: the code of the video, or the title when it has one.
		await page.goto( SECTIONS );
		const rows = page.locator( ROWS );
		const count = await rows.count();
		const labelOf = ( row ) => row.locator( 'h3.cmb-group-title .dli-section-label' );
		await expect( labelOf( rows.nth( count - 2 ) ) ).toContainText( 'aaaaaaaaaaa' );
		await expect( labelOf( rows.nth( count - 1 ) ) ).toContainText( 'Intervista' );

		// The home: two sections, two players, each button asks the shared function for its own player and address.
		await page.goto( '/' );
		const first = page.locator( `#blocco-video-${ count - 1 }` );
		const second = page.locator( `#blocco-video-${ count }` );
		await expect( first ).toBeVisible();
		await expect( second ).toBeVisible();
		await expect( first.locator( 'video' ) ).toHaveCount( 1 );
		await expect( second.locator( 'video' ) ).toHaveCount( 1 );
		expect( await page.evaluate( () => typeof window.dliLoadYouTubeVideo ) ).toBe( 'function' );
		const buttons = page.locator( '[data-bs-accept-from="youtube.com"]' );
		await expect( buttons.nth( 0 ) ).toHaveAttribute( 'onclick', /aaaaaaaaaaa/ );
		await expect( buttons.nth( 1 ) ).toHaveAttribute( 'onclick', /youtu\.be\/bbbbbbbbbbb/ );

		// Nothing in the page stopped a script.
		expect( errors, 'no script error in the home' ).toEqual( [] );

		// On a phone the sections do not overflow.
		await page.setViewportSize( { width: 320, height: 800 } );
		await page.goto( '/' );
		await expect( first ).toBeVisible();
		const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
		expect( overflow ).toBeLessThanOrEqual( 0 );
	} finally {
		await page.setViewportSize( { width: 1280, height: 720 } );
		await removeLastSections( page, 2 );
	}
} );
