# Manuale Gestione Contenuti Risorse Tecniche

## 1. Scopo
Questo manuale descrive il content type `Risorsa Tecnica` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Il content type `Risorsa Tecnica` rappresenta una risorsa/attrezzatura tecnica del laboratorio, con dati identificativi, stato, localizzazione, documentazione e referenti.

Caratteristiche principali:
- slug tecnico post type: `risorsa-tecnica`
- contenuto pubblico
- non gerarchico (`hierarchical = false`)
- campi nativi WordPress supportati:
1. `Titolo`
2. `Contenuto` (descrizione estesa)
3. `Immagine in evidenza`

## 3. Dove si gestisce nel backoffice
- Menu principale: `Risorse Tecniche`.
- Azioni disponibili:
1. `Risorse Tecniche > Aggiungi una risorsa`
2. `Risorse Tecniche > Tutte le Risorse Tecniche`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Risorsa Tecnica` sono presenti i seguenti campi.

### 4.1 Elenco campi ACF (tipo, obbligatorio, correlazioni, funzione)
- `Foto`
  tipo: `image`
  funzione: immagine principale alternativa della risorsa.
- `Descrizione breve`
  tipo: `textarea`
  obbligatorio: `Si`
  funzione: sintesi usata in card/elenco.
- `Codice interno`
  tipo: `text`
  funzione: identificativo interno inventariale.
- `Costo`
  tipo: `text`
  funzione: valore economico della risorsa.
- `Posizione`
  tipo: `text`
  funzione: posizione fisica interna (es. stanza/lab/reparto).
- `Localizzazione`
  tipo: `relationship`
  correlazioni: `Luogo` (post type `luogo`, max 1)
  funzione: collega la risorsa a una sede/luogo del sito.
- `Marca`
  tipo: `text`
  funzione: produttore della risorsa.
- `Modello`
  tipo: `text`
  funzione: modello/versione tecnica.
- `Anno acquisizione`
  tipo: `number`
  funzione: anno di acquisizione della risorsa.
- `Stato`
  tipo: `text`
  funzione: stato operativo/amministrativo della risorsa.
- `Dimensioni e peso`
  tipo: `text`
  funzione: caratteristiche fisiche principali.
- `Conformita`
  tipo: `text`
  funzione: riferimenti a conformita'/certificazioni.
- `Responsabili`
  tipo: `relationship`
  correlazioni: `Persona` (post type `persona`)
  funzione: persone referenti della risorsa.
- `Scheda tecnica`
  tipo: `file`
  funzione: documento tecnico ufficiale.
- `Manuale uso`
  tipo: `file`
  funzione: manuale d'uso operativo.
- `Allegato 1`
  tipo: `file`
  funzione: documento aggiuntivo.
- `Allegato 2`
  tipo: `file`
  funzione: documento aggiuntivo.
- `Promuovi in home`
  tipo: `true_false`
  funzione: evidenza della risorsa in homepage.
- `Promuovi in carousel`
  tipo: `true_false`
  funzione: evidenza della risorsa nel carousel.

## 5. Inserimento
1. Aprire `Risorse Tecniche > Aggiungi una risorsa`.
2. Compilare `Titolo`.
3. Scrivere la descrizione nell'editor principale.
4. Compilare il campo ACF obbligatorio `Descrizione breve`.
5. Compilare i campi consigliati:
- `Codice interno`, `Stato`, `Anno acquisizione`
- `Localizzazione`, `Posizione`
- `Marca`, `Modello`, `Dimensioni e peso`, `Conformita`
- `Responsabili`
- allegati (`Scheda tecnica`, `Manuale uso`, `Allegato 1`, `Allegato 2`)
6. Assegnare almeno una tassonomia in `Tipo risorsa`.
7. Assegnare eventuali `Categorie` e `Tag`.
8. Impostare `Foto` e/o `Immagine in evidenza`.
9. Attivare, se utile, `Promuovi in home` e/o `Promuovi in carousel`.
10. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Risorse Tecniche > Tutte le Risorse Tecniche`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare metadati tecnici, relazioni, allegati e tassonomie.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda risorsa tecnica.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Risorse Tecniche e scegliere `Elimina definitivamente`.

Nota: prima dell'eliminazione definitiva verificare eventuali progetti collegati alla risorsa.

## 8. Tassonomie
Il content type `Risorsa Tecnica` usa:
- tassonomia custom `Tipo risorsa` (`tipo-risorsa-tecnica`)
- tassonomia WordPress `Categorie` (`category`)
- tassonomia WordPress `Tag` (`post_tag`)

Uso pratico:
- nella pagina pubblica `Risorse Tecniche` i filtri principali sono anno di acquisizione, tipo di risorsa e ricerca libera.

## 9. Gestione dei campi correlati
Correlazioni principali:
- nella scheda risorsa: `Localizzazione` -> `Luogo`, `Responsabili` -> `Persona`.
- da altri contenuti verso risorsa: in `Progetto` esiste il campo relazione `Risorse tecniche`.

Flusso consigliato:
1. valorizzare in risorsa `Localizzazione` e `Responsabili`;
2. nelle schede `Progetto` pertinenti valorizzare `Risorse tecniche`;
3. verificare nel frontend della risorsa la sezione `Progetti` collegati.

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Risorse tecniche`) sono presenti i campi:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Queste opzioni governano la pagina panoramica delle risorse tecniche, non la singola scheda.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- campo ACF obbligatorio `Descrizione breve` valorizzato
- `Tipo risorsa` assegnato
- metadati tecnici principali compilati (`Codice interno`, `Stato`, `Anno acquisizione`)
- relazioni principali valorizzate (se disponibili): `Localizzazione`, `Responsabili`
- allegati principali presenti (se disponibili): `Scheda tecnica`, `Manuale uso`
- immagine/foto coerente e visibile
- flag `Promuovi in home` / `Promuovi in carousel` coerenti con la visibilita' desiderata
