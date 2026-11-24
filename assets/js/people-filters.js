/**
 * Filters of the People page.
 *
 * The pure functions are the rules of the filters: the text search is the same rule that the server applies in
 * inc/people-filters.php (the cases of tests/unit/fixtures/people-search-cases.json are checked against both).
 * initPeopleTable() runs the table view: it filters, sorts and pages the rows in the browser and keeps the filters in
 * the address and in the links between the views. initPeopleSearch() runs the search field of the cards view, whose
 * filters are applied by the server: Enter and the clear button reload the page with the search.
 *
 * In the browser the functions are in window.DLIPeopleFilters and the table starts by itself when the page carries
 * window.DLI_PEOPLE_TABLE; in Node (the unit tests) they are the exports of the module.
 *
 * @package Design_Laboratori_Italia
 */
( function ( root ) {
	'use strict';

	/**
	 * The parameters of the address of each filter, by the name of the filter.
	 */
	var PARAMS = { struttura: 'struttura', level: 'level', type: 'tipologia', search: 'cerca' };

	/**
	 * The accented letters (in lower case) and the letter each one is compared as: the same table as
	 * dli_people_search_letters() in inc/people-filters.php.
	 */
	var LETTERS = ( function () {
		var groups  = {
			a: 'àáâãäåāăą',
			c: 'çćč',
			d: 'ďđ',
			e: 'èéêëēėęě',
			i: 'ìíîïīį',
			l: 'ľĺł',
			n: 'ñńň',
			o: 'òóôõöøōő',
			r: 'ŕř',
			s: 'śšş',
			t: 'ťţ',
			u: 'ùúûüūůűų',
			y: 'ýÿ',
			z: 'źżž',
		};
		var letters = {};
		Object.keys( groups ).forEach( function ( plain ) {
			Array.from( groups[ plain ] ).forEach( function ( letter ) {
				letters[ letter ] = plain;
			} );
		} );
		return letters;
	}() );

	/**
	 * The text of a search as it is compared: every run of spaces (also no-break spaces) is one space, the spaces
	 * around are removed, the letters are in lower case and without accents («Nicolo» finds «Nicolò»).
	 *
	 * @param {string} text The text.
	 * @return {string} The normalized text.
	 */
	function normalizeSearch( text ) {
		return Array.from( String( text || '' ).replace( /[\s ]+/g, ' ' ).trim().toLowerCase() ).map( function ( letter ) {
			return LETTERS[ letter ] || letter;
		} ).join( '' );
	}

	/**
	 * Whether a row of the People page (a person with one of its types) matches a search: the normalized text is part
	 * of «name surname», of «surname name», of the name of the type of the row or of the name of one of the structures
	 * of the person. An empty text matches every row.
	 *
	 * @param {string} search The text of the search.
	 * @param {Object} row    The row: name, surname, type (strings) and structures (names).
	 * @return {boolean} Whether the row matches.
	 */
	function rowMatchesSearch( search, row ) {
		var text    = normalizeSearch( search );
		var name    = row && row.name ? String( row.name ) : '';
		var surname = row && row.surname ? String( row.surname ) : '';
		var fields;

		if ( '' === text ) {
			return true;
		}
		fields = [ name + ' ' + surname, surname + ' ' + name, row && row.type ? String( row.type ) : '' ];
		if ( row && Array.isArray( row.structures ) ) {
			fields = fields.concat( row.structures );
		}
		return fields.some( function ( field ) {
			return -1 !== normalizeSearch( field ).indexOf( text );
		} );
	}

	/**
	 * Whether the structures of a person match the structure chosen: a structure includes its substructures.
	 *
	 * @param {string[]} slugs       Slugs of the structures of the person.
	 * @param {string}   selected    Slug of the structure chosen, '' for none.
	 * @param {Object}   descendants Slug of each structure → slugs of the structure and of all its substructures.
	 * @return {boolean} Whether they match.
	 */
	function structureMatches( slugs, selected, descendants ) {
		var accepted;

		if ( ! selected ) {
			return true;
		}
		accepted = descendants && Array.isArray( descendants[ selected ] ) ? descendants[ selected ] : [ selected ];
		return ( slugs || [] ).some( function ( slug ) {
			return -1 !== accepted.indexOf( slug );
		} );
	}

	/**
	 * Whether a row of the table matches all the filters.
	 *
	 * @param {Object} row         The row, as the server gives it.
	 * @param {Object} filters     struttura, level, type and search ('' or absent for none).
	 * @param {Object} descendants See structureMatches().
	 * @return {boolean} Whether the row matches.
	 */
	function rowMatchesFilters( row, filters, descendants ) {
		if ( ! structureMatches( row.structureSlugs, filters.struttura || '', descendants ) ) {
			return false;
		}
		if ( filters.level && -1 === ( row.tagSlugs || [] ).indexOf( filters.level ) ) {
			return false;
		}
		if ( filters.type && row.typeId !== filters.type ) {
			return false;
		}
		return rowMatchesSearch( filters.search || '', {
			name: row.name,
			surname: row.surname,
			type: row.type,
			structures: row.structureNames || [],
		} );
	}

	/**
	 * An address with the filters: an active filter is set, an empty one is removed, the old `cognome` is always
	 * removed (the search is `cerca`), and every other parameter is kept.
	 *
	 * @param {string} href    The address.
	 * @param {Object} filters struttura, level, type and search.
	 * @return {string} The new address.
	 */
	function filterUrl( href, filters ) {
		var url = new URL( href );

		Object.keys( PARAMS ).forEach( function ( name ) {
			var value = String( filters[ name ] || '' ).trim();
			if ( value ) {
				url.searchParams.set( PARAMS[ name ], value );
			} else {
				url.searchParams.delete( PARAMS[ name ] );
			}
		} );
		url.searchParams.delete( 'cognome' );
		return url.toString();
	}

	/**
	 * The address that the search field of the cards view opens: `cerca` set (or removed when empty), the old
	 * `cognome` removed, the other filters kept, and back to the first page of a paged address.
	 *
	 * @param {string} href  The address of the page.
	 * @param {string} value The text of the search.
	 * @return {string} The new address.
	 */
	function searchReloadUrl( href, value ) {
		var url  = new URL( href );
		var text = String( value || '' ).trim();

		url.pathname = url.pathname.replace( /\/page\/\d+\//, '/page/1/' );
		if ( text ) {
			url.searchParams.set( PARAMS.search, text );
		} else {
			url.searchParams.delete( PARAMS.search );
		}
		url.searchParams.delete( 'cognome' );
		return url.toString();
	}

	/**
	 * The search field of the cards view: Enter and its clear button reload the page with the search. Enter never
	 * sends the form, whose controls have no name: sent, it would open the page without any filter.
	 *
	 * @param {Object} doc The document.
	 * @param {Object} win The window.
	 */
	function initPeopleSearch( doc, win ) {
		var field = doc.querySelector( '[data-dli-people-search]' );

		if ( ! field ) {
			return;
		}
		field.addEventListener( 'keydown', function ( e ) {
			if ( 'Enter' === e.key ) {
				e.preventDefault();
				win.location.href = searchReloadUrl( win.location.href, field.value );
			}
		} );
		field.addEventListener( 'search', function () {
			if ( '' === field.value ) {
				win.location.href = searchReloadUrl( win.location.href, '' );
			}
		} );
	}

	/**
	 * Escape a text for HTML.
	 *
	 * @param {*} s The text.
	 * @return {string} The escaped text.
	 */
	function escHtml( s ) {
		return String( s )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#39;' );
	}

	/**
	 * Run the table view of the People page.
	 *
	 * @param {Object} cfg The data of the page (DLI_PEOPLE_TABLE): rows, structureDescendants, perPage,
	 *                     paginationEnabled, cols, spritesUrl, i18n.
	 * @param {Object} doc The document.
	 * @param {Object} win The window.
	 */
	function initPeopleTable( cfg, doc, win ) {
		var allRows      = cfg.rows || [];
		var descendants  = cfg.structureDescendants || {};
		var tbody        = doc.getElementById( 'people-tbody' );
		var countEl      = doc.getElementById( 'dliPeopleCount' );
		var paginEl      = doc.getElementById( 'people-pagination' );
		var selSort      = doc.getElementById( 'dliPeopleSort' );
		var selStruttura = doc.getElementById( 'selectTableStructure' );
		var selLevel     = doc.getElementById( 'selectTableLevel' );
		var selType      = doc.getElementById( 'selectTableType' );
		var inputSearch  = doc.getElementById( 'dliPeopleSearch' );
		var addressTimer = null;

		var state = {
			filters: {
				struttura: selStruttura ? selStruttura.value : '',
				level: selLevel ? selLevel.value : '',
				type: selType ? selType.value : '',
				search: inputSearch ? inputSearch.value : '',
			},
			sortCol: selSort ? selSort.value : 'sortKey',
			sortAsc: true,
			page: 1,
		};

		/**
		 * Keep the filters in the links between views immediately, independently of browser history updates.
		 */
		function updateViewLinks() {
			doc.querySelectorAll( '#personeform .btn-group a[href]' ).forEach( function ( link ) {
				var view = new URL( link.href, win.location.href ).searchParams.get( 'vista' );
				if ( 'chip' === view || 'tabella' === view ) {
					link.href = filterUrl( new URL( link.href, win.location.href ).toString(), state.filters );
				}
			} );
		}

		/**
		 * Update the address after a pause in filter changes, without letting a refused history write break filtering.
		 */
		function scheduleAddressUpdate() {
			if ( null !== addressTimer ) {
				win.clearTimeout( addressTimer );
			}
			addressTimer = win.setTimeout( function () {
				addressTimer = null;
				try {
					win.history.replaceState( win.history.state, '', filterUrl( win.location.href, state.filters ) );
				} catch ( error ) {
					// A browser may refuse history updates; the results and view links have already been updated.
				}
			}, 200 );
		}

		function applySort( rows ) {
			return rows.slice().sort( function ( a, b ) {
				var av  = String( a[ state.sortCol ] || '' );
				var bv  = String( b[ state.sortCol ] || '' );
				var cmp = av.localeCompare( bv, 'it', { sensitivity: 'base', numeric: true } );
				return state.sortAsc ? cmp : -cmp;
			} );
		}

		function renderTbody( pageRows ) {
			var html = '';
			var cols = cfg.cols || {};
			if ( ! pageRows.length ) {
				var colCount = 2
					+ ( cols.type ? 1 : 0 )
					+ ( cols.structure ? 1 : 0 )
					+ ( cols.tag ? 1 : 0 )
					+ ( cols.email ? 1 : 0 )
					+ ( cols.phone ? 1 : 0 );
				html = '<tr><td colspan="' + colCount + '" class="text-center py-4">'
					+ escHtml( cfg.i18n.noResults ) + '</td></tr>';
			} else {
				pageRows.forEach( function ( row ) {
					html += '<tr>';
					var nameText = escHtml( row.name + ' ' + row.surname );
					if ( row.link ) {
						var rel    = '_blank' === row.linkTarget ? ' rel="noopener noreferrer"' : '';
						var target = row.linkTarget ? ' target="' + escHtml( row.linkTarget ) + '"' : '';
						html += '<td><a href="'
							+ escHtml( row.link ) + '"' + target + rel + '>' + nameText + '</a></td>';
					} else {
						html += '<td>' + nameText + '</td>';
					}
					if ( cols.type )      { html += '<td>' + escHtml( row.type ) + '</td>'; }
					if ( cols.structure ) { html += '<td class="d-none d-md-table-cell">' + escHtml( row.structure ) + '</td>'; }
					if ( cols.tag )       { html += '<td class="d-none d-md-table-cell">' + escHtml( row.tag ) + '</td>'; }
					if ( cols.email ) {
						html += '<td class="d-none d-md-table-cell">';
						if ( row.email ) {
							// The address is already obfuscated with HTML entities by antispambot() on the server.
							html += '<a href="mailto:' + row.email + '">' + row.email + '</a>';
						}
						html += '</td>';
					}
					if ( cols.phone ) {
						html += '<td class="d-none d-md-table-cell">';
						if ( row.phone ) {
							html += '<a href="tel:' + escHtml( row.phoneHref ) + '">' + escHtml( row.phone ) + '</a>';
						}
						html += '</td>';
					}
					html += '</tr>';
				} );
			}
			tbody.innerHTML = html;
		}

		function updateFilterIndicators() {
			[ [ selStruttura, state.filters.struttura ], [ selType, state.filters.type ], [ selLevel, state.filters.level ] ].forEach( function ( pair ) {
				var wrapper = pair[ 0 ] ? pair[ 0 ].closest( '.select-wrapper' ) : null;
				if ( ! wrapper ) {
					return;
				}
				if ( '' !== pair[ 1 ] ) {
					wrapper.classList.add( 'dli-filter-active' );
				} else {
					wrapper.classList.remove( 'dli-filter-active' );
				}
			} );
		}

		function renderCount( total ) {
			if ( ! countEl ) {
				return;
			}
			countEl.textContent = total + ' ' + ( 1 === total ? cfg.i18n.resultSingular : cfg.i18n.resultPlural );
		}

		function renderPagination( totalPages ) {
			if ( ! paginEl ) {
				return;
			}
			if ( ! cfg.paginationEnabled || totalPages <= 1 ) {
				paginEl.innerHTML = '';
				return;
			}
			var html = '<ul class="pagination">';
			if ( state.page > 1 ) {
				html += '<li class="page-item"><button class="page-link" data-page="' + ( state.page - 1 ) + '"'
					+ ' aria-label="' + escHtml( cfg.i18n.prevPage ) + '">'
					+ '<svg class="icon icon-primary" aria-hidden="true"><use href="' + escHtml( cfg.spritesUrl ) + '#it-chevron-left"></use></svg>'
					+ '</button></li>';
			}
			for ( var p = 1; p <= totalPages; p++ ) {
				var isCurrent = ( p === state.page );
				html += '<li class="page-item' + ( isCurrent ? ' active' : '' ) + '">'
					+ '<button class="page-link" data-page="' + p + '"'
					+ ( isCurrent ? ' aria-current="page"' : '' ) + '>'
					+ p + '</button></li>';
			}
			if ( state.page < totalPages ) {
				html += '<li class="page-item"><button class="page-link" data-page="' + ( state.page + 1 ) + '"'
					+ ' aria-label="' + escHtml( cfg.i18n.nextPage ) + '">'
					+ '<svg class="icon icon-primary" aria-hidden="true"><use href="' + escHtml( cfg.spritesUrl ) + '#it-chevron-right"></use></svg>'
					+ '</button></li>';
			}
			html += '</ul>';
			paginEl.innerHTML = html;
			paginEl.querySelectorAll( 'button[data-page]' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					var table = doc.getElementById( 'people-table' );
					state.page = parseInt( btn.getAttribute( 'data-page' ), 10 );
					render();
					if ( table ) {
						table.scrollIntoView( { behavior: 'smooth', block: 'start' } );
					}
				} );
			} );
		}

		function render() {
			var filtered   = allRows.filter( function ( row ) {
				return rowMatchesFilters( row, state.filters, descendants );
			} );
			var sorted     = applySort( filtered );
			var total      = sorted.length;
			var perPage    = cfg.paginationEnabled ? cfg.perPage : total;
			var totalPages = cfg.paginationEnabled ? Math.max( 1, Math.ceil( total / perPage ) ) : 1;
			if ( state.page > totalPages ) {
				state.page = Math.max( 1, totalPages );
			}
			var offset   = ( state.page - 1 ) * perPage;
			var pageRows = cfg.paginationEnabled ? sorted.slice( offset, offset + perPage ) : sorted;
			renderTbody( pageRows );
			renderCount( total );
			renderPagination( totalPages );
			updateFilterIndicators();
		}

		/**
		 * Follow a filter control: draw the table and update view links immediately, then schedule the address update.
		 *
		 * @param {Object} control The control.
		 * @param {string} event   The event.
		 * @param {string} name    The name of the filter.
		 */
		function follow( control, event, name ) {
			if ( ! control ) {
				return;
			}
			control.addEventListener( event, function () {
				state.filters[ name ] = control.value;
				state.page = 1;
				render();
				updateViewLinks();
				scheduleAddressUpdate();
			} );
		}

		if ( selSort ) {
			selSort.addEventListener( 'change', function () {
				state.sortCol = selSort.value;
				state.page = 1;
				render();
			} );
		}
		follow( selStruttura, 'change', 'struttura' );
		follow( selLevel, 'change', 'level' );
		follow( selType, 'change', 'type' );
		follow( inputSearch, 'input', 'search' );
		if ( inputSearch ) {
			inputSearch.addEventListener( 'keydown', function ( e ) {
				if ( 'Enter' === e.key ) {
					e.preventDefault();
				}
			} );
		}

		render();
	}

	var api = {
		normalizeSearch: normalizeSearch,
		rowMatchesSearch: rowMatchesSearch,
		structureMatches: structureMatches,
		rowMatchesFilters: rowMatchesFilters,
		filterUrl: filterUrl,
		searchReloadUrl: searchReloadUrl,
		initPeopleTable: initPeopleTable,
		initPeopleSearch: initPeopleSearch,
	};

	if ( 'undefined' !== typeof module && module.exports ) {
		module.exports = api;
		return;
	}
	root.DLIPeopleFilters = api;
	if ( root.document && root.DLI_PEOPLE_TABLE ) {
		initPeopleTable( root.DLI_PEOPLE_TABLE, root.document, root );
	}
	if ( root.document ) {
		initPeopleSearch( root.document, root );
	}
}( this ) );
