# Test e scanner

La cartella `tests/` raccoglie due famiglie di strumenti, con scopi diversi.

| Famiglia | Cartella | Cosa fa | Esito |
|---|---|---|---|
| **Test** (con asserzioni) | `tests/unit/` | Verifica che il codice e i file del repository si comportino come previsto | Passano o falliscono (`composer test:unit`) |
| **Scanner** (producono report) | `tests/report/` | Guardano il sito in funzione e producono un report HTML e JSON | Un report da leggere; `--gate` dà un codice di errore |

Le suite di integrazione (WordPress reale) e di E2E (browser su un sito dedicato) sono
previste ma non ancora presenti: quando esisteranno avranno una propria cartella e un
proprio README.

Questa cartella è nel repository clonato ma **non** nel pacchetto zip che GitHub genera
per ogni versione (`export-ignore` in `.gitattributes`).


## Test unit

- Cartella: `tests/unit/`, configurazione in `phpunit.xml`, avvio in `tests/bootstrap-unit.php`.
- Comando, dalla radice del tema: `composer test:unit`.
- Non servono WordPress, database o browser. Il hook `pre-commit` li esegue a ogni commit.
- Dettagli, elenco dei file di test e come aggiungerne uno: [`tests/unit/README.md`](unit/README.md).


## Scanner

| Scanner | Comando | Cosa verifica |
|---|---|---|
| `status-report` | `npm run status:scan -- <url>` | Errori HTTP, PHP e JavaScript, risorse non caricate, pagine vuote, timeout, tempi e overflow orizzontale a 320 px |
| `html-report` | `npm run html:scan -- <url>` | Validità dell'HTML (Nu Html Checker) |
| `pa11y-report` | `npm run pa11y:scan -- <url>` | Accessibilità WCAG 2 AA a 1280 px e a 320 px |
| `issues-report` | trigger `/issues-snapshot` | Cartella degli snapshot delle issue: non è uno scanner |

I report sono scritti in `tests/report/<scanner>/reports/` e non vengono tracciati da Git.
Opzioni, varianti `:gate` e `:compare` e formato dei report sono nei README di ogni
cartella: [`status-report`](report/status-report/README.md),
[`html-report`](report/html-report/README.md),
[`pa11y-report`](report/pa11y-report/README.md).


## Altre cartelle

- `tests/phpstan/`: stub delle funzioni di WordPress per PHPStan (`composer run lint:phpstan`).

Per i controlli statici (PHPCS, PHPStan, audit delle dipendenze) vedi la sezione
"Sviluppo e verifiche" del `README.md` principale.
