let $j = jQuery.noConflict();
let form = $j('.cmb-form');

$j(function() {
    $j('#toplevel_page_dli_options').find('ul').addClass('hidden');

    form.data('serialize',form.serialize());
    $j('.nav-tab').each(function() {
        $j(this).click(function(e) {
            $j( "body" ).append('<div id="spinner-overlayer"></div>');
            if (form.serialize() !== form.data('serialize')) {
                $j('#spinner-overlayer').remove();
                e.preventDefault();
                e.stopPropagation();

                let link = $j(this).attr('href');

                let modal = $j('<div>').html("Attenzione, le informazioni inserite nel form non sono state salvate, si vuole proseguire comunque?");
                let args = {
                    title: "Attenzione!",
                    draggable: false,
                    resizable: false,
                    modal: true,
                    buttons: {
                        "Annulla": function() {
                            $j(this).dialog('destroy').remove();
                        },
                        "Continua": function() {
                            window.location.href = link;
                        }
                    }
                };
                modal.dialog(args);
            }
        });
    });

    form.submit(function() {
        $j( "body" ).append('<div id="spinner-overlayer"></div>');
    });

    // ---- Repeatable groups that can be reordered (Configurazione -> Sezioni HP, and the tabs of Layout HP) ----
    //
    // Everything here works from the outside of CMB2: it listens to the public events of the library
    // (cmb2_add_row, cmb2_shift_rows_start, cmb2_shift_rows_complete) and moves around elements that CMB2
    // has already created, so that the library can be updated by copying the new version over the old one.
    // The arrows themselves come from the option 'sortable' => true of the group (inc/admin/options.php).
    // This file must run after cmb.init(): dli_enqueue_options_script_after_cmb2() queues it from CMB2's
    // public cmb2_footer_enqueue hook.

    // The groups that get the arrows in the header of the row (selector of the repeatable group).
    var dliReorderableGroups = ['#site_sections_repeat', '#home_tabs_items_repeat'];

    // The row of a group shows a label after its title: the name of the chosen section ("Sezione 1 (Hero
    // principale)") or the label of a tab ("Tab 1 (Aree)"). The label is a span of its own, so that the language
    // of the admin does not matter: nothing is read back from the title of CMB2, which writes the title again from
    // scratch (cmb.resetGroupTitles) every time two rows are swapped and every time a row is removed, so the label
    // is added again after each of them. readLabel gets the row and gives the text, or null when the row has not
    // got its field yet; changeEvents are the events of the field that change the label.
    // The code of a video in its address (watch?v=CODE, youtu.be/CODE, /embed/CODE, /shorts/CODE, /live/CODE), or an
    // empty string when the address is empty, not complete yet or has no code.
    function dliVideoCode(address) {
        if (!address) {
            return '';
        }
        try {
            var url = new URL(address);
            var fromQuery = url.searchParams.get('v');
            if (fromQuery) {
                return fromQuery;
            }
            var parts = url.pathname.split('/').filter(Boolean);
            if (/(^|\.)youtu\.be$/.test(url.hostname)) {
                return parts[0] || '';
            }
            var known = ['embed', 'shorts', 'live', 'v'];
            return parts.length > 1 && known.indexOf(parts[0]) !== -1 ? parts[1] : '';
        } catch (error) {
            return '';
        }
    }

    // A section that can be in the home only once (the list comes from the server: window.dliHpSections.single, built
    // from the catalog) is not offered again in the other rows of Sezioni HP: its option is disabled in the menu of every
    // row that has not chosen it. The option a row has chosen is never disabled, so that a row saved before this rule
    // (one single section twice) keeps its value: the browser does not send a disabled option. A row that is added
    // starts from the first section that is still available. The rows already saved are never changed.
    function dliLimitSectionChoices(groupSelector) {
        var single = (window.dliHpSections && window.dliHpSections.single) || [];
        if (!single.length || !$j(groupSelector).length) {
            return;
        }
        var selectSelector = groupSelector + ' > .cmb-repeatable-grouping select[name$="[id]"]';

        function takenValues($except) {
            return $j(selectSelector).not($except).map(function() {
                return $j(this).val();
            }).get();
        }

        function update() {
            $j(selectSelector).each(function() {
                var $select = $j(this);
                var own = $select.val();
                var taken = takenValues($select);
                $select.find('option').each(function() {
                    this.disabled = this.value !== own && -1 !== single.indexOf(this.value) && -1 !== taken.indexOf(this.value);
                });
            });
        }

        function startFromFirstAvailable() {
            var $select = $j(selectSelector).last();
            var taken = takenValues($select);
            var $first = $select.find('option').filter(function() {
                return -1 === single.indexOf(this.value) || -1 === taken.indexOf(this.value);
            }).first();
            if ($first.length && $first.val() !== $select.val()) {
                // The change updates the fields that depend on the section, as if the editor had chosen it.
                $select.val($first.val()).trigger('change');
            }
            update();
        }

        update();
        $j(document).on('change', selectSelector, update);
        $j('.cmb2-wrap > .cmb2-metabox').on('cmb2_add_row', startFromFirstAvailable);
        $j(document).on('cmb2_remove_row', groupSelector, update);
    }

    function dliShowRowLabels(groupSelector, readLabel, fieldSelector, changeEvents) {
        function update() {
            $j(groupSelector + ' > .cmb-repeatable-grouping').each(function() {
                var $row = $j(this);
                var $title = $row.find('h3.cmb-group-title').first();
                var label = readLabel($row);
                if (null === label || !$title.length) {
                    return;
                }
                $title.find('.dli-section-label').remove();
                label = $j.trim(label);
                if (label) {
                    $title.append($j('<span class="dli-section-label"></span>').text(' (' + label + ')'));
                }
            });
        }

        update();
        // The label may depend on more than one field of the row: fieldSelector can be a list, and each selector of the
        // list is searched inside the group (a list put after the group would apply the group to its first item only).
        var delegated = $j.map(fieldSelector.split(','), function(selector) {
            return groupSelector + ' ' + $j.trim(selector);
        }).join(', ');
        $j(document).on(changeEvents, delegated, update);
        $j('.cmb2-wrap > .cmb2-metabox').on('cmb2_add_row', update);
        $j(document).on('cmb2_shift_rows_complete cmb2_remove_row', groupSelector, update);
    }

    // cmb.shiftRows renames the fields of the two rows in two separate steps. For radio buttons this makes
    // the selected option of one row take, for an instant, the same name as the option still selected in the
    // other row: the browser allows only one selected radio per name and clears one of the two, and the lost
    // value is not sent when the form is saved. The state of the radios of both rows is saved before the swap
    // (cmb2_shift_rows_start, before any renaming) and applied again after it (cmb2_shift_rows_complete),
    // when the new names are stable.
    var dliShiftRowsRadioSnapshot = null;

    function dliCaptureRadioStates($row) {
        var state = {};
        $row.find('.inside input[type="radio"]').each(function() {
            var $radio = $j(this);
            if ($radio.is(':checked')) {
                state[$radio.attr('name')] = $radio.val();
            }
        });
        return state;
    }

    function dliRestoreRadioStates($scope, snapshot) {
        $j.each(snapshot.radios, function(oldName, value) {
            var newName = oldName.replace('[' + snapshot.oldIterator + ']', '[' + snapshot.newIterator + ']');
            $scope.find('input[type="radio"][name="' + newName + '"][value="' + value + '"]').prop('checked', true);
        });
    }

    // CMB2 puts the arrows inside the body of each row, that is hidden when the row is closed. They are moved
    // to the header of the row, always visible, before the "x" that removes the row (the CSS of the arrows is
    // in inc/admin-css/style-admin.css). cmb.shiftRows finds the row starting from the clicked button, so moving
    // the links in the page does not change what they do.
    function dliMoveShiftRowsToHeader($group) {
        $group.children('.cmb-repeatable-grouping').each(function() {
            var $row = $j(this);
            if ($row.hasClass('dli-shift-rows-moved')) {
                return;
            }
            var $arrows = $row.find('.cmb-shift-rows');
            var $remove = $row.children('.cmb-remove-group-row');
            if (!$arrows.length || !$remove.length) {
                return;
            }
            $row.addClass('dli-shift-rows-moved');
            $arrows.insertBefore($remove);
            // The arrows of CMB2 have only an icon: give them a name for screen readers (and a tooltip).
            var l10n = window.dliOptionsL10n || {};
            var isTabs = $group.is('#home_tabs_items_repeat');
            var moveUp = (isTabs ? l10n.moveTabUp : l10n.moveUp) || 'Move up';
            var moveDown = (isTabs ? l10n.moveTabDown : l10n.moveDown) || 'Move down';
            $arrows.filter('.move-up').attr({ 'aria-label': moveUp, title: moveUp });
            $arrows.filter('.move-down').attr({ 'aria-label': moveDown, title: moveDown });
        });
    }

    $j.each(dliReorderableGroups, function(index, selector) {
        var $group = $j(selector);
        if (!$group.length) {
            return;
        }

        $j(document).on('cmb2_shift_rows_start', selector, function(evt, $link, $from, $goto) {
            var fromIterator = $from.attr('data-iterator');
            var toIterator = $goto.attr('data-iterator');
            // cmb.shiftRows renames the rows by changing only the attribute data-iterator, while jQuery keeps in
            // its cache the number it read (or set) earlier, for example when the row was added in the page. The
            // rich text editors of the rows (cmb2-wysiwyg.js) are built again after the swap from that cached
            // number, so the second swap of a row put its description under the wrong tab. Forgetting the cached
            // number makes jQuery read the attribute again. If CMB2 fixes this, the two lines can be removed.
            $from.removeData('iterator');
            $goto.removeData('iterator');
            dliShiftRowsRadioSnapshot = {
                from: { radios: dliCaptureRadioStates($from), oldIterator: fromIterator, newIterator: toIterator },
                goto: { radios: dliCaptureRadioStates($goto), oldIterator: toIterator, newIterator: fromIterator }
            };
        });

        $j(document).on('cmb2_shift_rows_complete', selector, function() {
            if (dliShiftRowsRadioSnapshot) {
                dliRestoreRadioStates($group, dliShiftRowsRadioSnapshot.from);
                dliRestoreRadioStates($group, dliShiftRowsRadioSnapshot.goto);
                dliShiftRowsRadioSnapshot = null;
            }
        });

        dliMoveShiftRowsToHeader($group);
        $j('.cmb2-wrap > .cmb2-metabox').on('cmb2_add_row', function() {
            dliMoveShiftRowsToHeader($group);
        });
    });

    if ($j('#site_sections_repeat').length) {
        dliLimitSectionChoices('#site_sections_repeat');
        dliShowRowLabels('#site_sections_repeat', function($row) {
            var $select = $row.find('select[name$="[id]"]').first();
            if (!$select.length) {
                return null;
            }
            var label = $select.find('option:selected').text();
            // Several indexes of a page can be in the home: each is told apart by the page it shows. The choices of the
            // pages indent the subpages with dashes, which are not part of the name. Without a page there is only the name.
            var $page = $row.find('select[name$="[page_id]"]').first();
            if ('page_index_section' === $select.val() && $page.length && $page.val()) {
                var pageName = $j.trim($page.find('option:selected').text()).replace(/^(?:\u2014\s*)+/, '');
                if (pageName) {
                    label = $j.trim(label) + ' \u2014 ' + pageName;
                }
            }
            // Several banner sections can be in the home: each is told apart by its mode and, in the automatic one, the
            // number of banners when the row says it.
            if ('banners_section' === $select.val()) {
                var $mode = $row.find('input[type="radio"][name$="[banner_mode]"]:checked').first();
                var detail = $mode.length ? $j.trim($row.find('label[for="' + $mode.attr('id') + '"]').text()) : '';
                var count = $j.trim($row.find('input[name$="[max_items]"]').val());
                if (count && 'manual' !== $mode.val()) {
                    detail = $j.trim(detail + ' ' + count);
                }
                if (detail) {
                    label = $j.trim(label) + ' \u2014 ' + detail;
                }
            }
            // Several videos can be in the home, all of them on the same site: each is told apart by its title in Italian
            // when it has one, otherwise by the code of the video in its address.
            var $video = $row.find('input[name$="[video_url]"]').first();
            if ('video_section' === $select.val() && $video.length) {
                var videoTitle = $j.trim($row.find('input[name$="[title]"]').first().val());
                var videoName = videoTitle || dliVideoCode($j.trim($video.val()));
                if (videoName) {
                    label = $j.trim(label) + ' \u2014 ' + videoName;
                }
            }
            return label;
        }, 'select[name$="[id]"], select[name$="[page_id]"], input[name$="[video_url]"], input[name$="[title]"], input[name$="[banner_mode]"], input[name$="[max_items]"]', 'change input');
    }

    if ($j('#home_tabs_items_repeat').length) {
        dliShowRowLabels('#home_tabs_items_repeat', function($row) {
            var $input = $row.find('input[name$="[etichetta]"]').first();
            return $input.length ? $input.val() : null;
        }, 'input[name$="[etichetta]"]', 'input change');
    }
});

// ---- Configurazione -> Area riservata ----
//
// While "Gestione area riservata" is No, the settings that depend on it (rows with the class
// dli-reserved-area-dependent, set in inc/reserved-area.php) are disabled; the server ignores them anyway.
// A disabled field is not sent with the form and CMB2 would save it as empty, so the fields are enabled
// again just before the form is sent: their saved values are kept.
( function() {
	const switches = document.querySelectorAll( 'input[name="reserved_area_enabled"]' );
	if ( ! switches.length ) {
		return;
	}
	const dependentFields = document.querySelectorAll( '.dli-reserved-area-dependent input, .dli-reserved-area-dependent select' );

	const isEnabled = function() {
		const checked = document.querySelector( 'input[name="reserved_area_enabled"]:checked' );
		return null !== checked && 'true' === checked.value;
	};

	const update = function() {
		const disabled = ! isEnabled();
		dependentFields.forEach( function( field ) {
			field.disabled = disabled;
		} );
	};

	switches.forEach( function( input ) {
		input.addEventListener( 'change', update );
	} );

	const formElement = switches[ 0 ].form;
	if ( formElement ) {
		formElement.addEventListener( 'submit', function() {
			dependentFields.forEach( function( field ) {
				field.disabled = false;
			} );
		} );
	}

	update();
}() );
