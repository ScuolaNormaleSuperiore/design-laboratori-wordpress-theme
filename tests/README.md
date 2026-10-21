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


## Variabili d'ambiente

I test unit non ne richiedono. Le altre suite leggono le variabili seguenti (mai scritte
in un file del repository). Quelle senza valore predefinito vanno definite prima di
lanciare la suite.

| Variabile | Suite | Significato | Valore predefinito |
|---|---|---|---|
| `WP_TESTS_DIR` | integrazione | cartella `tests/phpunit` di `wordpress-develop` | `C:/WordpressDEV/wordpress-develop/tests/phpunit` |
| `WP_TESTS_CONFIG_FILE_PATH` | integrazione | `wp-tests-config.php` alternativo | quello nella radice di `wordpress-develop` |
| `DLI_TEST_PLUGINS_DIR` | integrazione | cartella con ACF e Polylang | `tests/integration/.cache/plugins` |
| `DLI_E2E_BASE_URL` | E2E | URL del sito E2E | `https://dli-wp-e2e.local` |
| `DLI_E2E_ADMIN_USER` | E2E | utente amministratore del sito E2E | `e2e-admin` |
| `DLI_E2E_ADMIN_PASSWORD` | E2E | password dell'amministratore | `password` |

Gli utenti di prova del sito E2E sono `e2e-admin`, `e2e-super-editor` ed `e2e-editor`,
tutti con password `password` (sito locale di prova: non riusare mai queste credenziali
su un sito vero). Sono descritti, con la baseline del database, in
[`tests/e2e/README.md`](e2e/README.md).

I test E2E ripristinano il database del sito prima e dopo l'esecuzione e richiedono
`DLI_E2E_SITE_PATH` (cartella del sito); `DLI_E2E_SKIP_RESET=1` salta il ripristino.
Anche le credenziali degli utenti di prova hanno un valore predefinito. Tutto è descritto
in [`tests/e2e/README.md`](e2e/README.md).

Esempio di sostituzione di un valore (PowerShell), valido per la sessione corrente:

```
$env:DLI_E2E_BASE_URL = 'https://altro-sito.local'
```


## Altre cartelle

- `tests/utils/`: file di supporto. `wordpress-lite-stubs.php` contiene gli stub delle
  funzioni di WordPress per PHPStan (`composer run lint:phpstan`);
  `fetch-test-plugins.php` scarica ACF e Polylang per i test di integrazione
  (lo lancia `composer test:integration`).

Per i controlli statici (PHPCS, PHPStan, audit delle dipendenze) vedi la sezione
"Sviluppo e verifiche" del `README.md` principale.
