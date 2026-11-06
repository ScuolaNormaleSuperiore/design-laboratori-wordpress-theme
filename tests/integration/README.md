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
[`../../phpunit-integration.xml`](../../phpunit-integration.xml).

Il codice di uscita è `0` se tutto passa, `1` altrimenti. Se manca un requisito la suite si ferma
subito con un messaggio che dice cosa manca.


### Eseguire un solo test

Ha senso e funziona: ogni test gira in una transazione che viene annullata alla fine, quindi un test isolato non dipende da
quelli che lo precedono e non lascia dati nel database di prova. Comandi provati, dalla radice del tema:

```sh
composer test:integration -- tests/integration/DLI_Environment_Integration_Test.php                       # un solo file
composer test:integration -- --filter DLI_Content_Managers_Integration_Test                         # una sola classe
composer test:integration -- --filter test_super_editor_role_exists                                 # un solo test
composer test:integration -- --filter DLI_Content_Managers_Integration_Test::test_publication_post_type_and_taxonomy_are_registered
vendor/bin/phpunit -c phpunit-integration.xml --filter test_super_editor_role_exists                # senza passare da Composer
vendor/bin/phpunit -c phpunit-integration.xml --list-tests                                          # tutti i test, come Classe::metodo
```

- Con `composer test:integration` gli argomenti dopo `--` arrivano a PHPUnit, ma lo script lancia prima `support/fetch-test-plugins.php`
  (pochi secondi in più; scarica ACF e Polylang solo al primo avvio). Con `vendor/bin/phpunit -c phpunit-integration.xml` si salta quel
  passaggio: usarlo solo se i plugin di test sono già stati preparati.
- `--filter` accetta un nome intero o una parte, anche nella forma `Classe::metodo`.
- **Se il filtro non trova nulla**, PHPUnit scrive `No tests executed!` ma esce con codice `0`: guardare sempre il numero di test eseguiti.
- Servono gli stessi requisiti della suite completa (database `dli_wordpress_test`, `wordpress-develop`): un solo test non li evita.


### Plugin di test

ACF e Polylang si scaricano da `downloads.wordpress.org` **la prima volta** che si lanciano i test, e solo
se mancano, in `tests/integration/.cache/plugins/` (ignorata da Git, mai nel pacchetto di rilascio). Si
scarica l'ultima versione stabile; la versione e l'impronta SHA-256 sono registrate in `.versions.json`
nella stessa cartella. Per aggiornare un plugin si cancella la sua cartella dalla cache.

Per usare una versione precisa (la CI lo fa, per avere corse ripetibili) si fissa con una variabile
d'ambiente, un numero a punti di due-quattro parti: `DLI_ACF_VERSION` e `DLI_POLYLANG_VERSION`.

```sh
DLI_ACF_VERSION=6.8.10 DLI_POLYLANG_VERSION=3.9.1 composer test:integration
```

Con una versione fissata si scarica proprio quella, e una copia in cache di un'altra versione viene
sostituita; un valore che non è un numero di versione ferma lo script con un errore. Senza variabili
non cambia nulla. Le funzioni che scelgono l'indirizzo e controllano la cache sono in
[`support/plugin-versions.php`](support/plugin-versions.php) e sono provate dal test unit
`DLI_Test_Plugin_Versions_Unit_Test`.

Se PHP non riesce a verificare il certificato (succede se manca il file `cacert.pem` indicato in `php.ini`),
lo script ripiega sul comando `curl` del sistema.


## Cosa copre

| File | Cosa verifica |
|---|---|
| `DLI_Attached_Posts_Integration_Test.php` | Attached Posts: il template JavaScript conserva l'URL di modifica WordPress e il segnaposto dell'ID, anziché ricevere «1» dalla conversione a booleano. |
| `DLI_Environment_Integration_Test.php` | L'ambiente: tema attivo, ACF e Polylang attivi, ruolo Super Editor, pagine create da `after_switch_theme` |
| `DLI_Search_Autocomplete_Integration_Test.php` | L'autocomplete della ricerca: rotta assente di default e presente se attiva, query corta, solo contenuti pubblicati, limite massimo di risultati |
| `DLI_Import_Rest_Integration_Test.php` | Le rotte REST che avviano gli import di Indico e IRIS: assenti con la REST API spenta, solo POST, autenticazione Basic (credenziali assenti, malformate o sbagliate, utente senza `manage_options`), amministratore che raggiunge l'importatore (con il modulo spento risponde senza chiamate remote) e interruttore `setup_rest_api()` |
| `DLI_Homepage_Sections_Integration_Test.php` | Le sezioni della home configurate (le ripetibili sono tre, tutte le altre sono singole): restituisce tutte le sezioni oppure solo quelle abilitate (con la posizione salvata), la lettura non riscrive le opzioni; la normalizzazione delle righe è in `DLI_Home_Sections_Unit_Test` |
| `DLI_Publication_Link_Integration_Test.php` | Dove porta il titolo di una pubblicazione (`dli_get_publication_link()`): tabella di verità scheda (anche senza scelta salvata) / sito web con e senza `Url` / allegato con e senza file / pubblicazione protetta da password (porta sempre alla scheda); il wrapper usato da ricerca, suggerimenti e carousel con link, descrizione e contenuto (vuoti se protetta); gli eventi continuano a usare il loro campo `sitoweb` |
| `DLI_Publication_Archive_Integration_Test.php` | L'elenco pubblicazioni (`page-templates/pubblicazioni.php`) e la ricerca del sito (`cerca.php`): titolo verso la scheda, verso il sito esterno con avviso «si apre in una nuova finestra» o testo semplice senza link (mai `href=""`), autori al posto della descrizione e nessun autore per una pubblicazione protetta, risultato di ricerca senza link senza anchor |
| `DLI_Publication_Detail_Integration_Test.php` | La scheda di una pubblicazione: dettaglio attivo (tipo di contenuto sempre pubblico e cercabile), indirizzo leggibile `/pubblicazione/<slug>/`, regole riscritte una sola volta per versione, rotta REST leggibile da un anonimo, archivio elencato; pulsante «Scarica la pubblicazione» per l'allegato (assente senza file e per una pubblicazione protetta); il template mostrato con tipologia, anno, autori (con link, senza link se la scheda persona è disattivata, mai in bozza o senza nome), descrizione (il solo `.` è ignorato), link esterno, segnaposto o copertina con il suo testo alternativo, e per una pubblicazione protetta da password solo il modulo. Dettaglio: dopo aver impostato i permalink il test registra di nuovo il tipo di contenuto, perché nel processo di test WordPress aggiunge le regole di un tipo solo se i permalink sono attivi alla registrazione |
| `DLI_Content_Managers_Integration_Test.php` | I manager dei contenuti: gruppi di campi ACF registrati con le loro chiavi e i loro campi, tipo di contenuto `pubblicazione` con la sua tassonomia; `DLI_ContentsManager` e `DLI_OG_Wrapper` nei file che portano il loro nome, dati Open Graph restituiti come `DLI_OG_Wrapper`; `get_post_types_with_results()` restituisce solo i tipi con contenuti nello stato richiesto, nell'ordine dato e senza duplicati, e un risultato vuoto per un input non valido |
| `DLI_Categories_By_Content_Type_Integration_Test.php` | Le categorie dei filtri degli archivi (`dli_get_all_categories_by_ct()`): elenca quelle usate da contenuti pubblicati, con i dati necessari, mai «Uncategorized» e non quelle usate solo da bozze |
| `DLI_Base_Importer_Integration_Test.php` | L'assistente dei termini degli importatori (`get_or_create_term_id()`): crea un termine mancante, riusa quello esistente senza duplicarlo, ignora gli spazi, rifiuta nome vuoto o tassonomia sconosciuta |
| `DLI_Newsletter_Integration_Test.php` | Client Brevo della newsletter: payload di double opt-in e gestione di risposte accettate o di errore, senza richieste esterne |
| `DLI_Contact_Form_Integration_Test.php` | Il form dei contatti (`page-templates/contatti.php` mostrato con una richiesta POST simulata, CAPTCHA finto e `wp_mail()` intercettato, senza SMTP): invio riuscito, invio fallito, CAPTCHA o prefisso sbagliati, sfida usata una sola volta (riuso e risposta sbagliata la consumano), nonce sbagliato o assente, campi obbligatori ed e-mail non valida, nessuna intestazione aggiunta con gli a capo nell'indirizzo, copia di ricevuta solo se richiesta, form non utilizzabile senza il plugin CAPTCHA, form non inviato senza messaggi. Dettaglio: nel `wordpress-develop` di prova mancano i file compilati (emoji, stili dei blocchi), quindi il test stacca le azioni di WordPress che li leggono |
| `DLI_Newsletter_Page_Captcha_Integration_Test.php` | Il CAPTCHA della pagina newsletter (`page-templates/newsletter.php`, CAPTCHA finto e Brevo intercettato): una sfida si usa una volta, anche con risposta sbagliata, e il modulo torna con una sfida nuova |
| `DLI_Home_Video_Integration_Test.php` | La sezione «Video»: URL per riga (valore storico ignorato, indirizzo non web scartato, `dli_sanitize_hp_video_url()`), ID, etichette e pulsanti distinti per istanza e funzione JavaScript unica, ID invariati senza istanza (schede di evento, brevetto e spin-off), sfondo, titolo solo nella lingua della pagina e solo se mostrato |
| `DLI_Home_Banners_Integration_Test.php` | La sezione «Banner»: modalità automatica (per priorità, numero della riga, 3 di default) e manuale (ordine scelto, traduzione, bozze/cestinati/mancanti scartati, nessuna riempitura), ID unici per istanza, titolo come il Video, scelte del campo con i banner già scelti ma non visibili, nessun N+1 |
| `DLI_Home_Page_Index_Integration_Test.php` | La sezione «Indice di una pagina» (`dli_get_page_link_group()`, `dli_get_page_index()` e il template): colonne solo per le sottopagine con figli pubblicati e senza password, limiti di colonne e link, pagina assente, bozza, protetta o non pagina senza indice, traduzione della pagina scelta (senza traduzione niente), titolo e nome accessibile, ID unici con due copie della stessa pagina, sfondo bianco, scelte che tengono la pagina già scelta ma non più visibile |
| `DLI_Home_Sections_Fields_Integration_Test.php` | I campi di una riga di Sezioni HP: le descrizioni di «Numero massimo di elementi» e «Numero massimo di sottopagine per gruppo» dicono che cosa conta il numero per ogni sezione (card, gruppi, colonne, link) e mantengono l'intervallo da 1 a 12 |
| `DLI_Options_Save_Permissions_Integration_Test.php` | Il salvataggio delle pagine di Configurazione con il gestore di CMB2 (`admin-post.php`) sui veri box del tema: un Editor o un Subscriber non salva anche con un nonce valido, il Super Editor e l'amministratore salvano, le schede Indico, Iris e Altro esistono solo per l'amministratore (`dli_options_can_save_by_capability()`) |
| `DLI_Home_Presentation_Integration_Test.php` | La sezione «Il laboratorio» (`site-presentation.php`): spaziatura con padding esplicito (`pt-5 pb-5`, senza la classe inesistente `my-12`), titolo e testo, niente stampa a sezione spenta. |
| `DLI_People_Table_Integration_Test.php` | La tabella delle persone (`template-parts/persone/view-tabella.php`): con la colonna nascosta telefono ed e-mail non sono né letti né nella pagina (HTML e dati dello script); il link per chiamare ha solo cifre e `+`; il sito di una persona è un link solo con schema http o https (gli altri, come `javascript:`, ricadono sulla scheda); nessun link con la scheda disattivata |
| `DLI_Search_Content_Types_Integration_Test.php` | I tipi di contenuto offerti come filtro dalla ricerca (`get_contenttypes_with_search_results()`): solo quelli con una corrispondenza pubblicata, nell'ordine della ricerca, per la lingua corrente, senza le tipologie delle persone; nessuna query carica tutte le corrispondenze |

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
