# Manuale Operatore - Gestione contenuti "Persone"

Versione: 1.0  
Data: 2026-02-17  
Ambito: gestione editoriale del content-type `Persona`

## 1. Scopo
Questo manuale spiega come gestire correttamente le schede `Persona` nel backoffice WordPress: significato, creazione, modifica, pubblicazione, sospensione e cancellazione.

## 2. Che cos'e' il content-type `Persona`
`Persona` rappresenta un profilo individuale (membro del laboratorio/centro) mostrato:
- nella pagina elenco `Persone`
- nella pagina dettaglio della singola persona (se abilitata)
- in alcune relazioni con altri contenuti (es. progetti, pubblicazioni)

Riferimenti tecnici:
- `inc/classes/class-peoplemanager.php`
- `page-templates/persone.php`
- `single-persona.php`

## 3. Dove si gestisce nel backoffice
Menu WordPress:
- `Persone` (elenco + nuova scheda)
- `Struttura` (tassonomia usata per filtro/organizzazione)
- `Tipologia Persona` (categorie logiche dell'elenco persone)

Nota: `Tipologia Persona` e `Struttura` devono essere coerenti prima di pubblicare molte schede.

## 4. Campi principali della scheda Persona
Campi ACF definiti in `class-peoplemanager.php`:
- `Titolo` (opzionale): prefisso tipo Dott., Prof., ecc.
- `Nome` (obbligatorio)
- `Cognome` (obbligatorio)
- `Email` (opzionale)
- `Telefono` (opzionale)
- `Sito web` (opzionale)
- `Categoria di appartenenza` (obbligatoria): relazione verso `Tipologia Persona`
- `Escludi da elenco` (boolean)
- `Disattiva pagina dettaglio` (boolean)
- `Abilita link diretto alla pagina della persona` (boolean, in pratica link esterno se presente `Sito web`)
- `Allegato CV` (PDF)
- `Allegato 1/2/3` (opzionali)
- `Immagine in evidenza` (avatar/persona)
- `Biografia` (contenuto principale della pagina)
- Tassonomia `Struttura`
- Tag WordPress (usati come filtro "livello" in pagina Persone)

## 5. Inserimento di una nuova persona
1. Vai su `Persone -> Aggiungi nuova`.
2. Nel titolo inserisci il nome completo (come suggerito anche dall'editor).
3. Compila almeno `Nome`, `Cognome`, `Categoria di appartenenza`.
4. Inserisci `Biografia` nel contenuto principale.
5. Imposta `Immagine in evidenza`.
6. Assegna almeno una `Struttura`.
7. (Opzionale) aggiungi Tag per filtro livello.
8. (Opzionale) compila contatti e allegati.
9. Salva bozza e verifica anteprima.
10. Pubblica.

## 6. Significato operativo dei campi booleani
- `Escludi da elenco = ON`:
  la persona non appare nella pagina elenco `Persone`.
- `Disattiva pagina dettaglio = ON`:
  il nome in elenco non e' cliccabile (nessuna pagina dettaglio raggiungibile da elenco).
- `Abilita link diretto alla pagina della persona = ON`:
  se la pagina dettaglio e' attiva, il nome punta al campo `Sito web` invece che alla scheda interna.

Combinazione consigliata (uso standard):
- `Escludi da elenco = OFF`
- `Disattiva pagina dettaglio = OFF`
- `Abilita link diretto... = OFF`

## 7. Modifica di una persona esistente
1. Vai su `Persone -> Tutte le persone`.
2. Apri la scheda da modificare.
3. Aggiorna i campi necessari.
4. Clicca `Aggiorna`.
5. Verifica in frontend:
- pagina elenco Persone
- pagina dettaglio (se non disattivata)

Quando modifichi campi strutturali (`Categoria di appartenenza`, `Struttura`, flag booleani), controlla sempre anche l'effetto sui filtri.

## 8. Stati di pubblicazione
Stati WordPress consigliati:
- `Bozza`: scheda in lavorazione.
- `Pubblicato`: visibile lato pubblico.
- `Privato`: visibile solo a utenti autorizzati.

Buona pratica:
- usa sempre `Bozza` finche' non hai completato nome, categoria, struttura e biografia minima.

## 9. Cancellazione e ripristino
Per rimuovere una persona:
1. Apri la scheda.
2. Clicca `Sposta nel cestino`.

Comportamento:
- nel cestino non e' piu' visibile sul sito.
- puo' essere ripristinata da `Cestino`.
- eliminazione definitiva solo dopo svuotamento cestino.

Alternativa meno distruttiva:
- lascia la scheda pubblicata ma imposta `Escludi da elenco = ON` (utile per contenuti storici/non attivi).

## 10. Gestione correlati (Tipologia Persona e Struttura)
### 10.1 Tipologia Persona
Serve per raggruppare le persone in blocchi/categorie nell'elenco.

Suggerimenti:
- definire un set stabile di tipologie
- usare il campo `Priorita` (in Tipologia Persona) per ordinare le sezioni

### 10.2 Struttura
E' la tassonomia usata per i filtri nella pagina Persone.

Suggerimenti:
- evitare termini duplicati/simili
- usare naming coerente tra IT/EN

## 11. Checklist rapida prima della pubblicazione
- Nome e Cognome compilati
- Categoria di appartenenza valorizzata
- Struttura assegnata
- Biografia presente
- Immagine in evidenza impostata
- Flag booleani verificati
- Link esterni testati (se presenti)
- Allegati corretti (CV PDF se previsto)

## 12. Errori comuni da evitare
- pubblicare senza `Categoria di appartenenza`
- usare `Disattiva pagina dettaglio` senza motivo editoriale
- attivare `Abilita link diretto...` senza compilare `Sito web`
- dimenticare `Struttura`, causando filtri incompleti
- caricare allegati non coerenti (es. CV non PDF)

## 13. Procedura consigliata per manutenzione periodica
Frequenza mensile:
1. verifica persone senza immagine
2. verifica persone senza categoria
3. verifica link esterni non funzionanti
4. archivia profili non attivi (`Escludi da elenco` o cestino)
5. allinea tag/livelli per filtri coerenti

## 14. Riferimenti interni
- Definizione post type/campi: `inc/classes/class-peoplemanager.php`
- Definizione tipologie: `inc/classes/class-peopletypemanager.php`
- Pagina elenco Persone: `page-templates/persone.php`
- Pagina dettaglio Persona: `single-persona.php`
