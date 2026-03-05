# Manuale Gestione Contenuti Eventi

## 1. Scopo
Questo manuale descrive il content type `Evento` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Il content type `Evento` rappresenta gli appuntamenti del laboratorio/centro (seminari, workshop, conferenze, incontri, ecc.).

Caratteristiche principali:
- slug tecnico post type: `evento`
- contenuto pubblico
- non gerarchico (`hierarchical = false`)
- campi nativi WordPress supportati:
1. `Titolo`
2. `Contenuto` (descrizione estesa)
3. `Immagine in evidenza`

## 3. Dove si gestisce nel backoffice
- Menu principale: `Eventi`.
- Azioni disponibili:
1. `Eventi > Aggiungi un Evento`
2. `Eventi > Tutti gli Eventi`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Evento` sono presenti i seguenti campi.

### 4.1 Campi obbligatori
- `Descrizione breve` (max 255 caratteri)
- `Data inizio`
- `Data fine`
- `Luogo`

### 4.2 Campi data e ora
- `Data inizio`
- `Ora inizio`
- `Data fine`
- `Orario fine`

### 4.3 Campi contatti e risorse
- `Label Contatti`
- `Telefono`
- `Email`
- `Sito web`
- `Allegato`
- `Video`

### 4.4 Campo di navigazione link
- `Link dettaglio`:
1. `Scheda dettaglio`
2. `Sito web`
3. `Link ad allegato`

Nota: se scegli `Sito web` o `Link ad allegato`, devi compilare rispettivamente `Sito web` o `Allegato`.

### 4.5 Campi correlati e promozione
- `Progetto` (relazione verso `Progetto`)
- `Indirizzo di ricerca` (relazione verso `Indirizzo di ricerca`)
- `Promuovi in carousel`
- `Promuovi in home`

## 5. Inserimento
1. Aprire `Eventi > Aggiungi un Evento`.
2. Compilare `Titolo`.
3. Scrivere la descrizione nell'editor principale.
4. Compilare i campi obbligatori:
- `Descrizione breve`
- `Data inizio`
- `Data fine`
- `Luogo`
5. Compilare (se disponibili) `Ora inizio` e `Orario fine`.
6. Impostare `Link dettaglio` e valorizzare il campo coerente (`Sito web` o `Allegato`) se necessario.
7. Assegnare almeno una `Categoria` (consigliato) e i `Tag`.
8. Impostare `Immagine in evidenza`.
9. Compilare eventuali relazioni (`Progetto`, `Indirizzo di ricerca`).
10. Attivare, se utile, `Promuovi in home` e/o `Promuovi in carousel`.
11. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Eventi > Tutti gli Eventi`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare campi testo, date/ore, tassonomie, relazioni e link.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda evento.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Eventi e scegliere `Elimina definitivamente`.

Nota: prima dell'eliminazione definitiva verificare se l'evento e' usato in home/carousel o collegato a un progetto.

## 8. Tassonomie
Il content type `Evento` usa:
- `Categorie` (`category`)
- `Tag` (`post_tag`)

Uso pratico:
- le categorie sono usate come filtro nella pagina pubblica `Gli eventi`
- i tag servono a classificare temi trasversali e a migliorare la ricerca.

## 9. Gestione dei campi correlati
Relazioni disponibili nella scheda evento:
- `Progetto`
- `Indirizzo di ricerca`

Flusso consigliato:
1. Collegare l'evento al `Progetto` corretto (se pertinente).
2. Collegare l'evento all'`Indirizzo di ricerca` corretto (se pertinente).
3. Salvare/aggiornare la scheda.
4. Verificare in frontend le sezioni collegate (es. dettaglio progetto con blocco "Eventi e notizie").

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Eventi`) sono presenti i campi di configurazione della pagina panoramica:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Queste opzioni governano la pagina lista `Gli eventi`, non i campi della singola scheda evento.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- `Descrizione breve` valorizzata
- `Data inizio`, `Data fine` e `Luogo` valorizzati
- `Link dettaglio` coerente con i campi compilati
- almeno una `Categoria` assegnata
- `Tag` coerenti (se usati)
- immagine in evidenza presente
- relazioni (`Progetto`/`Indirizzo di ricerca`) valorizzate se pertinenti
- flag `Promuovi in home` / `Promuovi in carousel` coerenti con la visibilita' desiderata
