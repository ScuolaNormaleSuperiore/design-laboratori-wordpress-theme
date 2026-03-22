# TODO — Dashboard SitoFederato

## Obiettivo

Sostituire le voci di menu custom aggiunte dal tema nel pannello di amministrazione WordPress con una pagina **dashboard centralizzata** (voce "SitoFederato") che raccoglie tutti i link necessari per creare e gestire contenuti e tassonomie.

**Piano di rilascio:**
1. **Fase 1** — Aggiungere la nuova voce di menu "SitoFederato" con la dashboard, mantenendo le voci esistenti.
2. **Fase 2** — Rimuovere le voci di menu custom esistenti ora confluite nella dashboard.

---

## Contenuti della dashboard

La dashboard deve includere i seguenti link, raggruppati per tipo di contenuto:

### Configurazione
- Configurazione

### Banner
- Elenco banner
- Aggiungi un banner

### Persone
- Elenco persone
- Aggiungi persona
- Tag
- Struttura
- Tipologia persone

### Indirizzi di ricerca
- Elenco indirizzi
- Aggiungi indirizzo
- Tag

### Progetti
- Elenco progetti
- Aggiungi progetto
- Tag

### Pubblicazioni
- Elenco pubblicazioni
- Aggiungi pubblicazione
- Tag
- Tipo pubblicazione

### Brevetti
- Elenco brevetti
- Aggiungi brevetto
- Categorie
- Tag
- Area tematica

### Notizie
- Elenco notizie
- Aggiungi notizia
- Categorie
- Tag

### Eventi
- Elenco eventi
- Aggiungi evento
- Categorie
- Tag

### Luoghi
- Elenco luoghi
- Aggiungi luogo
- Tipologia luogo

### Spin-off
- Elenco spin-off
- Aggiungi spin-off
- Categorie
- Tag
- Settore attività
- Tipologia luogo

### Sponsor
- Elenco sponsor
- Aggiungi sponsor
- Categorie
- Tag

### Risorse tecniche
- Elenco risorse
- Aggiungi risorsa
- Categorie
- Tag
- Tipo risorsa

---

## Raggruppamento in sezioni

Le card della dashboard sono organizzate nelle seguenti sezioni tematiche:

| Sezione               | Tipi di contenuto                                          |
|-----------------------|------------------------------------------------------------|
| Persone e struttura   | Persone, Tipologie persone                                 |
| Attività scientifica  | Indirizzi di ricerca, Progetti, Pubblicazioni              |
| Comunicazione         | Notizie, Eventi, Banner                                    |
| Ricerca               | Brevetti, Risorse tecniche, Spin-off, Sponsor              |
| Impostazioni          | Configurazione, Luoghi                                     |

---

## Varianti di design proposte

Il layout è realizzato con i componenti e le classi di **Bootstrap Italia** (versione già inclusa nel tema).

### Variante A — Card Grid (consigliata per operatori esperti)

Una griglia di card Bootstrap Italia (`card` + `card-header` + `card-body`), una per tipo di contenuto, organizzate nelle sezioni sopra.

**Struttura di ogni card:**
- **Header** (`card-header`) con sfondo colorato per sezione, icona e nome del tipo di contenuto
- **Corpo** (`card-body`) con due sotto-sezioni:
  - *Contenuti*: link a elenco e aggiunta (con icona `it-file`)
  - *Tassonomie*: link alle tassonomie associate (con icona `it-tag`)

**Layout responsive con grid Bootstrap Italia:**
- Desktop (≥ 1200px): `col-xl-3` — 4 card per riga
- Tablet (≥ 768px): `col-md-6` — 2 card per riga
- Mobile: `col-12` — 1 card per riga

**Pro:** compatta, facilmente scannable, estendibile.

---

### Variante B — Card con raggruppamento visivo per sezione (consigliata per team misto)

Stessa struttura di card della Variante A, ma le sezioni sono marcate con un titolo di gruppo (`h2` + `section`) e una riga separatrice. Ogni gruppo di card ha un colore tematico distinto nell'header.

Uso dei componenti Bootstrap Italia:
- `it-header-wrapper` per l'header della pagina
- `section` con classe `py-4` per separare le sezioni
- `badge` per distinguere i link per tipo (contenuto / tassonomia)

**Pro:** orientamento immediato per operatori che gestiscono solo un'area. Aggiunge gerarchia visiva senza sacrificare la densità informativa.

---

### Variante C — Lista compatta con accordion

Ogni sezione è un pannello accordion Bootstrap Italia (`accordion` + `accordion-item`). Cliccando sul titolo della sezione si espandono i link contenuti.

**Pro:** minimo ingombro verticale, buona accessibilità nativa.
**Contro:** meno visuale, richiede interazione per scoprire i contenuti.

---

## Raccomandazione

Adottare la **Variante B**: card con raggruppamento visivo per sezione.
È la soluzione più adatta a un team misto in cui alcuni operatori lavorano solo su determinate aree tematiche.

---

## Layout header della pagina

La pagina deve aprirsi con:
1. **Logo** del tema (Design Laboratori Italia)
2. **Titolo**: "SitoFederato — Pannello di gestione"
3. **Descrizione breve**: una o due righe che descrivono il tema e rimandano alla documentazione
4. **Separatore**, poi le sezioni con le card

Componente Bootstrap Italia da usare: `it-hero-wrapper` o un semplice `callout` con logo e testo descrittivo.

---

## Struttura tecnica proposta

```
inc/
  admin/
    class-dashboard.php      ← nuova classe DLI_Dashboard (registra menu, enqueue stili)
    dashboard-page.php       ← template HTML della pagina dashboard
```

**Note tecniche:**
- La voce di menu è registrata con `add_menu_page()` in `DLI_LabManager::plugin_setup()`
- I link sono generati con `admin_url()` usando gli slug dei CPT e tassonomie già definiti in `config-lab.php`
- Il layout usa i componenti e le classi di **Bootstrap Italia** già inclusi nel tema (nessun CSS aggiuntivo dedicato, salvo piccoli aggiustamenti per il contesto admin)
- Il foglio di stile di Bootstrap Italia viene enqueued solo sulla pagina dashboard (hook `admin_enqueue_scripts` con controllo `$hook`)
- Accessibilità: landmark HTML semantici (`<main>`, `<section>`, `<nav>`), attributi `aria-label`, icone con testo alternativo, contrasto conforme WCAG 2.1 AA

---

## Stato

- [ ] Approvazione design (Variante A / B / C)
- [ ] Implementazione `class-dashboard.php`
- [ ] Implementazione `dashboard-page.php`
- [ ] Test accessibilità
- [ ] Fase 2: rimozione voci di menu precedenti
