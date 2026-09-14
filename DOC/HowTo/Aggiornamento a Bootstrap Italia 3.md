# Aggiornamento del tema a Bootstrap Italia 3 (3.0.0-beta.5)

Documento di lavoro che raccoglie l'attività di allineamento del tema **Design Laboratori Italia** alla versione 3 di Bootstrap Italia (era sulla 2.18.3), avviata perché la libreria è ancora in **beta** e introduce cambi strutturali rilevanti (temizzazione tramite CSS custom properties `--bsi-*` al posto delle variabili Sass, markup nuovo per diversi componenti).

**Stato:** in corso. Lavoro svolto direttamente sul branch `main` di una copia locale del repository (non un fork/branch dedicato), con commit **solo locali**: nessun push è stato fatto né va fatto verso il repository ufficiale (`https://github.com/ScuolaNormaleSuperiore/design-laboratori-wordpress-theme`). La copia locale serve esclusivamente per analisi e test live sul container Docker della demo (`SETUP/Docker`).

## Vincoli del perimetro di lavoro

- Si lavora **solo sui template di pagina e sul frontend grafico** (CSS, markup dei `template-parts`, `header.php`/`footer.php`, `single-*.php`, ecc.).
- **Non si toccano** le interfacce di amministrazione dei content type né la configurazione (`inc/admin/*`, CMB2, ACF export in `SETUP/ACF_Custom_Fields`).
- La verifica del rendering avviene **via Docker/HTTP** (curl contro il container `demolab`, bind-mount su questa copia locale), non tramite browser grafico.
- Riferimento di design: il prototipo statico `bs-playground` (branch `feat/bootstrap-italia-3-beta5`), che ha già completato il porting completo a Bootstrap Italia 3 di tutte le famiglie di pagina — vedi in particolare `CONTESTO/AVANZAMENTO_PORTING_BOOTSTRAP_ITALIA_3.md` in quel repository per i pattern di markup già validati (hero+breadcrumb, card, chip, header, ecc.).

## 1. Dipendenza e asset

- `package.json`: `bootstrap-italia` aggiornato da `^2.18.3` a `3.0.0-beta.5`.
- Aggiunto lo script npm `assets:sync` (`SETUP/npm_scripts/sync-bootstrap-italia.mjs`, nuovo file) che copia `node_modules/bootstrap-italia/dist` in `assets/bootstrap-italia/` (233 file), con un controllo di versione che blocca l'esecuzione se la dipendenza installata non è esattamente quella attesa. Stesso approccio già validato nel porting statico di `bs-playground` (`scripts/sync-bootstrap-italia.mjs`).
- `assets/bootstrap-italia/` risincronizzata interamente alla v3.0.0-beta.5.

## 2. Meccanismo di temizzazione: da variabili Sass a CSS custom properties

Il tema personalizzava il colore/font tramite variabili Sass (`$primary-h/-s/-b`, `$font-family-*` in `assets/scss/bootstrap-italia-custom.scss`), compilate con `npm run create_layout` in `assets/css/bootstrap-italia-custom.min.css` (percorso servito quando l'opzione **WP → Configurazione → "choose_style" = "custom"** è attiva).

**Da Bootstrap Italia 3 queste variabili Sass non esistono più nel sorgente** (verificato: 0 occorrenze di `$primary-h/-s/-b` in `node_modules/bootstrap-italia/src/scss`) e la compilazione produce un output identico byte-per-byte al CSS v3 di default: nessun errore, ma nessun effetto. Bootstrap Italia 3 tema i componenti tramite custom property CSS (`--bsi-*`), non più in fase di compilazione Sass.

**Soluzione adottata:** `assets/scss/bootstrap-italia-custom.scss` è stato lasciato con un commento di avviso esplicito (le vecchie dichiarazioni restano solo come riferimento per un eventuale rollback alla 2.18.3) e il brand reale è ora gestito in un nuovo file separato, `assets/css/custom-colors.css`, caricato da `functions.php` **dopo** il CSS base di Bootstrap Italia, solo quando `choose_style = 'custom'`.

## 3. Brand SNS (colori e font)

`assets/css/custom-colors.css` applica lo stesso brand già usato nel prototipo statico `bs-playground` (Ottanio `#00728d`, Pantone 314U, primario; Dark Blue `#183f56`, secondario), riportando 1:1 le custom property `--bsi-*` già verificate là (`src/styles/adapters/bootstrap-italia.css` + `src/styles/tokens/sns-*.css`).

Punti da ricordare (già causa di problemi durante questo lavoro, vedi sezione bug):
- Il colore **non** era quello del tema originale (navy `#17324d`): è stato sostituito col brand SNS ufficiale su richiesta esplicita, perché l'obiettivo finale è che questa istanza WordPress diventi il sito reale corrispondente al prototipo.
- Il **font** è stato lasciato al default di Bootstrap Italia 3 (Titillium Web): nessun override `--bsi-font-*`, coerentemente col prototipo statico che non lo sovrascrive.
- Le variabili `--bsi-icon-*` sono **separate** da `--bsi-color-*-primary/-secondary` (valori letterali propri, non `var()`) e vanno sempre sovrascritte a parte, altrimenti icone/header restano sul blu di default anche a brand corretto.
- `.it-header-slim-wrapper` (barra "Ente appartenenza") non eredita `--bsi-color-background-primary` da `:root`: dichiara `--bsi-header-slim-background` direttamente sull'elemento, quindi va sovrascritta sullo stesso selettore.

`assets/css/main.css` (override custom del tema, caricato per ultimo) conteneva inoltre diversi colori/font hardcoded dell'epoca v2 che vincevano silenziosamente sulla cascata di custom property: navy `#17324d`/`#30475f` su hover pulsante newsletter e chip selezionate, un riferimento errato a `--bs-primary` (namespace Bootstrap 5 standard, mai esistito in questo progetto, che ricadeva sempre sul fallback hardcoded `#0066cc`), e uno stack font `"Roboto", "Titillium Web", "Lora", ...` hardcoded su hero/carousel/card che sovrascriveva anche la regola corretta di v3 per `.font-serif`. Tutti rimossi/sostituiti con riferimenti a `var(--bsi-color-background-primary)` / `var(--bsi-font-sans)`.

## 4. Bug della libreria (v3.0.0-beta.5) trovati e corretti nell'adapter

Non ancora segnalati upstream. Entrambi risolti in `assets/css/custom-colors.css` con un selettore più specifico, senza toccare markup o libreria:

- **`.it-hero-text-wrapper.bg-dark` non produce sfondo scuro**: la regola base di `.it-hero-text-wrapper` (`background: rgba(0,0,0,0) !important`) ha la stessa specificità della utility `.bg-dark` (anch'essa `!important` sulla stessa proprietà); a parità di specificità vince l'ultima dichiarata nel foglio compilato, cioè quella trasparente.
- **`.it-header-navbar-wrapper.theme-light-desk` mantiene lo sfondo colorato invece di restare bianco**: la variante `.theme-light` sovrascrive `--bsi-header-nav-background` (usata dal wrapper per il proprio `background-color`) con `--bsi-color-background-inverse`; la variante `.theme-light-desk`, usata da sola in `header.php`, sovrascrive invece solo lo sfondo del `.navbar` annidato e il bordo, lasciando `--bsi-header-nav-background` sul valore di base (`--bsi-color-background-primary`, cioè il brand).

## 5. Cache-busting per singolo file

`functions.php` (`dli_scripts()`) usava `wp_get_theme()->get('Version')` come `?ver=` per tutti i CSS: la versione del tema non cambia tra una modifica e l'altra di questi 3 file, quindi il browser poteva continuare a servire dalla cache una versione precedente anche dopo un refresh normale. Introdotta una chiusura `$dli_asset_version()` basata su `filemtime()`, applicata a `bootstrap-italia-custom.min.css`, `custom-colors.css` e `main.css` (gli unici file attivamente modificati in questa attività).

## 6. Causa radice di un bug di rendering "colori/font ancora vecchi"

Dopo aver introdotto `custom-colors.css`, il sito in demo continuava a mostrare i colori istituzionali blu invece del brand SNS. La causa **non era la cache del browser**, ma il fatto che `assets/css/bootstrap-italia-custom.min.css` — il file base effettivamente servito quando `choose_style = 'custom'` — era rimasto fisicamente compilato con **Bootstrap Italia 2.18.3**: `npm run create_layout` scrive solo in `assets/css/compiled/`, e il passo di copia sul file servito (normalmente eseguito da `npm run update_layout_linux`/`update_layout_win`) non era mai stato lanciato dopo il bump della dipendenza. Le custom property del brand venivano quindi applicate sopra una base CSS ancora v2. Risolto eseguendo `npm run update_layout_linux` (verificato: la stringa di versione embedded nel file passa da `2.18.3` a `3.0.0-beta.5`).

## Nota operativa per chi verifica la demo

Perché i colori/font del brand SNS siano visibili, l'opzione **WP → Configurazione → "choose_style"** deve essere impostata su **"custom"** (in caso contrario viene caricato `assets/bootstrap-italia/css/bootstrap-italia.min.css`, la libreria "pulita" senza alcun override, e `custom-colors.css` non viene nemmeno caricato).

## Famiglia Persone (elenco + scheda) — portata a v3

Primo caso reale di applicazione dei pattern trasversali:

- **Hero con breadcrumb integrato**: `template-parts/common/breadcrumb-hero.php` (bozza preesistente, mai agganciata: corretto un bug per cui lo step attivo del breadcrumb renderizzava un link invece di un testo statico) ora è usata sia dall'elenco (`template-parts/hero/persone.php`, hero a due colonne con immagine) sia dalla scheda (`single-persona.php`, stesso pattern "senza foto di copertina": sfondo Ottanio pieno invece del pattern foto+overlay). Aggiunto `assets/img/placeholder-sns.png` (stesso asset del prototipo) per la colonna immagine, non essendoci un campo immagine reale per queste pagine.
- **Filtri** (`template-parts/persone/filters.php`): niente più chip (rimosse anche nella forma v3 appena introdotta, su richiesta esplicita) — il filtro struttura è sempre a `<select>`, come già faceva la vista tabella. Tutti i filtri (cognome, struttura, tipologia, livello) sulla stessa riga, ciascuno visibile anche quando è lui stesso l'unico filtro attivo a produrre 0 risultati (evita di intrappolare l'utente). Scoperto e corretto, prima di rimuovere le chip, un bug di contrasto reale sulla forma chip v3: col brand SNS la famiglia "primary" è appiattita su un solo Ottanio (stessa scelta del prototipo, mai esercitata però su un componente chip), quindi `.chip-primary` calcolava testo e sfondo nella stessa tinta — l'override resta in `custom-colors.css` (`.chip-primary`) anche se qui non più usato, potrà servire altrove.
- **Filtro "Cerca per cognome"**: aggiunto (presente nel prototipo, mancava nella vista a schede — la vista tabella lo aveva già lato client) con filtraggio server-side reale (`DLI_ContentsManager::get_people_page_data()`, nuovo parametro `selected_cognome`), stessa logica di ricarica GET già usata dagli altri filtri. Non portato il filtro "Ciclo" del prototipo: nessun campo/tassonomia corrispondente esiste per questo post type.
- **Toggle Schede/Tabella**: la vista non è più solo una scelta fissa da configurazione (`people_view_type`) ma può essere cambiata dall'utente in pagina (bottoni `btn-group` "Schede"/"Tabella", pattern del prototipo, nuovo `template-parts/persone/view-toggle.php` condiviso tra le due viste) via `?vista=chip|tabella`, che quando presente vince sul default di configurazione; i filtri attivi restano preservati passando da una vista all'altra.
- **Card correlate** (`single-persona.php`: Progetti, Indirizzi di ricerca): da markup Bootstrap generico v2 (`card`/`card-body`/`card-title`/`card-text`, wrapper `card-teaser-wrapper`, assente/non tematizzato in v3) a `it-card`/`it-card-body`/`it-card-title`/`it-card-text` su griglia `row`/`col-md-6`. Pubblicazioni portate a lista con citazione, Ulteriori informazioni (CV e allegati) a `it-list-wrapper`/`it-list` con metadata — stesso linguaggio visivo già usato per i Contatti, pattern del prototipo per contenuti "a elenco lungo" invece che a card.
- **Corpo della scheda**: da `col-12 col-lg-9` a `col-12 col-lg-8 offset-lg-1` (il solo gutter di bootstrap lasciava il testo troppo a ridosso della sidebar dell'indice, stesso fix documentato nel prototipo statico) — pattern da riapplicare a ogni scheda di dettaglio con la stessa sidebar `it-navscroll`.

## Famiglia Progetti (elenco, archivio, scheda) — portata a v3

Stessi pattern di Persone, applicati anche a `page-templates/archive-progetti.php` (l'archivio dei progetti conclusi: nessun prototipo statico dedicato — bs-playground porta solo l'archivio news/eventi — quindi riusa lo stesso schema hero+breadcrumb validato per le pagine di elenco/archivio):

- **Hero con breadcrumb integrato**: `template-parts/hero/progetti.php` e `progetti-archive.php` riscritti sul pattern a due colonne (come Persone).
- **Filtro TAG** (`page-templates/progetti.php`): da chip v2 a `<select>` singolo, stessa scelta di Persone.
- **Card degli elenchi**: da `card`/`card-bg`/`card-big`/`no-after` (wrapper `card-space`, link "Vai al progetto" separato) a `it-card`/`it-card-image`/`it-card-body`/`it-card-title` con footer `it-card-footer` (`it-card-signature` per i responsabili, `it-card-taxonomy`/`it-card-chips`/`chip-secondary` per i tag) — titolo della card come link, pattern ufficiale `it-card`: niente più link "Vai al progetto" separato.
- **Scheda progetto** (`single-progetto.php`): pattern "scheda con foto" (diverso da quello "senza foto" di Persone) — breadcrumb in overlay assoluto sopra l'immagine di copertina (`.it-hero-breadcrumb`, nuove regole aggiunte in `assets/css/main.css`: mancavano nel tema, esistevano solo nel prototipo statico), nessun box scuro locale sul testo (la leggibilità è già garantita dall'overlay + text-shadow globali già presenti), chip categoria rimosse dall'hero (pattern del prototipo: nessuna chip nell'hero di scheda, "da riposizionare altrove" resta un TODO aperto anche là).
- **Template-part condivisi** (usati da Progetti ma anche da altre schede — Indirizzi di ricerca, Risorse tecniche): `sezione-indirizzidiricerca.php`/`sezione-progetti.php` (card→`it-card`), `sezione-pubblicazioni.php` (card→lista con citazione), `sezione-allegati.php` (card→`it-list-wrapper`/`it-list` con metadata) — stessi pattern già validati per Persone, ora condivisi da più content-type in un colpo solo.
- Non toccati in questa passata: `template-parts/common/sezione-persone.php` (già v3-compatibile), `sezione-related-items.php`/`sezione-related-technical-resources.php` (nessuna classe v2-only trovata), `template-parts/common/paginazione.php` (usa `paginate_links()` nativo WP con CSS custom proprio, non le classi `pagination`/`page-item` di Bootstrap Italia — non è un problema di compatibilità v2/v3, è un design preesistente diverso, fuori perimetro di questa attività).

## Prossimi passi

- Replicare lo stesso porting (hero+breadcrumb, chip/filtri, card→it-card) sulle altre famiglie di pagina (attività, pubblicazioni, brevetti, impatti, risorse tecniche, luoghi, archivio news/eventi), seguendo l'ordine e i pattern già chiusi in `bs-playground`.
- Vista tabella di Persone (`template-parts/persone/view-tabella.php`) e vista chip (`template-parts/persone/view-chip.php`) non ancora verificate per classi v2-only residue: da controllare quando si passa a quella famiglia/vista specifica.
- `template-parts/common/paginazione.php`: valutare se allineare l'output di `paginate_links()` al componente `pagination` di Bootstrap Italia 3 (lavoro più ampio, non un semplice porting di classi).
