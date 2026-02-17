# Manuale Gestione Contenuti Attivita' di Ricerca

## 1. Scopo
Questo manuale descrive il content type `Attivita' di ricerca` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Nel tema, le attivita' di ricerca sono implementate come content type `Indirizzo di ricerca` (slug tecnico `indirizzo-di-ricerca`).

Caratteristiche principali:
- contenuto pubblico
- supporta i campi nativi WordPress:
1. `Titolo`
2. `Contenuto` (descrizione estesa)
3. `Immagine in evidenza`
- in frontend compare nella sezione/pagina "Attivita' di ricerca".

## 3. Dove si gestisce nel backoffice
- Menu principale: `Indirizzi di ricerca` (attivita' di ricerca).
- Azioni disponibili:
1. `Indirizzi di ricerca > Aggiungi un Indirizzo di ricerca`
2. `Indirizzi di ricerca > Tutti gli Indirizzi di ricerca`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Indirizzo di ricerca` sono presenti i seguenti campi.

### 4.1 Elenco campi ACF (tipo, obbligatorio, correlazioni, funzione)
- `Descrizione breve`
  tipo: `textarea`
  obbligatorio: `Si`
  funzione: testo sintetico usato in card/elenco.
- `Email`
  tipo: `email`
  funzione: contatto email dell'attivita' di ricerca.
- `Telefono`
  tipo: `text`
  funzione: contatto telefonico dell'attivita' di ricerca.
- `Sito web`
  tipo: `url`
  funzione: link esterno di riferimento.
- `Responsabile dell’attivita' di ricerca`
  tipo: `relationship`
  correlazioni: `Persona` (post type `persona`)
  funzione: collega il referente scientifico/organizzativo.
- `Priorita`
  tipo: `number`
  funzione: ordine di visualizzazione negli elenchi.

## 5. Inserimento
1. Aprire `Indirizzi di ricerca > Aggiungi un Indirizzo di ricerca`.
2. Compilare `Titolo`.
3. Scrivere la descrizione nell'editor principale.
4. Compilare il campo ACF obbligatorio `Descrizione breve`.
5. Compilare i campi consigliati:
- `Responsabile dell’attivita' di ricerca`
- `Email`, `Telefono`, `Sito web`
- `Priorita`
6. Assegnare eventuali `Tag`.
7. Impostare `Immagine in evidenza` (se disponibile).
8. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Indirizzi di ricerca > Tutti gli Indirizzi di ricerca`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare descrizione, contatti, responsabile, priorita' e tag.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda dell'attivita' di ricerca.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Indirizzi di ricerca e scegliere `Elimina definitivamente`.

Nota: prima dell'eliminazione definitiva verificare contenuti collegati (progetti, eventi, notizie).

## 8. Tassonomie
Il content type `Indirizzo di ricerca` usa:
- tassonomia WordPress `Tag` (`post_tag`)

Uso pratico:
- i tag servono per classificazione tematica e ricerca trasversale.

## 9. Gestione dei campi correlati
Correlazioni principali:
- nella scheda attivita' di ricerca: `Responsabile dell’attivita' di ricerca` -> `Persona`.
- da altri contenuti verso attivita' di ricerca:
  `Progetto` (campo `Elenco indirizzi di ricerca correlati`), `Evento` e `Notizia` (campo `Indirizzo di ricerca`).

Flusso consigliato:
1. valorizzare il responsabile nella scheda attivita' di ricerca;
2. collegare l'attivita' nelle schede `Progetto`, `Evento` e `Notizia` pertinenti;
3. verificare in frontend le sezioni correlate (`Progetti`, `Eventi e notizie`).

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Indirizzi di ricerca`) sono presenti i campi:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Queste opzioni governano la pagina panoramica delle attivita' di ricerca, non la singola scheda.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- campo ACF obbligatorio `Descrizione breve` valorizzato
- contenuto descrittivo completo e coerente
- contatti (`Email`, `Telefono`, `Sito web`) valorizzati e validi (se disponibili)
- `Responsabile dell’attivita' di ricerca` valorizzato (se disponibile)
- `Priorita` valorizzata in modo coerente con l'ordinamento desiderato
- `Tag` coerenti assegnati (se usati)
- immagine in evidenza presente (se disponibile)
