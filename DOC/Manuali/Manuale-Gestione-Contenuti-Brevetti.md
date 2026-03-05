# Manuale Gestione Contenuti Brevetti

## 1. Scopo
Questo manuale descrive il content type `Brevetto` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione, tassonomie e contenuti correlati.

## 2. Che cos'e' il content type
Il content type `Brevetto` rappresenta un brevetto del laboratorio/centro, con metadati tecnici e amministrativi (codice, stato, deposito, inventori, area tematica, famiglia brevettuale).

Caratteristiche principali:
- slug tecnico post type: `brevetto`
- contenuto pubblico
- non gerarchico (`hierarchical = false`)
- campi nativi WordPress supportati:
1. `Titolo`
2. `Contenuto` (usato come abstract esteso)
3. `Immagine in evidenza`

## 3. Dove si gestisce nel backoffice
- Menu principale: `Brevetti`.
- Azioni disponibili:
1. `Brevetti > Aggiungi un Brevetto`
2. `Brevetti > Tutti i Brevetti`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Brevetto` sono presenti i seguenti campi.

### 4.1 Elenco campi ACF (tipo, obbligatorio, correlazioni)
- `Sommario elenco**`
  tipo: `textarea`
- `Altre informazioni**`
  tipo: `wysiwyg`
- `Video**`
  tipo: `url`
- `Codice brevetto`
  tipo: `text`
  obbligatorio: `Si`
- `Stato legale**`
  tipo: `text`
- `Stato Iris`
  tipo: `select`
- `Data deposito`
  tipo: `date_picker`
- `Anno deposito`
  tipo: `number`
- `Numero deposito`
  tipo: `text`
- `Inventori referenti`
  tipo: `text`
- `Inventori`
  tipo: `text`
- `Altri inventori**`
  tipo: `text`
- `Titolari`
  tipo: `text`
- `Prioritario`
  tipo: `true_false`
- `Id famiglia`
  tipo: `text`
- `Famiglia`
  tipo: `text`
- `Promuovi in carousel`
  tipo: `true_false`
- `Promuovi in home`
  tipo: `true_false`

## 5. Inserimento
1. Aprire `Brevetti > Aggiungi un Brevetto`.
2. Compilare `Titolo`.
3. Inserire l'abstract nell'editor principale.
4. Compilare il campo ACF obbligatorio `Codice brevetto`.
5. Compilare i campi consigliati:
- `Sommario elenco**`
- `Stato legale**` o `Stato Iris`
- `Data deposito` / `Anno deposito` / `Numero deposito`
- `Titolari`, `Inventori referenti`, `Inventori`, `Altri inventori**`
6. Assegnare almeno una tassonomia in `Area tematica`.
7. Assegnare eventuali `Categorie` e `Tag`.
8. Impostare `Immagine in evidenza` (se disponibile).
9. Attivare, se utile, `Promuovi in home` e/o `Promuovi in carousel`.
10. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Brevetti > Tutti i Brevetti`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare campi testuali, dati di deposito, stato, inventori/titolari e tassonomie.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda brevetto.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Brevetti e scegliere `Elimina definitivamente`.

## 8. Tassonomie
Il content type `Brevetto` usa:
- tassonomia custom `Area tematica` (`area-tematica`)
- tassonomia WordPress `Categorie` (`category`)
- tassonomia WordPress `Tag` (`post_tag`)

Uso pratico:
- nella pagina pubblica `Brevetti` i filtri principali sono anno di deposito, area tematica e ricerca libera.

## 9. Gestione dei campi correlati
Nella scheda `Brevetto` non sono presenti campi ACF di relazione verso altri content type.

## 10. Configurazioni backoffice
Nelle opzioni tema (`dli_options > Brevetti`) sono presenti i campi:
- `Descrizione Sezione` (IT)
- `Descrizione Sezione ENG` (EN)

Configurazioni aggiuntive:
- in `dli_options > IRIS` e' disponibile la sezione di integrazione/import `IRIS AP Brevetti` (abilitazione, endpoint, credenziali, schedulazione, modalita' import e gestione elementi esistenti).
- l'import puo' essere eseguito anche manualmente tramite endpoint REST protetto da autenticazione: `/wp-json/custom/v1/iris-ap-brevetti-import`.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- campo ACF `Codice brevetto` valorizzato
- abstract/contenuto coerente
- `Sommario elenco**` valorizzato
- dati di deposito coerenti (`Data`, `Anno`, `Numero`)
- `Area tematica` assegnata
- inventori/titolari valorizzati (se disponibili)
- immagine in evidenza presente (se disponibile)
- flag `Promuovi in home` / `Promuovi in carousel` coerenti con la visibilita' desiderata
