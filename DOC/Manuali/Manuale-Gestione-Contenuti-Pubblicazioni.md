# Manuale Gestione Contenuti Pubblicazioni

## 1. Scopo
Questo manuale descrive il content type `Pubblicazione` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Il content type `Pubblicazione` rappresenta una pubblicazione scientifica o tecnica del laboratorio/centro.

Caratteristiche principali:
- slug tecnico post type: `pubblicazione`
- contenuto pubblico
- gerarchico (`hierarchical = true`)
- campi nativi WordPress supportati:
1. `Titolo`
2. `Contenuto` (testo descrittivo/scheda breve della pubblicazione)
3. `Immagine in evidenza`

## 2 bis. La scheda di dettaglio della pubblicazione
Ogni pubblicazione ha una propria pagina pubblica, raggiungibile da un indirizzo leggibile del tipo
`/pubblicazione/titolo-della-pubblicazione/` (in inglese `/en/pubblicazione/titolo-in-inglese/`).

Cosa mostra la scheda:
- in alto: la `Tipo pubblicazione`, il `Titolo` e l'`Anno`;
- a sinistra: la copertina (l'`Immagine in evidenza`; se manca, un'immagine segnaposto con lo stemma) e i pulsanti di condivisione;
- a destra, nell'ordine: `Autori`, `Descrizione` (il testo dell'editor) e il pulsante `Consulta la pubblicazione` (il campo `Url`, che si apre in una nuova finestra).

Ogni blocco compare solo se ha un contenuto: una pubblicazione con solo titolo e anno mostra soltanto quelli.

Regole da conoscere:
- **Autori:** il nome è un link alla scheda della persona, tranne quando la persona ha `Disattiva pagina di dettaglio`: in quel caso il nome compare senza link. Una persona in bozza (non pubblicata) **non** compare tra gli autori.
- **Descrizione `.`:** il testo formato da un solo punto (lo scrivono gli importatori quando manca una descrizione) viene considerato vuoto e il blocco `Descrizione` non compare. Il contenuto nel database non viene modificato.
- **Testo alternativo della copertina:** è quello scritto nella libreria dei media (campo `Testo alternativo`). Se è vuoto, la scheda usa il testo generico «Copertina della pubblicazione»: per chi usa un lettore di schermo conviene compilarlo.
- **Protetta da password:** se in `Visibilità` si imposta `Protetto da password`, la scheda mostra il titolo, l'anno e il modulo per inserire la password; autori, descrizione e link compaiono solo dopo averla inserita.
- **Indirizzo:** deriva dal campo `Indirizzo` (slug) della pubblicazione, che WordPress compila dal titolo al primo salvataggio. Se lo si modifica su una pubblicazione già pubblicata, i link salvati altrove (altri siti, segnalibri) possono smettere di funzionare.

## 3. Dove si gestisce nel backoffice
- Menu principale: `Pubblicazioni`.
- Azioni disponibili:
1. `Pubblicazioni > Aggiungi Pubblicazione`
2. `Pubblicazioni > Tutte le Pubblicazioni`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Pubblicazione` sono presenti i seguenti campi.

### 4.1 Elenco campi ACF (tipo, obbligatorio, correlazioni)
- `Anno`
  tipo: `number`
  obbligatorio: `Si`
  correlazioni: `Nessuna`
- `Autori`
  tipo: `relationship`
  obbligatorio: `No`
  correlazioni: `Persona` (post type `persona`)
- `Url`
  tipo: `url`
  obbligatorio: `No`
  correlazioni: `Nessuna` (link esterno alla pubblicazione)
- `Promuovi in carousel`
  tipo: `true_false`
  obbligatorio: `No`
  correlazioni: `Nessuna` (usato per evidenza in homepage/carousel)
- `Promuovi in Home`
  tipo: `true_false`
  obbligatorio: `No`
  correlazioni: `Nessuna` (usato per evidenza in homepage)

## 5. Inserimento
1. Aprire `Pubblicazioni > Aggiungi Pubblicazione`.
2. Compilare `Titolo`.
3. Inserire il testo nell'editor principale.
4. Compilare il campo ACF obbligatorio `Anno`.
5. Compilare i campi consigliati:
- `Autori`
- `Url` (se disponibile)
6. Assegnare almeno una tipologia in `Tipo pubblicazione`.
7. Assegnare eventuali `Tag` utili.
8. Impostare `Immagine in evidenza` (se pertinente).
9. Attivare, se necessario, `Promuovi in Home` e/o `Promuovi in carousel`.
10. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Pubblicazioni > Tutte le Pubblicazioni`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare metadati (`Anno`, `Autori`, `Url`), tassonomie e testo.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda pubblicazione.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Pubblicazioni e scegliere `Elimina definitivamente`.

Nota: prima dell'eliminazione definitiva verificare eventuali progetti collegati che la referenziano.

## 8. Tassonomie
Il content type `Pubblicazione` usa:
- tassonomia custom `Tipo pubblicazione` (`tipo-pubblicazione`)
- tassonomia WordPress `Tag` (`post_tag`)

Uso pratico:
- `Tipo pubblicazione` viene usata come filtro nella pagina pubblica `Le pubblicazioni`.
- i `Tag` servono per classificazione trasversale e ricerca.

## 9. Gestione dei campi correlati
Correlazioni principali:
- dalla pubblicazione verso `Persona`: campo ACF `Autori`.
- da altri contenuti verso pubblicazione: in `Progetto` e' presente il campo relazione `Pubblicazioni`.

Flusso consigliato:
1. compilare nella pubblicazione il campo `Autori`;
2. nelle schede `Progetto` pertinenti, valorizzare il campo `Pubblicazioni`;
3. verificare in frontend che i collegamenti siano coerenti.

## 9 bis. Dopo l'aggiornamento del tema su un sito già in uso
La prima volta che una pagina del sito viene aperta dopo l'aggiornamento, il tema registra da solo i nuovi indirizzi delle pubblicazioni
(`/pubblicazione/...`); non serve salvare i permalink a mano. Se per qualche motivo un indirizzo risponde «Pagina non trovata», si
risolve da `Impostazioni > Permalink` premendo `Salva modifiche` senza cambiare nulla.

Da questo momento le pubblicazioni sono anche leggibili tramite l'API REST di WordPress (`/wp-json/wp/v2/pubblicazione`), come i
contenuti pubblici delle altre tipologie, a meno che l'API REST sia disattivata nelle opzioni del tema.

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Pubblicazioni`) sono presenti i campi:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Queste opzioni governano la pagina panoramica delle pubblicazioni, non la singola scheda.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- campo ACF `Anno` valorizzato
- `Tipo pubblicazione` assegnata
- `Autori` valorizzati (se disponibili)
- `Url` valorizzato e valido (se disponibile)
- contenuto testuale coerente e aggiornato (se non c'e' una descrizione, lasciare vuoto l'editor)
- `Immagine in evidenza` con `Testo alternativo` compilato, se si carica una copertina
- `Tag` coerenti (se usati)
- eventuali relazioni con `Progetti` verificate
- flag `Promuovi in Home` / `Promuovi in carousel` coerenti con la visibilita' desiderata
