# Manuale Gestione Contenuti Luoghi

## 1. Scopo
Questo manuale descrive il content type `Luogo` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Il content type `Luogo` rappresenta una sede o uno spazio fisico collegato alle attivita' del laboratorio/centro.

Caratteristiche principali:
- slug tecnico post type: `luogo`
- contenuto pubblico
- gerarchico (`hierarchical = true`)
- campi nativi WordPress supportati:
1. `Titolo` (nome del luogo)
2. `Contenuto` (descrizione estesa)
3. `Immagine in evidenza`

## 3. Dove si gestisce nel backoffice
- Menu principale: `Luoghi`.
- Azioni disponibili:
1. `Luoghi > Aggiungi Luogo`
2. `Luoghi > Tutti i Luoghi`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Luogo` sono presenti i seguenti campi.

Nota importante: nel content type `Luogo` nessun campo ACF e' tecnicamente obbligatorio (`required = 0`).

### 4.1 Campi descrittivi
- `Descrizione breve` -> obbligatorio: `No`
- `Come raggiungerci` -> obbligatorio: `No`

### 4.2 Campi geolocalizzazione e contatti
- `Posizione GPS` (mappa OpenStreetMap) -> obbligatorio: `No`
- `Indirizzo` -> obbligatorio: `No`
- `CAP` -> obbligatorio: `No`
- `Riferimento telefonico` -> obbligatorio: `No`
- `Riferimento mail` -> obbligatorio: `No`
- `PEC` -> obbligatorio: `No`
- `Orario per il pubblico` -> obbligatorio: `No`

### 4.3 Campi di promozione
- `Promuovi in home` -> obbligatorio: `No`

### 4.4 Correlazioni nei campi ACF del luogo
Nella scheda `Luogo` non ci sono campi relazione ACF verso altri content type.
Le correlazioni con altri contenuti sono gestite principalmente dalle altre schede (es. `Risorsa tecnica`).

## 5. Inserimento
1. Aprire `Luoghi > Aggiungi Luogo`.
2. Compilare `Titolo`.
3. Scrivere la descrizione nell'editor principale.
4. Compilare i campi ACF piu' rilevanti:
- `Descrizione breve`
- `Indirizzo`, `CAP`
- `Posizione GPS`
- `Riferimento telefonico`, `Riferimento mail`/`PEC`
- `Orario per il pubblico`
5. Assegnare la tassonomia `Tipologia luogo`.
6. Impostare `Immagine in evidenza`.
7. Attivare `Promuovi in home` se necessario.
8. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Luoghi > Tutti i Luoghi`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare testo, tipologia e campi ACF (contatti, indirizzo, mappa, ecc.).
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda luogo.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Luoghi e scegliere `Elimina definitivamente`.

Nota: prima dell'eliminazione definitiva verificare eventuali contenuti collegati (es. risorse tecniche localizzate su quel luogo).

## 8. Tassonomie
Il content type `Luogo` usa la tassonomia custom:
- `Tipologia luogo` (`tipologia-luogo`)

Uso pratico:
- nella pagina pubblica `I Luoghi` la tipologia e' usata come filtro.
- conviene mantenere tipologie poche, chiare e non duplicate.

## 9. Gestione dei campi correlati
Le relazioni con `Luogo` sono principalmente esterne alla sua scheda:
- il content type `Risorsa tecnica` ha il campo relazione `Localizzazione` che punta a `Luogo` (max 1 luogo).

Flusso consigliato:
1. creare/aggiornare il luogo;
2. aprire le `Risorse tecniche` pertinenti;
3. valorizzare il campo `Localizzazione` con il luogo corretto;
4. verificare in frontend la coerenza delle informazioni di localizzazione.

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Luoghi`) sono presenti i campi:
- `Descrizione Sezione Luoghi` (IT)
- `Descrizione Sezione Luoghi ENG` (EN)
- `Visualizza mappa` (si/no)

Queste opzioni governano la pagina panoramica dei luoghi, non la singola scheda luogo.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- descrizione estesa coerente e aggiornata
- `Descrizione breve` valorizzata
- `Tipologia luogo` assegnata
- `Indirizzo` e `CAP` coerenti (se disponibili)
- `Posizione GPS` valorizzata e verificata (se disponibile)
- contatti (`telefono`, `mail`, `PEC`) corretti
- immagine in evidenza presente
- flag `Promuovi in home` coerente con la visibilita' desiderata
