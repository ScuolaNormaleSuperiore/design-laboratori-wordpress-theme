# Migrazione da 1.4.x a 1.5.x

Nella versione **1.5.x** le sezioni principali del sito sono state portate tutte al primo livello.
E' stato tolto quindi il livello "il-laboratorio" e le sezioni del tipo: *Home->Il laboratorio->Persone* sono diventate del tipo: *Home->Persone*.

Il menu "**Il laboratorio**" (The Lab) è stato chiamato "**Menu principale**" (Main menu).

Il menu "**Footer it**" (Footer en) è stato chiamato "**Menu footer**" (Menu footer en).

Le modifiche hanno ripercussioni sui menu, il breadcrumb e la mappa del sito.

Passi da seguire per l'aggiornamento:

1) Effettuare un backup del sistema (soprattutto del database).
2) Sostituire la versione vecchia del tema design-laboratori-wordpress-theme con quella nuova.
3) Modificare tutte le pagine che hanno come Genitore "Il laboratorio" o "The Lab" e impostare "Pagina principale (nessun genitore)". Le pagine da modificare sono: Archivio progetti (Projects archive), Attività di ricerca (Research activities), Blog (blog), Brevetti (Patents), Gli eventi (Events), I luoghi (Places), Le notizie (News). Si può usare la "modifica rapida" delle pagine per fare prima.
4) Lanciare la procedura che crea pagine e menu se non esistono: **WP->Aspetto->Ricaricarica i dati->Ricarica i dati di attivazione (menu, pagine, tassonomie, etc).**
5) Aprire la pagina in **WP->Aspetto->Menu**.
6) Associare alla posizione:  "***Menu principale***" di link a sinistra Italiano il menu: "***Menu principale***".
7) Associare alla posizione: "***Menu principale di link a sinistra English***" il menu: "***Main menu***".
8) Associare alla posizione: "***Menu a piè di pagina di link - footer Italiano***" il menu: "***Menu footer***".
9) Associare alla posizione: "***Menu a piè di pagina di link - footer English***"  il menu:  "***Menu footer en***".
10) Riportare manualmente le modifiche effettuate sui vecchi menu nei nuovi menu.
11) Cancellare se si vuole i menu: "Il laboratorio", "The lab", "Footer it", "Footer en".
