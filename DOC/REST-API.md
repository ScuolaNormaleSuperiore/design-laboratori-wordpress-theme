# Web service esposti nel tema

1. REST API custom — Namespace custom/v1
| Aspetto | Valore |
|---|---|
| Attivazione endpoint | Attivi solo se l'opzione `rest_api_enabled` è `true` nelle impostazioni del tema |
| Autenticazione | Basic Authentication con credenziali di un utente `administrator` |
| Metodo consentito per import | Solo `POST` |
| Metodi non consentiti | Rifiutati con `HTTP 405` |


| Endpoint | Metodo | File | Funzione |
|---|---|---|---|
| `POST /wp-json/custom/v1/indico-import` | `POST` | `class-indicoimporter.php:17` | Avvia l'import di eventi da Indico |
| `POST /wp-json/custom/v1/iris-patent-import` | `POST` | `class-patentimporter.php:27` | Avvia l'import di brevetti da IRIS |


La classe base è class-base-importer.php, che gestisce registrazione endpoint, autenticazione e risposta.
