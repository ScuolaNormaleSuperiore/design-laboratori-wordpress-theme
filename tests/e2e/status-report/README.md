# DLI Site Status Scanner

Scanner automatico pre-produzione per siti basati sul tema **Design Laboratori WordPress**.

Visita tutte le pagine del sito partendo dalla mappa del sito e produce un report
dettagliato degli errori trovati: errori HTTP, errori PHP nel sorgente, errori
JavaScript a runtime, risorse non caricate (immagini, CSS, font, script) e tempi
di risposta per pagina.


## Indice

- [Cosa fa](#cosa-fa)
- [Come funziona](#come-funziona)
- [Risultati prodotti](#risultati-prodotti)
- [Requisiti](#requisiti)
- [Installazione](#installazione)
- [Esecuzione](#esecuzione)
- [Opzioni disponibili](#opzioni-disponibili)
- [Note importanti](#note-importanti)


---


## Cosa fa

Lo scanner esegue questi controlli su ogni pagina del sito:

| Categoria | Cosa rileva |
|---|---|
| **HTTP** | Pagine che rispondono con status diverso da 200 (404, 500, redirect non risolti) |
| **PHP** | Stringhe di errore PHP nel sorgente HTML della pagina (`Fatal error`, `Warning:`, `Notice:`, `Deprecated:`, `Parse error`) |
| **JS** | Errori JavaScript a runtime: messaggi `console.error()` ed eccezioni non catturate |
| **NET** | Risorse non caricate durante il rendering: immagini, fogli CSS, script JS, font che rispondono con status >= 400 |
| **CONTENT** | Pagine con body inferiore a 200 caratteri visibili, spia di un rendering vuoto o interrotto |
| **TIMEOUT** | Pagine che non rispondono entro il timeout configurato |

Per ogni pagina lo scanner registra anche due misure di tempo:

| Metrica | Descrizione |
|---|---|
| **TTFB** | *Time To First Byte* — tempo tra l'invio della richiesta HTTP e il primo byte ricevuto dal server. Misura esclusivamente la latenza server-side (PHP + DB) ed è **indipendente dalla concorrenza** dello scanner. |
| **Load** | Tempo totale dall'inizio della navigazione all'evento `load` del browser. Include il caricamento di tutte le risorse (CSS, JS, immagini) ed è influenzato dal numero di pagine visitate in parallelo (`--concurrency`). |

Usare il **TTFB** per identificare colli di bottiglia server-side (query lente, PHP pesante). Se TTFB è basso ma Load è alto, il problema è nel numero o nel peso delle risorse della pagina.


---


## Come funziona

Lo script opera in tre fasi:

**Fase 1 — Raccolta URL**

Apre con un browser Chromium headless (invisibile) la pagina della mappa del sito
(default: `/mappa-sito/`) ed estrae tutti i link interni presenti. Deduplica e
normalizza gli URL, quindi aggiunge sempre la homepage come punto di partenza.

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
nella cartella `tests/e2e/status-report/reports/` con suffisso data/ora automatico.


---


## Risultati prodotti

### Console

Durante la scansione lo script stampa l'avanzamento in tempo reale:

```
============================================================
DLI Site Status Scanner
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
  - Indicatore globale **PASS** (zero errori) o **FAIL** (errori presenti)
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


---


## Requisiti

- **Node.js** >= 18
- Connessione di rete al sito da scansionare
- Il sito deve essere raggiungibile (staging o produzione)


---


## Installazione

L'installazione va eseguita una sola volta dalla **root del tema**.
`playwright` è incluso nelle devDependencies del `package.json` principale.

```bash
npm install
npm run scan:install-browser
```

`npm run scan:install-browser` scarica il browser Chromium headless (~150 MB).


---


## Esecuzione

Tutti i comandi si eseguono dalla **root del tema**.

**Scansione del sito locale (URL preconfigurato: `https://laboratorio1.local`):**

```bash
npm run scan:demo
```

**Scansione di un URL personalizzato:**

```bash
npm run scan -- https://mio-sito.example.com
```

**Oppure direttamente con Node:**

```bash
node tests/e2e/status-report/scan.js https://mio-sito.example.com
```

Al termine troverai i file in `tests/e2e/status-report/reports/` con suffisso data/ora automatico, es.:

```
tests/e2e/status-report/reports/report_20260305_1430.html
tests/e2e/status-report/reports/report_20260305_1430.json
```

Apri il file `.html` direttamente nel browser.
I file nella cartella `reports/` non vengono committati su git.


---


## Opzioni disponibili

```
npm run scan -- <baseUrl> [opzioni]
```

| Opzione | Default | Descrizione |
|---|---|---|
| `<baseUrl>` | — | URL base del sito, obbligatorio. Es: `https://laboratorio1.local` |
| `--sitemap <path>` | `/mappa-sito/` | Percorso della pagina che contiene i link alle pagine del sito |
| `--timeout <ms>` | `15000` | Millisecondi di attesa massima per ogni pagina prima di segnare TIMEOUT |
| `--concurrency <n>` | `3` | Numero massimo di pagine visitate contemporaneamente |
| `--out <path>` | `./tests/e2e/status-report/report` | Percorso di output senza estensione — lo script aggiunge `.html` e `.json` |

**Esempi:**

```bash
# Solo URL base — output con suffisso data/ora automatico
npm run scan -- https://laboratorio1.local

# Mappa del sito su percorso diverso
npm run scan -- https://laboratorio1.local --sitemap /sitemap-pages/

# Timeout più lungo per siti lenti, un tab alla volta
npm run scan -- https://laboratorio1.local --timeout 30000 --concurrency 1

# Output su nome file personalizzato (senza suffisso automatico)
npm run scan -- https://laboratorio1.local --out ./output/2026-03-05
```


---


## Note importanti

**Errori PHP visibili solo in modalità debug**

Gli errori PHP compaiono nel sorgente HTML solo se WordPress è configurato con:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
```

Usare lo scanner sull'ambiente di **staging** con debug attivo.
In produzione con `WP_DEBUG = false` la categoria PHP non produce risultati
(il che è corretto: in produzione il debug deve essere disattivato).

**Contenuti AJAX**

I contenuti caricati via chiamate AJAX dopo il page load potrebbero non essere
inclusi nella scansione del sorgente HTML. Gli errori JS delle chiamate AJAX
vengono comunque catturati tramite `console.error` e `pageerror`.

**File di output esclusi da git**

La cartella `reports/` è nel `.gitignore` di questa directory: i report
generati non vengono committati. Solo il file `reports/.gitkeep` è tracciato
per mantenere la cartella nel repository.
