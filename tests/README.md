# Controlli, test e report

Questo è il punto di ingresso: spiega a cosa serve ogni strumento e come si lancia. Requisiti,
configurazione e variabili d'ambiente di ogni suite stanno nel README della sua cartella.

Gli strumenti di verifica del tema sono di tre famiglie, con scopi diversi.

| Famiglia | A cosa risponde | Serve un sito in funzione? | Strumenti | Esito |
|---|---|---|---|---|
| **Controlli statici** | Il codice è scritto bene e può rompersi? | No: leggono i sorgenti | PHPCS, PHPStan, `composer audit`, `npm audit` | Elenco di segnalazioni (PHPCS può correggere da solo con `lint:php:fix`, PHPStan no) |
| **Test** | Il codice si comporta come deve? | Unit: no. Integrazione: sì, un WordPress di test. E2E: sì, un sito Local dedicato | PHPUnit, Playwright | Passano o falliscono |
| **Report** | Il sito in funzione ha errori, HTML non valido, problemi di accessibilità? | Sì, un sito raggiungibile | Moduli di `tests/report/` | Un risultato HTML e JSON da leggere; `--gate` dà un codice di uscita |

I Controlli statici e i Test si lanciano con **Composer**, i Report con **npm**, sempre dalla
radice del tema.


## Come si lancia ciascuno

| Cosa | Comando | Tempo indicativo | Nell'hook `pre-commit`? | Requisiti e variabili |
|---|---|---|---|---|
| Stile del codice (PHPCS) | `composer run lint:php` (`lint:php:fix` per le correzioni automatiche) | — | No | — |
| Analisi statica (PHPStan) | `composer run lint:phpstan` | — | No | — |
| Test unit | `composer test:unit` (`composer test` lancia la stessa suite) | meno di un secondo | **Sì**, blocca il commit | [`unit/README.md`](unit/README.md) |
| Test di integrazione | `composer test:integration` | circa 10 secondi (il primo avvio scarica ACF e Polylang) | No | [`integration/README.md`](integration/README.md) |
| Test E2E | `composer test:e2e` (`test:e2e:headed` per vedere il browser, `test:e2e:ui` per l'interfaccia di Playwright) | circa 40 secondi | No | [`e2e/README.md`](e2e/README.md) |
| Tutti i test | `composer test:all` (unit, integrazione ed E2E, in quest'ordine) | circa 1 minuto e mezzo | No | requisiti di tutte e tre le suite; si ferma alla prima che fallisce |
| Report | `npm run status:scan`, `html:scan`, `pa11y:scan` (con l'URL dopo `--`) | minuti, dipende dal numero di pagine | No | [`report/README.md`](report/README.md) |

Quando eseguirli:
- **Prima di ogni commit:** il test unit parte da solo con l'hook; PHPCS e PHPStan si lanciano a mano sui file toccati.
- **Dopo una modifica che dipende da WordPress** (ruoli, permessi, rotte REST, query, tipi di contenuto): i test di integrazione.
- **Dopo una modifica a ciò che l'utente vede o a un flusso dell'amministrazione:** i test E2E.
- **Prima di una release, o dopo modifiche ai template e agli stili:** i Report, su un sito vero o su `laboratorio1`.


## Controlli statici

- `composer run lint:php` verifica lo stile con PHPCS (regole in `phpcs.xml.dist`); `composer run lint:php:fix` corregge da solo ciò che può.
- `composer run lint:phpstan` analizza il codice con PHPStan (configurazione in `phpstan.neon`).
- `composer audit` e `npm audit --omit=dev` cercano vulnerabilità note nelle dipendenze.

La soglia del tema è PHPStan livello 4, attivata sull'intero tema dopo il
completamento della migrazione in corso. `composer run lint:phpstan:level5`
misura invece il lavoro residuo verso il livello 5 e non blocca i commit; gli
errori non vanno nascosti con baseline generiche o `ignoreErrors` generici.

PHPStan non conosce WordPress da solo: alcuni file di supporto in `tests/utils/` gli dicono
come sono fatte le funzioni e le classi usate dal tema.


## Test

Le tre suite hanno ciascuna la propria cartella, configurazione e README:

- **Unit** (`tests/unit/`): funzioni e file del repository, senza WordPress, database o browser.
- **Integrazione** (`tests/integration/`): WordPress vero, con ACF e Polylang reali e il tema attivo, su un database dedicato che si svuota a ogni esecuzione.
- **E2E** (`tests/e2e/`): Playwright su un sito Local dedicato, che torna a uno stato di partenza prima e dopo ogni corsa.

Scelta del livello: si usa il più basso che dimostra il comportamento. Ciò che dipende da
WordPress va in integrazione; il browser serve solo per ciò che lo richiede, perché è il più
lento. Le regole per chi scrive o modifica codice (quando serve un test, test di regressione
per i difetti corretti) sono in `AGENTS/AI_BEHAVIOR.md`.


## Report

I moduli (`status-report`, `html-report`, `pa11y-report`) e le convenzioni comuni sono in
[`report/README.md`](report/README.md). I risultati finiscono in `tests/report/<modulo>/reports/` e
non vengono tracciati da Git.

Varianti: `status:scan:gate`, `html:scan:gate` e `pa11y:scan:gate` danno un codice di uscita diverso da zero
se il sito ha problemi; `status:compare` e `html:compare` confrontano gli ultimi due risultati. Chromium si installa
una volta con `npm run status:scan:install-browser`.


## Mappa della cartella

| Cartella | Contenuto |
|---|---|
| `tests/unit/` | Test unit (PHPUnit), con `tests/bootstrap-unit.php` e `phpunit.xml` nella radice |
| `tests/integration/` | Test di integrazione (PHPUnit), con `tests/bootstrap-integration.php` e `phpunit-integration.xml` |
| `tests/e2e/` | Test E2E (Playwright): specifiche, supporto, baseline del database; configurazione in `playwright.config.js` |
| `tests/report/` | Moduli Report |
| `tests/utils/` | File di supporto: stub di WordPress e dei plugin per PHPStan, `fetch-test-plugins.php` per i test di integrazione |


## Pacchetto di rilascio

Tutta la cartella `tests/`, `phpunit.xml`, `phpunit-integration.xml` e `playwright.config.js` sono nel
repository clonato ma **non** nel pacchetto zip che GitHub genera per ogni versione
(`export-ignore` in `.gitattributes`).
