# Change Log

Tutte le modifiche notevoli a questo progetto saranno documentate in questo file.

Il formato è basato su [Keep a Changelog](http://keepachangelog.com/)
e questo progetto segue [Semantic Versioning](http://semver.org/).


TAGS: Aggiunto (Added), Modificato (Changed), Deprecato (Deprecated), Rimosso (Removed), Corretto (Fixed), Sicurezza (Security).


## [Non rilasciato]
- Refactoring codice menu configurazione (creazione ConfigurationManager).
- Refactoring codice wrapper Polylang (creazione PolylangManager).
- Refactoring: spostare nel PluginManager tutte le configurazioni fatte nel file functions.php.

## [1.3.3] - 2024-09-30
### Corretto
- Corretta sezione di condivisione dei contenuti sui social.

### Aggiunto
- Aggiunti OG tags per indicizzazione e condivisione dei contenuti sui social.

### Sicurezza
- Disabilitato XMLRPC.


## [1.3.2] - 2024-09-04
### Corretto
- Corretto bug paginazione ricerca.
- Corretto bug visualizzazione data eventi e notizie.

### Modificato
- Aggiornato Dockerfile per sito demo.
- Aggiornata la versione di Bootstrap Italia alla versione 2.9.0.


## [1.3.1] - 2024-07-26
### Corretto
- Spostato codice analytics in template a parte.
- Corretta visualizzazione data blog.
- Corretto bug visualizzazione biografia nella scheda di una persona.
- Corretto file publicode.yml.


## [1.3.0] - 2024-06-12
### Corretto
- Corretta associazione dei template alle pagine.

### Modificato
- Aggiornata la versione di Bootstrap Italia alla versione 2.8.8.
- Aggiornato Dockerfile per sito demo.


## [1.2.9] - 2024-06-05
### Corretto
- Corretto bug visualizzazione orario eventi.


## [1.2.8] - 2024-06-04
### Aggiunto
- Integrazione con Indico.
- Sezione articoli in Home Page.
- Nuova pagina di dettaglio per eventi, notizie e blog.
- Aggiunta gestione dei tag nei post type.
- Aggiunta gestione dei banner in Home Page con un post type personalizzato.

### Modificato
- Tolta dipendenza da plugin "Disable Gutenberg". Se serve va installato "a mano".
- Modificata gestione dei template nella sezione "Contenuti in evidenza".
- Aggiornata la versione di Bootstrap Italia alla versione 2.8.7.

### Corretto
- Corrette errate invocazioni della funzione: dli_get_post_main_category.
- Corretta selezione degli eventi da mostrare nella sezione Eventi della Home Page.
- Corretta visualizzazione dell'elenco delle persone nella pagina di dettaglio di un progetto.
- Corretta gestione dei filtri per categorie nelle pagine: eventi e news.
- Corretta visualizzazione dei chip delle categorie nelle pagine di dettaglio di eventi e notizie.
- Aggiornato Dockerfile per sito demo.
- Corretto l'ordine di visualizzazione degli eventi nella sezione eventi in HP.


## [1.2.7] - 2024-04-30
### Aggiunto
- Aggiunto ruolo "Super Editor".
- Aggiunta della sezione "Elenco contenuti" con le strisce "News", "Eventi" e "Pubblicazioni".

### Modificato
- Aggiornata libreria Bootstrap Italia alla versione 2.8.4.
- Corretta traduzione etichette.

### Corretto
- Aggiornato sito demo.


## [1.2.6] - 2024-03-07

### Aggiunto
- Aggiunto in configurazione flag per nascondere logo nel footer e uso del flag nel footer.
- Aggiunta possibilità di specificare un logo diverso nel footer.

### Sicurezza
- Corretto escaping dei dati delle form: contatti, ricerca e newsletter.

### Corretto
- Tolta gestione del numero di telefono nell'anagarafica di Brevo.


## [1.2.5] - 2024-03-04

### Aggiunto
- Aggiunta integrazione con Brevo.

### Sicurezza
- Aggiunta gestione nonce nelle form: contatti e ricerca.
- Corretta gestione sanitize input nelle form: contatti e ricerca.
- Reso meno specifico il testo del messaggio di login errato.
- Nascosto tag "generator" nel sorgente delle pagine del sito.

### Modificato
- Aggiornata libreria Bootstrap Italia alla versione 2.8.2.
- Corretto codice in base ai warning generati da PHP 8.2 soprattutto sulle librerie CMB2.
- Aggiornato sistema di demo basato su Docker.
