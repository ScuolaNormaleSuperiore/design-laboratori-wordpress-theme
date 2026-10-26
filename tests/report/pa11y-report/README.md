# Report di accessibilità (`pa11y-report`)

Report di accessibilità per siti basati sul tema **Design Laboratori WordPress**. Fa parte dei
[Report](../README.md): requisiti comuni e convenzioni sono lì.

Controlla le pagine del sito rispetto a **WCAG 2 AA** con [pa11y-ci](https://github.com/pa11y/pa11y-ci),
usando i due motori `axe` e `htmlcs` (HTML CodeSniffer).

Produce un risultato (HTML e JSON) e non contiene asserzioni pass/fail, tranne la modalità `--gate`.


## Cosa controlla

- Ogni pagina viene controllata **due volte**: con viewport desktop (1280 px) e con
  viewport stretto (320 px), che corrisponde al criterio WCAG di *reflow* (1.4.10).
- L'elenco delle pagine arriva dalla **sitemap XML** del tema (`/sitemap.xml`, che
  rimanda a `/sitemap-it.xml` e `/sitemap-en.xml`).
- Se la sitemap non è raggiungibile, si usa una lista fissa (`/`, `/mappa-sito/`,
  `/en/site-map/`).
- Per aggiungere pagine, creare il file `pa11yci.urls.local.json` in [questa cartella](./)
  (ignorato da Git) con un array di percorsi o URL assoluti:

```json
["/pagina-con-un-form/", "https://mio-sito.example.com/altra-pagina/"]
```

Nel report i risultati di ogni pagina sono distinti con un suffisso (`#desktop-1280`
e `#mobile-320`).


## Requisiti

Quelli comuni a tutti i Report (Node.js, `npm ci`, Chromium di Playwright) sono in
[`tests/report/README.md`](../README.md). `pa11y-report` riusa il Chromium di Playwright e non
scarica un altro browser. Non serve Java.


## Esecuzione

```sh
npm run pa11y:scan -- https://mio-sito.example.com
npm run pa11y:scan:gate -- https://mio-sito.example.com
```

L'URL può anche essere dato con la variabile d'ambiente `PA11Y_BASE_URL`.

| Comando | Cosa fa | Codice di uscita |
|---|---|---|
| `pa11y:scan` | Report completo: errori, avvisi e note | `0`, anche con problemi (`1` solo per errori di esecuzione) |
| `pa11y:scan:gate` | Conta **solo gli errori WCAG**, senza scrivere report | `0` se non ci sono errori oltre la soglia, `2` altrimenti, `1` per errori di esecuzione |

Opzioni:

| Opzione | Descrizione |
|---|---|
| `--gate` | Modalità gate (vedi sopra) |
| `--threshold <n>` | Errori tollerati prima di fallire (default `0`). Utile per adottare il gate gradualmente |
| `--limit <n>` | Controlla solo le prime `n` pagine |
| `--timeout <ms>` | Timeout per pagina (default `60000`) |
| `--concurrency <n>` | Pagine controllate in parallelo (default `2`) |
| `--insecure` | Accetta certificati HTTPS non validi (solo sviluppo locale) |
| `--out <percorso>` | Percorso dei report senza estensione: scrive `<percorso>.json` e la cartella HTML `<percorso>/` |

Esempio: `npm run pa11y:scan:gate -- https://laboratorio1.local --threshold 5`


## Risultati

I report sono scritti nella [cartella `reports/`](reports/) (non tracciata da Git):

- `pa11y_report_AAAAMMGG_HHMM/index.html`: **apri questo file nel browser**. È il riepilogo, con una riga per pagina e viewport (errori, avvisi, note). Cliccando sull'URL si apre la **pagina di dettaglio** di quella pagina, nella stessa cartella.
- `pa11y_report_AAAAMMGG_HHMM/<pagina>-<viewport>.html`: una pagina di dettaglio per ogni URL controllato. Per ogni problema mostra il livello (errore, avviso, nota), il titolo, la regola (`axe: color-contrast`, `htmlcs: WCAG2AA…`), l'elemento HTML coinvolto e il suo **selettore CSS**, con i collegamenti alla spiegazione della regola. In alto, "Toggle by type" permette di mostrare solo gli **errori**; conviene farlo, perché le note sono molte.
- `pa11y_report_AAAAMMGG_HHMM.json`: gli stessi dati in formato JSON, pagina per pagina, per elaborarli con altri strumenti.

La cartella HTML va tenuta intera: l'indice rimanda alle pagine di dettaglio con collegamenti relativi.

Nella scansione normale avvisi e note sono inclusi, quindi una pagina risulta "non
superata" anche con soli avvisi. Il gate guarda solo gli errori.


## Note importanti

- Il primo `pa11y:scan:gate` può fallire finché non si correggono gli errori esistenti:
  per adottarlo gradualmente usare `--threshold`.
- Gli strumenti automatici trovano solo una parte dei problemi di accessibilità e
  non sostituiscono la verifica manuale con la tastiera (vedi `AGENTS/CODING_STANDARDS.md`).
- Se Chromium non viene trovato, il modulo lo dice e si ferma.
