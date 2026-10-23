# Report

I Report guardano un **sito in funzione** e producono un risultato (HTML e JSON) da leggere:
non hanno asserzioni e non fanno passare o fallire nulla, salvo con l'opzione `--gate`. Non
sono Test né Controlli statici: la panoramica delle tre famiglie è in
[`tests/README.md`](../README.md).

| Modulo | Cosa verifica | Comandi | Dettagli |
|---|---|---|---|
| `status-report` | Errori HTTP, PHP e JavaScript, risorse non caricate, pagine vuote, timeout, tempi e overflow orizzontale a 320 px | `status:scan`, `status:scan:gate`, `status:compare` | [`status-report/README.md`](status-report/README.md) |
| `html-report` | Validità dell'HTML (Nu Html Checker) | `html:scan`, `html:scan:gate`, `html:compare` | [`html-report/README.md`](html-report/README.md) |
| `pa11y-report` | Accessibilità WCAG 2 AA a 1280 px e a 320 px | `pa11y:scan`, `pa11y:scan:gate` | [`pa11y-report/README.md`](pa11y-report/README.md) |

La cartella `issues-report/` non contiene un Report: raccoglie gli snapshot delle issue
prodotti dal trigger `/issues-snapshot` (vedi `AGENTS/AI_BEHAVIOR.md`).


## Requisiti comuni

Da installare una sola volta, dalla radice del tema:

```sh
npm ci
npm run status:scan:install-browser
```

- **Node.js** 18 o superiore e le dipendenze del tema (`npm ci`).
- **Chromium di Playwright** (`npm run status:scan:install-browser`, circa 150 MB). Tutti i moduli lo
  riusano: `pa11y-report` non scarica un altro browser.
- **Java 8 o superiore** nel `PATH`, solo per `html-report` (il validatore Nu Html Checker è un programma Java).
- Un sito **raggiungibile** dalla macchina: un sito Local, di staging o di produzione. Il modulo
  non avvia nessun sito.


## Convenzioni comuni

- **Come si lanciano:** con npm, dalla radice del tema, passando l'URL del sito dopo `--`:

  ```sh
  npm run status:scan -- https://laboratorio1.local
  ```

- **Risultati:** in `tests/report/<modulo>/reports/`, con data e ora nel nome. Quella cartella è
  ignorata da Git e non va committata. Ogni modulo scrive un file HTML da aprire nel browser e un
  file JSON con gli stessi dati.
- **Certificati dei siti Local:** `pa11y-report` legge la sitemap fidandosi dei certificati di
  sistema e ha l'opzione `--insecure` (solo in sviluppo); per gli altri moduli vedi il loro README.
- **`:gate`:** variante che dà un codice di uscita diverso da zero quando il sito ha problemi, per
  poterla usare in uno script o in un hook.
- **`:compare`:** confronta gli ultimi due risultati e mostra cosa è cambiato (`status:compare`
  e `html:compare`; `pa11y-report` non ha un comparatore).

| Comando | Codice di uscita |
|---|---|
| `…:scan` | `0` anche con problemi sul sito; `1` solo per errori di esecuzione |
| `status:scan:gate`, `html:scan:gate` | `1` se il verdetto è FAIL |
| `pa11y:scan:gate` | `2` se gli errori WCAG superano la soglia (`--threshold`, predefinita `0`); `1` per errori di esecuzione |

Opzioni, formato dei risultati e regole del verdetto sono nel README di ciascun modulo.
