# Import dei brevetti da IRIS

## Descrizione
Il sistema SitoFederato permette di importare i brevetti dal software di CINECA IRIS.
Per farlo è necessario attivare il servizio e configurarlo da back-office nella sezione: *Configurazione-->Iris*.
Si può invocare il servizio messo a disposizione da CINECA o un servizio che faccia da wrapper ad esso, ma che restituisca i dati nello stesso formato.


## Attivazione e configurazione
Si può configurare il servizio da back-office nella sezione: *Configurazione-->Iris*.

## Import da Web Service
Dopo aver attivato e configurato il servizio, abilitare `Abilita REST API` in
`Configurazione → Altro` e inviare una richiesta `POST` a
`/wp-json/custom/v1/iris-patent-import`.
È richiesta l'autenticazione HTTP Basic di un utente con il permesso
`manage_options`. Usare HTTPS e non salvare le credenziali nei documenti.
Vedi [REST API del tema](REST-API.md).

## Import schedulato
L'importazione può essere eseguita anche tramite WP-Cron, configurando la
schedulazione nella scheda del servizio. L'opzione `Abilita REST API` riguarda
l'avvio via HTTP, non la schedulazione.

## Descrizione del codice

 - **class-patentmanager.php**: Qui c'è il codice per la gestione del post type personalizzato Patent e delle tassonomie associate.
 
 - **class-patentimporter.php**: contiene `DLI_IrisPatentImporter`, che importa i brevetti tramite web service o WP-Cron.

 - **class-dli-labmanager.php**: inizializza il manager dei brevetti `Patent_Manager` e l'importatore `DLI_IrisPatentImporter`.
  
 - **options.php**: in questo file ci sono le opzioni di configurazione dell'importer dei brevetti e i dati di connessione al web service per il recupero dei dati da importare.
