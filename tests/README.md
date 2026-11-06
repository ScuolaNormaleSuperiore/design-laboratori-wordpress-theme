# Controlli, test e report

Questo è il punto di ingresso: spiega a cosa serve ogni strumento e come si lancia. Requisiti,
configurazione e variabili d'ambiente sono nei README di [test unit](unit/README.md),
[integrazione](integration/README.md), [E2E](e2e/README.md), [PHPStan](phpstan/README.md)
e [Report](report/README.md).

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
| Stile del codice (PHPCS) | `composer run lint:php` (`lint:php:fix` per le correzioni automatiche) | l'intero tema, qualche minuto; l'hook controlla solo i file in staging, meno di un secondo ciascuno | **Sì, solo gli errori** dei file PHP, CSS e SCSS in staging | — |
| Analisi statica (PHPStan) | `composer run lint:phpstan` | pochi secondi | **Sì**, blocca il commit | — |
| Test unit | `composer test:unit` (`composer test` lancia la stessa suite) | meno di un secondo | **Sì**, blocca il commit | [`unit/README.md`](unit/README.md) |
| Test di integrazione | `composer test:integration` | circa 20 secondi (il primo avvio scarica ACF e Polylang) | No | [`integration/README.md`](integration/README.md) |
| Test E2E | `composer test:e2e` (`test:e2e:headed` per vedere il browser, `test:e2e:ui` per l'interfaccia di Playwright) | circa 10 minuti per l'intera suite (una sola specifica, pochi secondi) | No | [`e2e/README.md`](e2e/README.md) |
| Unit e integrazione insieme | `composer test:all` (unit e integrazione, in quest'ordine; **gli E2E non ci sono**) | circa 2 minuti | No | requisiti delle due suite; si ferma alla prima che fallisce. La suite E2E è lunga (circa 10 minuti) e si lancia a parte con `composer test:e2e`. Composer interrompe ogni comando dopo 300 secondi per impostazione predefinita: `composer.json` alza il limite a 1200 (`config.process-timeout`), perché la suite E2E supera i 300 secondi |
| Report | `npm run status:scan`, `html:scan`, `pa11y:scan` (con l'URL dopo `--`) | minuti, dipende dal numero di pagine | No | [`report/README.md`](report/README.md) |

Quando eseguirli:
- **Prima di ogni commit:** l'hook esegue da solo PHPStan (livello 5, intero tema), PHPCS sui file in staging e i test unit, e blocca il commit se uno fallisce. Per PHPCS bloccano solo gli **errori** (gli avvisi non vengono mostrati) e conta ciò che è in staging, non il file su disco.
- **Dopo ogni push:** GitHub Actions (workflow `CI`) ripete su una macchina pulita sintassi, test unit, PHPCS sui file modificati e PHPStan; sintassi e test unit girano anche su PHP 8.0. Vedi [Controlli in CI](#controlli-in-ci-github-actions).
- **Dopo una modifica che dipende da WordPress** (ruoli, permessi, rotte REST, query, tipi di contenuto): i test di integrazione.
- **Dopo una modifica a ciò che l'utente vede o a un flusso dell'amministrazione:** i test E2E.
- **Prima di una release, o dopo modifiche ai template e agli stili:** i Report, su un sito vero o su `laboratorio1`.


### Eseguire un solo test

Ha senso e funziona con tutte e tre le suite. Serve mentre si sviluppa un test o si indaga su un fallimento:
si risparmia tempo e si vede un solo risultato. Non sostituisce la suite completa, che resta quella da lanciare prima di un commit
o di una release. I comandi, tutti da lanciare dalla radice del tema, sono stati provati:

| Suite | Un solo file | Un solo test | Elencare i test disponibili |
|---|---|---|---|
| Unit | `composer test:unit -- tests/unit/DLI_Utils_Unit_Test.php` | `composer test:unit -- --filter test_persona_display_name_joins_first_and_last_name` | `composer test:unit -- --list-tests` |
| Integrazione | `composer test:integration -- tests/integration/DLI_Environment_Integration_Test.php` | `composer test:integration -- --filter test_super_editor_role_exists` | `vendor/bin/phpunit -c phpunit-integration.xml --list-tests` |
| E2E | `composer test:e2e -- tests/e2e/specs/admin-login.spec.js` | `composer test:e2e -- tests/e2e/specs/contact-form.spec.js:44` oppure `composer test:e2e -- -g "wrong nonce"` | `composer test:e2e -- --list` |

Regole utili:
- Tutto ciò che sta dopo `--` viene passato allo strumento che esegue i test (PHPUnit o Playwright).
- Con PHPUnit, `--filter` accetta un nome intero o una parte (`--filter DLI_Utils_Unit_Test` esegue tutta la classe) e anche la forma `Classe::metodo`.
- **Se il filtro non trova nulla**, PHPUnit scrive «No tests executed!» ma esce con codice `0`: un errore di battitura sembra un successo. Guardare sempre il numero di test eseguiti. Playwright, nello stesso caso, esce con codice `1`.
- Un test di integrazione o E2E isolato può passare da solo e fallire dentro la suite (o il contrario) se dipende dallo stato lasciato da un altro: ogni test di integrazione gira in una transazione annullata a fine test, mentre per l'E2E la baseline si ripristina prima e dopo ogni corsa.
- Il dettaglio di ciascuna suite (altre opzioni, ripetizioni, variabili) è nel suo README: [unit](unit/README.md#eseguire-un-solo-test), [integrazione](integration/README.md#eseguire-un-solo-test), [E2E](e2e/README.md#eseguire-un-solo-test).


## Cosa controlla il commit

Gli hook di Git stanno in `.githooks/` e si attivano una volta per clone con `git config core.hooksPath .githooks`. A ogni commit scattano sei controlli, nell'ordine della tabella:

| # | Controllo | Cosa guarda | Blocca il commit? |
|---|---|---|---|
| 1 | Segreti (`check-staged-secrets.sh`) | Le righe aggiunte: chiavi private, token (AWS, GitHub, Slack), `sk-…`, URL con utente e password, `password = "…"` | Sì |
| 2 | Guardia l10n (`check-staged-l10n.sh`) | I file `languages/*.l10n.php`: rimette la riga `defined( 'ABSPATH' ) \|\| exit;` che Loco Translate toglie a ogni esportazione | No: corregge da sola e rimette il file in staging |
| 3 | PHPStan livello 5 (`check-phpstan.sh`) | L'intero tema, non solo i file del commit | Sì, a qualunque errore |
| 4 | PHPCS (`check-staged-phpcs.sh`) | I file PHP, CSS e SCSS in staging, sul contenuto in staging | Sì, solo per gli **errori**; gli avvisi non bloccano |
| 5 | Test unit | La suite unit (`composer test:unit`) | Sì |
| 6 | Testo del commit (`commit-msg`) | Un trailer `Co-Authored-By:` che attribuisce il commit a un assistente AI | Sì |

Cinque controlli su sei bloccano; i controlli 1-5 sono nel `pre-commit` e il 6 nel `commit-msg`.

- Il primo controllo che fallisce ferma gli altri: i controlli seguenti non partono finché non lo risolvi.
- PHPCS blocca anche per gli errori già presenti in un file che tocchi: chi modifica un file con errori deve correggerli.
- `git commit --no-verify` salta **tutti** i controlli, segreti compresi: non si usa.
- Non partono nel commit: integrazione, E2E, Report, `composer audit` e `composer run lint:phpstan:level6`.

## Controlli in CI (GitHub Actions)

Il workflow [`.github/workflows/ci.yml`](../.github/workflows/ci.yml) è la rete di sicurezza dell'hook: gli stessi controlli su una macchina pulita, per chi non ha attivato gli hook e per le pull request, più PHP 8.0 (il minimo dichiarato). È **osservativo**: una corsa rossa non rifiuta il push e non blocca il merge. Parte a ogni push su `dev`, `main` e sui rami `features/**`, `bugfix/**`, `refactor/**`, `docs/**`, a ogni pull request verso `dev` e `main`, e a mano dalla scheda *Actions*.

| Controllo | Comando | PHP |
|---|---|---|
| Sintassi PHP | `php -l` su tutti i file | 8.0 e 8.4 |
| Marcatori di conflitto e spazi finali | `git grep`, `git diff --check` | 8.4 |
| Test unit | `composer test:unit` | 8.0 e 8.4 |
| PHPCS (solo errori) | `.github/scripts/phpcs-changed.sh`, sui file PHP, CSS e SCSS modificati | 8.4 |
| PHPStan livello 5 | `composer run lint:phpstan`, sull'intero tema | 8.4 |

La scansione dei segreti, la guardia l10n e il controllo del messaggio di commit esistono solo negli hook locali. I test di integrazione girano in CI solo nel job `integration` (push su `dev` e `main`, PR verso `main`, avvio manuale), con WordPress, ACF e Polylang a versioni fissate; le corse restano osservate nel tempo.

Sempre su GitHub, il workflow *Scorecard supply-chain security* (OpenSSF Scorecard, badge in cima al README) esegue un controllo periodico e non bloccante delle pratiche di sicurezza della supply chain del repository. I risultati sono consultabili nella sezione *Security* del repository; non sostituisce i controlli locali. La tabella completa, aggiornata insieme al workflow, è in [`AGENTS/ARCHITECTURE.md`](../AGENTS/ARCHITECTURE.md).

### Cosa gira e quando

Vista per momento; la fonte di verità sono gli hook in [`../.githooks/`](../.githooks/) e i workflow in [`../.github/workflows/`](../.github/workflows/), e questa tabella si aggiorna nello stesso lavoro che li cambia. Salvo dove indicato, in CI un controllo rosso non blocca nulla.

| Momento | Cosa gira | Blocca? |
|---|---|---|
| **Commit** (hook locale, se attivo) | Segreti, guardia l10n (corregge da sola), PHPStan livello 5 sull'intero tema, PHPCS sui file PHP, CSS e SCSS in staging (solo errori), test unit; controllo del messaggio (trailer AI) | Sì, tranne la guardia l10n; `--no-verify` salta tutto |
| **Push** su `dev` o `main` | Job `checks` (sintassi su PHP 8.0 e 8.4, conflitti, spazi finali, test unit su 8.0 e 8.4, PHPCS sui file modificati, PHPStan livello 5) e job `integration` (333 test su WordPress 7.1.4 con ACF, Polylang e MariaDB); se cambiano `DOC/**` o simili, anche `documentation-check` | No |
| **Push** su `features/**`, `bugfix/**`, `refactor/**`, `docs/**` | Solo `checks` (e `documentation-check` su `docs/**`) | No |
| **Pull request verso `dev`** (per esempio Dependabot) | `checks` | No |
| **Pull request verso `main`** (rilascio da `dev`) | `checks` e `integration`; `documentation-check` se cambia la documentazione | No |
| **Merge su `main`** (push) | `checks` e `integration`; `documentation-pages` (pubblica la documentazione su GitHub Pages, se cambia); Scorecard | No |
| **Programmati** | Scorecard ogni lunedì e al cambio di una regola di protezione dei rami; Dependabot, PR mensili verso `dev` (GitHub Actions, Composer, npm, Docker) | No |
| **Solo a mano** | Test E2E, Report (`status`, `html`, `pa11y`), `composer audit`, `npm audit --omit=dev`, `composer run lint:phpstan:level6`, e l'avvio manuale di `CI` dalla scheda *Actions* | — |

Non esiste una protezione dei rami (decisione D3): nessun controllo impedisce un push o un merge.


## Controlli statici

- `composer run lint:php` verifica lo stile con PHPCS (regole in [`../phpcs.xml.dist`](../phpcs.xml.dist)); `composer run lint:php:fix` corregge da solo ciò che può. Il tema ha ancora molte segnalazioni preesistenti (issue nel backlog): l'hook `pre-commit` esegue PHPCS ([`../.githooks/check-staged-phpcs.sh`](../.githooks/check-staged-phpcs.sh)) sui file in staging e blocca il commit se hanno **errori**, anche preesistenti; chi tocca un file con errori deve quindi correggerli. È una prova decisa il 2026-10-26: se blocca troppo si passa a «nessuna segnalazione nuova rispetto a `HEAD`».
- `composer run lint:phpstan` analizza il codice con PHPStan (configurazione in [`../phpstan.neon`](../phpstan.neon)).
- `composer audit` e `npm audit --omit=dev` cercano vulnerabilità note nelle dipendenze.

La soglia del tema è PHPStan livello 5 sull'intero tema ed è un controllo bloccante
nell'hook `pre-commit`. `composer run lint:phpstan:level6`
misura invece il lavoro residuo verso il livello 6 e non blocca i commit; gli
errori non vanno nascosti con baseline generiche o `ignoreErrors` generici.

PHPStan non conosce WordPress da solo: gli [stub locali](phpstan/README.md) gli dicono
come sono fatte le funzioni e le classi usate dal tema.


## Test

Le tre suite hanno ciascuna la propria cartella, configurazione e README:

- **[Unit](unit/README.md)**: funzioni e file del repository, senza WordPress, database o browser.
- **[Integrazione](integration/README.md)**: WordPress vero, con ACF e Polylang reali e il tema attivo, su un database dedicato che si svuota a ogni esecuzione.
- **[E2E](e2e/README.md)**: Playwright su un sito Local dedicato, che torna a uno stato di partenza prima e dopo ogni corsa.

Scelta del livello: si usa il più basso che dimostra il comportamento. Ciò che dipende da
WordPress va in integrazione; il browser serve solo per ciò che lo richiede, perché è il più
lento. Le regole per chi scrive o modifica codice (quando serve un test, test di regressione
per i difetti corretti) sono in [`../AGENTS/AI_BEHAVIOR.md`](../AGENTS/AI_BEHAVIOR.md).


## Report

I moduli (`status-report`, `html-report`, `pa11y-report`) e le convenzioni comuni sono in
[`report/README.md`](report/README.md). I risultati finiscono nelle cartelle [`report/<modulo>/reports/`](report/) e
non vengono tracciati da Git.

Varianti: `status:scan:gate`, `html:scan:gate` e `pa11y:scan:gate` danno un codice di uscita diverso da zero
se il sito ha problemi; `status:compare` e `html:compare` confrontano gli ultimi due risultati. Chromium si installa
una volta con `npm run status:scan:install-browser`.


## Mappa della cartella

| Cartella | Contenuto |
|---|---|
| [unit/](unit/) | Test unit (PHPUnit), con [`bootstrap-unit.php`](bootstrap-unit.php) e [`phpunit.xml`](../phpunit.xml) nella radice |
| [integration/](integration/) | Test di integrazione (PHPUnit), con [`bootstrap-integration.php`](bootstrap-integration.php) e [`phpunit-integration.xml`](../phpunit-integration.xml) |
| [integration/support/](integration/support/) | Script di preparazione dei plugin richiesti dai test di integrazione |
| [e2e/](e2e/) | Test E2E (Playwright): [specifiche](e2e/specs/), [supporto](e2e/support/), [baseline](e2e/fixtures/); configurazione in [`../playwright.config.js`](../playwright.config.js) |
| [report/](report/) | Moduli Report |
| [phpstan/](phpstan/) | Stub locali di WordPress e dei plugin caricati soltanto da PHPStan: vedi [`phpstan/README.md`](phpstan/README.md) |


## Pacchetto di rilascio

Tutta la cartella [`tests/`](.), [`phpunit.xml`](../phpunit.xml),
[`phpunit-integration.xml`](../phpunit-integration.xml) e [`playwright.config.js`](../playwright.config.js) sono nel
repository clonato ma **non** nel pacchetto zip che GitHub genera per ogni versione
(`export-ignore` in [`../.gitattributes`](../.gitattributes)).
