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

    // "Sezioni HP": mostra nel titolo collassato di ogni riga il nome della
    // sezione scelta (es. "Sezione 1 (Hero principale)"), cosi' le righe si
    // riconoscono e si riordinano (con le frecce su/giu, vedi sotto) senza
    // doverle aprire una per una. Il titolo di base ("Sezione {#}") resta
    // quello generato da CMB2: qui si aggiunge solo l'etichetta tra
    // parentesi, letta dal testo dell'opzione selezionata nel campo
    // "Sezione" della stessa riga. Si rilegge il titolo di base dal testo
    // corrente (invece di tenerlo in cache) perche' dopo uno spostamento con
    // le frecce CMB2 (cmb.resetGroupTitles) riscrive il titolo da zero,
    // numero incluso, e un valore in cache andrebbe disallineato.
    function dliUpdateSectionRowTitles() {
        $j('#site_sections_repeat .cmb-repeatable-grouping').each(function() {
            var $row = $j(this);
            var $select = $row.find('select[name$="[id]"]').first();
            var $title = $row.find('> .cmb-group-title').first();
            if (!$select.length || !$title.length) {
                return;
            }
            var currentText = $j.trim($title.text());
            var baseMatch = currentText.match(/^Sezione\s+\d+/);
            var baseTitle = baseMatch ? baseMatch[0] : currentText;
            var label = $j.trim($select.find('option:selected').text());
            $title.text(label ? baseTitle + ' (' + label + ')' : baseTitle);
        });
    }

    // "Sezioni HP": cmb.shiftRows (cmb2.js) rinomina i campi delle due righe
    // coinvolte in due passaggi separati e sequenziali (prima tutti i campi
    // della riga che si sposta, poi tutti i campi dell'altra). Per i campi
    // radio/radio_inline (es. "Abilita la sezione", "Mostra il titolo",
    // "Sfondo") questo crea un istante in cui l'opzione selezionata di una
    // riga viene rinominata con lo stesso "name" dell'opzione ancora
    // selezionata nell'altra riga: il browser, per vincolo nativo dei radio
    // button (un solo selezionato per "name"), deseleziona una delle due.
    // Il valore perso non viene inviato nel form, da cui il warning "Undefined
    // array key" lato PHP al salvataggio successivo. Si rimedia salvando lo
    // stato (quale opzione era selezionata, per ogni campo radio) di entrambe
    // le righe PRIMA dello spostamento (evento 'cmb2_shift_rows_start', prima
    // di qualunque rinomina) e riapplicandolo DOPO (evento
    // 'cmb2_shift_rows_complete'), quando i nuovi "name" sono ormai stabili.
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

    function dliRestoreRadioStates(snapshot) {
        $j.each(snapshot.radios, function(oldName, value) {
            var newName = oldName.replace('[' + snapshot.oldIterator + ']', '[' + snapshot.newIterator + ']');
            $j('#site_sections_repeat').find(
                'input[type="radio"][name="' + newName + '"][value="' + value + '"]'
            ).prop('checked', true);
        });
    }

    $j(document).on('cmb2_shift_rows_start', '#site_sections_repeat', function(evt, $link, $from, $goto) {
        var fromIterator = $from.attr('data-iterator');
        var toIterator   = $goto.attr('data-iterator');
        dliShiftRowsRadioSnapshot = {
            from: { radios: dliCaptureRadioStates($from), oldIterator: fromIterator, newIterator: toIterator },
            goto: { radios: dliCaptureRadioStates($goto), oldIterator: toIterator, newIterator: fromIterator }
        };
    });

    $j(document).on('cmb2_shift_rows_complete', '#site_sections_repeat', function() {
        if (dliShiftRowsRadioSnapshot) {
            dliRestoreRadioStates(dliShiftRowsRadioSnapshot.from);
            dliRestoreRadioStates(dliShiftRowsRadioSnapshot.goto);
            dliShiftRowsRadioSnapshot = null;
        }
    });

    // "Sezioni HP": CMB2 aggiunge di suo le frecce su/giu per riordinare le
    // righe (gruppo "sortable"), ma le inserisce dentro il corpo apribile di
    // ogni riga (vicino al pulsante "Rimuovi"), quindi di default sono
    // utilizzabili solo con la riga aperta. Le spostiamo nell'intestazione
    // sempre visibile, a sinistra, prima sia del nome sezione sia della "x"
    // di rimozione (con spaziatura dedicata per evitare click accidentali
    // sulla "x"), cosi' si puo' riordinare senza aprire la riga. cmb.shiftRows (cmb2.js) risale al
    // '.cmb-repeatable-grouping' partendo dal bottone cliccato, quindi
    // spostare i link nel DOM non ne altera il funzionamento. Richiede che
    // options.js giri dopo cmb.init() (vedi dipendenza 'cmb2-scripts' in
    // dli_options_assets()), altrimenti le frecce non esistono ancora.
    function dliMoveShiftRowsToHeader() {
        $j('#site_sections_repeat > .cmb-repeatable-grouping').each(function() {
            var $row = $j(this);
            if ($row.hasClass('dli-shift-rows-moved')) {
                return;
            }
            var $arrows = $row.find('> .inside .cmb-shift-rows');
            if (!$arrows.length) {
                return;
            }
            $row.addClass('dli-shift-rows-moved');
            $arrows.insertBefore($row.find('> .cmb-remove-group-row'));
        });
    }

    if ($j('#site_sections_repeat').length) {
        dliUpdateSectionRowTitles();
        $j(document).on('change', '#site_sections_repeat select[name$="[id]"]', dliUpdateSectionRowTitles);
        $j('.cmb2-wrap > .cmb2-metabox').on('cmb2_add_row', dliUpdateSectionRowTitles);
        // Dopo uno spostamento con le frecce su/giu, cmb.resetGroupTitles ha
        // gia' riscritto il titolo di entrambe le righe coinvolte (senza
        // l'etichetta tra parentesi): lo si riapplica qui.
        $j(document).on('cmb2_shift_rows_complete', '#site_sections_repeat', dliUpdateSectionRowTitles);

        dliMoveShiftRowsToHeader();
        $j('.cmb2-wrap > .cmb2-metabox').on('cmb2_add_row', dliMoveShiftRowsToHeader);
    }
});