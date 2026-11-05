# Primo avvio

Questa guida prepara un'installazione nuova di Design Laboratori Italia. Prima di iniziare, esegui un backup del sito WordPress e del database se l'ambiente non è vuoto.

## Requisiti

Il sito deve usare WordPress 6.6.2 o successivo e PHP 8.0 o successivo. Il tema richiede questi plugin attivi:

- Advanced Custom Fields;
- ACF OpenStreetMap Field;
- Polylang;
- WP Mail SMTP;
- Really Simple CAPTCHA.

Per una prova locale è disponibile la [demo Docker](../../SETUP/Docker/README.md). Usa dati dimostrativi: non inserire credenziali o dati personali in immagini, dump o log della demo.

## Installa il tema e i plugin

1. Copia la cartella `design-laboratori-wordpress-theme` in `wp-content/themes/` dell'installazione WordPress.
2. Attiva **Design Laboratori Italia** da **Aspetto → Temi**. L'attivazione crea pagine, menu e tassonomie predefinite.
3. Installa e attiva tutti i plugin richiesti. Il tema mostra un avviso in bacheca quando ne manca uno.
4. Completa il wizard di Polylang: aggiungi italiano come lingua predefinita e inglese, abilita la traduzione dei media e assegna inizialmente l'italiano ai contenuti senza lingua.

Polylang è indispensabile: il tema non si attiva senza di esso. Per la gestione quotidiana delle traduzioni consulta il [manuale delle lingue](../Manuali/Manuale-Gestione-Contenuti-Lingue.md).

## Crea la struttura iniziale

Apri **Strumenti DLI → Utilità** e usa **Ricarica i dati di attivazione** per rigenerare pagine, menu e tassonomie predefinite quando necessario. Poi vai in **Aspetto → Menu**, verifica le assegnazioni alle posizioni disponibili e salva ciascun menu.

Per importare un albero di pagine, usa **Strumenti DLI → Importa → Pagine**: il comando offre una simulazione e richiede una conferma prima di creare pagine. Scarica il modello CSV e gli esempi dalla stessa schermata. Gli esempi servono alle prove e non vanno importati su un sito di produzione.

## Configura e verifica

Con un account Amministratore o Super Editor apri **Configurazione** e compila prima le informazioni essenziali del laboratorio. Imposta quindi le sezioni della home e pubblica almeno una pagina o un contenuto per controllare il risultato. Il [manuale del pannello di configurazione](../Manuali/Manuale-Utente-Pannello-Configurazione.md) descrive ogni scheda e campo.

Prima di pubblicare, verifica che le pagine e i contenuti siano nella lingua corretta, che le traduzioni italiane e inglesi siano collegate, che i menu siano salvati e che moduli ed e-mail funzionino con dati di prova. Controlla anche la home da telefono e con la tastiera.

## Ruoli

Il ruolo **Super Editor** può modificare contenuti, menu e gran parte della configurazione. Le schede **Altro**, **Indico** e **Iris** sono riservate agli Amministratori. Per i dettagli sui permessi e sugli import consulta il [Manuale amministratore](../Manuali/Manuale-Amministratore.md).
