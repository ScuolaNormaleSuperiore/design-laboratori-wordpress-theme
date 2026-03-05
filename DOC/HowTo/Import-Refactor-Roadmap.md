# Refactoring Roadmap: Import Procedure (INDICO + PATENT/IRIS)

## Goal

Ridurre il codice duplicato tra gli importer esistenti e rendere più veloce
l'aggiunta di nuove sorgenti, senza rompere le funzionalità esistenti.

## Vincoli

- Tema WordPress: niente over-engineering.
- Endpoint REST, cron e comportamento dry-run/commit devono restare invariati.
- Ogni fase deve essere deployabile in modo autonomo e reversibile.
- Nessuna dipendenza esterna aggiuntiva.

## Importer attuali

- `DLI_IndicoImporter` — importa eventi da Indico
- `DLI_IrisPatentImporter` — importa brevetti da IRIS

Base class: `DLI_BaseImporter`

---

## Pattern adottato: Template Method + DTO

### Perché Template Method

La base class definisce il flusso dell'import (pipeline).
Ogni importer figlio implementa solo le parti specifiche della propria sorgente.
Il flusso comune (loop, dry-run, contatori, report, insert/update WP) vive nella base class.

### Perché il DTO `DLI_ImportItem`

Senza DTO, il codice di persistenza WordPress deve ancora conoscere il formato
grezzo di Indico o IRIS. Con il DTO, ogni importer espone solo un mapper che
converte il dato grezzo in un oggetto normalizzato. La pipeline base lavora
sempre con `DLI_ImportItem`, indipendentemente dalla sorgente.

**Aggiungere una nuova sorgente significherà scrivere solo:**
1. Il costruttore con la configurazione specifica.
2. `fetch_raw_items(array $conf): array` — chiamata HTTP + decode.
3. `map_item(mixed $raw, array $conf): DLI_ImportItem` — mapping sorgente → DTO.
4. `persist_meta(int $post_id, DLI_ImportItem $item): void` — salvataggio campi ACF specifici.

Tutto il resto (loop, insert/update, lingua, report) è ereditato.

---

## Struttura DTO `DLI_ImportItem`

Campi comuni a tutti gli importer:

```php
class DLI_ImportItem {
    public string $external_id   = '';   // ID univoco nella sorgente
    public string $title         = '';   // titolo principale (lingua primaria)
    public string $slug          = '';   // post_name
    public string $content       = '';   // post_content
    public string $excerpt       = '';   // descrizione breve
    public string $post_status   = 'draft';
    public string $language      = 'it';
    public array  $taxonomies    = [];   // [ taxonomy => [term_name, ...] ]
    public array  $meta          = [];   // [ meta_key => value ]
    public array  $translations  = [];   // [ 'en' => DLI_ImportItem|null ]
}
```

I campi specifici di una sorgente (es. date evento, inventori brevetto) vanno
in `meta` oppure gestiti nell'override di `persist_meta()`.

---

## Fasi di refactoring

### Fase A — Fondamenta (nessun cambio di comportamento)

Obiettivo: introdurre DTO e metodi astratti nella base class senza toccare
gli importer esistenti.

**File modificati:**
- `inc/classes/class-base-importer.php` — aggiunta metodi abstract e pipeline
- `inc/classes/class-import-item.php` — nuovo file con DTO `DLI_ImportItem`

**Cosa NON cambia:** `DLI_IndicoImporter` e `DLI_IrisPatentImporter` restano
invariati. La pipeline astratta coesiste con il codice esistente.

**Validazione:** lint PHP, nessuna regressione su endpoint e cron.

---

### Fase B — Migrazione INDICO

Obiettivo: riscrivere `DLI_IndicoImporter` usando la pipeline della base class.

**File modificati:**
- `inc/classes/class-indicoimporter.php`

**Cosa NON cambia:** endpoint `POST /wp-json/custom/v1/indico-import`,
comportamento cron, dry-run, tassonomie, lingua.

**Validazione:**
- Test endpoint con curl (dry-run e import reale).
- Verifica contatori nel report finale.
- Verifica lingua e tassonomia degli eventi creati.

---

### Fase C — Migrazione PATENT/IRIS

Obiettivo: riscrivere `DLI_IrisPatentImporter` usando la stessa pipeline.

**File modificati:**
- `inc/classes/class-patentimporter.php`

**Cosa NON cambia:** endpoint `POST /wp-json/custom/v1/iris-patent-import`,
gestione multilingue IT/EN, tassonomie aree tematiche, report contatori.

**Validazione:**
- Test endpoint (dry-run e import reale).
- Verifica creazione/aggiornamento versioni IT e EN.
- Verifica tassonomie tradotte e report.

---

### Fase D — Guida per nuove sorgenti

Obiettivo: documentare il minimo necessario per aggiungere un nuovo importer.

**File:**
- `DOC/import-new-source-guide.md`

**Contenuto:** lista dei 4 metodi da implementare, esempio minimale, checklist
di validazione.

---

## Strategia di rollout

- Una fase per volta, commit separati.
- Fase A non tocca gli importer: zero rischio di regressione.
- Fase B e C: prima di mergere, test manuale sull'endpoint.
- Non migrare entrambi gli importer nello stesso commit.

---

## Definition of Done

- Entrambi gli importer usano la pipeline della base class.
- Il codice specifico di ogni sorgente è limitato a fetch + mapping + meta.
- Aggiungere un nuovo importer richiede meno di 150 righe di codice.
- Nessuna regressione su endpoint, cron, dry-run e report.
