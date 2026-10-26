# Report di stato del sito (`status-report`)

Report pre-produzione per siti basati sul tema **Design Laboratori WordPress**. Fa parte dei
[Report](../README.md): requisiti, installazione e convenzioni comuni sono lì.

Visita tutte le pagine del sito partendo dalla mappa del sito e produce un risultato
dettagliato degli errori trovati: errori HTTP, errori PHP nel sorgente, errori
JavaScript a runtime, risorse non caricate (immagini, CSS, font, script), overflow
orizzontale a 320 px e tempi di risposta per pagina.

Il confronto tra due esecuzioni (`status:compare`) è descritto in [`COMPARE.md`](COMPARE.md).


## Indice

- [Cosa fa](#cosa-fa)
- [Come funziona](#come-funziona)
- [Risultati prodotti](#risultati-prodotti)
- [Requisiti](#requisiti)
- [Esecuzione](#esecuzione)
- [Opzioni disponibili](#opzioni-disponibili)
- [Note importanti](#note-importanti)


## Cosa fa

Il modulo esegue questi controlli su ogni pagina del sito:

| Categoria | Cosa rileva |
|---|---|
| **HTTP** | Pagine che rispondono con status diverso da 200 (404, 500, redirect non risolti) |
| **PHP** | Stringhe di errore PHP nel sorgente HTML della pagina (`Fatal error`, `Warning:`, `Notice:`, `Deprecated:`, `Parse error`) |
| **JS** | Errori JavaScript a runtime: messaggi `console.error()` ed eccezioni non catturate |
| **NET** | Risorse non caricate durante il rendering: immagini, fogli CSS, script JS, font che rispondono con status >= 400 |
| **CONTENT** | Pagine con body inferiore a 200 caratteri visibili, spia di un rendering vuoto o interrotto |
| **OVERFLOW** | Pagine che a 320 px di larghezza scorrono in orizzontale (criterio WCAG di *reflow*, 1.4.10). È un **avviso**: porta il verdetto a `WARN` ma non fa fallire il gate. Elenca fino a 5 elementi che sporgono, ignorando elementi nascosti, testo riservato agli screen reader ed elementi ritagliati da un contenitore con scorrimento |
| **TIMEOUT** | Pagine che non rispondono entro il timeout configurato |

Per ogni pagina il modulo registra anche due misure di tempo:

| Metrica | Descrizione |
|---|---|
| **TTFB** | *Time To First Byte* — tempo tra l'invio della richiesta HTTP e il primo byte ricevuto dal server. Misura esclusivamente la latenza server-side (PHP + DB) ed è **indipendente dalla concorrenza** del modulo. |
| **Load** | Tempo totale dall'inizio della navigazione all'evento `load` del browser. Include il caricamento di tutte le risorse (CSS, JS, immagini) ed è influenzato dal numero di pagine visitate in parallelo (`--concurrency`). |

Usare il **TTFB** per identificare colli di bottiglia server-side (query lente, PHP pesante). Se TTFB è basso ma Load è alto, il problema è nel numero o nel peso delle risorse della pagina.

## Come funziona

Lo script opera in tre fasi:

**Fase 1 — Raccolta URL**

Apre con un browser Chromium headless (invisibile) le due mappe standard del
tema, `/mappa-sito/` e `/en/site-map/`, ed estrae tutti i link interni presenti.
Una mappa è considerata disponibile solo se, dopo eventuali redirect, risponde
con `200` e contiene `#dli-sitemap`; questo consente di gestire siti italiani,
inglesi o bilingui senza dipendere dalla lingua principale. Deduplica e
normalizza gli URL, compresa la homepage con o senza slash finale.

**Fase 2 — Scansione pagine**

Visita ogni URL raccolto con una concorrenza configurabile (default: 3 pagine
in parallelo). Per ciascuna pagina:

- registra il codice HTTP di risposta
- intercetta tutte le richieste di risorse durante il caricamento
- ascolta gli eventi `console` e `pageerror` del browser per catturare gli errori JS
- legge il sorgente HTML completo e cerca pattern di errore PHP
- misura la lunghezza del testo visibile nel body
- registra il **TTFB** (latenza server) tramite la Navigation Timing API del browser
- registra il **tempo di caricamento totale** (dal goto all'evento `load`)

Se una pagina va in timeout lo script la segnala come `TIMEOUT` e prosegue
senza bloccarsi.

**Fase 3 — Generazione report**

Al termine della scansione produce il riepilogo numerico in console (con tempi
medi e massimi) e scrive i due file di output (`report.html` e `report.json`)
nella [cartella `reports/`](reports/) con suffisso data/ora automatico.

## Risultati prodotti

### Console

Durante la scansione lo script stampa l'avanzamento in tempo reale:

```
============================================================
DLI Site Status Report
============================================================
Base URL    : https://laboratorio1.local
Sitemap     : /mappa-sito/
Timeout     : 15000ms
Concurrency : 3
Output      : ./report.html / ./report.json
============================================================

Reading sitemap: https://laboratorio1.local/mappa-sito/
Found 34 unique internal URLs.

Scanning 34 pages (concurrency: 3)...

  ✓  https://laboratorio1.local/ [200]
  ✗  https://laboratorio1.local/ricerca/progetti/ [200] (2 errors)
       [PHP] Notice: Undefined variable $lab_id in template-parts/projects.php:47
       [NET] 404 https://laboratorio1.local/assets/img/placeholder-old.png
  ✓  https://laboratorio1.local/chi-siamo/ [200]
  ✗  https://laboratorio1.local/persone/mario-rossi/ [200] (1 error)
       [JS] TypeError: Cannot read properties of undefined (reading 'map')
  ...

============================================================
SCAN COMPLETE
============================================================
Total pages : 34
Pages OK    : 30
With errors : 4
HTTP errors : 1
PHP errors  : 1
JS errors   : 1
Res. errors : 1
Timeouts    : 0
Avg time    : 620ms
Max time    : 3840ms
Slow pages  : 1 (> 3s)
  3840ms  https://laboratorio1.local/ricerca/progetti/
============================================================
Result      : FAIL
============================================================
```

### report.html

File HTML autonomo (nessuna dipendenza esterna, apribile direttamente nel browser)
con:

- **Intestazione** con URL del sito, data e ora della scansione, numero di pagine
- **Riepilogo numerico** — card sempre visibili con:
  - Indicatore globale **PASS** (zero errori e zero avvisi), **WARN** (nessun errore ma almeno una pagina con overflow a 320 px) o **FAIL** (errori presenti)
  - Pagine OK / Pagine con errori / Totale pagine
  - Contatore per categoria: HTTP, PHP, JS, risorse 404, timeout
  - Tempo medio e tempo massimo di risposta
- **Tabella pagine con errori** — ogni riga è cliccabile e mostra il dettaglio
  degli errori con badge colorato per categoria; include la colonna **TTFB / Load**
- **Sezione pagine senza errori** — collassata di default, espandibile; include
  anch'essa la colonna TTFB / Load
- **Top 10 pagine più lente** — in fondo al report, ordinate per TTFB (latenza
  server, indipendente dalla concorrenza), con badge `>3s` sulle righe che
  superano la soglia e colonne separate TTFB e Load
- Link cliccabili agli URL scansionati
- Codice colore tempi: verde ≤ 500 ms TTFB, arancio ≤ 1500 ms, rosso > 1500 ms
- Codice colore errori: rosso (HTTP/fatal), arancio (PHP), viola (JS), blu (risorse)

### report.json

File JSON strutturato, utile per integrazione con altri strumenti o per confrontare
scansioni successive:

```json
{
  "baseUrl": "https://laboratorio1.local",
  "scannedAt": "2026-03-05T10:00:00.000Z",
  "totalPages": 34,
  "pagesWithErrors": 4,
  "summary": {
    "httpErrors": 1,
    "phpErrors": 1,
    "jsErrors": 1,
    "resourceErrors": 1,
    "contentErrors": 0,
    "timeouts": 0,
    "avgResponseTimeMs": 620,
    "maxResponseTimeMs": 3840,
    "slowPages": [
      { "url": "https://laboratorio1.local/ricerca/progetti/", "ttfbMs": 2100, "responseTimeMs": 3840 }
    ]
  },
  "pages": [
    {
      "url": "https://laboratorio1.local/ricerca/progetti/",
      "status": 200,
      "ttfbMs": 2100,
      "responseTimeMs": 3840,
      "timedOut": false,
      "errors": [
        { "type": "PHP", "message": "Notice: Undefined variable $lab_id..." },
        { "type": "NET", "status": 404, "message": "404 https://...placeholder-old.png" }
      ]
    }
  ]
}
```

## Requisiti

Quelli comuni a tutti i Report (Node.js, `npm ci`, Chromium di Playwright) sono in
[README dei Report](../README.md). In più serve un sito raggiungibile, di staging o di produzione.


## Esecuzione

Tutti i comandi si eseguono dalla **root del tema**.

**Scansione del sito locale:**

```bash
npm run status:scan -- https://laboratorio1.local
```

**Scansione di un URL personalizzato:**

```bash
npm run status:scan -- https://mio-sito.example.com
```

**Oppure direttamente con Node:**

```bash
node tests/report/status-report/scan.js https://mio-sito.example.com
```

Al termine troverai i file nella [cartella `reports/`](reports/) con suffisso data/ora automatico, es.:

```
tests/report/status-report/reports/status_report_20260305_1430.html
tests/report/status-report/reports/status_report_20260305_1430.json
```

Apri il file `.html` direttamente nel browser.
I file nella cartella `reports/` non vengono committati su git.

## Opzioni disponibili

```
npm run status:scan -- <baseUrl> [opzioni]
```

| Opzione | Default | Descrizione |
|---|---|---|
| `<baseUrl>` | — | URL base del sito, obbligatorio. Es: `https://laboratorio1.local` |
| `--sitemap <path>` | entrambe le mappe standard | Scansiona soltanto una mappa specifica invece di `/mappa-sito/` e `/en/site-map/` |
| `--timeout <ms>` | `15000` | Millisecondi di attesa massima per ogni pagina prima di segnare TIMEOUT |
| `--concurrency <n>` | `3` | Numero massimo di pagine visitate contemporaneamente |
| `--delay <ms>` | `1000` | Pausa in ms prima di avviare ogni richiesta di pagina, per ridurre il carico sul server. `--delay 0` disabilita il delay. |
| `--out <path>` | `./tests/report/status-report/reports/status_report_<YYYYMMDD_HHMM>` | Percorso di output senza estensione — lo script aggiunge `.html` e `.json` |
| `--gate` | disattivato | Esce con codice 1 se il verdetto è FAIL (almeno una pagina con errori). Un verdetto `WARN` (solo overflow a 320 px) non fa fallire il gate. Scorciatoia: `npm run status:scan:gate -- <baseUrl>` |

**Esempi:**

```bash
# Solo URL base — output con suffisso data/ora automatico
npm run status:scan -- https://laboratorio1.local

# Mappa del sito su percorso diverso
npm run status:scan -- https://laboratorio1.local --sitemap /mappa-sito/

# Timeout più lungo per siti lenti, un tab alla volta
npm run status:scan -- https://laboratorio1.local --timeout 30000 --concurrency 1

# Output su nome file personalizzato (senza suffisso automatico)
npm run status:scan -- https://laboratorio1.local --out ./output/2026-03-05

# Con gate: codice di uscita 1 se ci sono pagine con errori
npm run status:scan:gate -- https://laboratorio1.local
```

## Note importanti

**Errori PHP visibili solo in modalità debug**

Gli errori PHP compaiono nel sorgente HTML solo se WordPress è configurato con:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
```

Eseguire il modulo sull'ambiente di **staging** con debug attivo.
In produzione con `WP_DEBUG = false` la categoria PHP non produce risultati
(il che è corretto: in produzione il debug deve essere disattivato).

**Contenuti AJAX**

I contenuti caricati via chiamate AJAX dopo il page load potrebbero non essere
inclusi nella scansione del sorgente HTML. Gli errori JS delle chiamate AJAX
vengono comunque catturati tramite `console.error` e `pageerror`.

**Risultati esclusi da Git**

La cartella `reports/` è nel `.gitignore` della radice del tema: i risultati generati non vengono committati.
