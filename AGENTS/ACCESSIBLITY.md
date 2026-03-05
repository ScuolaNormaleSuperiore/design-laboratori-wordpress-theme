# ACCESSIBLITY.md

## Scopo
Specifica per uno script di report focalizzato sull'accessibilità, complementare a:
- controlli statici di qualità del codice (`tests/e2e/quality-report`)
- controlli runtime di stato (`tests/e2e/status-report`)

Questo documento definisce ambito, metriche e strategia di esecuzione per controlli di accessibilità, comportamento responsive e verifiche orientate WCAG.


## Perché Un Report Accessibilità Dedicato
I controlli di accessibilità e responsive sono per lo più guidati da runtime/DOM e differiscono dai controlli statici di qualità.

Architettura raccomandata:
- Mantenere `quality-report` focalizzato su controlli statici/codice.
- Aggiungere uno script separato `accessibility-report` per i controlli runtime di accessibilità.
- Aggregare opzionalmente i verdict finali in un riepilogo di livello superiore.

Motivazioni:
- ownership più chiara e meno rumore in ciascun report;
- migliore analisi dei trend (regressioni di accessibilità separate da lint/stile);
- gate CI più semplici per dominio.


## Ambito Proposto Dello Script
Directory target:
- `tests/e2e/accessibility-report/`

Output proposti:
- report JSON (`*.json`)
- report HTML (`*.html`)
- report di confronto opzionale (stile `compare.js`, simile agli altri report)

URL target:
- homepage
- template di archivio principali
- template singoli principali
- landing page selezionate dalla navigazione


## Controlli Da Includere
Set minimo ad alto valore:

1. Violazioni Axe (o Pa11y)
- raggruppate per severità: `critical`, `serious`, `moderate`, `minor`
- includere id regola e selettori interessati

2. Accessibilità Lighthouse
- punteggio accessibilità per URL
- elenco audit principali falliti

3. Responsive smoke checks
- set viewport: mobile/tablet/desktop
- rilevazione overflow orizzontale
- assenza meta viewport
- indicatori principali di rottura layout

4. Keyboard navigation smoke checks
- presenza sequenza di elementi focusabili
- rilevazione focus visibile sui principali controlli interattivi
- assenza di evidenti keyboard trap

5. Baseline semantica/ARIA
- label form mancanti
- bottoni/link senza nome accessibile
- problemi su `alt` immagini (mancante/vuoto quando non decorative)
- segnali di uso errato relazioni ARIA (quando rilevabili)

6. Controlli contrasto colore
- conteggio fallimenti WCAG 2.1 AA (dove l’automazione riesce a rilevarli)


## Mappatura WCAG (Pragmatica)
L’automazione dovrebbe etichettare i finding con mappatura WCAG approssimata quando possibile:
- 1.1.1 Non-text Content
- 1.3.1 Info and Relationships
- 1.4.3 Contrast (Minimum)
- 2.1.1 Keyboard
- 2.4.7 Focus Visible
- 3.3.2 Labels or Instructions
- 4.1.2 Name, Role, Value

Nota:
- i controlli automatici non possono certificare in modo completo la conformità WCAG;
- resta necessaria una validazione manuale per pagine critiche ai fini del rilascio.


## Modello Stati (Suggerito)
Stato per singolo check:
- `PASS`: nessun finding bloccante
- `WARN`: presenti finding non bloccanti
- `FAIL`: finding bloccanti oltre soglia configurata
- `SKIP`: check non eseguibile nell’ambiente

Suggerimento verdict globale:
- `FAIL` se viene superata almeno una soglia di accessibilità `critical`
- `WARN` se non ci sono fail critical ma sono presenti problemi
- `PASS` altrimenti


## Soglie Suggerite (Iniziali)
Usare soglie iniziali conservative:
- `FAIL` se esiste almeno una violazione Axe `critical` sulle pagine chiave
- `WARN` se esistono violazioni `serious`
- `WARN` per punteggio Lighthouse accessibilità sotto 90
- `FAIL` per punteggio sotto 80 sulle pagine chiave

Le soglie vanno tarate dopo la raccolta della baseline.


## Formato Dichiarazione Sorgente
Per allinearsi ai report esistenti, ogni check dovrebbe esporre la sorgente come:
- `Sorgente: URL runtime`
- `Sorgente: tutto il progetto` (if globally scoped)
- `Sorgente: altro` (fallback)


## Includere Nel Quality O Separare?
Decisione:
- mantenere l’accessibilità in un report separato (`accessibility-report`) invece di fonderla in `quality-report`.

Quando aggregare:
- fornire un riepilogo combinato di alto livello per valutare la release readiness:
  - status-report verdict
  - quality-report verdict
  - accessibility-report verdict


## Non Obiettivi
- non dichiarare piena certificazione WCAG basandosi solo sull’automazione
- non mescolare rumore da lint statico con finding runtime di accessibilità in un unico report grezzo


## Appunti Sessione (2026-03-05)
- Lo script accessibilità deve funzionare a runtime, navigando URL reali del sito (non solo analisi filesystem).
- Il modello operativo deve essere simile a `tests/e2e/status-report/scan.js`:
  - esecuzione CLI Node
  - output JSON + HTML
  - stati `PASS/WARN/FAIL/SKIP`
  - possibilità di confronto tra report (compare).
- Le librerie già usate da `status-report` e `quality-report` non sono sufficienti per WCAG/accessibilità.
- Sono necessarie librerie dedicate browser/a11y, ad esempio:
  - `playwright` (o `puppeteer`) per rendering e navigazione headless
  - `@axe-core/playwright` (o equivalente integrazione Axe) per finding accessibilità
  - `lighthouse` opzionale per punteggi/audit accessibilità.
