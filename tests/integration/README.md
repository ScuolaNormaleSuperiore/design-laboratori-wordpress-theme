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
| `DLI_Environment_Integration_Test.php` | L'ambiente: ruolo Super Editor, pagine create da `after_switch_theme` (che il tema, ACF e Polylang siano attivi lo dimostra ogni altro test) |
| `DLI_Reserved_Area_Roles_Integration_Test.php` | Ciò che i due ruoli dell'area riservata («Utente in attesa» e «Utente area riservata») hanno in comune, controllato una volta per entrambi con un data provider: il ruolo esiste con le sue capability (nessuna, oppure il solo marcatore del tema), l'utente non può modificare il proprio profilo né con l'aggiornamento REST (con `first_name` e con `email`), ricreare il ruolo non toglie le capability aggiunte dopo; con i controlli sul Subscriber. Il resto è in `DLI_Pending_User_Role_Integration_Test` e `DLI_Reserved_Area_User_Role_Integration_Test` |
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
| `DLI_Home_Sections_Fields_Integration_Test.php` | I campi di una riga di Sezioni HP: le descrizioni di «Numero massimo di elementi» e «Numero massimo di sottopagine per gruppo» mantengono l'intervallo da 1 a 12 |
| `DLI_Options_Save_Permissions_Integration_Test.php` | Il salvataggio delle pagine di Configurazione con il gestore di CMB2 (`admin-post.php`) sui veri box del tema: un Editor o un Subscriber non salva anche con un nonce valido, il Super Editor e l'amministratore salvano, le schede Indico, Iris e Altro esistono solo per l'amministratore (`dli_options_can_save_by_capability()`) |
| `DLI_Home_Presentation_Integration_Test.php` | La sezione «Il laboratorio» (`site-presentation.php`): spaziatura con padding esplicito (`pt-5 pb-5`, senza la classe inesistente `my-12`), titolo e testo, niente stampa a sezione spenta. |
| `DLI_People_Table_Integration_Test.php` | La tabella delle persone (`template-parts/persone/view-tabella.php`): con la colonna nascosta telefono ed e-mail non sono né letti né nella pagina (HTML e dati dello script); il link per chiamare ha solo cifre e `+`; il sito di una persona è un link solo con schema http o https (gli altri, come `javascript:`, ricadono sulla scheda); nessun link con la scheda disattivata |
| `DLI_Search_Content_Types_Integration_Test.php` | I tipi di contenuto offerti come filtro dalla ricerca (`get_contenttypes_with_search_results()`): solo quelli con una corrispondenza pubblicata, nell'ordine della ricerca, per la lingua corrente, senza le tipologie delle persone; nessuna query carica tutte le corrispondenze |
| `DLI_Image_Sizes_Integration_Test.php` | Le dimensioni delle immagini del tema: `item-thumb` (250 × 250, ritagliata) è registrata, quelle esistenti non cambiano, la foto di una persona è il file 250 × 250 e senza foto resta l'avatar dell'utente |
| `DLI_Image_Field_Descriptions_Integration_Test.php` | I testi sul formato sotto i campi CMB2 della configurazione (hero e tre loghi): la frase sullo scopo del logo resta, il formato viene dall'elenco, nessun markup |
| `DLI_Image_Field_Instructions_Integration_Test.php` | Le istruzioni dei campi ACF «Logo» (spin-off) e «Foto» (risorsa tecnica), impostate dal filtro `acf/load_field`: solo consiglio, nessun limite che rifiuti il file |
| `DLI_Featured_Image_Guideline_Integration_Test.php` | Il testo sotto «Immagine in evidenza» solo per Banner e Sponsor (editor classico): il riquadro di WordPress resta, gli altri tipi, le pagine e gli articoli non cambiano |
| `DLI_Media_Window_Guideline_Integration_Test.php` | Il messaggio della finestra dei media: i limiti di caricamento restano, segue la riga generale sul formato; il link alla guida compare solo con un indirizzo http o https, ed è escluso se l'indirizzo è ostile |
| `DLI_Restricted_Content_Integration_Test.php` | Un contenuto riservato da Groups (riprodotto con un filtro `posts_where` e una classe `Groups_Post_Access` finta): 401 con challenge `WWW-Authenticate: DLI-SSO` e pulsante «Accedi» per il visitatore, 403 senza challenge per l'utente collegato (con il messaggio di attesa per l'utente in attesa), il template e il titolo senza il nome del contenuto, `noindex`; vale per pagina, articolo, notizia, evento, persona e pubblicazione e per i percorsi «pretty» di una sottopagina; indirizzo inesistente o con un percorso sbagliato che termina con lo slug di un contenuto riservato, stesso slug sotto genitori diversi, ordine rispetto al redirect canonico, bozza, scheda spenta dal tema e contenuto leggibile restano come prima; con l'area spenta o con una richiesta normale non si aggiunge nessuna query |
| `DLI_Acf_Exports_Integration_Test.php` | Gli export ACF in `SETUP/ACF_Custom_Fields/` descrivono gli stessi campi dei gruppi registrati dal tema (proprietà di campi e gruppi, campi mancanti o in più), e ogni gruppo ha un export |
| `DLI_Detail_Pages_Integration_Test.php` | Le pagine di dettaglio disattivate dall'editor (`inc/detail-pages.php`): 404 per la persona con `disattiva_pagina_dettaglio` e per evento, notizia e pubblicazione con `link_dettaglio` verso sito o allegato; pagina mantenuta con il dettaglio attivo, con il link diretto al sito della persona e per la pubblicazione protetta; visibile a chi può modificarla; altri tipi non toccati; la ricerca non elenca le persone escluse e non dà il link alle persone senza dettaglio |
| `DLI_Category_Archive_Integration_Test.php` | L'archivio di una categoria risponde 404 con la pagina 404 del tema; una pagina filtrata per categoria non è un 404 |
| `DLI_Search_Fallback_Integration_Test.php` | La ricerca nativa di WordPress (`/?s=termine`) porta alla pagina di ricerca del tema: termine codificato, senza markup, barre rovesciate conservate, tagliato se troppo lungo, ricerca vuota, redirect che resta sul sito; `index.php` risponde 404 con la pagina del tema e una pagina resta una pagina |
| `DLI_Restricted_Pages_Integration_Test.php` | Gli elenchi di pagine del tema rispettano un plugin di restrizione come Groups (riprodotto con un filtro sulla query): card delle sottopagine, card promosse, indice di una pagina, gruppi della navigazione per pagine |
| `DLI_Page_Subpage_Cards_Integration_Test.php` | La pagina base e le card delle sue sottopagine: campi della pagina con le loro chiavi, riassunto attivo, sottopagine pubblicate dirette in ordine di menu, riassunto o testo tagliato, card prima del testo solo se richiesto, nessuna lista vuota né figlia protetta, pagina protetta senza elenco |
| `DLI_Header_Language_Integration_Test.php` | La lingua dichiarata dall'head: `lang` dell'elemento `html` secondo la pagina, niente meta obsoleto `content-language`, gli altri meta restano |
| `DLI_Header_Menus_Integration_Test.php` | I due menu dell'header (principale e secondario): identificatore di ogni tendina e suo pulsante, `data-element` dalla pagina della voce, voci senza indirizzo escluse con markup bilanciato, genitore senza indirizzo senza link a sé stesso, tendina stampata allo stesso modo nei due menu |
| `DLI_Footer_Headings_Integration_Test.php` | I titoli del footer: nessun salto di livello, motto come paragrafo, titoli delle colonne senza link vuoti, titolo della newsletter con il suo link |
| `DLI_Options_Assets_Integration_Test.php` | Gli script della pagina di configurazione: lo script del tema è caricato dopo il footer di CMB2 e riceve l'elenco delle sezioni della home che si possono aggiungere una volta sola |
| `DLI_Home_Navigation_Section_Integration_Test.php` | La sezione «Card di navigazione»: pagine promosse pubblicate in ordine e nella lingua corrente, protette e bozze escluse a ogni livello, riassunto o testo tagliato, numero di card e livello delle pagine dalla riga (con il limite applicato dopo il livello), titolo e testo dalla configurazione, identificatori per istanza, sfondo, colonne bilanciate, niente stampato senza pagine o con la sezione spenta |
| `DLI_Home_Navigation_Groups_Integration_Test.php` | La sezione «Navigazione per pagine»: gruppi fatti dalle pagine promosse di ogni livello con sottopagine pubblicate, ordine prima del limite, protette e bozze escluse, lingua corrente, limiti dalla riga, titoli dei gruppi come testo e sottopagine come link, identificatori per istanza e gruppo, primo pannello dell'accordion aperto |
| `DLI_Home_Tabs_Integration_Test.php` | La sezione «Pannello tab»: pagine collegate pubblicate, senza password e tradotte nella lingua corrente; tab senza contenuto escluse; tab e pannelli legati, il primo aperto; identificatori unici per istanza; descrizione ripulita alla stampa; titolo e testo dalla configurazione; scelta delle pagine ad albero che conserva quelle già scelte |
| `DLI_Featured_Contents_Integration_Test.php` | La sezione «Contenuti in evidenza»: tipi che si possono promuovere, i tre contenuti promossi più recenti della lingua, box configurati con contenuti (anche due dello stesso tipo), titolo per lingua, colonne e titoli in ordine, renderer per tipo (notizia, evento, pubblicazione, generico), link del titolo secondo la scelta dell'editor, pulsante verso la pagina di elenco nella lingua corrente, sezione spenta che non interroga il database |
| `DLI_Publication_Home_Cards_Integration_Test.php` | Le pubblicazioni della home (sezione elenco e box dei contenuti in evidenza): al massimo tre, titolo secondo il link e tipo che filtra l'archivio, autori e anno al posto della descrizione, pubblicazione protetta con solo titolo e tipo, nessun link vuoto senza la pagina delle pubblicazioni |
| `DLI_Reserved_Area_Integration_Test.php` | L'area riservata: scheda «Area riservata» solo per l'amministratore, campi dipendenti con la classe dello script, «Accesso solo tramite SAML» attivo di default con l'avviso quando WP SAML Auth manca, impostazioni ignorate ad area spenta, promozione dell'utente in attesa all'ingresso in un gruppo (anche dal profilo; non con il gruppo `Registered`, un gruppo sconosciuto o l'area spenta), slug neutro per l'utente creato da SAML |
| `DLI_Reserved_Area_Access_Integration_Test.php` | Dove arrivano gli utenti dell'area riservata e come accedono: pagina scelta o home dopo il login, ritorno alla pagina di partenza, redirect da `wp-admin` solo per i due ruoli, barra di amministrazione nascosta, accesso solo SAML (niente password, `wp_authenticate_user` e REST rifiutati) solo con WP SAML Auth presente |
| `DLI_Reserved_Area_Header_Integration_Test.php` | L'header pubblico per gli utenti dell'area riservata: saluto e uscita per i suoi utenti, nessun controllo di accesso per gli altri utenti collegati |
| `DLI_Reserved_Area_Login_Integration_Test.php` | La schermata di login con l'area riservata attiva: testi di WP SAML Auth in italiano (gli altri restano), logo del sito o logo predefinito del tema, indirizzo del logo che non esce dal CSS, link alla home con il nome del sito; nulla cambia con l'area spenta |
| `DLI_Reserved_Area_Page_Activation_Integration_Test.php` | La pagina «Area riservata» creata dai dati di attivazione: è tra le pagine del tema e nei menu del footer, il ricaricamento la crea nelle due lingue e la sceglie, nessuna copia al secondo ricaricamento, scelta esistente o «Nessuna» voluta conservate, pagina esistente o non pubblicata non scelta |
| `DLI_Pending_User_Role_Integration_Test.php` | Ciò che è proprio del ruolo «Utente in attesa»: non può fare nulla e gli altri ruoli non cambiano (ciò che condivide con l'altro ruolo è in `DLI_Reserved_Area_Roles_Integration_Test`) |
| `DLI_Reserved_Area_User_Role_Integration_Test.php` | Ciò che è proprio del ruolo «Utente area riservata»: costanti di ruolo e capacità, nessuna capacità di WordPress oltre a quella del tema, l'utente in attesa non la riceve, l'amministratore può modificarlo |
| `DLI_Tools_Utilities_Integration_Test.php` | La pagina Utilità di Strumenti DLI ha solo lo strumento di ricarica dei dati di attivazione (le pagine di esempio si creano da Importa → Pagine) |
| `DLI_CSV_File_Integration_Test.php` | Il controllo del file CSV caricato e i messaggi comuni agli import (`DLI_CSV_File`): file accettato o codice dell'errore (nessun file, non caricato via HTTP, estensione, dimensione, tipo), messaggi del caricamento e del lettore con il loro dettaglio, colonne ignorate; le Pagine usano i messaggi comuni |
| `DLI_Page_File_Importer_Integration_Test.php` | L'import delle pagine da file (`DLI_Page_File_Importer`): prerequisiti, piano che non scrive, albero creato, Lorem solo nelle pagine nuove, riesecuzione che salta le pagine già importate, traduzioni collegate per `page_key` in qualsiasi ordine, conflitti di indirizzo (anche di un'altra lingua o di un genitore esistente), pagina nel cestino, identità ambigua, gruppo di traduzioni che ha già la lingua, interruzione durante la scrittura e ripresa |
| `DLI_Page_Import_Admin_Integration_Test.php` | Strumenti DLI → Importa → Pagine (`DLI_Page_Import_Admin`): permessi e nonce, simulazione predefinita che non scrive, conferma necessaria, Lorem, riesecuzione, lingua del sito, file rifiutati (caricamento, intestazione, codifica, 200 righe), errori del file con le righe, colonne in più e righe vuote che non fermano l'import, report con colonne ignorate e `page_key`, conflitti, download degli esempi solo per nome, modulo e foglio di stile |
| `DLI_People_File_Importer_Integration_Test.php` | Una riga dell'import delle persone (`DLI_People_File_Importer::process_row()`): campi obbligatori, simulazione che non scrive, creazione con titolo, campi, stato e lingua, stato `publish` o `draft`, flag, strutture e tipologia, le quattro modalità secondo l'esistenza della persona, stato mantenuto in aggiornamento, email di due persone come errore |
| `DLI_People_Import_Admin_Integration_Test.php` | Strumenti DLI → Importa → Persone (`DLI_People_Import_Admin`): permessi e nonce (anche del download dell'esempio), modalità sconosciute, file rifiutati (caricamento, codifica, colonne obbligatorie, 200 righe), simulazione che non scrive, valori predefiniti, riga con errore o troppo corta che non ferma le altre, le quattro modalità, colonne in più ignorate ed elencate nel report, righe vuote saltate, `body` tra virgolette con `;` e a capo, modulo, file di esempio del tema |

Sono ancora da scrivere, tra gli altri: i permessi, sitemap e manifest, le query dei
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
