# Test unit (PHPUnit senza WordPress)

I test unit controllano la logica che **non dipende da WordPress**: funzioni pure,
tabelle di configurazione e "test di contratto" sui file del repository (versioni,
date di rilascio, documentazione per gli agenti AI). Girano in meno di un secondo e non
richiedono database, sito o plugin. Il hook `pre-commit` li esegue a ogni commit e blocca il commit
se uno fallisce.

I test che hanno bisogno di WordPress (hook, query, REST, ruoli) appartengono alla
suite di integrazione, non a questa.


## Requisiti

- PHP 8.0 o superiore (provato su 8.4) e Composer
- Dipendenze di sviluppo: `composer install` (installa PHPUnit 9.6 e `yoast/phpunit-polyfills`, tra le altre)


## Configurazione e variabili d'ambiente

Nessuna: la suite non legge variabili d'ambiente e non ha bisogno di un file di configurazione oltre a `phpunit.xml`.


## Esecuzione

Dalla radice del tema:

```sh
composer test:unit
```

`composer test` lancia PHPUnit con `phpunit.xml`, cioè la stessa suite `unit`; i test di integrazione hanno un
comando a parte (vedi [`../integration/README.md`](../integration/README.md)).
Per eseguire un solo file o un solo test:

```sh
vendor/bin/phpunit tests/unit/DLI_Utils_Unit_Test.php
vendor/bin/phpunit --filter test_monthname_for_valid_months
```

Il codice di uscita è `0` se tutti i test passano, `1` altrimenti.


## Cosa copre

| File | Cosa verifica |
|---|---|
| `DLI_Utils_Unit_Test.php` | Funzioni pure di `inc/utils.php`: nome visualizzato delle persone, ricerca in array annidati, nomi dei mesi, creazione e formattazione di date |
| `DLI_Config_Unit_Test.php` | Costanti di `config-lab.php`: lingue, plugin richiesti (ACF e Polylang), sezioni della home, contenuti con pagina di elenco |
| `DLI_Release_Version_Unit_Test.php` | La versione è la stessa in `VERSION.txt`, `style.css`, `package.json`, `publiccode.yml` e nella prima voce di `CHANGELOG.md` |
| `DLI_Release_Date_Unit_Test.php` | `releaseDate` di `publiccode.yml` coincide con la data della prima voce di `CHANGELOG.md` |
| `DLI_Agents_Docs_Unit_Test.php` | I documenti citati da `AGENTS/AI_BOOTSTRAP.md` esistono (esclusi i file locali ignorati da Git) |
| `DLI_Captcha_Unit_Test.php` | L’helper CAPTCHA fallisce in modo sicuro quando il plugin opzionale Really Simple CAPTCHA non è disponibile |

Se un test di contratto fallisce, di solito significa che una versione o una data è
stata aggiornata in un file e dimenticata in un altro: va allineata, non il test.


## Come funziona

- `phpunit.xml` (radice del tema) definisce la suite `unit` su `tests/unit/`.
- `tests/bootstrap-unit.php` carica l'autoloader di Composer, definisce `ABSPATH` e pochi
  *stub* di funzioni di WordPress (`__`, `_x`, `add_action`, `plugin_dir_path`, ...), poi
  include `config-lab.php`, `inc/utils.php` e `inc/captcha.php`. Gli stub servono solo per poter caricare
  questi file: non riproducono il comportamento di WordPress.
- Le classi di test si chiamano `DLI_<Nome>_Unit_Test`, un file per area, con il nome
  del file uguale a quello della classe.
- La cartella `tests/` è esclusa da PHPCS (`phpcs.xml.dist`): lo stile dei test non è
  controllato automaticamente.

## Come aggiungere un test

Creare `tests/unit/DLI_<Nome>_Unit_Test.php` con una classe che
estende `PHPUnit\Framework\TestCase`. Se la funzione da provare dipende da WordPress,
non è un test unit.
