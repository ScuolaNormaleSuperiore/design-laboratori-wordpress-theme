# Migrazione da 1.6.x a 1.7.x

Nella versione **1.7.x** sono state introdotte alcune modifiche che possono creare dei malfunzionamenti se non si eseguono alcune procedure di migrazione.

Attenzione:
 - Prima di aggiornare un sistema dalla 1.6.x alla 1.7.x effettuare un backup del sistema: database e file system.

 - Prima di eseguire l'aggiornamento segnarsi quali sono le sezioni attivate e presenti nella Home Page.

Queste sono le procedure di aggiornamento da applicare:


1. Gestione della Home Page come sezioni ordinabili.

2. Riduzione del numero di formato delle immagini.

3. Introduzione della priorità per progetti e attività di ricerca.



## Gestione della Home Page come sezioni ordinabili.
Prima di aver aggiornato il tema appuntarsi tutte le sezioni che si vedono e sono attive nella Home Page.
Dopo al'aggiornamento andare in ***HP->Configurazione->Sezioni HP*** e aggiungere le stesse sezioni nello stesso ordine.

Assicurarsi, in questa maschera, che le sezioni che si vuole visualizzare siano abilitate.


## Riduzione del numero di formato delle immagini.
Dopo aver aggiornato il tema è meglio rimuovere tutte le immagini che non verranno più usate così da liberare dello spazio.
Inoltre è meglio assicurarsi di aver generato le miniature con l'opzione "crop=false" abilitata.

Seguire questa procedura:

	a. Installare e attivare il plugin "Regenerate Thumbnails" di Alex Millis.

	b. Andare in WP->Strumenti->Regenerate Thumbnails

	c. Deselezionare la voce "Salta la rigenerazione delle miniature esistenti con dimensioni corrette (più veloce)".

	d. Selezionare la voce "Elimina i file delle miniature vecchie e con dimensioni non più registrate...".

	e. cliccare su "Rigenera le miniature per tutti i xx allegati".

	f. Disattivare ed eventualmente rimuovere il plugin.

## Introduzione della priorità per progetti e attività di ricerca.
Dopo l'aggiornamento del tema potrebbe essere necessario salvare tutti i progetti e le attività di ricerca già esistenti sul sito perché non prevedono il campo priorità.
Si può utilizzare il plugin [](https://github.com/ScuolaNormaleSuperiore/design-laboratori-wordpress-theme/tree/main/SETUP/PluginPerAggiornamenti/dli-aggiorna-priorita-plugin.zip) seguendo questa procedure:
1. Scompattare il file ***dli-aggiorna-priorita-plugin.zip*** nella cartella plugins.
2. Da WP->Plugin attivare il plugin: il plugin si attiva, esegue la procedura e si disattiva.
3. Eliminare dli-aggiorna-priorita-plugin dalla cartella plugins.
