// Unit tests of the filters of the People table, in assets/js/people-filters.js: the pure rules (structure with its
// substructures, all the filters together, the address) and the table itself on a small fake page, without a browser.
// The text search has its own cases in people-search.test.js.
const { test } = require( 'node:test' );
const assert = require( 'node:assert/strict' );
const { structureMatches, rowMatchesFilters, filterUrl, initPeopleTable } = require( '../../assets/js/people-filters.js' );

const DESCENDANTS = { scienze: [ 'scienze', 'fisica' ], fisica: [ 'fisica' ], 'centro-dati': [ 'centro-dati' ] };

/**
 * A row of the table, as the server gives it.
 *
 * @param {Object} values Values that replace the default ones.
 * @return {Object} The row.
 */
function row( values ) {
	return Object.assign(
		{
			name: 'Mario',
			surname: 'Rossi',
			sortKey: 'rossi mario',
			type: 'Professore',
			typeSort: 'professore',
			typeId: '1',
			structure: 'Laboratorio di Fisica',
			structureSort: 'laboratorio di fisica',
			structureNames: [ 'Laboratorio di Fisica' ],
			structureSlugs: [ 'fisica' ],
			tag: '',
			tagSort: '',
			tagSlugs: [],
		},
		values
	);
}

test( 'a structure includes its substructures', () => {
	assert.equal( structureMatches( [ 'fisica' ], '', DESCENDANTS ), true, 'No structure chosen.' );
	assert.equal( structureMatches( [ 'fisica' ], 'fisica', DESCENDANTS ), true );
	assert.equal( structureMatches( [ 'fisica' ], 'scienze', DESCENDANTS ), true, 'The parent finds the child.' );
	assert.equal( structureMatches( [ 'scienze' ], 'fisica', DESCENDANTS ), false, 'The child does not find the parent.' );
	assert.equal( structureMatches( [ 'centro-dati' ], 'scienze', DESCENDANTS ), false );
	assert.equal( structureMatches( [ 'altro' ], 'altro', {} ), true, 'A structure without a map is itself.' );
	assert.equal( structureMatches( [], 'scienze', DESCENDANTS ), false );
} );

test( 'all the filters together', () => {
	const mario = row( { tagSlugs: [ 'senior' ] } );
	const filters = { struttura: 'scienze', level: 'senior', type: '1', search: 'ross' };
	assert.equal( rowMatchesFilters( mario, filters, DESCENDANTS ), true );
	assert.equal( rowMatchesFilters( mario, Object.assign( {}, filters, { level: 'junior' } ), DESCENDANTS ), false );
	assert.equal( rowMatchesFilters( mario, Object.assign( {}, filters, { type: '2' } ), DESCENDANTS ), false );
	assert.equal( rowMatchesFilters( mario, Object.assign( {}, filters, { struttura: 'centro-dati' } ), DESCENDANTS ), false );
	assert.equal( rowMatchesFilters( mario, Object.assign( {}, filters, { search: 'bianchi' } ), DESCENDANTS ), false );
	assert.equal( rowMatchesFilters( mario, { struttura: '', level: '', type: '', search: '' }, DESCENDANTS ), true, 'No filter.' );
	assert.equal( rowMatchesFilters( mario, { search: 'fisica' }, DESCENDANTS ), true, 'The search finds the structure.' );
	assert.equal( rowMatchesFilters( mario, { search: 'mario rossi' }, DESCENDANTS ), true, 'The search finds name and surname.' );
} );

test( 'the address keeps the active filters, writes cerca and drops the old cognome', () => {
	const start = 'https://example.test/persone/?vista=tabella&cognome=rossi&lang=it&struttura=vecchia';
	const url = new URL( filterUrl( start, { struttura: 'scienze', level: 'senior', type: '3', search: 'Rossi Mario' } ) );
	assert.equal( url.searchParams.get( 'struttura' ), 'scienze' );
	assert.equal( url.searchParams.get( 'level' ), 'senior' );
	assert.equal( url.searchParams.get( 'tipologia' ), '3' );
	assert.equal( url.searchParams.get( 'cerca' ), 'Rossi Mario' );
	assert.equal( url.searchParams.has( 'cognome' ), false );
	assert.equal( url.searchParams.get( 'vista' ), 'tabella', 'Other parameters are kept.' );
	assert.equal( url.searchParams.get( 'lang' ), 'it' );

	const cleared = new URL( filterUrl( url.toString(), { struttura: '', level: '', type: '', search: '   ' } ) );
	for ( const name of [ 'struttura', 'level', 'tipologia', 'cerca' ] ) {
		assert.equal( cleared.searchParams.has( name ), false, `${ name } is removed when cleared.` );
	}
	assert.equal( cleared.searchParams.get( 'vista' ), 'tabella' );
} );

/**
 * A small fake page with the controls of the table, the view links and the address.
 *
 * @param {Object} values Initial values of the controls.
 * @return {Object} The page: document, window, controls, body of the table and links.
 */
function fakePage( values ) {
	const listeners = {};
	const timers = new Map();
	const historyCalls = [];
	let now = 0;
	let timerId = 0;
	const control = ( id, value ) => ( {
		value,
		addEventListener: ( event, callback ) => {
			listeners[ id + ':' + event ] = callback;
		},
		closest: () => null,
	} );
	const controls = {
		selectTableStructure: control( 'selectTableStructure', values.struttura || '' ),
		selectTableLevel: control( 'selectTableLevel', values.level || '' ),
		selectTableType: control( 'selectTableType', values.type || '' ),
		dliPeopleSearch: control( 'dliPeopleSearch', values.search || '' ),
		dliPeopleSort: control( 'dliPeopleSort', 'sortKey' ),
	};
	const tbody = { innerHTML: '' };
	const count = { textContent: '' };
	const links = [ { href: 'https://example.test/persone/?vista=chip' } ];
	const location = { href: 'https://example.test/persone/?vista=tabella' };
	const document = {
		getElementById: ( id ) => ( Object.assign( { 'people-tbody': tbody, dliPeopleCount: count }, controls )[ id ] || null ),
		querySelectorAll: () => links,
	};
	const window = {
		location,
		history: {
			state: null,
			replaceState: ( state, title, url ) => {
				historyCalls.push( { state, title, url } );
				location.href = url;
			},
		},
		setTimeout: ( callback, delay ) => {
			const id = ++timerId;
			timers.set( id, { callback, due: now + delay } );
			return id;
		},
		clearTimeout: ( id ) => timers.delete( id ),
	};
	const fire = ( id, event ) => listeners[ id + ':' + event ]();
	const tick = ( delay ) => {
		now += delay;
		for ( const [ id, timer ] of timers ) {
			if ( timer.due <= now ) {
				timers.delete( id );
				timer.callback();
			}
		}
	};
	return { document, window, controls, tbody, count, links, location, fire, tick, historyCalls };
}

const CONFIG = {
	rows: [
		row(),
		row( { name: 'Anna', surname: 'Bianchi', sortKey: 'bianchi anna', typeId: '2', type: 'Ricercatore', typeSort: 'ricercatore', structure: 'Centro Dati', structureSort: 'centro dati', structureNames: [ 'Centro Dati' ], structureSlugs: [ 'centro-dati' ], tagSlugs: [ 'senior' ] } ),
	],
	structureDescendants: DESCENDANTS,
	cols: {},
	i18n: { resultSingular: 'persona trovata', resultPlural: 'persone trovate', noResults: 'Nessuna persona' },
	paginationEnabled: false,
};

test( 'the table starts from the filters of the address', () => {
	const page = fakePage( { struttura: 'scienze' } );
	initPeopleTable( CONFIG, page.document, page.window );
	assert.match( page.tbody.innerHTML, /Rossi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Bianchi/ );
	assert.equal( page.count.textContent, '1 persona trovata' );
} );

test( 'each filter updates the rows, the address and the view links', () => {
	const page = fakePage( {} );
	initPeopleTable( CONFIG, page.document, page.window );
	assert.match( page.tbody.innerHTML, /Rossi/ );
	assert.match( page.tbody.innerHTML, /Bianchi/ );

	page.controls.selectTableStructure.value = 'scienze';
	page.fire( 'selectTableStructure', 'change' );
	assert.doesNotMatch( page.tbody.innerHTML, /Bianchi/ );
	page.tick( 200 );
	assert.equal( new URL( page.location.href ).searchParams.get( 'struttura' ), 'scienze' );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.get( 'struttura' ), 'scienze' );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.get( 'vista' ), 'chip', 'The link keeps its view.' );

	page.controls.selectTableStructure.value = '';
	page.fire( 'selectTableStructure', 'change' );
	page.controls.selectTableLevel.value = 'senior';
	page.fire( 'selectTableLevel', 'change' );
	assert.match( page.tbody.innerHTML, /Bianchi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Rossi/ );
	page.tick( 200 );
	assert.equal( new URL( page.location.href ).searchParams.has( 'struttura' ), false, 'A cleared filter leaves the address.' );
	assert.equal( new URL( page.location.href ).searchParams.get( 'level' ), 'senior' );

	page.controls.selectTableLevel.value = '';
	page.fire( 'selectTableLevel', 'change' );
	page.controls.dliPeopleSearch.value = 'centro';
	page.fire( 'dliPeopleSearch', 'input' );
	assert.match( page.tbody.innerHTML, /Bianchi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Rossi/ );
	page.tick( 200 );
	assert.equal( new URL( page.location.href ).searchParams.get( 'cerca' ), 'centro' );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.get( 'cerca' ), 'centro' );
} );

test( 'the type filter keeps working as before (regression of the type kept between the views)', () => {
	const page = fakePage( { type: '1' } );
	initPeopleTable( CONFIG, page.document, page.window );
	assert.match( page.tbody.innerHTML, /Rossi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Bianchi/ );

	page.controls.selectTableType.value = '2';
	page.fire( 'selectTableType', 'change' );
	assert.match( page.tbody.innerHTML, /Bianchi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Rossi/ );
	page.tick( 200 );
	assert.equal( new URL( page.location.href ).searchParams.get( 'tipologia' ), '2' );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.get( 'tipologia' ), '2' );

	page.controls.selectTableType.value = '';
	page.fire( 'selectTableType', 'change' );
	assert.match( page.tbody.innerHTML, /Rossi/ );
	assert.match( page.tbody.innerHTML, /Bianchi/ );
	page.tick( 200 );
	assert.equal( new URL( page.location.href ).searchParams.has( 'tipologia' ), false );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.has( 'tipologia' ), false );
} );

test( 'search updates results and view links immediately but writes history only after a pause', () => {
	const page = fakePage( {} );
	const historyState = { preserved: true };
	page.window.history.state = historyState;
	initPeopleTable( CONFIG, page.document, page.window );

	for ( const value of [ 'r', 'ro', 'rossi' ] ) {
		page.controls.dliPeopleSearch.value = value;
		page.fire( 'dliPeopleSearch', 'input' );
	}
	assert.match( page.tbody.innerHTML, /Rossi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Bianchi/ );
	assert.equal( page.count.textContent, '1 persona trovata' );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.get( 'cerca' ), 'rossi' );
	assert.equal( page.historyCalls.length, 0, 'Typing does not write history immediately.' );
	page.tick( 199 );
	assert.equal( page.historyCalls.length, 0 );

	page.controls.dliPeopleSearch.value = 'bianchi';
	page.fire( 'dliPeopleSearch', 'input' );
	assert.match( page.tbody.innerHTML, /Bianchi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Rossi/ );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.get( 'cerca' ), 'bianchi' );
	page.tick( 199 );
	assert.equal( page.historyCalls.length, 0, 'Further typing restarts the pause.' );
	page.tick( 1 );
	assert.equal( page.historyCalls.length, 1, 'The burst produces one history write.' );
	assert.equal( new URL( page.location.href ).searchParams.get( 'cerca' ), 'bianchi' );
	assert.equal( page.historyCalls[ 0 ].state, historyState, 'Existing history state is preserved.' );
} );

test( 'filters keep working and view links stay current when the browser refuses history updates', () => {
	const page = fakePage( {} );
	initPeopleTable( CONFIG, page.document, page.window );
	const originalUrl = page.location.href;
	page.window.history.replaceState = () => {
		const error = new Error( 'History update refused' );
		error.name = 'SecurityError';
		throw error;
	};

	page.controls.dliPeopleSearch.value = 'bianchi';
	assert.doesNotThrow( () => page.fire( 'dliPeopleSearch', 'input' ) );
	assert.doesNotThrow( () => page.tick( 200 ) );
	assert.match( page.tbody.innerHTML, /Bianchi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Rossi/ );
	assert.equal( page.count.textContent, '1 persona trovata' );
	assert.equal( new URL( page.links[ 0 ].href ).searchParams.get( 'cerca' ), 'bianchi' );
	assert.equal( page.location.href, originalUrl, 'A refused update leaves the address alone.' );

	page.controls.dliPeopleSearch.value = '';
	assert.doesNotThrow( () => page.fire( 'dliPeopleSearch', 'input' ) );
	page.controls.selectTableStructure.value = 'scienze';
	assert.doesNotThrow( () => page.fire( 'selectTableStructure', 'change' ) );
	assert.doesNotThrow( () => page.tick( 200 ) );
	assert.match( page.tbody.innerHTML, /Rossi/ );
	assert.doesNotMatch( page.tbody.innerHTML, /Bianchi/ );
	const link = new URL( page.links[ 0 ].href );
	assert.equal( link.searchParams.has( 'cerca' ), false );
	assert.equal( link.searchParams.get( 'struttura' ), 'scienze' );
	assert.equal( link.searchParams.get( 'vista' ), 'chip' );
} );

test( 'the search of the cards view reloads the page with cerca, from the first page', () => {
	const { searchReloadUrl } = require( '../../assets/js/people-filters.js' );
	const start = 'https://example.test/persone/page/3/?vista=chip&cognome=rossi&struttura=scienze';
	const url = new URL( searchReloadUrl( start, '  Rossi Mario ' ) );
	assert.equal( url.pathname, '/persone/page/1/', 'Back to the first page.' );
	assert.equal( url.searchParams.get( 'cerca' ), 'Rossi Mario' );
	assert.equal( url.searchParams.has( 'cognome' ), false, 'The old parameter is removed.' );
	assert.equal( url.searchParams.get( 'struttura' ), 'scienze', 'The other filters are kept.' );
	assert.equal( url.searchParams.get( 'vista' ), 'chip' );

	const cleared = new URL( searchReloadUrl( url.toString(), '' ) );
	assert.equal( cleared.searchParams.has( 'cerca' ), false, 'An empty search is removed.' );
	assert.equal( cleared.searchParams.get( 'struttura' ), 'scienze' );
} );

test( 'Enter and the clear button of the search field reload the cards view; the form is never sent', () => {
	const { initPeopleSearch } = require( '../../assets/js/people-filters.js' );
	const listeners = {};
	const field = {
		value: 'rossi',
		getAttribute: ( name ) => ( 'data-dli-people-search' === name ? 'cerca' : null ),
		addEventListener: ( event, callback ) => {
			listeners[ event ] = callback;
		},
	};
	const location = { href: 'https://example.test/persone/?struttura=scienze' };
	const document = { querySelector: ( selector ) => ( '[data-dli-people-search]' === selector ? field : null ) };
	initPeopleSearch( document, { location } );

	let prevented = false;
	listeners.keydown( { key: 'a', preventDefault: () => { prevented = true; } } );
	assert.equal( prevented, false, 'Other keys are left alone.' );
	assert.equal( location.href, 'https://example.test/persone/?struttura=scienze' );

	listeners.keydown( { key: 'Enter', preventDefault: () => { prevented = true; } } );
	assert.equal( prevented, true, 'Enter does not send the form, which has no named field and would lose the filters.' );
	assert.equal( new URL( location.href ).searchParams.get( 'cerca' ), 'rossi' );
	assert.equal( new URL( location.href ).searchParams.get( 'struttura' ), 'scienze' );

	field.value = '';
	listeners.search();
	assert.equal( new URL( location.href ).searchParams.has( 'cerca' ), false, 'The clear button removes the search.' );
} );
