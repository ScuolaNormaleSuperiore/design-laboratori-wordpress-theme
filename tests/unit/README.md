# Test unit (PHPUnit e Node, senza WordPress)

I test unit controllano la logica che **non dipende da WordPress**: funzioni pure in PHP e in JavaScript,
tabelle di configurazione e "test di contratto" sui file del repository (versioni,
date di rilascio, documentazione per gli agenti AI). Non
richiedono database, sito o plugin. Il hook `pre-commit` li esegue a ogni commit e blocca il commit
se uno fallisce.

I test che hanno bisogno di WordPress (hook, query, REST, ruoli) appartengono alla
suite di integrazione, non a questa.


## Requisiti

- PHP 8.0 o superiore (provato su 8.4) e Composer
- Node sul `PATH`, per i test JavaScript: usano solo `node:test` e `node:assert`, nessun pacchetto npm. Su Windows serve **Node 22 o superiore**: Composer lancia i comandi con `cmd`, che non espande `tests/unit/*.test.js`, e solo Node 21 e successivi espandono il pattern da soli. Su Linux e macOS (anche nella CI) lo espande la shell e basta Node 18
- Dipendenze di sviluppo: `composer install` (installa PHPUnit 9.6 e `yoast/phpunit-polyfills`, tra le altre)


## Configurazione e variabili d'ambiente

Nessuna: la suite non legge variabili d'ambiente e non ha bisogno di un file di configurazione oltre a [`../../phpunit.xml`](../../phpunit.xml).


## Esecuzione

Dalla radice del tema:

```sh
composer test:unit
```

Il comando esegue prima PHPUnit con [`../../phpunit.xml`](../../phpunit.xml), poi i test JavaScript con
`node --test tests/unit/*.test.js`; `composer test` è un altro nome dello stesso comando. Gli argomenti dopo `--`
(per esempio `--filter`) arrivano solo a PHPUnit; un solo file JavaScript si lancia con `node --test tests/unit/<file>.test.js`; i test di integrazione hanno un
comando a parte (vedi i [test di integrazione](../integration/README.md)).
Il codice di uscita è `0` se tutti i test passano, `1` altrimenti.


### Eseguire un solo test

Ha senso: un solo test rende l'output leggibile quando si sta scrivendo o correggendo
un test. Comandi provati, dalla radice del tema:

```sh
composer test:unit -- tests/unit/DLI_Utils_Unit_Test.php                                   # un solo file
composer test:unit -- --filter DLI_Utils_Unit_Test                                         # una sola classe
composer test:unit -- --filter test_persona_display_name_joins_first_and_last_name          # un solo test
composer test:unit -- --filter DLI_Utils_Unit_Test::test_persona_display_name_joins_first_and_last_name
composer test:unit -- --list-tests                                                         # tutti i test, come Classe::metodo
```

Tutto ciò che sta dopo `--` va a PHPUnit. Gli stessi argomenti funzionano con `vendor/bin/phpunit` (usa lo stesso `phpunit.xml`), per
esempio `vendor/bin/phpunit --filter test_monthname_for_valid_months`.

- `--filter` accetta un nome intero o una parte: basta un frammento del nome per eseguire più test.
- **Se il filtro non trova nulla**, PHPUnit scrive `No tests executed!` ma esce con codice `0`, quindi un errore di battitura sembra
  un successo: controllare il numero di test eseguiti (`OK (1 test, …)`).
- Un test isolato non sostituisce la suite: l'hook `pre-commit` esegue comunque tutta la suite unit.


## Cosa copre

| File | Cosa verifica |
|---|---|
| `DLI_Utils_Unit_Test.php` | Funzioni pure di `inc/utils.php`: nome visualizzato delle persone, ricerca in array annidati, nomi dei mesi, creazione e formattazione di date |
| `DLI_Cmb2_Loader_Unit_Test.php` | Come il tema carica CMB2: percorsi di `inc/cmb2.php` ancorati su `__DIR__`, versione dello stub di PHPStan uguale a quella della libreria, nessun `package*.json` o file di lock dentro `inc/vendor/CMB2` |
| `DLI_Config_Unit_Test.php` | Costanti di `config-lab.php`: lingue, plugin richiesti (ACF e Polylang), contenuti con pagina di elenco |
| `DLI_Release_Version_Unit_Test.php` | La versione è la stessa in `VERSION.txt`, `style.css`, `package.json`, `publiccode.yml` e nella prima voce di `CHANGELOG.md` |
| `DLI_Release_Date_Unit_Test.php` | `releaseDate` di `publiccode.yml` e la data del Manuale Operatore (`DOC/Manuali/manuale-operatore.md`) coincidono con la data della prima voce di `CHANGELOG.md`; le date sono valide |
| `DLI_Agents_Docs_Unit_Test.php` | I documenti citati da `AGENTS/AI_BOOTSTRAP.md` esistono (esclusi i file locali ignorati da Git) |
| `DLI_Image_Guidelines_Unit_Test.php` | L'elenco dei formati delle immagini (`inc/image-guidelines.php`): ogni posizione ha i suoi campi, il minimo non supera il consigliato, le misure seguono il rapporto, il testo dice «consigliato» e «indicative», dà la zona sicura di hero e banner e il peso massimo letto da `DLI_MAX_IMG_FILE_SIZE_MB` |
| `DLI_Image_Guide_Unit_Test.php` | La guida `DOC/HowTo/Gestione-delle-immagini.md`: esiste, è nel manifest della documentazione, riporta ogni misura e il nome di ogni posizione dell'elenco, dà il peso massimo e non ha riferimenti privati |
| `DLI_Image_Coverage_Unit_Test.php` | Ogni file del tema che carica o mostra un'immagine (dimensioni, immagine in evidenza, allegati, campi ACF e CMB2) è nell'elenco dei file noti, legato a una posizione dell'elenco dei formati o con un'eccezione motivata; segnala anche i file noti che non mostrano più immagini |
| `DLI_Captcha_Unit_Test.php` | L’helper CAPTCHA fallisce in modo sicuro quando il plugin opzionale Really Simple CAPTCHA non è disponibile |
| `DLI_PHPStan_Config_Unit_Test.php` | `phpstan.neon`: ogni file PHP della radice è nei percorsi analizzati, ogni cartella con codice PHP è analizzata o esclusa in modo esplicito e il livello non scende sotto il 6 |
| `DLI_Pagination_Unit_Test.php` | La dimensione della pagina chiesta nell'indirizzo (`inc/pagination.php`): solo numeri interi positivi, scelta tra i valori offerti o il predefinito, limite al valore massimo |
| `DLI_Home_Sections_Unit_Test.php` | Gli helper puri delle sezioni della home (`inc/home-sections.php`): righe salvate rese sicure (sezione, flag, posizione, sfondo, numero di elementi e sottoelementi, livello delle pagine, pagina scelta, modalità e banner scelti), colonne di card e gruppi, tab per lingua, contenuti in evidenza (renderer, colonne, box), titolo della riga nella lingua della pagina |
| `DLI_Menus_Unit_Test.php` | Gli helper puri dei menu dell'header (`inc/menus.php`): `data-element` dallo slug della pagina nelle due lingue o dal titolo, identificatore del menu a tendina |
| `DLI_Test_Plugin_Versions_Unit_Test.php` | Gli helper che scelgono la versione dei plugin scaricati dalla suite di integrazione: ultima versione stabile o versione fissata con la variabile d'ambiente di ogni plugin, versioni e slug non validi rifiutati, copia in cache riusata solo se è la versione fissata |
| `DLI_Csv_Reader_Unit_Test.php` | Il lettore comune dei file CSV degli import (`DLI_CSV_Reader`), cioè il formato comune: BOM, UTF-8, separatore sempre `;`, virgolette e a capo nelle celle, ogni fine riga, celle senza spazi ai lati, righe vuote saltate con i numeri di riga del file, nomi delle colonne senza maiuscole, colonne fuori schema ignorate ed elencate, colonne dello schema mancanti o ripetute, righe corte, limite di 200 righe e di dimensione, lettura senza schema |
| `DLI_Page_Import_Parser_Unit_Test.php` | Il parser dell'import delle pagine (`DLI_Page_File_Importer::parse_rows()`): righe del file trasformate in albero di pagine o nell'elenco degli errori con le righe (livelli, titolo unico, testo semplice, ordine, flag, `page_key`, slug, percorsi ripetuti, identità), massimo 200 pagine |
| `DLI_Page_Import_Lorem_Unit_Test.php` | Il Lorem ipsum delle pagine nuove dell'import: da uno a tre paragrafi della raccolta, stesso testo per la stessa chiave e testi diversi per chiavi diverse, un `<p>` per paragrafo |
| `DLI_Example_Pages_Csv_Unit_Test.php` | Test di contratto dei file di esempio delle pagine in `SETUP/CSV_Import/`: dimensione dell'esempio, stesse `page_key` nei file italiano e inglese, modello fatto della sola intestazione |
| `DLI_Example_People_Csv_Unit_Test.php` | Test di contratto del file di esempio delle persone (`SETUP/CSV_Import/people-example.csv`): letto dal lettore comune con tutte le colonne delle persone, nessuna ignorata, due persone, una cella con `;` tra virgolette |
| `DLI_People_Search_Unit_Test.php` | La ricerca testuale della pagina Persone (`inc/people-filters.php`): testo normalizzato (spazi, spazi non divisibili, minuscole, accenti conservati) e corrispondenza con nome e cognome nei due ordini, tipologia della riga o una delle strutture. I casi sono in `fixtures/people-search-cases.json`, condiviso con `people-search.test.js`. Anche la ricerca chiesta nell'indirizzo (`cerca`, o il vecchio `cognome` quando `cerca` manca) e i parametri dei link tra le viste (solo i filtri attivi, mai `cognome`) |
| `people-search.test.js` | La stessa ricerca nella versione JavaScript (`assets/js/people-filters.js`), sugli stessi casi di `fixtures/people-search-cases.json`: le due versioni devono dare la stessa risposta |
| `people-filters.test.js` | I filtri della tabella delle persone (`assets/js/people-filters.js`), senza browser: struttura con le sue sottostrutture, tutti i filtri insieme, indirizzo (filtri attivi impostati, vuoti tolti, `cognome` tolto, altri parametri conservati); la tabella su una pagina finta parte dai filtri dell'indirizzo e a ogni cambio di struttura, TAG, tipologia o ricerca aggiorna subito righe e link di cambio vista, mentre indirizzo e cronologia seguono dopo 200 ms di pausa; verifica una sola scrittura dopo più battute, la conservazione dello stato della cronologia e il funzionamento di risultati e link anche quando `replaceState()` viene rifiutato (oltre alla regressione del filtro per tipologia); il campo di ricerca della vista Schede ricarica con `cerca` dalla prima pagina con Invio (senza inviare il modulo) e con la ×, togliendo `cognome` e conservando gli altri filtri |

Se un test di contratto fallisce, di solito significa che una versione o una data è
stata aggiornata in un file e dimenticata in un altro: va allineata, non il test.


## Come funziona

- [`phpunit.xml`](../../phpunit.xml) (radice del tema) definisce la suite `unit` su questa [cartella](./).
- [`../bootstrap-unit.php`](../bootstrap-unit.php) carica l'autoloader di Composer, definisce `ABSPATH` e pochi
  *stub* di funzioni di WordPress (`__`, `_x`, `add_action`, `plugin_dir_path`, ...), poi
  include `config-lab.php`, `inc/utils.php`, `inc/image-guidelines.php` e `inc/captcha.php`. Gli stub servono solo per poter caricare
  questi file: non riproducono il comportamento di WordPress.
- Le classi di test si chiamano `DLI_<Nome>_Unit_Test`, un file per area, con il nome
  del file uguale a quello della classe.
- La cartella [`../`](../) è esclusa da PHPCS ([`../../phpcs.xml.dist`](../../phpcs.xml.dist)): lo stile dei test non è
  controllato automaticamente.

## Come aggiungere un test

Creare un file nella [cartella dei test unit](./) chiamato `DLI_<Nome>_Unit_Test.php` con una classe che
estende `PHPUnit\Framework\TestCase`. Se la funzione da provare dipende da WordPress,
non è un test unit.

Per il codice JavaScript creare un file `<area>.test.js` nella stessa cartella, con `node:test` e
`node:assert` e nessun'altra dipendenza; il file del tema espone le sue funzioni pure a Node (per esempio con
`module.exports` quando `module` esiste), così il test le carica con `require()` senza browser. Non estrarre lo
script dal testo di un template PHP.
