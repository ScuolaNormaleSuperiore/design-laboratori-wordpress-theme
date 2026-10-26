# Report di validità HTML (`html-report`)

Valida l'HTML renderizzato di tutte le pagine del sito con il **Nu Html Checker (VNU)**. Fa parte dei
[Report](../README.md): requisiti comuni e convenzioni sono lì.

Ogni pagina viene caricata con Playwright (rendering completo del browser, quindi con l'output di PHP) e
l'HTML ottenuto passa a VNU tramite standard input. L'elenco delle pagine viene dalla mappa del sito del tema.


## Requisiti

Oltre a quelli comuni ai Report (Node.js, `npm ci`, Chromium di Playwright) serve **Java 8 o superiore** nel
`PATH`: VNU è un programma Java. Il pacchetto `vnu-jar` è già tra le dipendenze del tema, non va installato a parte.


## Esecuzione

```sh
# Scansione completa
npm run html:scan -- https://laboratorio1.local

# Con gate: codice di uscita 1 se il verdetto è FAIL
npm run html:scan:gate -- https://laboratorio1.local

# Confronto degli ultimi due risultati
npm run html:compare

# Confronto di due risultati precisi (percorsi relativi alla radice del tema)
npm run html:compare -- tests/report/html-report/reports/html_report_20260310_1400.json tests/report/html-report/reports/html_report_20260309_0900.json
```


## Opzioni

| Opzione | Default | Descrizione |
|---|---|---|
| `<baseUrl>` | _(obbligatorio)_ | URL base del sito da controllare |
| `--sitemap <path>` | `/mappa-sito/` | Percorso della pagina con la mappa del sito |
| `--timeout <ms>` | `30000` | Tempo massimo di caricamento di una pagina |
| `--delay <ms>` | `1000` | Pausa tra una pagina e l'altra (`0` = nessuna pausa) |
| `--out <path>` | data e ora automatiche | Percorso dei risultati senza estensione |
| `--gate` | disattivato | Esce con codice 1 se il verdetto è FAIL |

`html:compare` accetta due file JSON (il più recente, poi il precedente) oppure, senza argomenti, usa i due più
recenti della cartella `reports/`; l'opzione `--out <file.html>` sceglie dove scrivere il confronto.


## Risultati prodotti

Ogni scansione scrive due file in `tests/report/html-report/reports/`:

- `html_report_AAAAMMGG_HHMM.json`: i dati strutturati;
- `html_report_AAAAMMGG_HHMM.html`: il risultato da aprire nel browser.

Ogni confronto scrive un file nella stessa cartella: `html_compare_<nuovo>_vs_<vecchio>.html`.


## Regole del verdetto

| Verdetto | Condizione |
|---|---|
| **PASS** | Nessun errore e nessun avviso |
| **WARN** | Ci sono avvisi, nessun errore |
| **FAIL** | Almeno un errore HTML |

Gli **errori** di VNU (`type: "error"`) danno FAIL. Gli **avvisi** (`type: "info", subType: "warning"`) danno WARN. I
messaggi solo informativi sono ignorati.


## Struttura del JSON

```json
{
  "scannedAt": "ISO timestamp",
  "baseUrl": "https://...",
  "git": { "branch": "dev", "commit": "abc1234" },
  "summary": {
    "pagesScanned": 42,
    "pagesWithErrors": 3,
    "pagesWithWarnings": 5,
    "totalErrors": 12,
    "totalWarnings": 8,
    "verdict": "FAIL"
  },
  "pages": [
    {
      "url": "https://...",
      "errorCount": 2,
      "warningCount": 1,
      "errors": [
        { "message": "...", "line": 42, "col": 10, "extract": "..." }
      ],
      "warnings": [
        { "message": "...", "line": 55, "col": 3, "extract": "..." }
      ],
      "vnuError": null,
      "pageError": null
    }
  ]
}
```


## Note importanti

- Le pagine si controllano **una alla volta** (nessuna concorrenza) perché VNU consuma molta CPU.
- Il modulo usa Playwright per ottenere l'HTML già renderizzato (compreso l'output di PHP), non un semplice scaricamento.
- `pageError`: valorizzato quando Playwright non riesce a caricare la pagina (timeout, errore di rete).
- `vnuError`: valorizzato quando il processo VNU stesso fallisce (Java non trovato, errore del jar).
- La cartella `reports/` non è tracciata da Git.
