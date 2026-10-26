# Stub per PHPStan

PHPStan legge i sorgenti del tema **senza eseguire WordPress né i plugin**. Per sapere che cosa fanno le funzioni, le
classi e le costanti che il tema usa, ha bisogno delle loro dichiarazioni. Questa cartella contiene quelle che non
arrivano da altre parti. Il comando è, dalla radice del tema:

```sh
composer run lint:phpstan
```

La configurazione è in [`phpstan.neon`](../../phpstan.neon): livello **4** su tutti i file PHP del tema, ed è un
controllo che blocca il commit (vedi [`tests/README.md`](../README.md)).


## Da dove arrivano le dichiarazioni

| Origine | Che cosa fornisce |
|---|---|
| `php-stubs/wordpress-stubs` 6.6.2 e `szepeviktor/phpstan-wordpress` 2.0 (dipendenze di sviluppo) | Il core di WordPress, con i tipi di ritorno corretti per le sue funzioni |
| **I file di questa cartella** | Costanti che dipendono dall'installazione, plugin richiesti od opzionali, librerie incluse nel tema |

## I file

| File | Che cosa dichiara |
|---|---|
| `wordpress-runtime-stubs.php` | Costanti che WordPress definisce all'avvio e che non sono negli stub del core, perché il loro valore dipende dall'installazione: `MINUTE_IN_SECONDS`, `DAY_IN_SECONDS`, `MB_IN_BYTES` |
| `theme-runtime-stubs.php` | Costanti del tema che `config-lab.php` definisce chiamando funzioni, quindi con un valore che PHPStan non riesce a calcolare: `DLI_THEMA_PATH` (`plugin_dir_path()`) e `DLI_POST_PER_PAGE_VALUES_COMBINED` (`array_combine()`) |
| `polylang-stubs.php` | Le funzioni `pll_*` che il tema chiama (lingua corrente e predefinita, elenco delle lingue, traduzioni di articoli e termini). Polylang non è installato nell'ambiente di analisi |
| `third-party-runtime-stubs.php` | `acf_add_local_field_group()` e `cmb2_get_option()`, fornite da dipendenze che durante l'analisi non vengono caricate |
| `wordpress-lite-stubs.php` | Dichiarazioni minime di librerie incluse o richieste: CMB2 (`CMB2_Boxes`, `CMB2`, `CMB2_Options_Hookup`, `new_cmb2_box()`, `CMB2_VERSION`), ACF (`get_field()`, `update_field()`) e `tgmpa()` |
| `really-simple-captcha-stubs.php` | La classe `ReallySimpleCaptcha`, con la sola parte dell'interfaccia usata dal tema |

## Come vengono usati

- Sono elencati in `scanFiles` di `phpstan.neon`: PHPStan li **legge** per conoscere i simboli, ma non li analizza.
- **Non sono mai caricati da WordPress né da PHPUnit.** Esistono solo per l'analisi statica.
- La cartella è esclusa dall'analisi (`tests/*`) e da PHPCS, e non entra nel pacchetto di rilascio (`export-ignore`).
- Le funzioni e le classi sono protette da `function_exists()` e `class_exists()`, per non entrare in conflitto con quelle
  vere se un giorno venissero caricate insieme. Le cinque costanti (`wordpress-runtime-stubs.php` e
  `theme-runtime-stubs.php`) no: quei file non vengono mai caricati a runtime.


## Come aggiungere uno stub

1. Lanciare `composer run lint:phpstan`. Un simbolo mancante dà un errore come `function.notFound`,
   `class.notFound` o `constant.notFound`.
2. Capire da dove viene il simbolo:
   - del **core di WordPress**: di norma è già negli stub; se manca, può dipendere dalla versione degli stub
     (6.6.2, la versione minima richiesta dal tema), non da qui;
   - una **costante del tema** definita con un'espressione non costante: `theme-runtime-stubs.php`;
   - una **costante di WordPress** che dipende dall'installazione: `wordpress-runtime-stubs.php`;
   - una funzione o una classe di un **plugin o di una libreria**: il file corrispondente della tabella sopra.
3. Scrivere la dichiarazione **minima**, con il tipo di ritorno e dei parametri **esatti** nel PHPDoc: PHPStan li usa
   per controllare i chiamanti, quindi un tipo approssimativo nasconde errori veri o ne crea di falsi.
4. Rilanciare `composer run lint:phpstan` e `composer test:unit`, poi aggiornare la tabella di questo file.


## Regole

- Si dichiara **solo** ciò che il codice del tema chiama davvero.
- Non si aggiunge uno stub per far sparire un errore sul codice del tema: lì si corregge il codice o il suo PHPDoc.
- Niente baseline e niente `ignoreErrors` generici. Le sole eccezioni ammesse sono le costanti dichiarate dinamiche
  in `phpstan.neon` (`ABSPATH`, `CMB2_VERSION`, `DLI_PUBLICATION_HAS_DETAIL`, `DLI_POST_TYPES_TO_TRANSLATE`) e le
  annotazioni `@phpstan-ignore <identificatore> (motivo)` sul singolo punto, con il motivo scritto accanto.
- Il livello non scende sotto il 4 e ogni file PHP del tema deve essere nei percorsi analizzati: lo verifica
  `tests/unit/DLI_PHPStan_Config_Unit_Test.php`.
- Quando si aggiorna ACF, Polylang, CMB2 o Really Simple CAPTCHA, controllare che le firme dichiarate qui siano ancora
  quelle vere.
