# Test E2E (browser su un sito dedicato, in Docker)

I test E2E controllano il comportamento **come lo vede un utente**: Playwright pilota un browser
vero su un sito WordPress dedicato, in un ambiente **Docker** creato da
[wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) (`@wordpress/env`),
come i [test di integrazione](../integration/README.md). Sono i più lenti dei tre livelli, quindi
coprono solo ciò che richiede un browser. Non vanno confusi con i [Report](../report/README.md), che
fotografano un sito in funzione senza asserzioni.


## Requisiti

- **Docker Desktop** acceso (o un demone Docker compatibile).
- Node.js con `npm ci` (installa Playwright e `@wordpress/env`; rieseguirlo dopo un `git pull` che cambia
  `package.json` o `package-lock.json`).
- Chromium per la versione di Playwright installata, scaricato una volta con `npm run status:scan:install-browser`
  (lo stesso browser dei Report).
- La porta **8892** libera. La 8893 è dell'ambiente di integrazione, le porte 8890 e 8891 del plugin
  `kk-content-exporter`: gli ambienti possono girare insieme.

Non servono un sito Local, PHP né WP-CLI sul computer: il ripristino usa il WP-CLI del container.


## L'ambiente

[`../../.wp-env.e2etest.json`](../../.wp-env.e2etest.json) fissa tutto ciò che entra nel sito, così due corse
sono uguali su ogni computer e in CI:

| Cosa | Valore |
|---|---|
| WordPress | 7.1.4 (zip di `wordpress.org`), la versione minima dichiarata dal tema |
| PHP | 8.4 |
| Plugin | ACF 6.8.12, Polylang 3.9.2 e ACF OpenStreetMap Field 1.6.1 (zip di `downloads.wordpress.org`) |
| Tema | la copia di lavoro (`themes: ["."]`) |
| Indirizzo | `http://localhost:8892` |
| Debug | `WP_DEBUG` e `WP_DEBUG_LOG` attivi, `WP_DEBUG_DISPLAY` spento |

Per cambiare una versione si modifica quel file **e** il test unit `DLI_Docker_Tests_Config_Unit_Test`,
che controlla che i valori restino fissati (e uguali a quelli dell'ambiente di integrazione).


## Variabili d'ambiente

Nessuna è necessaria: i valori predefiniti coincidono con gli utenti di prova qui sotto.

| Variabile | Significato | Valore predefinito |
|---|---|---|
| `DLI_E2E_SKIP_RESET` | `1` per saltare il ripristino prima e dopo la corsa, per fare il debug di una specifica | non impostata |
| `DLI_E2E_ADMIN_USER`, `DLI_E2E_ADMIN_PASSWORD` | amministratore | `e2e-admin`, `password` |
| `DLI_E2E_SUPER_EDITOR_USER`, `DLI_E2E_SUPER_EDITOR_PASSWORD` | Super Editor | `e2e-super-editor`, `password` |
| `DLI_E2E_EDITOR_USER`, `DLI_E2E_EDITOR_PASSWORD` | Editor | `e2e-editor`, `password` |


## Esecuzione

Si lancia con Composer, come le altre suite. Ogni script di Composer chiama lo script `npm run test:e2e*` con lo
stesso nome (si può usare l'uno o l'altro), cioè [`../bin/e2e.mjs`](../bin/e2e.mjs), che controlla che Docker
risponda, avvia l'ambiente (se è già acceso non fa nulla) e lancia Playwright.

```
composer test:e2e           # senza finestra del browser
composer test:e2e:headed    # browser visibile
composer test:e2e:ui        # interfaccia di Playwright
composer test:e2e -- config-permissions    # una sola specifica (le opzioni dopo --)
```

Dopo la corsa il sito è alla baseline e l'ambiente resta acceso, così le corse successive partono subito. La
prima volta wp-env scarica immagini Docker, WordPress e plugin, e il ripristino installa le traduzioni italiane
di WordPress, ACF e Polylang: qualche minuto in più.

| Comando | Cosa fa |
|---|---|
| `composer env:start` | accende l'ambiente senza lanciare i test (il sito è su `http://localhost:8892`) |
| `composer env:stop` | lo spegne (i dati restano) |
| `composer env:destroy` | lo cancella del tutto: container, database e traduzioni scaricate |

In CI la suite gira con il workflow [`functional.yml`](../../.github/workflows/functional.yml), lanciato a mano dalla
scheda Actions (Run workflow): stessi comandi, e se un test fallisce il report di Playwright e le tracce restano
come artefatto per 7 giorni.


### Eseguire un solo test

Ha senso, per esempio per riprovare un test segnalato come «flaky» o per sviluppare una specifica nuova. Tutto ciò che sta dopo `--`
va a Playwright. Dalla radice del tema:

```sh
composer test:e2e -- tests/e2e/specs/site-search.spec.js                  # un solo file (qui contiene un solo test)
composer test:e2e -- site-search                                          # lo stesso: un frammento del nome del file
composer test:e2e -- tests/e2e/specs/contact-form.spec.js:10              # il test che inizia alla riga 10
composer test:e2e -- -g "password-protected"                                     # per titolo (espressione regolare sul titolo)
composer test:e2e -- --list                                               # elenca i test senza eseguirli
composer test:e2e -- tests/e2e/specs/site-search.spec.js --repeat-each=5  # lo ripete 5 volte (per capire se è instabile)
composer test:e2e:headed -- site-search                                   # con il browser visibile
```

- `--list` non esegue nulla e non ripristina la baseline (avvia però l'ambiente, se è spento): è il modo sicuro per controllare che il filtro scelga i test voluti
  (`--list -g "password-protected"`, `--list site-search`). Se non trova nulla scrive `Total: 0 tests` ed esce con codice `1`.
- Il numero di riga (`:44`) è quello che compare nell'errore, ma cambia quando si modifica il file: per un riferimento stabile
  usare il filtro per titolo `-g`.
- **Anche per un solo test la suite ripristina la baseline prima e dopo** ([`global-setup.js`](global-setup.js) e
  [`global-teardown.js`](global-teardown.js)).
- Per saltare il ripristino mentre si fa il debug di una specifica, impostare `DLI_E2E_SKIP_RESET=1`. Il sito resta allora nello stato in cui
  il test lo lascia: prima di una corsa vera ripristinarlo (vedi sotto) o lanciare la suite senza la variabile.

  ```sh
  DLI_E2E_SKIP_RESET=1 composer test:e2e -- site-search      # Git Bash
  ```
  ```powershell
  $env:DLI_E2E_SKIP_RESET = '1'; composer test:e2e -- site-search   # PowerShell
  ```
- Un test E2E isolato non sostituisce la suite: due test possono influenzarsi se uno lascia dati, e solo la corsa completa lo mostra.


## Utenti di prova

La baseline ([`fixtures/baseline.sql`](fixtures/baseline.sql)) contiene questi utenti. La password è la stessa per tutti ed è volutamente
banale: il sito è un sito di prova in Docker, non raggiungibile da fuori. **Non riusare mai queste credenziali su
un sito vero.**

| Utente | Password | Ruolo | A cosa serve |
|---|---|---|---|
| `e2e-admin` | `password` | Amministratore | tutto, compresa la pagina Configurazione |
| `e2e-super-editor` | `password` | Super Editor (`dli_super_editor`) | può aprire la pagina Configurazione |
| `e2e-editor` | `password` | Editor | non può aprire la pagina Configurazione: il lato negativo dei test sui permessi |


## Baseline

[`fixtures/baseline.sql`](fixtures/baseline.sql) è un dump del database del sito E2E in uno stato noto, con l'indirizzo
`http://localhost:8892` dell'ambiente: tema, ACF, ACF OpenStreetMap
Field e Polylang attivi, permalink `/%postname%/`, lingue `it` ed `en`, le 42 pagine e le voci di menu create
dall'attivazione del tema, i tre utenti qui sopra e i contenuti di esempio elencati sotto. Non contiene dati
personali reali, transient né sessioni. Contiene anche le regole degli indirizzi (`rewrite_rules`) già scritte, con quelle delle pubblicazioni
(`/pubblicazione/<slug>/`), e l'opzione `dli_publication_rules_version`: se si cambia un indirizzo dei contenuti, la baseline va rigenerata
(altrimenti il tema riscrive le regole alla prima richiesta di ogni corsa).

### Contenuti di esempio

Tutti inventati, assegnati all'utente `e2e-admin`. Servono alle specifiche sulle pubblicazioni e sui loro autori:

| Contenuto | Lingua | Cosa lo distingue |
|---|---|---|
| Persona «Autore Uno E2E» | it | scheda di dettaglio attiva |
| Persona «Autore Due E2E» | it | `disattiva_pagina_dettaglio` attivo (autore senza link alla scheda) |
| «Pubblicazione E2E completa» | it | anno 2024, due autori, link esterno, descrizione, tipologia «Articolo in rivista», in evidenza nella home |
| «E2E complete publication» | en | traduzione della precedente, tipologia «Article in journal» |
| «Pubblicazione E2E minima» | it | solo titolo e anno 2023, tipologia «Monografia»: senza autori, link esterno, descrizione né copertina |
| «Pubblicazione E2E protetta» | it | protetta da password (`e2e-password`), un autore, link esterno, tipologia «Articolo in rivista» |
| «Pubblicazione E2E sito esterno» | it | «Link dettaglio» = Sito web, `Url` `https://example.org/e2e-sito-esterno`: il titolo nelle liste porta al sito, in una nuova finestra |
| «Pubblicazione E2E senza link» | it | «Link dettaglio» = Sito web ma `Url` vuoto: il titolo è testo semplice, senza link |
| «Pubblicazione E2E con allegato» | it | «Link dettaglio» = Link ad allegato, con una voce di media PDF (`2026/10/e2e-articolo.pdf`, senza il file nel dump: si verifica l'indirizzo, non il download) |

Pagine di esempio (italiano, inventate), per le card delle sottopagine e per la sezione «Card di navigazione» (tre pagine sono promosse alla home, più una promossa e protetta da password `e2e-password`, «Pagina E2E promossa protetta», e una promossa in bozza, «Pagina E2E promossa bozza»):

| Pagina | Cosa la distingue |
|---|---|
| «Pagina E2E con sottopagine» | `Mostra le sottopagine come card` attivo; figlie «Sottopagina E2E uno» (riassunto scritto), «Sottopagina E2E due» (nessun riassunto, testo lungo 45 parole), «Sottopagina E2E protetta» (password `e2e-password`), «Sottopagina E2E bozza» (bozza); nipote «Nipote E2E» sotto la prima |
| «Pagina E2E senza sottopagine» | campo attivo ma nessuna figlia |
| «Pagina E2E card spente» | una figlia, campo spento |

La baseline contiene anche le opzioni della home (`homepage_sections` e `homepage`): sezioni «Contenuti in evidenza» (box 1: pubblicazioni, etichetta «Pubblicazioni in evidenza») e «Pubblicazioni» attive, con il titolo. Nessun box di news o eventi.

Le tipologie di pubblicazione sono quelle create dall'attivazione del tema (non ne esistono altre). Le persone non hanno
una tipologia, quindi non compaiono nell'elenco `/persone/`: servono solo come autori. Nessun contenuto ha una immagine
in evidenza: i file dei media non sono nel dump, quindi la copertina si prova con i test di integrazione.


### Ripristino automatico

`composer test:e2e` ripristina la baseline **prima e dopo** la corsa ([`global-setup.js`](global-setup.js) e [`global-teardown.js`](global-teardown.js),
che chiamano [`support/reset.js`](support/reset.js)). Il ripristino usa WP-CLI nel container `cli` dell'ambiente
([`support/wp.js`](support/wp.js)):

1. controlla che il sito risponda con `siteurl` `http://localhost:8892`, cioè che sia l'ambiente E2E;
2. importa la baseline (`wp db import`), che cancella e ricrea ogni tabella;
3. installa le traduzioni italiane di WordPress, ACF e Polylang, se mancano (sono file, non dati: restano tra
   un ripristino e l'altro);
4. forza italiano (`it_IT`) come lingua WordPress, lingua predefinita Polylang e lingua dei tre utenti E2E
   ([`support/force-italian.php`](support/force-italian.php)): le specifiche amministrative usano quindi stringhe
   italiane. I casi multilingua visitano esplicitamente `/en/`; non cambiano la lingua di base della suite.

Per fare il debug di una specifica senza ripristino: `DLI_E2E_SKIP_RESET=1`. Per ripristinare a mano basta lanciare
un test qualsiasi, oppure `npx wp-env run --config=.wp-env.e2etest.json cli -- wp db import
wp-content/themes/design-laboratori-wordpress-theme/tests/e2e/fixtures/baseline.sql`.


### Rigenerazione

La baseline la rigenerano solo i manutentori, dopo aver preparato il sito E2E nello stato voluto (a partire dalla
baseline attuale). I comandi `wp` qui sotto si lanciano nel container, con
`npx wp-env run --config=.wp-env.e2etest.json cli -- wp ...`:

1. Cancellare i transient: `wp db query "DELETE FROM wp_options WHERE option_name REGEXP 'transient'"`
   (non scrivere `%` nella query: `cmd.exe` lo espande).
2. Esportare: `wp db export wp-content/themes/design-laboratori-wordpress-theme/tests/e2e/fixtures/baseline.sql --skip-extended-insert --skip-comments --skip-dump-date`
   (una riga per record, così una modifica si vede come una piccola differenza).
3. Mascherare gli indirizzi dell'ente che il tema scrive nella pagina predefinita della Privacy
   (`protocollo@pec.sns.it`, `dpo@sns.it`, `privacy@sns.it`): sostituire ogni lettera e cifra con `x`, mantenendo
   `@` e i punti, **senza cambiare la lunghezza** (il dump contiene dati serializzati, dove è scritta la
   lunghezza di ogni stringa).
4. Controllare che il file non contenga nomi, email, indirizzi IP, token o percorsi locali reali e che ogni
   valore serializzato sia ancora leggibile.
5. Controllare con `git diff` che cambino solo le righe volute: il sito che gira aggiunge da solo transient, cron
   e i ruoli che il tema registra all'avvio, che non vanno nella baseline.

Il dump è stato fatto con MariaDB 10.11 su un sito Local (`http://dli-wp-e2e.local`) e poi portato all'indirizzo
`http://localhost:8892` sostituendo il testo (nessuna occorrenza era in un valore serializzato).


## Cosa copre

| File | Test |
|---|---|
| `home.spec.js` | la home si carica (stato inferiore a 400, attributo `lang`) |
| `home-carousel-reflow.spec.js` | carousel con frecce visibili sulle home IT/EN: controlli dentro lo schermo e nessuno scorrimento orizzontale da 320 a 1280 px, avanzamento con click e ritorno con tastiera. La baseline non ha eventi: il test inserisce nella sola risposta del browser una fixture con lo stesso contenitore/variante del template Eventi, usando CSS e JavaScript reali del tema, senza modificare il database |
| `content-iframe-reflow.spec.js` | un iframe incollato nel contenuto con larghezza fissa (500 px) non fa scorrere la pagina di lato a 320, 360 e 400 px e resta nella sua colonna; a 1280 px conserva la larghezza scelta. La baseline non ha un contenuto simile: il test inserisce nella sola risposta del browser la colonna di `page.php` con l'iframe, usando il CSS reale del tema, senza modificare il database |
| `config-permissions.spec.js` | amministratore e Super Editor aprono la pagina Configurazione, l'Editor riceve 403 |
| `contact-form.spec.js` | il form dei contatti si vede e, senza il plugin CAPTCHA, ha il pulsante disabilitato (i messaggi di un invio forzato, il loro ordine e il nonce controllato per primo sono in `DLI_Contact_Form_Integration_Test`) |
| `newsletter.spec.js` | pagina newsletter e campi del form |
| `site-search.spec.js` | ricerca pubblica: invio di una query senza risultati, messaggio di stato e annullamento della ricerca |
| `people-filters.spec.js` | archivio Persone: filtro per cognome senza risultati, mantenimento del valore e aggiornamento dell'URL |
| `publication-links.spec.js` | dove porta il titolo di una pubblicazione nell'elenco (scheda, sito esterno con avviso, file, testo semplice); il resto (ricerca, autori nelle card, pulsante «Scarica la pubblicazione») è in `DLI_Publication_Archive_Integration_Test`, `DLI_Publication_Detail_Integration_Test` e `DLI_Publication_Link_Integration_Test` |
| `home-navigation-groups.spec.js` | sezione «Navigazione per pagine»: campi della riga solo per questa sezione, due gruppi in ordine con il titolo come testo e le sottopagine come link (né protetta né bozza, né pagina senza figli), accordion nascosto su desktop, link alla pagina; su telefono accordion con il primo pannello aperto e i link dei pannelli chiusi non visibili, nessun overflow (assenza in inglese e tetti sono in `DLI_Home_Navigation_Groups_Integration_Test`) |
| `home-tabs.spec.js` | sezione «Pannello tab»: scambio di righe del gruppo dei tab con l'editor di testo (ogni descrizione resta col suo tab, anche con righe aggiunte nella pagina, due scambi di fila e due salvataggi; l'etichetta nel titolo della riga resta dopo la cancellazione di una riga); home con titolo, primo tab aperto, tastiera (freccia sposta il focus, Invio apre il pannello), inglese con ripiego sull'italiano; su telefono nessun overflow |
| `home-video.spec.js` | sezione «Video»: il campo «URL del video» solo per quella sezione; due righe Video salvate, a riga chiusa il nome è il codice del video o il titolo; nella home due sezioni con un player ciascuno, la funzione condivisa `dliLoadYouTubeVideo` presente, nessun errore di script e nessun overflow a 320 px |
| `home-page-index.spec.js` | sezione «Indice di una pagina»: il campo «Pagina» e i due numeri solo per questa sezione, scelte rientrate e senza la bozza né la protetta, scelta mantenuta dopo il salvataggio; la riga chiusa nomina la pagina scelta e il nome segue la scelta, una riga nuova, il riordino e il ricaricamento (non per le altre sezioni); home con il titolo della pagina, una colonna («Sottopagina E2E uno», titolo come testo) con il link al nipote, senza la sottopagina senza figli, protetta e bozza; link senza sottolineatura a riposo e con sottolineatura su hover e focus; due indici con scelte opposte (sfondo, titolo, limiti) salvati, ricaricati e resi come scelto; copia disattivata e poi rimossa, l'altra conserva dati e sezione; su telefono pannello dell'accordion chiuso e riaperto con Invio e Spazio |
| `breadcrumb.spec.js` | breadcrumb dell'hero: su una pagina a tre livelli, a 320 px resta su una riga (voci precedenti senza ritorno a capo né riduzione), la voce corrente è tagliata con i puntini e il testo intero resta nella pagina, nessun overflow orizzontale; a 1280 px non è tagliato |
| `header-menu-mobile.spec.js` | pulsante del menu principale su schermo stretto (320, 360, 400 px): dentro lo schermo, apre il menu con il mouse, le voci del menu aperto restano dentro lo schermo, si chiude con Esc; raggiungibile e azionabile con la tastiera. Controlla l'invariante, che vale per qualunque logo e nome del sito: sul sito di prova (logo e nome corti) potrebbe passare anche senza la correzione |
| `footer-headings.spec.js` | titoli del footer nel browser: nome del laboratorio `h3`, motto come paragrafo, titoli delle colonne («Contatti», «Link utili», «Seguici su») `h4` in testo semplice, «Newsletter» `h3` con il suo link; nessun salto di livello e nessun link `#`. Controlla i titoli che la baseline ha (livelli e link sono anche in `DLI_Footer_Headings_Integration_Test`) |
| `pagination-per-page.spec.js` | la dimensione della pagina chiesta nell'indirizzo (`per_page`). Progetti (accetta ogni misura fino al massimo) e archivio progetti (accetta solo i valori offerti) hanno la serie completa: un valore enorme, anche più grande di un intero, è portato a 96 (o al valore predefinito); negativo, zero, testo, testo con numero, esponente e lista tornano al valore predefinito; un valore offerto si usa; una misura vecchia nell'intervallo si tiene solo dove prevista. Blog, luoghi e archivio per autore hanno solo un valore enorme e un testo, per provare che il template usa lo stesso controllo (il resto è in `DLI_Pagination_Unit_Test`) |
| `home-featured-contents.spec.js` | sezione «Contenuti in evidenza»: ogni box ha tipo e titolo IT/EN e nessun template da scegliere (i tipi offerti includono notizie, eventi e pubblicazioni, non le attività di ricerca); due box con contenuti si dividono la riga e il terzo, senza contenuti, non compare; titoli h3 e h4 senza salti, pulsante verso la pagina delle pubblicazioni, titolo inglese con ripiego sull'italiano; su telefono i box sono uno sotto l'altro, nessun overflow. I box tornano a quelli della baseline alla fine |
| `home-navigation-section.spec.js` | sezione «Card di navigazione»: da Layout HP e Sezioni HP alla home (campi della riga e livello a quattro scelte, titolo e sottotitolo, tre pagine promosse nell'ordine atteso, né la protetta né la bozza, sfondo grigio di default, link alla pagina) e a 320 px (una card per riga, sfondo bianco, nessun overflow). Lingua e livelli sono in `DLI_Home_Navigation_Section_Integration_Test` |
| `home-sections-admin.spec.js` | Configurazione → Sezioni HP: il menu «Sezione» disattiva le sezioni singole già presenti in un'altra riga e non quelle ripetibili, una riga nuova parte dalla prima disponibile, togliere una riga la restituisce; nome della sezione e frecce nell'intestazione di ogni riga anche chiusa (senza sovrapposizioni con la ✕), riga nuova con le sue frecce, scambio di due righe con valori opposti dei radio (valori conservati dopo lo scambio, il salvataggio e il ricaricamento), accesso negato a un Editor |
| `pages-import-tool.spec.js` | Strumenti DLI → Importa → Pagine: modulo e tastiera, download dell'esempio, 320 px senza overflow; simulazione che non scrive; conferma richiesta e importazione (pagina con 4 card, riesecuzione tutta saltata); italiano con Lorem ed inglese senza, collegate come traduzioni; file con errori e file non CSV |
| `people-import-tool.spec.js` | Strumenti DLI → Importa → Persone: un solo test, il percorso nel browser (scelta del file, invio, report, persona creata). Modulo, simulazione, righe con errore, file rifiutati e lettura del file sono in `DLI_People_Import_Admin_Integration_Test`, le modalità in `DLI_People_File_Importer_Integration_Test` |
| `page-subpage-cards.spec.js` | pagina base con le card delle sottopagine: link alla sottopagina e link senza sottolineatura a riposo (ordine, riassunto, esclusioni e posizione sono in `DLI_Page_Subpage_Cards_Integration_Test`) |
| `home-publications.spec.js` | pubblicazioni della home (configurate nella baseline): il tipo è sottolineato solo su hover e focus; dalla tipologia all'elenco filtrato e pulsante «Tutte le pubblicazioni» (il contenuto delle card è in `DLI_Publication_Home_Cards_Integration_Test`) |
| `publication-detail.spec.js` | scheda di una pubblicazione: protetta da password (solo modulo, poi contenuto dopo la password) e traduzione inglese (le schede completa e minima sono in `DLI_Publication_Detail_Integration_Test`) |

Sono oltre 60 test. Non coprono l'invio riuscito del form dei contatti, che richiede il plugin CAPTCHA e un server di posta. La copertura vera (opzioni condizionali
delle persone, selettore Schede/Tabella, cambio lingua, manifest) è da scrivere.


## Come funziona

- [`specs/`](specs/): i file di test (`*.spec.js`).
- [`support/auth.js`](support/auth.js): gli helper di login (`loginAsAdmin`, `loginAsSuperEditor`, `loginAsEditor`).
- [`support/reset.js`](support/reset.js) e [`support/force-italian.php`](support/force-italian.php): il ripristino della baseline.
- [`support/wp.js`](support/wp.js): lancia WP-CLI nel container `cli` dell'ambiente E2E.
- [`global-setup.js`](global-setup.js) e [`global-teardown.js`](global-teardown.js): lanciano il ripristino prima e dopo i test e fanno un login di
  riscaldamento (il primo accesso a `wp-admin` dopo il ripristino è lento).
- [`fixtures/baseline.sql`](fixtures/baseline.sql): la baseline.
- `test-results/` e `playwright-report/`: risultati generati, ignorati da Git.
- La configurazione è in [`../../playwright.config.js`](../../playwright.config.js) (radice del tema): un solo worker perché i test condividono lo
  stesso sito, timeout di 60 s per test e di 15 s per le asserzioni (login e prima pagina di amministrazione sono
  lenti sul sito di prova), un nuovo tentativo per test.

**Test «flaky».** Su Windows il login a volte non parte oppure il browser segnala `net::ERR_NETWORK_CHANGED` o
`net::ERR_CONNECTION_REFUSED`. Il nuovo tentativo di Playwright lo assorbe e il test compare come «flaky» nel
riepilogo: non è un errore del tema, ma se i «flaky» diventano frequenti vale la pena indagare.


## Come aggiungere una specifica

Su CI Playwright imposta `forbidOnly`: una specifica lasciata con `test.only` o
`test.describe.only` fa fallire la suite, evitando una corsa verde che abbia
escluso gli altri test. Il workflow E2E continua a essere avviato a mano.

Creare un file nella [cartella delle specifiche](specs/) chiamato `<nome>.spec.js` con `test( ... )` di `@playwright/test`. Per un utente autenticato usare gli helper
di [`support/auth.js`](support/auth.js). Provare solo ciò che richiede un browser: il resto va nei [test di integrazione](../integration/README.md). Il ripristino
della baseline avviene da solo; se la specifica richiede dati di esempio, vanno nella baseline (si rigenera come sopra).
