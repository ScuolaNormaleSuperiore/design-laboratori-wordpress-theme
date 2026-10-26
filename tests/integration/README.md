# Test di integrazione (WordPress reale)

I test di integrazione controllano il comportamento che **dipende da WordPress**: hook, ruoli e
permessi, tipi di contenuto, query, rotte REST, attivazione del tema. Girano con PHPUnit su un
WordPress vero (la suite di test di `wordpress-develop`), con i plugin **ACF** e **Polylang** reali
e questo tema attivo, su un database dedicato: il framework ne cancella e ricrea le tabelle
(prefisso `wptests_`) a ogni esecuzione.

Sono pensati per chi mantiene il tema (Windows e Local). Non girano nell'hook di commit e non
richiedono un sito, ma richiedono un database di test e `wordpress-develop`. La logica che non
dipende da WordPress va invece nei [test unit](../unit/README.md).


## Requisiti

- PHP 8.0 o superiore con `mysqli`, `zip` e `openssl`, e Composer (`composer install`).
- Una copia di `wordpress-develop` (predefinita: `C:/WordpressDEV/wordpress-develop`).
- Un database **vuoto** usato solo dai test, su MariaDB o MySQL di Local. Mai il database di un sito.
- Il file `wp-tests-config.php` nella radice di `wordpress-develop`.


### Preparazione una tantum

1. Creare il database:

   ```sql
   CREATE DATABASE IF NOT EXISTS dli_wordpress_test
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci;
   ```

   Se l'utente non è `root`: `GRANT ALL PRIVILEGES ON dli_wordpress_test.* TO 'utente'@'localhost';`.
   La porta si trova in Local, nella scheda Database del sito.
2. Creare `wp-tests-config.php` partendo da `wp-tests-config-sample.php`, con `DB_NAME` uguale a
   `dli_wordpress_test`, utente, password, `DB_HOST` con la porta di Local e `$table_prefix = 'wptests_';`.

**Per sicurezza** la suite si rifiuta di partire se `DB_NAME` non è `dli_wordpress_test`: il
framework cancellerebbe le tabelle di qualunque altro database indicato.


## Configurazione e variabili d'ambiente

| Variabile | Significato | Valore predefinito |
|---|---|---|
| `WP_TESTS_DIR` | cartella `tests/phpunit` di `wordpress-develop` | `C:/WordpressDEV/wordpress-develop/tests/phpunit` |
| `WP_TESTS_CONFIG_FILE_PATH` | `wp-tests-config.php` alternativo | quello nella radice di `wordpress-develop` |
| `DLI_TEST_PLUGINS_DIR` | cartella con le cartelle dei plugin da caricare | `tests/integration/.cache/plugins` |

Nessuna è obbligatoria con la configurazione predefinita. Esempio di sostituzione (PowerShell), valido per la sessione corrente:

```
$env:WP_TESTS_DIR = 'D:/altro/wordpress-develop/tests/phpunit'
```


## Esecuzione

Dalla radice del tema:

```sh
composer test:integration
```

Il comando lancia prima [`support/fetch-test-plugins.php`](support/fetch-test-plugins.php) (prepara ACF e Polylang, vedi sotto) e poi PHPUnit con
[`../../phpunit-integration.xml`](../../phpunit-integration.xml). Per un solo file o un solo test:

```sh
vendor/bin/phpunit -c phpunit-integration.xml --filter test_super_editor_role_exists
```

Il codice di uscita è `0` se tutto passa, `1` altrimenti. Se manca un requisito la suite si ferma
subito con un messaggio che dice cosa manca.


### Plugin di test

ACF e Polylang si scaricano da `downloads.wordpress.org` **la prima volta** che si lanciano i test, e solo
se mancano, in `tests/integration/.cache/plugins/` (ignorata da Git, mai nel pacchetto di rilascio). Si
scarica l'ultima versione stabile; la versione e l'impronta SHA-256 sono registrate in `.versions.json`
nella stessa cartella. Per aggiornare un plugin si cancella la sua cartella dalla cache.

Se PHP non riesce a verificare il certificato (succede se manca il file `cacert.pem` indicato in `php.ini`),
lo script ripiega sul comando `curl` del sistema.


## Cosa copre

| File | Cosa verifica |
|---|---|
| `DLI_Spike_Integration_Test.php` | L'ambiente: tema attivo, ACF e Polylang attivi, ruolo Super Editor, pagine create da `after_switch_theme`, tipo di contenuto `persona` |
| `DLI_Search_Autocomplete_Integration_Test.php` | L'autocomplete della ricerca: rotta assente di default e presente se attiva, query corta, solo contenuti pubblicati, limite massimo di risultati |
| `DLI_Homepage_Sections_Integration_Test.php` | Le sezioni della home configurate: restituisce tutte le sezioni oppure solo quelle abilitate quando richiesto |
| `DLI_Categories_By_Content_Type_Integration_Test.php` | Le categorie dei filtri degli archivi (`dli_get_all_categories_by_ct()`): elenca quelle usate da contenuti pubblicati, con i dati necessari, mai «Uncategorized» e non quelle usate solo da bozze |
| `DLI_Base_Importer_Integration_Test.php` | L'assistente dei termini degli importatori (`get_or_create_term_id()`): crea un termine mancante, riusa quello esistente senza duplicarlo, ignora gli spazi, rifiuta nome vuoto o tassonomia sconosciuta |
| `DLI_Newsletter_Integration_Test.php` | Client Brevo della newsletter: payload di double opt-in e gestione di risposte accettate o di errore, senza richieste esterne |
| `DLI_Contact_Form_Integration_Test.php` | Il form dei contatti (`page-templates/contatti.php` mostrato con una richiesta POST simulata, CAPTCHA finto e `wp_mail()` intercettato, senza SMTP): invio riuscito, invio fallito, CAPTCHA o prefisso sbagliati, nonce sbagliato o assente, campi obbligatori ed e-mail non valida, nessuna intestazione aggiunta con gli a capo nell'indirizzo, copia di ricevuta solo se richiesta, form non utilizzabile senza il plugin CAPTCHA, form non inviato senza messaggi. Dettaglio: nel `wordpress-develop` di prova mancano i file compilati (emoji, stili dei blocchi), quindi il test stacca le azioni di WordPress che li leggono |

Sono ancora da scrivere, tra gli altri: le rotte di importazione, i permessi, sitemap e manifest, le query dei
contenuti, gli elenchi vuoti, le modalità di visualizzazione delle persone, l'attivazione del tema, i tipi di
contenuto e le tassonomie.


## Come funziona

- [`phpunit-integration.xml`](../../phpunit-integration.xml) definisce la suite `integration` su questa [cartella](./).
- [`../bootstrap-integration.php`](../bootstrap-integration.php) controlla `DB_NAME`, sceglie la cartella dei plugin, carica la suite di
  WordPress e rende attivi ACF, Polylang e il tema (che sta fuori dal WordPress di test).
- La classe base [`DLI_Integration_Test_Case`](support/class-dli-integration-test-case.php) crea la lingua italiana, di cui Polylang
  ha bisogno per ogni query per lingua, e offre `create_post()` e `set_theme_options()`.
- Ogni test gira in una transazione annullata alla fine: i dati creati da un test non restano nel database.

Trabocchetti noti:
- Polylang carica la sua API solo quando ha un contesto; con un database vuoto non ce l'ha, quindi il
  bootstrap forza `PLL_Frontend` (filtro `pll_context`).
- Le pagine predefinite non si creano all'attivazione nel framework di test: nel test che le richiede
  si chiama `do_action( 'after_switch_theme' )`.
- Il ruolo Super Editor è creato su `init`, non all'attivazione del tema.
- CMB2 tiene in memoria le opzioni che ha letto per tutto il processo: le opzioni del tema si cambiano
  con `set_theme_options()`, non con `update_option()`.


## Come aggiungere un test

Creare un file nella [cartella dei test di integrazione](./) chiamato `DLI_<Nome>_Integration_Test.php` (nome del file uguale a quello della classe) con una
classe che estende `DLI_Integration_Test_Case`. Per ogni difetto di tipo `Bug` o `Security` corretto va aggiunto il
test di regressione (vedi `Test-driven workflow` in [`../../AGENTS/AI_BEHAVIOR.md`](../../AGENTS/AI_BEHAVIOR.md)). Se il codice da provare non
dipende da WordPress, è un test unit.
