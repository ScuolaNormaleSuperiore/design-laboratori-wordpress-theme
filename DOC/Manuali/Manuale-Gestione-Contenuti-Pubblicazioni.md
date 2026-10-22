# Manuale Gestione Contenuti Pubblicazioni

## 1. Scopo
Questo manuale descrive il content type `Pubblicazione` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Il content type `Pubblicazione` rappresenta una pubblicazione scientifica o tecnica del laboratorio/centro.

Caratteristiche principali:
- slug tecnico post type: `pubblicazione`
- contenuto pubblico, con una propria scheda di dettaglio (vedi sotto)
- non gerarchico (`hierarchical = false`)
- campi nativi WordPress supportati:
1. `Titolo`
2. `Contenuto` (testo descrittivo/scheda breve della pubblicazione, mostrato nella sezione "Descrizione" della scheda)
3. `Immagine in evidenza` (usata come copertina del volume, vedi 4.2)

## 3. Dove si gestisce nel backoffice
- Menu principale: `Pubblicazioni`.
- Azioni disponibili:
1. `Pubblicazioni > Aggiungi Pubblicazione`
2. `Pubblicazioni > Tutte le Pubblicazioni`
3. Apertura della singola scheda per modifica rapida o completa.

Ogni pubblicazione pubblicata ha una propria pagina pubblica di dettaglio, raggiungibile cliccando il titolo nell'elenco `Le pubblicazioni`. La scheda mostra: categoria/tipologia, titolo, anno in evidenza (hero); copertina (o un'immagine decorativa generica se non impostata) e il bottone "Condividi" in una colonna laterale; `Autori` (con link alle rispettive schede persona, quando disponibili), `Descrizione` e, se compilato, il bottone "Consulta la pubblicazione" che rimanda al campo `Url`.

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

### 4.2 Copertina
Non esiste un campo ACF dedicato: la copertina mostrata nella scheda di dettaglio e nell'elenco e' sempre il campo nativo `Immagine in evidenza`. Se non e' impostata, la scheda di dettaglio mostra un'immagine decorativa generica al suo posto; nell'elenco, le pubblicazioni senza immagine in evidenza vengono semplicemente mostrate senza copertina (card senza immagine).

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

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Pubblicazioni`) sono presenti i campi:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Queste opzioni governano la pagina panoramica delle pubblicazioni, non la singola scheda (che non ha testi configurabili: mostra sempre i campi della pubblicazione stessa).

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- campo ACF `Anno` valorizzato
- `Tipo pubblicazione` assegnata
- `Autori` valorizzati (se disponibili)
- `Url` valorizzato e valido (se disponibile)
- contenuto testuale coerente e aggiornato
- `Tag` coerenti (se usati)
- eventuali relazioni con `Progetti` verificate
- flag `Promuovi in Home` / `Promuovi in carousel` coerenti con la visibilita' desiderata
- `Immagine in evidenza` impostata, se disponibile una copertina reale (consigliata ma non obbligatoria: in sua assenza la scheda mostra un'immagine decorativa generica)
