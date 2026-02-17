# Manuale Gestione Contenuti Notizie

## 1. Scopo
Questo manuale descrive il content type `Notizia` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Dove si gestisce nel backoffice
- Menu principale: `Notizie`.
- Azioni disponibili:
1. `Notizie > Aggiungi una Notizia`
2. `Notizie > Tutte le Notizie`
3. Apertura della singola scheda per modifica rapida o completa.

## 3. Come e' fatto il content type Notizia
- Slug tecnico post type: `notizia`.
- Tipo: pubblico, visibile nel menu admin.
- Supporta i campi nativi WordPress:
1. `Titolo`
2. `Contenuto` (editor principale, usato come corpo della notizia)
3. `Immagine in evidenza`
- Tassonomie associate:
1. `Categorie` (`category`)
2. `Tag` (`post_tag`)
- Non e' gerarchico (`hierarchical = false`).

## 4. Campi principali della scheda Notizia (ACF)
Nel box `Campi Notizia` sono presenti i seguenti campi.

### 4.1 Campi obbligatori
- `Descrizione breve` (max 255 caratteri)

### 4.2 Campi di visibilita' e promozione
- `Promuovi in carousel`
- `Promuovi in home`

### 4.3 Campi link e allegato
- `Link dettaglio`:
1. `Scheda dettaglio` (apre la pagina dettaglio della notizia)
2. `Sito web` (usa il campo `Sito web`)
3. `Link ad allegato` (usa il campo `Allegato`)
- `Sito web` (URL esterno)
- `Allegato` (file media)

Nota: se `Link dettaglio` punta a `Sito web` o `Link ad allegato` e il campo corrispondente e' vuoto, il link in frontend risulta vuoto/non valido.

### 4.4 Campi relazionali
- `Progetto` -> relazione con contenuti `Progetto`
- `Indirizzo di ricerca` -> relazione con contenuti `Indirizzo di ricerca`

## 5. Inserimento di una nuova notizia
1. Aprire `Notizie > Aggiungi una Notizia`.
2. Compilare `Titolo`.
3. Scrivere il contenuto nell'editor principale.
4. Compilare `Descrizione breve`.
5. Impostare `Link dettaglio`:
- se `Scheda dettaglio`, non servono altri campi;
- se `Sito web`, compilare `Sito web`;
- se `Link ad allegato`, caricare `Allegato`.
6. Assegnare almeno una `Categoria` (consigliato) e i `Tag` utili.
7. Impostare `Immagine in evidenza`.
8. Valorizzare eventuali relazioni (`Progetto`, `Indirizzo di ricerca`).
9. Attivare, se necessario, `Promuovi in home` e/o `Promuovi in carousel`.
10. Cliccare `Pubblica`.

## 6. Modifica di una notizia esistente
1. Aprire `Notizie > Tutte le Notizie`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare testo, tassonomie, relazioni, link/allegati.
4. Cliccare `Aggiorna`.

## 7. Cancellazione di una notizia
1. Aprire la scheda notizia.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Notizie e scegliere `Elimina definitivamente`.

Nota: prima dell'eliminazione definitiva verificare eventuali sezioni/caroselli/home che la includevano.

## 8. Tassonomie del content type Notizia
Le tassonomie usate sono:
- `Categorie` (`category`): usate come filtro principale nella pagina `Le notizie`.
- `Tag` (`post_tag`): usati nel dettaglio della notizia come "Argomenti".

Uso pratico:
- usare categorie poche e stabili;
- usare tag per dettagliare temi trasversali;
- evitare duplicati semantici (es. singolare/plurale dello stesso concetto).

## 9. Campi correlati con altri content type
Le notizie possono essere collegate a:
- `Progetto`
- `Indirizzo di ricerca`

Flusso consigliato:
1. Compila il campo relazione nella scheda notizia.
2. Salva/aggiorna la notizia.
3. Verifica in frontend le sezioni collegate (es. dettaglio progetto con blocco "Eventi e notizie").

## 10. Configurazioni backoffice della sezione "Le Novita'"
Nelle opzioni tema (`dli_options > Novita`) sono presenti campi che governano la pagina lista:
- descrizione sezione (IT/EN)
- `Numero pagine collegate` (numero massimo di notizie/eventi associabili a una pagina)

## 11. Checklist rapida prima della pubblicazione
- `Titolo` valorizzato
- `Descrizione breve` valorizzata
- corpo notizia completo
- `Link dettaglio` coerente con i campi compilati
- almeno una `Categoria` assegnata
- `Tag` coerenti (se usati)
- immagine in evidenza presente
- relazioni (`Progetto`/`Indirizzo di ricerca`) valorizzate se pertinenti
