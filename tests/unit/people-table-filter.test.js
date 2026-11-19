const { test } = require( 'node:test' );
const assert = require( 'node:assert/strict' );
const fs = require( 'node:fs' );
const vm = require( 'node:vm' );

test( 'the table type filter updates its results, address and view links', () => {
	const source = fs.readFileSync( 'template-parts/persone/view-tabella.php', 'utf8' );
	const script = source.slice( source.indexOf( '( function () {' ), source.indexOf( '</script>' ) );
	const listeners = {};
	const select = { value: '1', addEventListener: ( event, callback ) => { listeners[ event ] = callback; }, closest: () => null };
	const tbody = { innerHTML: '' };
	const links = [ { href: 'https://example.test/persone/?vista=chip&tipologia=1' } ];
	const location = { href: 'https://example.test/persone/?vista=tabella&tipologia=1' };
	const document = {
		getElementById: ( id ) => ( { selectTableType: select, 'people-tbody': tbody }[ id ] || null ),
		querySelectorAll: () => links,
	};
	const history = { state: null, replaceState: ( state, title, url ) => { location.href = url; } };
	const rows = [ { typeId: '1', sortKey: 'a', name: 'Prima' }, { typeId: '2', sortKey: 'b', name: 'Seconda' } ];
	const cfg = { rows, cols: {}, i18n: {}, paginationEnabled: false };
	vm.runInNewContext( script, { document, window: { location, history }, URL, DLI_PEOPLE_TABLE: cfg } );
	assert.match( tbody.innerHTML, /Prima/ );
	assert.doesNotMatch( tbody.innerHTML, /Seconda/ );
	select.value = '2';
	listeners.change();
	assert.match( tbody.innerHTML, /Seconda/ );
	assert.doesNotMatch( tbody.innerHTML, /Prima/ );
	assert.equal( new URL( location.href ).searchParams.get( 'tipologia' ), '2' );
	assert.equal( new URL( links[ 0 ].href ).searchParams.get( 'tipologia' ), '2' );
	select.value = '';
	listeners.change();
	assert.match( tbody.innerHTML, /Prima/ );
	assert.match( tbody.innerHTML, /Seconda/ );
	assert.equal( new URL( location.href ).searchParams.has( 'tipologia' ), false );
	assert.equal( new URL( links[ 0 ].href ).searchParams.has( 'tipologia' ), false );
} );
