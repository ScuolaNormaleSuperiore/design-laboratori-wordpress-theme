# Import dei brevetti da IRIS

## Descrizione
Il sistema SitoFederato permette di importare i brevetti dal software di CINECA IRIS.
Per farlo è necessario attivare il servizio e configurarlo da back-office nella sezione: *Configurazione-->Iris*.
Si puà invocare il servizio messo a disposizione da CINECA o un servizio che faccia da wrapper ad esso, ma che ritorni i dati nello stesso formato.


## Attivazione e configurazione
Si può configurare il servizio da back-office nella sezione: *Configurazione-->Iris*.

## Import da Web Service
Dopo aver attivato e configurato ilo servizio, lo si può invocare utilizzando la REST api di WordPress e l'end-point:
E' richiesta la basic authentication con un account da amministratore.

## Import schedulato
L'impoirtazione può essere eseguita, attivando la registrazione del cronjob associato all'import nella maschera di configurazione del servizio.

## Descrizione del codice

 - **class-patentmanager.php**: Qui c'è il codice per la gestione del post type personalizzato Patent e delle tassonomie associate.
 
 - **class-irispatent-importer.php**: Qui è presente il codice che si occupa dell'importazione dei brevetti da ***web service*** o da ***cronjob***.

 - **class-labmanager.php**: qui c'è il codice per importare gli oggetti Patent_Manager e DLI_IrisPatentImporter e per inizializzarli (con il metodo **setup**).
  
 - **options.php**: in questo file ci sono le opzioni di configurazione dell'importer dei brevetti e i dati di connessione al web service per il recupero dei dati da importare.
