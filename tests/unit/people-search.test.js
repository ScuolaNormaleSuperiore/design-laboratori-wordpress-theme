// Unit tests of the text search of the People page, in assets/js/people-filters.js.
// The cases are in fixtures/people-search-cases.json, which DLI_People_Search_Unit_Test reads too: the JavaScript rule
// of the table and the PHP rule of the cards view give the same answer to every case.
const { test } = require( 'node:test' );
const assert = require( 'node:assert/strict' );
const path = require( 'node:path' );
const { normalizeSearch, rowMatchesSearch } = require( '../../assets/js/people-filters.js' );
const data = require( path.join( __dirname, 'fixtures', 'people-search-cases.json' ) );

for ( const item of data.cases ) {
	test( `shared case: ${ item.description }`, () => {
		assert.equal( rowMatchesSearch( item.search, data.rows[ item.row ] ), item.matches );
	} );
}

test( 'the text is normalized', () => {
	assert.equal( normalizeSearch( '  \t ' ), '' );
	assert.equal( normalizeSearch( '  Mario    ROSSI ' ), 'mario rossi' );
	assert.equal( normalizeSearch( 'NICCOLÒ' ), 'niccolo', 'Accents do not count.' );
} );

test( 'a row without some of its fields is read as if they were empty', () => {
	assert.equal( rowMatchesSearch( 'rossi', { surname: 'Rossi' } ), true );
	assert.equal( rowMatchesSearch( 'rossi', {} ), false );
	assert.equal( rowMatchesSearch( '', {} ), true );
} );
