# SITE_CHECK_COMPARE_SPEC — Specifiche script di confronto report

## Obiettivo

Script Node.js che confronta due report JSON prodotti da `scan.js` e indica
per ogni pagina se la situazione è **migliorata**, **peggiorata** o **invariata**
rispetto alla scansione precedente. Produce un report HTML e JSON sintetico
del delta.

---

## Collocazione

```
tests/e2e/status-report/compare.js
```

Output nella stessa cartella `reports/` dello scanner:

```
tests/e2e/status-report/reports/compare_<ts-nuovo>_vs_<ts-vecchio>.html
tests/e2e/status-report/reports/compare_<ts-nuovo>_vs_<ts-vecchio>.json
```

---

## Selezione automatica dei file da confrontare

Se non vengono forniti file esplicitamente, lo script:

1. Legge tutti i file `report_*.json` presenti in `tests/e2e/status-report/reports/`
2. Estrae il timestamp dal nome file (formato `report_YYYYMMDD_HHMM.json`)
3. Ordina per timestamp discendente
4. Usa il **più recente** come report "nuovo" e il **secondo più recente** come
   report "vecchio"
5. Stampa in console i due file selezionati prima di procedere
6. Esce con errore se non ci sono almeno due report disponibili

---

## Utilizzo CLI

```
node tests/e2e/status-report/compare.js [report-nuovo.json] [report-vecchio.json]
```

Oppure tramite npm (da aggiungere a `package.json`):

```
npm run compare
npm run compare -- reports/report_A.json reports/report_B.json
```

---

## Logica di confronto

### Confronto per URL

La chiave di join tra i due report è l'**URL** della pagina.

Per ogni URL classificare il delta:

| Caso | Stato delta |
|---|---|
| Presente in entrambi, errori = 0 in entrambi | `UNCHANGED_OK` |
| Presente in entrambi, errori > 0 in entrambi, stessa lista errori | `UNCHANGED_ERROR` |
| Errori > 0 nel vecchio, errori = 0 nel nuovo | `FIXED` |
| Errori = 0 nel vecchio, errori > 0 nel nuovo | `REGRESSION` |
| Errori > 0 in entrambi, lista diversa (tipo o messaggio) | `CHANGED_ERROR` |
| Presente solo nel nuovo | `NEW_PAGE` |
| Presente solo nel vecchio | `REMOVED_PAGE` |

### Confronto tempi di risposta (TTFB e Load)

Per ogni pagina presente in entrambi i report, calcolare il delta:

- `ttfbDelta = ttfbMs_nuovo - ttfbMs_vecchio`
- `loadDelta = responseTimeMs_nuovo - responseTimeMs_vecchio`

Classificare la variazione TTFB:

| Delta TTFB | Classificazione |
|---|---|
| > +500 ms | `SLOWER` (peggiorato) |
| < -500 ms | `FASTER` (migliorato) |
| tra -500 e +500 ms | `STABLE` |

La soglia di ±500 ms è configurabile via opzione `--timing-threshold <ms>`.

### Riepilogo globale

Calcolare e mostrare:

- Totale pagine confrontate
- Pagine `FIXED` (errori risolti)
- Pagine `REGRESSION` (errori nuovi)
- Pagine `CHANGED_ERROR` (errori modificati)
- Pagine `UNCHANGED_ERROR` (errori persistenti)
- Pagine `UNCHANGED_OK`
- Pagine `NEW_PAGE` / `REMOVED_PAGE`
- Numero pagine diventate più lente (TTFB +500ms)
- Numero pagine diventate più veloci (TTFB -500ms)
- Delta medio TTFB globale
- Delta medio Load globale

---

## Output console

```
============================================================
DLI Site Status Comparator
============================================================
Nuovo  : reports/report_20260310_1430.json  (2026-03-10 14:30)
Vecchio: reports/report_20260305_1000.json  (2026-03-05 10:00)
============================================================

RIEPILOGO DELTA
------------------------------------------------------------
Pagine confrontate : 34
Pagine fisse       :  3  (errori risolti)
Regressioni        :  1  (errori nuovi)
Errori modificati  :  2
Errori persistenti :  4
Pagine OK stabili  : 24
Pagine nuove       :  0
Pagine rimosse     :  0
------------------------------------------------------------
Pagine più veloci  :  5  (TTFB migliorato > 500ms)
Pagine più lente   :  2  (TTFB peggiorato > 500ms)
Delta TTFB medio   : -120ms
Delta Load medio   : -80ms
------------------------------------------------------------
Risultato          : MIGLIORATO  (regressioni: 1, fix: 3)
============================================================

REGRESSIONI (1)
  ✗  https://laboratorio1.local/eventi/  [nuovo: JS] TypeError: ...

PAGINE FISSE (3)
  ✓  https://laboratorio1.local/spinoff/biloab  (rimosso: JS)
  ✓  https://laboratorio1.local/ricerca/        (rimosso: NET)
  ✓  https://laboratorio1.local/persone/        (rimosso: PHP)
============================================================
```

---

## Output HTML

File HTML autonomo con:

- **Intestazione** con i due file confrontati e le rispettive date di scansione
- **Riepilogo numerico** — card con i contatori delta (stesso stile del report)
- **Verdetto globale**: `MIGLIORATO` / `PEGGIORATO` / `INVARIATO`
  - `MIGLIORATO`: regressioni = 0 e fix > 0, oppure regressioni < fix
  - `PEGGIORATO`: regressioni > 0 e fix = 0, oppure regressioni > fix
  - `INVARIATO`: nessuna variazione significativa
- **Sezione Regressioni** — tabella con le pagine che hanno nuovi errori,
  evidenziando i nuovi tipi di errore rispetto al vecchio report
- **Sezione Pagine fisse** — tabella con le pagine in cui gli errori sono stati
  risolti
- **Sezione Errori modificati** — tabella con le pagine in cui la lista errori
  è cambiata (alcuni risolti, altri aggiunti); mostra diff degli errori
- **Sezione Errori persistenti** — collassata di default
- **Sezione Tempi di risposta** — tabella delle pagine con variazione TTFB
  significativa (> ±500ms), ordinata per delta decrescente
- **Sezione Pagine nuove/rimosse** — se presenti
- Codice colore coerente con il report principale

---

## Output JSON

```json
{
  "comparedAt": "2026-03-10T14:45:00.000Z",
  "newReport":  { "file": "report_20260310_1430.json", "scannedAt": "...", "baseUrl": "..." },
  "oldReport":  { "file": "report_20260305_1000.json", "scannedAt": "...", "baseUrl": "..." },
  "summary": {
    "fixed":           3,
    "regressions":     1,
    "changedErrors":   2,
    "persistentErrors":4,
    "unchangedOk":    24,
    "newPages":        0,
    "removedPages":    0,
    "fasterPages":     5,
    "slowerPages":     2,
    "avgTtfbDeltaMs": -120,
    "avgLoadDeltaMs":  -80,
    "verdict":         "IMPROVED"
  },
  "pages": [
    {
      "url": "https://laboratorio1.local/spinoff/biloab",
      "delta": "FIXED",
      "old": { "errors": [{ "type": "JS", "message": "..." }], "ttfbMs": 210, "responseTimeMs": 980 },
      "new": { "errors": [], "ttfbMs": 185, "responseTimeMs": 870 },
      "ttfbDelta": -25,
      "loadDelta": -110
    }
  ]
}
```

---

## Opzioni CLI

| Opzione | Default | Descrizione |
|---|---|---|
| `[file-nuovo]` | auto (più recente) | Percorso del report JSON più recente |
| `[file-vecchio]` | auto (secondo più recente) | Percorso del report JSON di riferimento |
| `--timing-threshold <ms>` | `500` | Soglia in ms per classificare una variazione TTFB come significativa |
| `--out <path>` | `./tests/e2e/status-report/reports/compare_<ts>_vs_<ts>` | Percorso output senza estensione |

---

## Dipendenze

Nessuna dipendenza aggiuntiva rispetto a quelle già presenti. Usa solo moduli
Node.js built-in (`fs`, `path`). Il formato JSON prodotto da `report.js` è
sufficiente come input.

---

## Comandi npm da aggiungere a `package.json`

```json
"compare":       "node tests/e2e/status-report/compare.js",
"compare:last":  "node tests/e2e/status-report/compare.js"
```

`compare:last` è un alias esplicito per ricordare che senza argomenti confronta
automaticamente i due report più recenti.

---

## Limitazioni note

- Il confronto è basato sull'URL esatto: se tra le due scansioni un URL cambia
  (es. slug rinominato) viene rilevato come pagina rimossa + pagina nuova
- I messaggi di errore PHP possono variare leggermente tra scansioni (numeri di
  riga, valori variabili): considerare come `CHANGED_ERROR` anche se in realtà
  è lo stesso problema
- Le variazioni di TTFB inferiori alla soglia non vengono segnalate per evitare
  falsi positivi dovuti al rumore di misura
