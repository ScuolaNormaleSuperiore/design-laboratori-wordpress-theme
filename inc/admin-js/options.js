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
            return label;
        }, 'select[name$="[id]"], select[name$="[page_id]"]', 'change');
    }

    if ($j('#home_tabs_items_repeat').length) {
        dliShowRowLabels('#home_tabs_items_repeat', function($row) {
            var $input = $row.find('input[name$="[etichetta]"]').first();
            return $input.length ? $input.val() : null;
        }, 'input[name$="[etichetta]"]', 'input change');
    }
});
