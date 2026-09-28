/**
 * Site search autocomplete — initializes Bootstrap Italia's Autocomplete
 * component (bootstrap.SelectAutocomplete) on the "Cerca" page search field,
 * fetching suggestions from the dli/v1/suggest REST route.
 *
 * Configuration is provided by inc/search-autocomplete.php via the
 * window.DLI_SEARCH_AUTOCOMPLETE inline script. Progressive enhancement:
 * without this script (or with the feature disabled), the page's <noscript>
 * fallback field keeps the search fully functional.
 *
 * @package Design_Laboratori_Italia
 */

( function () {
	'use strict';

	var cfg = window.DLI_SEARCH_AUTOCOMPLETE;
	var wrapper = document.getElementById( 'searchstringWrapper' );
	var spinner = document.getElementById( 'searchstringSpinner' );

	if ( ! cfg || ! wrapper || ! window.bootstrap || typeof window.bootstrap.SelectAutocomplete !== 'function' ) {
		return;
	}

	function showSpinner() {
		if ( spinner ) {
			spinner.classList.remove( 'd-none' );
		}
	}

	function hideSpinner() {
		if ( spinner ) {
			spinner.classList.add( 'd-none' );
		}
	}

	/**
	 * Minimal sprintf: supports plain %d/%s (sequential) and positional
	 * %1$s/%2$d tokens, mixed if needed.
	 *
	 * @param {string} template Message template.
	 * @param {...*}   args     Replacement values.
	 * @return {string}
	 */
	function formatMessage( template ) {
		var args = Array.prototype.slice.call( arguments, 1 );
		var seq = 0;
		return template.replace( /%(\d+\$)?[sd]/g, function ( match, pos ) {
			var idx = pos ? parseInt( pos, 10 ) - 1 : seq++;
			return args[ idx ];
		} );
	}

	// label (as shown in the suggestion list) -> { title, url }.
	var resultsMap = new Map();
	var latestQuery = null;
	var debounceTimer = null;
	var abortController = null;

	function buildLabel( item, seenLabels ) {
		var typeLabel = cfg.typeLabels[ item.type ];
		var label = typeLabel ? item.title + ' — ' + typeLabel : item.title;
		if ( seenLabels.has( label ) ) {
			var n = 2;
			while ( seenLabels.has( label + ' (' + n + ')' ) ) {
				n++;
			}
			label = label + ' (' + n + ')';
		}
		seenLabels.add( label );
		return label;
	}

	/**
	 * accessible-autocomplete `source` option: called on every input change
	 * respecting its own minLength gate.
	 *
	 * @param {string}   query           Current field value.
	 * @param {Function} populateResults Callback to hand the label list to.
	 */
	function source( query, populateResults ) {
		latestQuery = query;
		// Shown immediately (covers both the debounce wait below and the
		// network round trip), not just while fetch() is in flight: otherwise
		// the delay configured in "Attesa dopo l'ultimo tasto" would look like
		// nothing is happening at all.
		showSpinner();

		if ( debounceTimer ) {
			clearTimeout( debounceTimer );
		}

		debounceTimer = setTimeout( function () {
			if ( abortController ) {
				abortController.abort();
			}
			abortController = new AbortController();

			var url = cfg.endpoint + '?q=' + encodeURIComponent( query ) + '&lang=' + encodeURIComponent( cfg.lang );

			fetch( url, { signal: abortController.signal } )
				.then( function ( response ) {
					return response.ok ? response.json() : [];
				} )
				.then( function ( items ) {
					// Ignore a response that arrived after a newer request was made:
					// a newer source() call already turned the spinner back on, so
					// leave it alone rather than hiding it here.
					if ( query !== latestQuery ) {
						return;
					}
					hideSpinner();
					resultsMap.clear();
					var seenLabels = new Set();
					var labels = ( Array.isArray( items ) ? items : [] ).map( function ( item ) {
						var label = buildLabel( item, seenLabels );
						resultsMap.set( label, { title: item.title, url: item.url } );
						return label;
					} );
					populateResults( labels );
				} )
				.catch( function () {
					if ( query === latestQuery ) {
						hideSpinner();
						populateResults( [] );
					}
				} );
		}, cfg.delay );
	}

	/**
	 * accessible-autocomplete `onConfirm` option: fires when a suggestion is
	 * chosen (click, or Enter/Tab on a highlighted option). The field already
	 * contains the chosen label text at this point.
	 *
	 * @param {string} label Chosen suggestion text.
	 */
	function onConfirm( label ) {
		if ( ! label ) {
			return;
		}

		var result = resultsMap.get( label );
		var form = document.getElementById( 'ricercasitoform' );

		if ( 'open' === cfg.onSelect && result && result.url ) {
			window.location.assign( result.url );
			return;
		}

		// No usable link (onSelect = 'search', a suggestion without a url —
		// e.g. a person with a disabled detail page — or an unmatched label):
		// fall back to a normal full-text search instead.
		var input = document.getElementById( 'searchstring' );
		if ( input ) {
			input.value = result ? result.title : label;
		}
		if ( form ) {
			if ( typeof form.requestSubmit === 'function' ) {
				form.requestSubmit();
			} else {
				form.submit();
			}
		}
	}

	/**
	 * Attaches two listeners to the input the library generates:
	 *
	 * - a "keydown" fallback for Enter. accessible-autocomplete intercepts
	 *   Enter and calls preventDefault() whenever its suggestion menu is
	 *   open, even when no option is highlighted — which silently swallows
	 *   the keystroke instead of submitting the form as expected. This
	 *   restores that submission, without interfering when an option *is*
	 *   highlighted (in that case the library's own handler already takes
	 *   care of onConfirm).
	 * - an "input" listener that turns the spinner back off if the user
	 *   deletes the query back below the minimum length: source() (and so
	 *   the fetch that would otherwise hide it) is never called in that case.
	 */
	function attachInputHandlers() {
		var input = document.getElementById( 'searchstring' );
		if ( ! input || input.dataset.dliHandlersAttached ) {
			return;
		}
		input.dataset.dliHandlersAttached = '1';

		input.addEventListener(
			'keydown',
			function ( event ) {
				if ( 'Enter' !== event.key ) {
					return;
				}
				var listbox = wrapper.querySelector( '[role="listbox"]' );
				var menuVisible = listbox && 'none' !== window.getComputedStyle( listbox ).display;
				if ( ! menuVisible ) {
					return; // Menu closed: native form submission already works.
				}
				var highlighted = wrapper.querySelector( '[role="option"][aria-selected="true"]' );
				if ( highlighted ) {
					return; // Let the library's own handler run onConfirm.
				}
				event.preventDefault();
				var form = document.getElementById( 'ricercasitoform' );
				if ( form ) {
					if ( typeof form.requestSubmit === 'function' ) {
						form.requestSubmit();
					} else {
						form.submit();
					}
				}
			},
			true // Capture phase: run before the library's own bubble-phase handler.
		);

		input.addEventListener( 'input', function () {
			if ( input.value.length < cfg.minChars ) {
				latestQuery = null; // Any in-flight response for a longer query becomes stale.
				hideSpinner();
			}
		} );
	}

	new window.bootstrap.SelectAutocomplete( wrapper, {
		id: 'searchstring',
		name: 'searchstring',
		minLength: cfg.minChars,
		defaultValue: wrapper.dataset.defaultValue || '',
		showNoOptionsFound: true,
		source: source,
		onConfirm: onConfirm,
		tAssistiveHint: function () {
			return cfg.messages.assistiveHint;
		},
		tNoResults: function () {
			return cfg.messages.noResults;
		},
		tStatusNoResults: function () {
			return cfg.messages.statusNoResults;
		},
		tStatusQueryTooShort: function ( minLength ) {
			return formatMessage( cfg.messages.statusQueryTooShort, minLength );
		},
		tStatusResults: function ( length, contentSelectedOption ) {
			var template = 1 === length ? cfg.messages.statusResultsSingular : cfg.messages.statusResultsPlural;
			return formatMessage( template, length ) + ' ' + contentSelectedOption;
		},
		tStatusSelectedOption: function ( selectedOption, length, index ) {
			return formatMessage( cfg.messages.statusSelectedOption, selectedOption, length, index + 1 );
		},
	} );

	// The library creates the actual #searchstring input asynchronously
	// (plugins/select-autocomplete.js delays ~100ms); wait for it instead of
	// assuming it already exists.
	attachInputHandlers();
	var observer = new MutationObserver( function () {
		if ( document.getElementById( 'searchstring' ) ) {
			attachInputHandlers();
			observer.disconnect();
		}
	} );
	observer.observe( wrapper, { childList: true, subtree: true } );
} )();
