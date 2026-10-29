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

    // ---- Repeatable groups that can be reordered (Configurazione -> Sezioni HP) ----
    //
    // Everything here works from the outside of CMB2: it listens to the public events of the library
    // (cmb2_add_row, cmb2_shift_rows_start, cmb2_shift_rows_complete) and moves around elements that CMB2
    // has already created, so that the library can be updated by copying the new version over the old one.
    // The arrows themselves come from the option 'sortable' => true of the group (inc/admin/options.php).
    // This file must run after cmb.init(): see the dependency 'cmb2-scripts' in dli_options_assets().

    // The groups that get the arrows in the header of the row (selector of the repeatable group).
    var dliReorderableGroups = ['#site_sections_repeat'];

    // The row of a group of sections shows the name of the chosen section after its title,
    // for example "Sezione 1 (Hero principale)". The label is a span of its own, so that the language
    // of the admin does not matter: nothing is read back from the title of CMB2, which rewrites the
    // title from scratch (cmb.resetGroupTitles) every time two rows are swapped.
    function dliUpdateSectionRowTitles() {
        $j('#site_sections_repeat > .cmb-repeatable-grouping').each(function() {
            var $row = $j(this);
            var $select = $row.find('select[name$="[id]"]').first();
            var $title = $row.find('h3.cmb-group-title').first();
            if (!$select.length || !$title.length) {
                return;
            }
            $title.find('.dli-section-label').remove();
            var label = $j.trim($select.find('option:selected').text());
            if (label) {
                $title.append($j('<span class="dli-section-label"></span>').text(' (' + label + ')'));
            }
        });
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
            $arrows.filter('.move-up').attr({ 'aria-label': l10n.moveUp || 'Move up', title: l10n.moveUp || 'Move up' });
            $arrows.filter('.move-down').attr({ 'aria-label': l10n.moveDown || 'Move down', title: l10n.moveDown || 'Move down' });
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
        dliUpdateSectionRowTitles();
        $j(document).on('change', '#site_sections_repeat select[name$="[id]"]', dliUpdateSectionRowTitles);
        $j('.cmb2-wrap > .cmb2-metabox').on('cmb2_add_row', dliUpdateSectionRowTitles);
        // After a swap CMB2 has rewritten the title of both rows, without the label.
        $j(document).on('cmb2_shift_rows_complete', '#site_sections_repeat', dliUpdateSectionRowTitles);
    }
});