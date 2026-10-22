# Manuale Utente — Design Laboratori Italia

Guida operativa per chi gestisce i contenuti del sito con il ruolo **Super Editor** (o superiore). Copre i content type disponibili, la composizione della home page, le pagine base e i menu.

> Nota per chi valuta il tema in ottica di riuso: ogni sezione indica anche lo slug tecnico del content type/campo e il file del tema coinvolto, per chi vuole verificare il comportamento direttamente nel codice.

## 1. Ruoli

Il tema aggiunge un ruolo **Super Editor** (basato su Editor) con due permessi in più:
- gestione dell'aspetto del tema (voce **Aspetto** del backoffice, necessaria per i menu di navigazione);
- accesso al pannello **WP → Configurazione** (quasi tutte le sue schede — i dettagli di cosa resta riservato al solo Amministratore sono nel [Manuale Amministratore](Manuale-Amministratore.md)).

Un Redattore/Editor "semplice" lavora solo sui contenuti, senza accesso alla configurazione del tema o alla gestione del menu.

## 2. Content type disponibili

Ogni content type ha una propria voce di menu nel backoffice WordPress, con le azioni standard "Aggiungi", "Tutti gli elementi", tassonomie/categorie dove presenti. Tutti supportano **Titolo**, **Contenuto** e, salvo indicazione contraria, **Immagine in evidenza**.

### 2.1 Persone (`persona`)
Il personale del laboratorio. Campi principali: Titolo (appellativo), Nome, Cognome, Email, Telefono, Sito web, allegati (CV + 3 allegati liberi), `Categoria di appartenenza` (collega la persona a una "Tipologia persona", usata per raggrupparla nell'elenco). Flag utili: `Escludi da elenco`, `Disattiva pagina dettaglio`, `Abilita link diretto alla pagina della persona` (apre il sito web esterno invece della scheda interna), `Promuovi in Home`.
Tassonomia: `Struttura` (gerarchica, dipartimento/struttura di afferenza).

### 2.2 Tipologia persona (`tipologia-persona`)
Content type di supporto: definisce le categorie mostrate nell'elenco Persone (es. "Docenti", "Dottorandi"). Campi: Nome, Descrizione, Priorità (ordinamento).

### 2.3 Progetti (`progetto`)
Campi: Descrizione breve, Data inizio/fine, Priorità, `Archiviato` (sposta il progetto nell'archivio progetti conclusi), Url, relazioni verso Responsabile, Persone coinvolte, Indirizzi di ricerca correlati, Pubblicazioni, Risorse tecniche, fino a 3 allegati. Flag: `Promuovi in Home`, `Promuovi in carousel`.
Tassonomia: `Tag` (post_tag nativo, usato come filtro "argomento").

### 2.4 Indirizzi di ricerca / Attività di ricerca (`indirizzo-di-ricerca`)
Le linee di ricerca del laboratorio. Campi: Descrizione breve, Email, Telefono, Sito web, `Responsabile dell'attività di ricerca`, Priorità. Tag mostrati in scheda ma non filtrabili nell'elenco pubblico.

### 2.5 Notizie (`notizia`)
Campi: Descrizione breve, Allegato, Sito web, `Link dettaglio` (apre la notizia sul sito o su un link esterno), relazioni verso Progetto/Indirizzo di ricerca. Flag: `Promuovi in Home`, `Promuovi in carousel`.
Tassonomie: `Categoria` e `Tag` nativi di WordPress (condivisi con il Blog).

### 2.6 Eventi (`evento`)
Campi: Descrizione breve, Data/ora inizio e fine, Luogo (testo libero), contatti (label, telefono, email, sito web), Allegato, Video, `Link dettaglio`, relazioni verso Progetto/Indirizzo di ricerca. Flag: `Promuovi in Home`, `Promuovi in carousel`.
Tassonomie: `Categoria` e `Tag` nativi.
Importazione automatica disponibile da **Indico** (vedi Manuale Amministratore).

### 2.7 Luoghi (`luogo`)
Campi: Descrizione breve, Posizione GPS (mappa OpenStreetMap), email/PEC, riferimento telefonico, indirizzo, CAP, orario per il pubblico, "Come raggiungerci". Flag: `Promuovi in Home`.
Tassonomia: `Tipologia luogo`.

### 2.8 Pubblicazioni (`pubblicazione`)
Campi: Anno (obbligatorio), `Autori` (relazione verso Persone — i nomi compaiono come link alla scheda persona), Url (link esterno a rivista/archivio). Flag: `Promuovi in Home`, `Promuovi in carousel`.
Tassonomie: `Tipo pubblicazione` (custom) e `Tag`.
**Scheda di dettaglio pubblica attiva**: ogni pubblicazione ha una propria pagina (titolo cliccabile dall'elenco) con copertina (= Immagine in evidenza, o un'immagine decorativa generica se assente), autori, descrizione e, se presente, il bottone "Consulta la pubblicazione" verso l'Url esterno.

### 2.9 Brevetti (`brevetto`)
Campi estesi: sommario, note, video, codice brevetto, stato legale (Iris o testo libero), date/numero di deposito, inventori, titolari, famiglia brevettuale. Flag: `Promuovi in Home`, `Promuovi in carousel`.
Tassonomia: `Area tematica`.
Importazione automatica disponibile da **IRIS** (vedi Manuale Amministratore).

### 2.10 Spin-off (`spinoff`)
Campi: Descrizione breve, Anno di costituzione, Stato, Logo, sito web, contatti, Note, Video. Flag: `Promuovi in Home`, `Promuovi in carousel`.
Tassonomia: `Settore di attività`.

### 2.11 Risorse tecniche (`risorsa-tecnica`)
Campi: Foto, Descrizione breve, codice interno, costo, posizione, `Localizzazione` (relazione verso Luoghi), marca/modello, anno di acquisizione, stato, dimensioni, conformità, Responsabili, scheda tecnica + manuale d'uso + 2 allegati. Flag: `Promuovi in Home`, `Promuovi in carousel`.
Tassonomia: `Tipo risorsa tecnica`.

### 2.12 Blog (post nativi di WordPress)
Usa il tipo di contenuto "Articolo" standard di WordPress. Flag aggiuntivi: `Promuovi in home`, `Promuovi in carousel`.
Tassonomie: `Categoria` e `Tag` (condivise con le Notizie).

### 2.13 Banner (`banner`)
Banner secondari mostrati in home. Campi: Sezione (etichetta), Priorità (ordinamento), Label pulsante, Link pulsante, `Apri in nuova finestra`.

### 2.14 Sponsor (`sponsor`)
Loghi del grid "Partner e collaborazioni" in home. Campi: Priorità, Link esterno. L'immagine è l'Immagine in evidenza.

## 3. La Home Page

Le sezioni della home sono gestite da **WP → Configurazione → Sezioni HP** (abilitazione, ordine, titolo visibile) e, per i testi/contenuti specifici, dalle schede **Layout HP**/**Il Laboratorio**. L'elenco completo delle sezioni disponibili, con dove si configura ciascuna, è nel [Manuale Amministratore](Manuale-Amministratore.md#3-sezioni-hp) — qui il riepilogo operativo di ognuna:

| Sezione | Come si popola |
|---|---|
| **Hero principale** | Testi/immagine in Layout HP |
| **Pagine in evidenza** | Automatica: pagine con il flag `Promuovi in Home` attivo (vedi §4), fino a 6, card con titolo/estratto/link |
| **Descrizione del sito** | Testo in "Il Laboratorio" |
| **Pannello tab** | Gruppo di tab configurato in Layout HP: ogni tab ha etichetta, descrizione e, facoltativamente, un elenco di pagine collegate mostrato in fondo al pannello (bilingue IT/EN) |
| **Carousel principale** | Contenuti con `Promuovi in carousel` attivo (selezione automatica o manuale) |
| **Contenuti in evidenza** | 3 box configurabili (tipo di contenuto + template) |
| **Eventi / Notizie / Pubblicazioni / Articoli** | Contenuti con `Promuovi in Home` attivo per quel tipo |
| **Banner** | Content type Banner |
| **Sponsor** | Content type Sponsor |
| **Navigazione per pagine** | Automatica: per ogni pagina con `Promuovi in Home` attivo mostra le sue sottopagine dirette come elenco di link (le pagine senza sottopagine non compaiono) |
| **Indice di una pagina** | Scegli una pagina in Sezioni HP: mostra una colonna per ogni sua sottopagina diretta, con le sottopagine di quella sottopagina come link — **ripetibile**: puoi aggiungere più righe, ciascuna con una pagina diversa |
| **Video** | Url video in Layout HP |

Per ciascuna sezione (tranne Hero/Carousel/Video) puoi anche scegliere, riga per riga in **Sezioni HP**, se lo sfondo è bianco o grigio chiaro.

## 4. Pagina base (content type `page`, nativo WordPress)

Le pagine generiche del sito (non un content type dedicato) usano il template "Pagina base". Oltre ai campi standard:

- **Pagine collegate**: relazione verso Notizie/Eventi mostrati in fondo alla pagina.
- **Promuovi in Home**: la pagina compare nella sezione home "Pagine in evidenza" (come card) e, se ha sottopagine, anche in "Navigazione per pagine" (come elenco).
- **Mostra le sottopagine come card**: se attivo, in testa al corpo della pagina compare un blocco "In questa sezione" con una card per ogni sottopagina diretta (titolo, estratto automatico, link "Vai al servizio") — utile per pagine "indice" con molte sottosezioni.

La gerarchia (pagina padre/figlia, impostabile in "Attributi pagina") determina anche la sidebar **"Pagine collegate"**, che mostra sempre l'intero albero della famiglia di pagine a cui appartiene quella corrente (fratelli e discendenti a qualunque profondità), con le voci con figli espandibili/comprimibili.

## 5. Menu

Da **Aspetto → Menu** si gestiscono 5 posizioni:
- **Menu principale di link a sinistra** (`menu-lab`): la barra di navigazione principale, voci semplici o con dropdown.
- **Menu secondario multilevel a destra** (`menu-right`): pensato per le Pagine base — una voce con sottopagine diventa automaticamente un dropdown con tutte le sue sottopagine dirette elencate.
- **Menu header in alto a destra**, **Menu footer "Link utili"**, **Menu a piè di pagina**: link semplici, nessun dropdown.

Ogni posizione ha anche una variante per l'inglese (gestita separatamente tramite Polylang).

## 6. Lingue (IT/EN)

Il sito usa Polylang: italiano è la lingua di default, inglese quella secondaria. Per ogni contenuto/pagina crea sempre la versione italiana e, se richiesto, la traduzione inglese collegata. I testi liberi delle sezioni home (titoli, sottotitoli, tab) hanno un campo IT e uno ENG separati in configurazione.

## 7. Checklist pubblicazione contenuto

- Titolo chiaro, Immagine in evidenza presente e pertinente (con testo alternativo compilato in libreria media — non lasciare il nome del file).
- Campi obbligatori del content type compilati (es. Anno per le Pubblicazioni).
- Tassonomie/categorie assegnate dove previste.
- Traduzione inglese creata se il contenuto deve essere bilingue.
- Flag `Promuovi in Home`/`Promuovi in carousel` coerenti con la visibilità desiderata.
- Verifica anteprima prima di pubblicare.
