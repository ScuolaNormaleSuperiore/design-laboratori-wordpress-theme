# Manuale Gestione Contenuti Banner

## 1. Scopo
Questo manuale descrive il content type `Banner` del tema, come e' strutturato e come gestirlo nel backoffice WordPress: creazione, modifica, cancellazione e configurazioni di visualizzazione in homepage.

## 2. Che cos'e' il content type
Il content type `Banner` rappresenta un elemento promozionale/istituzionale mostrato nella home page (sezione banner), con immagine, testo e pulsante opzionale.

Caratteristiche principali:
- slug tecnico post type: `banner`
- contenuto pubblico
- non gerarchico (`hierarchical = false`)
- campi nativi WordPress supportati:
1. `Titolo`
2. `Contenuto` (testo descrittivo del banner)
3. `Immagine in evidenza`

## 3. Dove si gestisce nel backoffice
- Menu principale: `Banner`.
- Azioni disponibili:
1. `Banner > Aggiungi un Banner`
2. `Banner > Tutti i Banner`
3. Apertura della singola scheda per modifica rapida o completa.

## 4. Campi principali della scheda (ACF)
Nel box `Campi Banner` sono presenti i seguenti campi.

### 4.1 Elenco campi ACF (tipo, obbligatorio, correlazioni, funzione)
- `Sezione`
  tipo: `text`
  obbligatorio: `Si`
  funzione: etichetta breve mostrata sopra al titolo del banner.
- `Priorita`
  tipo: `number`
  obbligatorio: `Si`
  funzione: ordine di visualizzazione nella sezione banner della home (valori piu' bassi prima).
- `Label pulsante`
  tipo: `text`
  funzione: testo del pulsante call-to-action.
- `Link pulsante`
  tipo: `url`
  funzione: URL di destinazione del pulsante.
- `Apri in nuova finestra`
  tipo: `true_false`
  funzione: se attivo apre il link del pulsante in una nuova scheda/finestra.

## 5. Inserimento
1. Aprire `Banner > Aggiungi un Banner`.
2. Compilare `Titolo`.
3. Inserire il testo nell'editor principale.
4. Compilare i campi ACF obbligatori:
- `Sezione`
- `Priorita`
5. Compilare i campi pulsante (se necessario):
- `Label pulsante`
- `Link pulsante`
- `Apri in nuova finestra`
6. Impostare `Immagine in evidenza`.
7. Cliccare `Pubblica`.

## 6. Modifica
1. Aprire `Banner > Tutti i Banner`.
2. Cercare il contenuto e cliccare `Modifica`.
3. Aggiornare titolo, testo, priorita', etichetta sezione, pulsante e immagine.
4. Cliccare `Aggiorna`.

## 7. Cancellazione
1. Aprire la scheda banner.
2. Cliccare `Sposta nel cestino`.
3. Per rimozione definitiva: aprire `Cestino` nel menu Banner e scegliere `Elimina definitivamente`.

## 8. Tassonomie
Il content type `Banner` non usa tassonomie dedicate.

## 9. Gestione dei campi correlati
Nella scheda `Banner` non sono presenti campi ACF di relazione verso altri content type.

## 10. Configurazioni backoffice
La visualizzazione dei banner in homepage dipende dalla configurazione delle sezioni home:
- in `dli_options > Homepage sections` va abilitata la sezione `Banner` (`banners_section`).
- i banner pubblicati vengono mostrati in home ordinati per campo `Priorita` (ascendente), con limite ai primi 3 elementi.

## 11. Check-list rapida prima della pubblicazione
- `Titolo` valorizzato
- campo ACF obbligatorio `Sezione` valorizzato
- campo ACF obbligatorio `Priorita` valorizzato
- testo del banner chiaro e coerente
- immagine in evidenza presente e corretta
- se presente il pulsante: `Label pulsante` e `Link pulsante` coerenti
- impostazione `Apri in nuova finestra` coerente con il tipo di link
- sezione `Banner` abilitata in homepage (se si vuole visualizzarlo subito)
