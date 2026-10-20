# ux-report

Runtime UX scanner: accessibilità (Axe/WCAG) e responsiveness (overflow) su tutte le pagine del sito.

Complementare a:
- `quality-report` — analisi statica del codice
- `status-report` — stato HTTP e errori runtime

---

## Prerequisiti

```bash
npm install --save-dev @axe-core/playwright
npx playwright install chromium
```

---

## Esecuzione

```bash
# Scan completo
npm run ux:scan -- https://laboratorio1.local

# Scan con gate CI (exit 1 se verdict = FAIL)
npm run ux:scan:gate -- https://laboratorio1.local

# Confronto ultimi due report
npm run ux:compare

# Confronto report specifici
npm run ux:compare -- reports/ux_report_20260310_1400.json reports/ux_report_20260309_0900.json
```

---

## Opzioni scan.js

| Opzione | Default | Descrizione |
|---|---|---|
| `<baseUrl>` | — | URL base del sito (obbligatorio) |
| `--sitemap <path>` | `/mappa-sito/` | Percorso pagina sitemap |
| `--timeout <ms>` | `30000` | Timeout per pagina |
| `--delay <ms>` | `500` | Pausa tra pagine (0 = disabilitata) |
| `--out <path>` | `reports/ux_report_<ts>` | Percorso output senza estensione |
| `--gate` | `false` | Exit 1 se verdict è FAIL |

Le pagine vengono scansionate **sequenzialmente** (nessuna concorrenza) perché l'analisi Axe è onerosa per il server.

---

## Output

- `reports/ux_report_YYYYMMDD_HHMM.json` — dati strutturati
- `reports/ux_report_YYYYMMDD_HHMM.html` — report leggibile

### Metriche per pagina

**Accessibilità (Axe/WCAG 2.1)**
- Violazioni per livello: `critical`, `serious`, `moderate`, `minor`
- Dettaglio per ogni violazione: regola Axe, descrizione, numero di elementi interessati, tag WCAG

**Responsiveness**
- Overflow rilevato a 375px (mobile), 768px (tablet), 1280px (desktop)
- `overflowCount` — numero di viewport con `scrollWidth > clientWidth`

### Verdict per pagina

| Verdict | Condizione |
|---|---|
| `FAIL` | `axe.critical > 0` |
| `WARN` | `axe.serious > 0` |
| `PASS` | nessuna delle precedenti |

### Verdict globale

| Verdict | Condizione |
|---|---|
| `FAIL` | `axeCriticalTotal > 0` |
| `WARN` | `axeSeriousTotal > 0` oppure `pagesWithOverflow > 0` |
| `PASS` | nessuna delle precedenti |

---

## Comparazione (compare.js)

Confronta due report JSON e mostra i delta numerici su tutte le metriche di summary e per ogni URL.

Output: `reports/ux_compare_<ts_new>_vs_<ts_old>.html`

Metriche confrontate (tutte "lower is better" tranne `pagesScanned`):
`pagesWithAxeViolations`, `pagesWithOverflow`, `axeCriticalTotal`, `axeSeriousTotal`, `axeModerateTotal`, `axeMinorTotal`, `axeTotal`

Verdict comparazione: `IMPROVED` / `DEGRADED` / `UNCHANGED` — basato sulla variazione di `axeCriticalTotal` e `axeTotal`.

---

## Struttura report JSON

```json
{
  "scannedAt": "2026-03-10T14:30:00.000Z",
  "baseUrl": "https://laboratorio1.local",
  "git": { "branch": "dev", "commit": "abc1234" },
  "summary": {
    "pagesScanned": 34,
    "pagesWithAxeViolations": 8,
    "pagesWithOverflow": 2,
    "axeCriticalTotal": 3,
    "axeSeriousTotal": 12,
    "axeModerateTotal": 21,
    "axeMinorTotal": 5,
    "axeTotal": 41,
    "verdict": "FAIL"
  },
  "pages": [
    {
      "url": "https://laboratorio1.local/spinoff/biloab",
      "axe": {
        "critical": 1, "serious": 2, "moderate": 1, "minor": 0, "total": 4,
        "violations": [
          { "id": "image-alt", "impact": "critical", "description": "...", "nodes": 2, "wcag": ["wcag2a"] }
        ]
      },
      "responsive": {
        "375": { "overflow": false },
        "768": { "overflow": false },
        "1280": { "overflow": true }
      },
      "overflowCount": 1
    }
  ]
}
```

---

## Aggiungere nuove metriche

Lo script è progettato per essere esteso. Per aggiungere un nuovo check:

1. Creare una funzione `async function checkXxx(page) { ... }` che restituisce un oggetto con i dati raccolti.
2. Chiamarla dentro `scanPage()` e salvare il risultato in `result.xxx`.
3. Aggiornare `computeSummary()` per aggregare le nuove metriche.
4. Aggiornare l'HTML report (`writeHtml`) per visualizzarle.
5. Aggiornare `compare.js` aggiungendo le nuove metriche a `METRIC_LABELS` e `LOWER_IS_BETTER`.
