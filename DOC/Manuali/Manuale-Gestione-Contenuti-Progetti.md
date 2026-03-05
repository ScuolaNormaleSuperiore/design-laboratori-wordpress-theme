# Manuale Gestione Contenuti Progetti

## 1. Scopo
Questo manuale descrive il content type `Progetto` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Dove si gestisce nel backoffice
- Menu principale: `Progetti`.
- Azioni disponibili:
1. `Progetti > Aggiungi un Progetto`
2. `Progetti > Tutti i Progetti`
3. Apertura della singola scheda per modifica rapida o completa.

## 3. Come e' fatto il content type Progetto
- Slug tecnico post type: `progetto`.
- Tipo: pubblico, visibile nel menu admin.
- Supporta i campi nativi WordPress:
1. `Titolo`
2. `Contenuto` (editor principale, usato come descrizione estesa)
3. `Immagine in evidenza`
- Tassonomia associata: `Tag` standard WordPress (`post_tag`).
- E' gerarchico (`hierarchical = true`): tecnicamente puo' avere padre/figlio, ma normalmente si usa come contenuto singolo.

## 4. Campi principali della scheda Progetto (ACF)
Nel box `Campi Progetto` sono presenti i seguenti campi.

### 4.1 Campi obbligatori
- `Descrizione breve` (max 255 caratteri)
- `Data fine`

### 4.2 Campi informativi e di stato
- `Data inizio`
- `Priorita` (numero, default `0`; usata per ordinamento elenco)
- `Archiviato` (se attivo, il progetto sparisce dalla pagina "I progetti" e va in "Archivio progetti")
- `Promuovi in home`
- `Promuovi in carousel`
- `Url` (sito web esterno del progetto)

### 4.3 Campi relazionali
- `Responsabile del progetto` -> relazione con contenuti `Persona`
- `Persone` -> relazione con contenuti `Persona`
- `Elenco indirizzi di ricerca correlati` -> relazione con `Indirizzo di ricerca`
- `Pubblicazioni` -> relazione con `Pubblicazione`
- `Risorse tecniche` -> relazione con `Risorsa tecnica`

### 4.4 Allegati
- `Allegato 1`
- `Allegato 2`
- `Allegato 3`

## 5. Inserimento di un nuovo progetto
1. Aprire `Progetti > Aggiungi un Progetto`.
2. Compilare `Titolo`.
3. Scrivere la descrizione estesa nell'editor principale.
4. Compilare i campi ACF minimi:
- `Descrizione breve`
- `Data fine`
5. Compilare i campi consigliati:
- `Data inizio`
- `Priorita`
- `Responsabile del progetto`
- `Persone`
- eventuali relazioni (`Pubblicazioni`, `Risorse tecniche`, `Indirizzi di ricerca`)
- eventuale `Url`
6. Assegnare i `Tag` (sono usati come filtro nella pagina pubblica "I progetti").
7. Impostare `Immagine in evidenza`.
8. Cliccare `Pubblica`.

## 6. Modifica di un progetto esistente
1. Aprire `Progetti > Tutti i Progetti`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare campi testo, relazioni, tag e allegati.
4. Cliccare `Aggiorna`.

Note operative:
- Se vuoi togliere un progetto dalla lista principale senza eliminarlo, usa `Archiviato`.
- Se vuoi metterlo in evidenza in homepage/carousel, usa i flag `Promuovi in home` e `Promuovi in carousel`.

## 7. Cancellazione di un progetto
1. Aprire la scheda progetto.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Progetti e scegliere `Elimina definitivamente`.

Nota: la cancellazione rompe eventuali relazioni verso quel progetto da altri contenuti (es. Notizie/Eventi). Prima della cancellazione definitiva verificare i contenuti collegati.

## 8. Tassonomie del content type Progetto
La tassonomia usata e' solo `Tag` (`post_tag`).

Uso pratico:
- I tag compaiono nella card del progetto e nel dettaglio.
- La pagina `I progetti` usa i tag come filtro (parametro `level`).
- Conviene mantenere un set di tag coerente e non duplicato (es. evitare varianti simili dello stesso concetto).

## 9. Campi correlati in altri content type
Per costruire relazioni bidirezionali (navigazione migliore), anche `Notizie` e `Eventi` hanno un campo relazione chiamato `Progetto`.

Flusso consigliato:
1. Nella scheda Notizia/Evento collega il campo `Progetto`.
2. Salva o aggiorna Notizia/Evento.
3. Verifica in frontend che la sezione "Eventi e notizie" del progetto sia valorizzata.

## 10. Configurazioni backoffice della sezione "I Progetti"
Nelle opzioni tema (`dli_options > Progetti`) sono presenti campi che governano la pagina lista, non la singola scheda:
- descrizione sezione (IT/EN)
- modalita' paginazione (`Mostra tutti` / `Attiva paginazione`)
- numero elementi per pagina
- etichette filtro tag (IT/EN)
- visibilita' etichetta "Dettagli del progetto" nella pagina di dettaglio

## 11. Checklist rapida prima della pubblicazione
- `Titolo` valorizzato
- `Descrizione breve` valorizzata
- `Data fine` valorizzata
- `Tag` coerenti assegnati
- immagine in evidenza presente
- relazioni principali compilate (almeno responsabile e persone, se disponibili)
- stato `Archiviato` coerente con visibilita' desiderata
