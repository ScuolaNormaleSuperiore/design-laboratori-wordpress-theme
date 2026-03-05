# Manuale Gestione Contenuti Spin-off

## 1. Scopo
Questo manuale descrive il content type `Spin-off` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Il content type `Spin-off` rappresenta una spin-off collegata alle attivita' del laboratorio/centro.

Caratteristiche principali:
- slug tecnico post type: `spinoff`
- contenuto pubblico
- non gerarchico (`hierarchical = false`)
- campi nativi WordPress supportati:
1. `Titolo`
2. `Contenuto` (descrizione estesa)
3. `Immagine in evidenza`

## 3. Dove si gestisce nel backoffice
- Menu principale: `Spin-off`.
- Azioni disponibili:
1. `Spin-off > Aggiungi una Spin-off`
2. `Spin-off > Tutte le Spin-off`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Spin-off` sono presenti i seguenti campi.

### 4.1 Elenco campi ACF (tipo, obbligatorio, correlazioni)
- `Descrizione breve`
  tipo: `textarea`
  obbligatorio: `Si`
- `Anno di costituzione`
  tipo: `number`
  obbligatorio: `Si`
- `Stato`
  tipo: `select`
  obbligatorio: `Si`
- `Logo`
  tipo: `image`
- `Sito web`
  tipo: `url`
- `Telefono`
  tipo: `text`
- `Email`
  tipo: `text`
- `Note`
  tipo: `wysiwyg`
- `Video`
  tipo: `url`
- `Promuovi in home`
  tipo: `true_false`
- `Promuovi in carousel`
  tipo: `true_false`

## 5. Inserimento
1. Aprire `Spin-off > Aggiungi una Spin-off`.
2. Compilare `Titolo`.
3. Scrivere la descrizione nell'editor principale.
4. Compilare i campi ACF obbligatori:
- `Descrizione breve`
- `Anno di costituzione`
- `Stato`
5. Compilare i campi consigliati:
- `Sito web`, `Telefono`, `Email`
- `Note`, `Video`
- `Logo`
6. Assegnare almeno un termine in `Settore di attivita'`.
7. Assegnare eventuali `Categorie` e `Tag`.
8. Impostare `Immagine in evidenza` (se disponibile).
9. Attivare, se necessario, `Promuovi in home` e/o `Promuovi in carousel`.
10. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Spin-off > Tutte le Spin-off`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare descrizione, metadati, contatti, tassonomie e media.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda spin-off.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Spin-off e scegliere `Elimina definitivamente`.

## 8. Tassonomie
Il content type `Spin-off` usa:
- tassonomia custom `Settore di attivita'` (`settore-attivita`)
- tassonomia WordPress `Categorie` (`category`)
- tassonomia WordPress `Tag` (`post_tag`)

Uso pratico:
- nella pagina pubblica `Spin-off` i filtri principali sono anno di costituzione, settore di attivita' e ricerca libera.

## 9. Gestione dei campi correlati
Nella scheda `Spin-off` non sono presenti campi ACF di relazione verso altri content type.

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Spin-off`) sono presenti i campi:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Queste opzioni governano la pagina panoramica delle spin-off, non la singola scheda.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- campi ACF obbligatori valorizzati (`Descrizione breve`, `Anno di costituzione`, `Stato`)
- `Settore di attivita'` assegnato
- descrizione estesa coerente e aggiornata
- contatti valorizzati e validi (se disponibili)
- immagine/logo coerenti (se disponibili)
- flag `Promuovi in home` / `Promuovi in carousel` coerenti con la visibilita' desiderata
