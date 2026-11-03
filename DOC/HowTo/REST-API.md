# Web service esposti nel tema

## Importazioni — namespace `custom/v1`

| Aspetto | Valore |
|---|---|
| Attivazione endpoint | Attivi solo se l'opzione `rest_api_enabled` è `true` nelle impostazioni del tema |
| Autenticazione | HTTP Basic con credenziali di un utente con il permesso `manage_options`; usare HTTPS |
| Metodo consentito per import | Solo `POST` |
| Altri metodi | Non registrati per queste route: WordPress può rispondere `rest_no_route` (404). Il controllo interno dell'importatore restituisce 405 se riceve un metodo diverso da POST |


| Endpoint | Metodo | File | Funzione |
|---|---|---|---|
| `/wp-json/custom/v1/indico-import` | `POST` | `inc/classes/class-indicoimporter.php` | Avvia l'import di eventi da Indico |
| `/wp-json/custom/v1/iris-patent-import` | `POST` | `inc/classes/class-patentimporter.php` | Avvia l'import di brevetti da IRIS |


La classe base `DLI_BaseImporter` (`inc/classes/class-base-importer.php`)
gestisce la registrazione delle route e il controllo di autenticazione e
permessi. Gli importatori specifici eseguono l'import e costruiscono la
risposta. Prima di invocare una route configurare il relativo servizio; la
scelta fra simulazione e import definitivo dipende dal pannello del tema.

## Suggerimenti di ricerca — namespace `dli/v1`

`GET /wp-json/dli/v1/suggest?q=ricerca&lang=it` restituisce un array JSON di
suggerimenti con i campi `title`, `type` e `url`.

- La route è pubblica: non richiede autenticazione.
- È registrata solo quando i suggerimenti sono attivi e sono selezionate
  tipologie valide in `Configurazione → Altro → Ricerca nel sito`.
- Funziona anche con `Abilita REST API` disattivato: quella scelta non
  disabilita il servizio dei suggerimenti.
- `q` è obbligatorio; sotto la soglia di caratteri configurata si riceve un
  array vuoto. `lang` è facoltativo e viene risolto sulle lingue del sito.
- Limiti, tipi di contenuto e durata della cache dipendono dalla configurazione
  del tema. Un `url` vuoto indica un risultato senza destinazione diretta.

Il codice di riferimento è `inc/search-autocomplete.php`.
