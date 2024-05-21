# Change Log

Tutte le modifiche notevoli a questo progetto saranno documentate in questo file.

Il formato è basato su [Keep a Changelog](http://keepachangelog.com/)
e questo progetto segue [Semantic Versioning](http://semver.org/).


TAGS: Aggiunto (Added), Modificato (Changed), Deprecato (Deprecated), Rimosso (Removed), Corretto (Fixed), Sicurezza (Security).


## [Non rilasciato]
- Fix contenuti in evidenza
- Tipologia eventi
- 


## [1.2.8] - 2024-05-30

### Aggiunto
- 

### Modificato
- Tolta dipendenza da plugin "Disable Gutenberg". Se serve va installato "a mano".
-

### Corretto
- Aggiornato sito demo.
- Corrette errate invocazioni della funzione: dli_get_post_main_category.
- Corretta selezione degli eventi da mostrare nella sezione Eventi della Home Page.


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
