# Change Log

Tutte le modifiche notevoli a questo progetto saranno documentate in questo file.

Il formato è basato su [Keep a Changelog](http://keepachangelog.com/)
e questo progetto segue [Semantic Versioning](http://semver.org/).


TAGS: Aggiunto (Added), Modificato (Changed), Deprecato (Deprecated), Rimosso (Removed), Corretto (Fixed), Sicurezza (Security).


## [Non rilasciato]
- Refactoring codice menu configurazione (creazione ConfigurationManager).
- Refactoring codice wrapper Polylang (creazione PolylangManager).
- Refactoring: spostare nel PluginManager tutte le configurazioni fatte nel file functions.php.



## [1.5.2] - 2024-02-06
### Aggiunto
- Gestione di etichette personalizzate nella tabella sf_
## Corretto
- Corretta gestione dei tag dei prpogetti.
## Modificato
- Aggiornato sito demo.
- Modificata procedura di attivazione di un nuovo sito.



## [1.5.1] - 2024-01-31
## Aggiunto
- Aggiunto contenuto Spin-Off.
## Corretto
- Corretti bug nell'attivazione di un nuovo sito.
## Modificato
- Aggiornati i file delle traduzioni.
- Aggiornata la versione di Bootstrap Italia alla versione 2.13.1.
- Aggiornato Dockerfile per sito demo.

## [1.5.0] - 2024-01-22
## Corretto
- Corretto import degli eventi da Indico: aggiunta immagine in modifica
## Modificato
- Tolta radice del sito: il-laboratorio.
- Aggiornato Dockerfile per sito demo.
- Modificata procedura di attivazione di un nuovo sito.
- Aggiornati i file delle traduzioni.
### Aggiunto
- Aggiunta procedura di migrazione da 1.4.x a 1.5.x.

## [1.4.2] - 2024-01-15
## Modificato
- Nuova gestione dei cookies.
- Aggiornato template per pagina dei cookies e pagina delle note legali.
- Aggiunta gestione delle categorie nell'import da Indico.
- Aggiornato Dockerfile per sito demo.

## [1.4.1] - 2024-12-12
## Corretto
- Corretta visualizzazione sezione "Seguici su".
## Modificato
- Modificata visualizzazione della data di un evento, negli eventi di un solo giorno.
- Brevetti: gestita importazione in italiano e inglese.
- Aggiornato Dockerfile per sito demo.

## [1.4.0] - 2024-12-10
### Aggiunto
- Home Page: Possibilità di visualizzare l'header principale in modalità "grande" o "piccolo".
- Elenco persone: Possibilità di attivare/disattivare il filtro struttura e, se attivato, di scegliere tra un filtro chip o select.
- Progetti: Possibile classificazione e filtro per TAG.
- Persone: Possibile classificazione e filtro per TAG.
### Modificato
- Portata configurazione lingue nel LabManager.
- Il campo Tagline, nella configurazione del sito, non è più obbligatorio.
- Aggiornati i file delle traduzioni.
- Aggiornato Dockerfile per sito demo.
- Refactoring sezione filtri nelle'elenco delle persone.
- Corretto overlay nella visualizzazione di banner e hero.
- Visualizzazione dei contenuti correlati come carousel nelle pagine del sito.
### Corretto
- Impostata struttura permalink in modalità articolo nel LabManager.
- Corretta visualizzazione chips persone.
- Refactoring nella gestione logo nello header e nel footer.

## [1.3.9] - 2024-12-05
### Corretto
- Corretti dei bug riguardanti la sezione dei brevetti.
### Modificato
- Aggiornata la versione di Bootstrap Italia alla versione 2.12.1.

## [1.3.8] - 2024-11-27
### Aggiunto
- Aggiunta configurazione per attivazione sezione "descrizione del sito" in Home Page.
### Modificato
- Aggiornata la versione di Bootstrap Italia alla versione 2.12.0.


## [1.3.7] - 2024-11-22
### Modificato
- Aggiornati file delle traduzioni.
- Aggiornata la versione di Bootstrap Italia alla versione 2.11.2.


## [1.3.6] - 2024-11-18
### Corretto
- corretto bug in cambio lingua.


## [1.3.5] - 2024-11-15
### Modificato
- Aggiunto campo video al contenuto brevetto.
- Aggiornata libreria BootstrapItalia alla versione 2.11.0.


## [1.3.4] - 2024-11-13
### Corretto
- Corretti bug: brevetti, visualizzazione eventi nelle pagine.
- Correzioni per l'accessibilità.

### Modificato
- Modificata visualizzazione brevetti


## [1.3.3] - 2024-10-25
### Corretto
- Corretta sezione di condivisione dei contenuti sui social.
- Corretta accessibilità del sito.

### Aggiunto
- Aggiunti OG tags per indicizzazione e condivisione dei contenuti sui social.
- Gestione del contenuto brevetto.
- Importazione dei brevetti da IRIS (AP).

### Sicurezza
- Disabilitato XMLRPC.

### Modificato
- Aggiornata libreria BootstrapItalia alla versione 2.10.0.
- Aggiornati file delle traduzioni.
- Aggiornato Dockerfile per sito demo.

### Refactoring
- Rifattorizzazione della creazione dei menu tramite file di configurazione dedicato.
- Rifattorizzazione della gestione degli import da Indico e IRIS.
- Introdotto DLI_ContentsManager per la gestione delle query dei contenuti e delle tassonomie.


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
- Aggiornati file delle traduzioni.

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
