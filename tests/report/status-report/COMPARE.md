# Comparatore di `status-report` (`status:compare`)

Fa parte del modulo [`status-report`](README.md). Le convenzioni comuni ai Report (requisiti, cartelle `reports/`)
sono in [`tests/report/README.md`](../README.md).

Script Node.js che confronta due risultati JSON prodotti da `scan.js` e indica
per ogni pagina se la situazione è **migliorata**, **peggiorata** o **invariata**
rispetto alla scansione precedente.


## Esecuzione

Tutti i comandi si eseguono dalla **root del tema**.

**Confronto automatico dei due report più recenti:**

```bash
npm run status:compare
```

**Confronto con file espliciti:**

```bash
npm run status:compare -- tests/report/status-report/reports/status_report_A.json tests/report/status-report/reports/status_report_B.json
```

**Oppure direttamente con Node:**

```bash
node tests/report/status-report/compare.js [report-nuovo.json] [report-vecchio.json]
```

Senza argomenti lo script seleziona automaticamente i due file `status_report_*.json`
più recenti presenti in `tests/report/status-report/reports/`.


## Opzioni disponibili

| Opzione | Default | Descrizione |
|---|---|---|
| `[file-nuovo]` | auto (più recente) | Percorso del report JSON più recente |
| `[file-vecchio]` | auto (secondo più recente) | Percorso del report JSON di riferimento |
| `--timing-threshold <ms>` | `500` | Soglia in ms per classificare una variazione TTFB come significativa |
| `--out <path>` | `./tests/report/status-report/reports/compare_<ts>_vs_<ts>` | Percorso output senza estensione |


## Risultati prodotti

Al termine della comparazione vengono creati due file nella cartella `reports/`:

```
tests/report/status-report/reports/compare_<ts-nuovo>_vs_<ts-vecchio>.html
tests/report/status-report/reports/compare_<ts-nuovo>_vs_<ts-vecchio>.json
```

### Console

```
============================================================
DLI Site Status Comparator
============================================================
Nuovo  : reports/status_report_20260310_1430.json  (2026-03-10 14:30)
Vecchio: reports/status_report_20260305_1000.json  (2026-03-05 10:00)
============================================================

RIEPILOGO DELTA
------------------------------------------------------------
Pagine confrontate :  34
Pagine fisse       :   3  (errori risolti)
Regressioni        :   1  (errori nuovi)
Errori modificati  :   2
Errori persistenti :   4
Pagine OK stabili  :  24
Pagine nuove       :   0
Pagine rimosse     :   0
------------------------------------------------------------
Pagine più veloci  :   5  (TTFB migliorato > 500ms)
Pagine più lente   :   2  (TTFB peggiorato > 500ms)
Delta TTFB medio   : -120ms
Delta Load medio   :  -80ms
------------------------------------------------------------
Risultato          : MIGLIORATO  (regressioni: 1, fix: 3)
============================================================
```

### compare.html

File HTML autonomo (apribile direttamente nel browser) con:

- Intestazione con i due file confrontati e le rispettive date
- Card riepilogo con i contatori delta
- **Verdetto globale**: `MIGLIORATO` / `PEGGIORATO` / `INVARIATO`
- Sezione **Regressioni** — pagine con errori nuovi
- Sezione **Pagine fisse** — pagine in cui gli errori sono stati risolti
- Sezione **Errori modificati** — pagine in cui la lista errori è cambiata
- Sezione **Errori persistenti** — collassata di default
- Sezione **Tempi di risposta** — pagine con variazione TTFB significativa (> ±500ms)
- Sezione **Pagine nuove/rimosse** — se presenti

### compare.json

```json
{
  "comparedAt": "2026-03-10T14:45:00.000Z",
  "newReport":  { "file": "report_20260310_1430.json", "scannedAt": "...", "baseUrl": "..." },
  "oldReport":  { "file": "report_20260305_1000.json", "scannedAt": "...", "baseUrl": "..." },
  "summary": {
    "fixed":            3,
    "regressions":      1,
    "changedErrors":    2,
    "persistentErrors": 4,
    "unchangedOk":     24,
    "newPages":         0,
    "removedPages":     0,
    "fasterPages":      5,
    "slowerPages":      2,
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

### Stati delta per pagina

| Stato | Significato |
|---|---|
| `FIXED` | Errori presenti nel vecchio report, assenti nel nuovo |
| `REGRESSION` | Nessun errore nel vecchio report, errori nel nuovo |
| `CHANGED_ERROR` | Errori in entrambi, ma lista diversa |
| `UNCHANGED_ERROR` | Errori identici in entrambi |
| `UNCHANGED_OK` | Nessun errore in entrambi |
| `NEW_PAGE` | URL presente solo nel nuovo report |
| `REMOVED_PAGE` | URL presente solo nel vecchio report |


## Dipendenze

Nessuna dipendenza aggiuntiva. `compare.js` usa solo moduli Node.js built-in
(`fs`, `path`): **non è necessario eseguire `npm install`** dopo aver aggiunto
questo script.
