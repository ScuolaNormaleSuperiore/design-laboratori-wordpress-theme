# TODO - Dashboard SitoFederato (Proposte v2 aggiornate)

## Obiettivo

Introdurre una nuova voce di menu admin WordPress chiamata **SitoFederato** che apra una dashboard unica con tutti i collegamenti operativi.

La dashboard deve distinguere chiaramente due famiglie di link:
- **Gestione contenuti**: elenco e creazione elementi
- **Classificazioni**: tassonomie e strutture di classificazione

Approccio di rilascio:
1. **Fase 1**: aggiungere la dashboard senza rimuovere le voci attuali.
2. **Fase 2**: rimuovere dal menu laterale le voci custom duplicate.

---

## Vincolo UI

Il layout va realizzato usando **Bootstrap Italia** (componenti e griglia), evitando layout custom "da zero".

---

## Contenuti richiesti (link dashboard)

### Configurazione
- Gestione contenuti: Configurazione
- Classificazioni: Nessuna

### Banner
- Gestione contenuti: Elenco banner, Aggiungi banner
- Classificazioni: Nessuna

### Persone
- Gestione contenuti: Elenco persone, Aggiungi persona, Tipologie persone
- Classificazioni: Tag, Struttura

### Indirizzi di ricerca
- Gestione contenuti: Elenco indirizzi, Aggiungi indirizzo
- Classificazioni: Tag

### Progetti
- Gestione contenuti: Elenco progetti, Aggiungi progetto
- Classificazioni: Tag

### Pubblicazioni
- Gestione contenuti: Elenco pubblicazioni, Aggiungi pubblicazione
- Classificazioni: Tag, Tipo pubblicazione

### Brevetti
- Gestione contenuti: Elenco brevetti, Aggiungi brevetto
- Classificazioni: Categorie, Tag, Area tematica

### Notizie
- Gestione contenuti: Elenco notizie, Aggiungi notizia
- Classificazioni: Categorie, Tag

### Eventi
- Gestione contenuti: Elenco eventi, Aggiungi evento
- Classificazioni: Categorie, Tag

### Luoghi
- Gestione contenuti: Elenco luoghi, Aggiungi luogo
- Classificazioni: Tipologia luogo

### Spin-off
- Gestione contenuti: Elenco spin-off, Aggiungi spin-off
- Classificazioni: Categorie, Tag, Settore attivita, Tipologia luogo

### Sponsor
- Gestione contenuti: Elenco sponsor, Aggiungi sponsor
- Classificazioni: Categorie, Tag

### Risorse tecniche
- Gestione contenuti: Elenco risorse, Aggiungi risorsa
- Classificazioni: Categorie, Tag, Tipo risorsa

---

## Regola di distinzione link (obbligatoria)

Per evitare ambiguita tra link dei vari content-type:

1. Ogni card deve avere come titolo il **nome del content-type**.
2. Ogni card deve avere sempre due blocchi fissi:
- **Gestione contenuti**
- **Classificazioni**
3. I link devono seguire una naming convention uniforme:
- `Elenco <tipo>`
- `Aggiungi <tipo singolare>`
- `Gestisci <tassonomia>`
4. I due blocchi devono essere distinguibili con:
- intestazione testuale esplicita
- icona diversa
- separazione visiva (colonna o sezione)

---

## Proposte di organizzazione

### Proposta A - Card grid
Una card per ogni content-type, in griglia.

Per ogni card:
- colonna sinistra: Gestione contenuti
- colonna destra: Classificazioni

Vantaggi:
- visione completa immediata
- meno click
- ottima per utenti esperti

### Proposta B - Sezioni + card
Le card sono raggruppate in sezioni:
- Persone e struttura
- Attivita scientifica
- Comunicazione
- Ricerca
- Impostazioni

Vantaggi:
- orientamento migliore per team con ruoli diversi
- riduzione del carico cognitivo

### Proposta C - Accordion per sezioni
Ogni sezione e collassabile. Dentro, card o lista dei content-type della sezione.

Vantaggi:
- pagina piu compatta
- buona scalabilita quando aumentano i link

Svantaggi:
- meno immediatezza rispetto alla griglia aperta

---

## Raccomandazione

Adottare **Proposta B** con possibilita di usare accordion a livello sezione su viewport piccoli.

---

## Layout pagina (Bootstrap Italia)

Ordine consigliato:
1. Header con logo, titolo e descrizione breve
2. Eventuali azioni rapide (es. Configurazione)
3. Sezioni contenuto
4. Card dei content-type
5. Link organizzati nei due blocchi: Gestione contenuti / Classificazioni

Componenti Bootstrap Italia suggeriti:
- `container`, `row`, `col-*` per la griglia responsive
- `card` per i content-type
- `accordion` per il raggruppamento sezioni
- `list-group` per i link
- `badge` e `it-icon` per aumentare scansionabilita e chiarezza

---

## Requisiti UX, responsive e accessibilita

- Layout responsive con griglia Bootstrap Italia
- Navigazione completa da tastiera
- Focus states visibili
- Contrasto colori WCAG 2.1 AA
- Distinzione non basata solo sul colore
- Struttura semantica corretta (`h1`, `h2`, `h3`, `main`, `section`, `nav`)
- Testi link espliciti e uniformi

---

## Strategia tecnica (fase iniziale)

- Nuova voce admin con `add_menu_page()` (label: **SitoFederato**)
- Pagina renderizzata da template dedicato
- Configurazione link data-driven tramite array centralizzato
- Stili limitati e coerenti con Bootstrap Italia
- CSS caricato solo sulla pagina dashboard (`admin_enqueue_scripts` + check hook)

---

## Checklist operativa

- [ ] Approvazione layout (A/B/C)
- [ ] Implementazione menu `SitoFederato`
- [ ] Implementazione dashboard con Bootstrap Italia
- [ ] Inserimento link completi (inclusi Indirizzi di ricerca)
- [ ] Verifica distinzione chiara Gestione contenuti / Classificazioni
- [ ] Test responsive (desktop/tablet/mobile)
- [ ] Test accessibilita base (keyboard + contrasto + struttura)
- [ ] Fase 2: rimozione voci custom duplicate dal menu laterale
