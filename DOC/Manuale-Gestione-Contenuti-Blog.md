# Manuale Gestione Contenuti Blog

## 1. Scopo
Questo manuale descrive la gestione del content type blog standard WordPress (`post`) nel tema: creazione, modifica, cancellazione, tassonomie, campi ACF e configurazioni collegate.

## 2. Che cos'e' il content type
Nel tema il blog usa il content type WordPress standard `Post` (slug tecnico `post`), pubblicato nella pagina archivio "Il blog".

Caratteristiche principali:
- contenuto pubblico
- supporta i campi nativi WordPress:
1. `Titolo`
2. `Contenuto`
3. `Immagine in evidenza`
4. `Estratto` (opzionale, se usato nel workflow editoriale)

## 3. Dove si gestisce nel backoffice
- Menu principale WordPress: `Articoli`.
- Azioni disponibili:
1. `Articoli > Aggiungi nuovo`
2. `Articoli > Tutti gli articoli`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Articolo` sono presenti i seguenti campi.

### 4.1 Elenco campi ACF (tipo, obbligatorio, correlazioni, funzione)
- `Promuovi in home`
  tipo: `true_false`
  funzione: evidenzia l'articolo nei blocchi home che usano il flag di promozione.
- `Promuovi in carousel`
  tipo: `true_false`
  funzione: rende l'articolo candidabile/visibile nel carousel home (in base alla configurazione del sito).

## 5. Inserimento
1. Aprire `Articoli > Aggiungi nuovo`.
2. Compilare `Titolo`.
3. Scrivere il contenuto nell'editor.
4. Assegnare almeno una `Categoria` (consigliato) e gli eventuali `Tag`.
5. Impostare `Immagine in evidenza`.
6. Valutare i campi ACF:
- `Promuovi in home`
- `Promuovi in carousel`
7. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Articoli > Tutti gli articoli`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare testo, tassonomie, immagine e flag ACF.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda articolo.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Articoli e scegliere `Elimina definitivamente`.

## 8. Tassonomie
Il content type `Post` usa le tassonomie standard WordPress:
- `Categorie` (`category`)
- `Tag` (`post_tag`)

Uso pratico:
- nella pagina `Il blog` e' disponibile il filtro per categoria;
- i tag supportano classificazione trasversale e ricerca tematica.

## 9. Gestione dei campi correlati
Nella scheda `Post` non sono presenti campi ACF di relazione verso altri content type.

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Blog`) sono presenti i campi:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Queste opzioni governano il testo introduttivo della pagina blog (`page-templates/blog.php`), non la singola scheda articolo.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- contenuto completo e coerente
- almeno una `Categoria` assegnata
- `Tag` coerenti (se usati)
- immagine in evidenza presente e corretta
- flag ACF (`Promuovi in home`, `Promuovi in carousel`) coerenti con la visibilita' desiderata
