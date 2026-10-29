# Test E2E (browser su un sito dedicato)

I test E2E controllano il comportamento **come lo vede un utente**: Playwright pilota un browser
vero su un sito WordPress dedicato (sito Local `dli-wp-e2e`). Sono i più lenti dei tre livelli, quindi
coprono solo ciò che richiede un browser. Non vanno confusi con i [Report](../report/README.md), che
fotografano un sito in funzione senza asserzioni.


## Requisiti

- Un sito Local `dli-wp-e2e`: WordPress pulito, con tema, ACF e Polylang collegati in `wp-content`
  (junction verso la copia di lavoro) e **attivati** (prima i plugin, poi il tema). Permalink salvati una volta.
- `DB_HOST` nel `wp-config.php` del sito impostato su host e porta del suo MariaDB, per esempio
  `127.0.0.1:10004` invece di `localhost`, perché lo script di ripristino, lanciato da una shell normale,
  raggiunga il database. La porta è quella che Local ha assegnato al sito (scheda Database) e può
  cambiare: va ricontrollata dopo ogni riavvio di Local.
- Node.js con `npm ci` (rieseguirlo dopo un `git pull` che cambia `package.json` o `package-lock.json`) e PHP con `mysqli` nel `PATH`
  (lo usa lo script di ripristino; non servono il client `mysql` né WP-CLI).
- Chromium per la versione di Playwright installata, scaricato una volta con `npm run status:scan:install-browser`
  (lo stesso browser dei Report).


## Configurazione e variabili d'ambiente

| Variabile | Significato | Valore predefinito |
|---|---|---|
| `DLI_E2E_SITE_PATH` | cartella dell'installazione WordPress del sito E2E (quella con `wp-config.php`), per esempio `C:\Users\<utente>\Local Sites\dli-wp-e2e\app\public` | nessuno: **obbligatoria**, salvo si salti il ripristino |
| `DLI_E2E_SKIP_RESET` | `1` per saltare il ripristino prima e dopo la corsa, per fare il debug di una specifica | non impostata |
| `DLI_E2E_BASE_URL` | indirizzo del sito E2E | `https://dli-wp-e2e.local` |
| `DLI_E2E_ADMIN_USER`, `DLI_E2E_ADMIN_PASSWORD` | amministratore | `e2e-admin`, `password` |
| `DLI_E2E_SUPER_EDITOR_USER`, `DLI_E2E_SUPER_EDITOR_PASSWORD` | Super Editor | `e2e-super-editor`, `password` |
| `DLI_E2E_EDITOR_USER`, `DLI_E2E_EDITOR_PASSWORD` | Editor | `e2e-editor`, `password` |

`DLI_E2E_SITE_PATH` dipende dalla macchina: va impostata una volta come variabile d'ambiente dell'utente,
poi si apre un terminale nuovo. Una variabile impostata in un terminale vale solo per quel terminale.

```
setx DLI_E2E_SITE_PATH "C:\Users\<utente>\Local Sites\dli-wp-e2e\app\public"
```

Per la sola sessione corrente (PowerShell):

```
$env:DLI_E2E_SITE_PATH = 'C:\Users\<utente>\Local Sites\dli-wp-e2e\app\public'
```

Gli altri valori predefiniti coincidono con gli utenti di prova qui sotto: su un sito preparato bene non serve altro.


## Esecuzione

Si lancia con Composer, come le altre suite. Playwright è uno strumento Node.js, quindi ogni script di Composer
chiama lo script `npm run test:e2e*` con lo stesso nome (si può usare l'uno o l'altro).

```
composer test:e2e           # senza finestra del browser
composer test:e2e:headed    # browser visibile
composer test:e2e:ui        # interfaccia di Playwright
composer test:e2e -- config-permissions    # una sola specifica (le opzioni dopo --)
```

Il sito deve essere acceso in Local. Una corsa dura circa 40 secondi. Dopo la corsa il sito è alla baseline.


### Eseguire un solo test

Ha senso, per esempio per riprovare un test segnalato come «flaky» o per sviluppare una specifica nuova. Tutto ciò che sta dopo `--`
va a Playwright. Dalla radice del tema:

```sh
composer test:e2e -- tests/e2e/specs/admin-login.spec.js                  # un solo file (qui contiene un solo test)
composer test:e2e -- admin-login                                          # lo stesso: un frammento del nome del file
composer test:e2e -- tests/e2e/specs/contact-form.spec.js:44              # il test che inizia alla riga 44
composer test:e2e -- -g "wrong nonce"                                     # per titolo (espressione regolare sul titolo)
composer test:e2e -- --list                                               # elenca i test senza eseguirli
composer test:e2e -- tests/e2e/specs/admin-login.spec.js --repeat-each=5  # lo ripete 5 volte (per capire se è instabile)
composer test:e2e:headed -- admin-login                                   # con il browser visibile
```

- `--list` non esegue nulla e non ripristina la baseline: è il modo sicuro per controllare che il filtro scelga i test voluti
  (`--list -g "wrong nonce"`, `--list admin-login`). Se non trova nulla scrive `Total: 0 tests` ed esce con codice `1`.
- Il numero di riga (`:44`) è quello che compare nell'errore, ma cambia quando si modifica il file: per un riferimento stabile
  usare il filtro per titolo `-g`.
- **Anche per un solo test la suite ripristina la baseline prima e dopo** ([`global-setup.js`](global-setup.js) e
  [`global-teardown.js`](global-teardown.js)): il tempo totale è quello del test più alcuni secondi di ripristino.
- Per saltare il ripristino mentre si fa il debug di una specifica, impostare `DLI_E2E_SKIP_RESET=1`. Il sito resta allora nello stato in cui
  il test lo lascia: prima di una corsa vera ripristinarlo (vedi sotto) o lanciare la suite senza la variabile.

  ```sh
  DLI_E2E_SKIP_RESET=1 composer test:e2e -- admin-login      # Git Bash
  ```
  ```powershell
  $env:DLI_E2E_SKIP_RESET = '1'; composer test:e2e -- admin-login   # PowerShell
  ```
- Un test E2E isolato non sostituisce la suite: due test possono influenzarsi se uno lascia dati, e solo la corsa completa lo mostra.


## Utenti di prova

La baseline ([`fixtures/baseline.sql`](fixtures/baseline.sql)) contiene questi utenti. La password è la stessa per tutti ed è volutamente
banale: il sito è un sito di prova locale, non raggiungibile da fuori. **Non riusare mai queste credenziali su
un sito vero.**

| Utente | Password | Ruolo | A cosa serve |
|---|---|---|---|
| `e2e-admin` | `password` | Amministratore | tutto, compresa la pagina Configurazione |
| `e2e-super-editor` | `password` | Super Editor (`dli_super_editor`) | può aprire la pagina Configurazione |
| `e2e-editor` | `password` | Editor | non può aprire la pagina Configurazione: il lato negativo dei test sui permessi |


## Baseline

[`fixtures/baseline.sql`](fixtures/baseline.sql) è un dump del database di `dli-wp-e2e` in uno stato noto: tema, ACF, ACF OpenStreetMap
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

Pagine di esempio (italiano, inventate), per le card delle sottopagine:

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
che chiamano [`support/reset.js`](support/reset.js), che lancia [`support/reset-baseline.php`](support/reset-baseline.php)). Lo script PHP legge i dati di
connessione dal `wp-config.php` del sito (`DLI_E2E_SITE_PATH`), esegue il dump con `mysqli` e cancella e
ricrea ogni tabella. Non serve altro che PHP.

Per sicurezza rifiuta di importare se il database non appartiene al sito della baseline: l'host di `siteurl`
nel database deve essere quello del dump (`dli-wp-e2e.local`) e il prefisso delle tabelle lo stesso. Quindi
puntare `DLI_E2E_SITE_PATH` a un sito di sviluppo non lo cancella, e il sito deve chiamarsi `dli-wp-e2e`. Per
fare il debug di una specifica senza ripristino: `DLI_E2E_SKIP_RESET=1`.

Per ripristinare a mano: `php tests/e2e/support/reset-baseline.php <cartella del sito> tests/e2e/fixtures/baseline.sql`.


### Rigenerazione

La baseline la rigenerano solo i manutentori, dopo aver preparato il sito nello stato voluto. Servono WP-CLI e
`mysqldump` (per esempio quelli di Local; mettere nel `PATH` la cartella `bin` del MariaDB di Local):

1. Cancellare i transient: `wp db query "DELETE FROM wp_options WHERE option_name REGEXP 'transient'"`
   (non scrivere `%` nella query: `cmd.exe` lo espande).
2. Esportare: `wp db export "<radice del tema>/tests/e2e/fixtures/baseline.sql" --skip-extended-insert --skip-comments`
   (una riga per record, così una modifica si vede come una piccola differenza; usare barre normali nel percorso).
3. Mascherare gli indirizzi dell'ente che il tema scrive nella pagina predefinita della Privacy
   (`protocollo@pec.sns.it`, `dpo@sns.it`, `privacy@sns.it`): sostituire ogni lettera e cifra con `x`, mantenendo
   `@` e i punti, **senza cambiare la lunghezza** (il dump contiene dati serializzati, dove è scritta la
   lunghezza di ogni stringa).
4. Controllare che il file non contenga nomi, email, indirizzi IP, token o percorsi locali reali e che ogni
   valore serializzato sia ancora leggibile.

Il dump è stato fatto con MariaDB 10.11 (quello di Local).


## Cosa copre

| File | Test |
|---|---|
| `home.spec.js` | la home si carica (stato inferiore a 400, attributo `lang`) |
| `admin-login.spec.js` | l'amministratore entra in `wp-admin` |
| `config-permissions.spec.js` | amministratore e Super Editor aprono la pagina Configurazione, l'Editor riceve 403 |
| `contact-form.spec.js` | il form dei contatti si vede e, senza il plugin CAPTCHA, ha il pulsante disabilitato; un invio forzato con campi vuoti mostra i tre messaggi di validazione in ordine; un invio con un nonce sbagliato viene rifiutato |
| `newsletter.spec.js` | pagina newsletter, campi del form e validazione browser dell'email non valida |
| `site-search.spec.js` | ricerca pubblica: invio di una query senza risultati, messaggio di stato e annullamento della ricerca |
| `people-filters.spec.js` | archivio Persone: filtro per cognome senza risultati, mantenimento del valore e aggiornamento dell'URL |
| `publication-links.spec.js` | dove porta il titolo di una pubblicazione nell'elenco (scheda, sito esterno con avviso, file, testo semplice) e nella ricerca del sito, autori al posto della descrizione nelle card (e non per una pubblicazione protetta), pulsante «Scarica la pubblicazione» nella scheda, scheda ancora raggiungibile con «Sito web» |
| `example-pages-tool.spec.js` | Strumenti DLI → Utilità: due box separati; il box «Pagine di esempio» crea la sezione in italiano e inglese (24 pagine, con le 4 card nelle due pagine principali), rilanciato non duplica, «Rimuovi» la cancella (404); rifiutando la conferma non si crea nulla |
| `page-subpage-cards.spec.js` | pagina base con le card delle sottopagine: ordine, riassunto o testo tagliato a 30 parole, assenza di bozza, sottopagina protetta e nipote, posizione prima del testo con il titolo «In questa sezione», link alla sottopagina, nessun elenco senza sottopagine o con il campo spento, nessun overflow a 320 px |
| `home-publications.spec.js` | pubblicazioni della home (box di «Contenuti in evidenza» e sezione «Pubblicazioni», configurati nella baseline): autori, tipologia e anno nella card, dal titolo alla scheda, dalla tipologia all'elenco filtrato, pulsante «Tutte le pubblicazioni», nessun overflow orizzontale a 320 px |
| `publication-detail.spec.js` | scheda di una pubblicazione: completa (tipologia, anno, autori con e senza link, descrizione, link esterno in una nuova finestra), minima (segnaposto della copertina, nessun blocco vuoto), protetta da password (solo modulo, poi contenuto dopo la password), traduzione inglese |

Sono trentuno test. Non coprono l'invio riuscito del form dei contatti, che richiede il plugin CAPTCHA e un server di posta. La copertura vera (opzioni condizionali
delle persone, selettore Schede/Tabella, cambio lingua, manifest) è da scrivere.


## Come funziona

- [`specs/`](specs/): i file di test (`*.spec.js`).
- [`support/auth.js`](support/auth.js): gli helper di login (`loginAsAdmin`, `loginAsSuperEditor`, `loginAsEditor`).
- [`support/reset.js`](support/reset.js) e [`support/reset-baseline.php`](support/reset-baseline.php): il ripristino della baseline.
- [`global-setup.js`](global-setup.js) e [`global-teardown.js`](global-teardown.js): lanciano il ripristino prima e dopo i test e fanno un login di
  riscaldamento (il primo accesso a `wp-admin` dopo il ripristino è lento).
- [`fixtures/baseline.sql`](fixtures/baseline.sql): la baseline.
- `test-results/` e `playwright-report/`: risultati generati, ignorati da Git.
- La configurazione è in [`../../playwright.config.js`](../../playwright.config.js) (radice del tema): un solo worker perché i test condividono lo
  stesso sito, timeout di 60 s per test e di 15 s per le asserzioni (login e prima pagina di amministrazione sono
  lenti su un sito locale), un nuovo tentativo per test.

**Test «flaky».** Con un sito Local su Windows il login a volte non parte (circa 1 su 40-80) oppure il browser
segnala `net::ERR_NETWORK_CHANGED`. Il nuovo tentativo di Playwright lo assorbe e il test compare come «flaky»
nel riepilogo: non è un errore del tema, ma se i «flaky» diventano frequenti vale la pena indagare.


## Come aggiungere una specifica

Creare un file nella [cartella delle specifiche](specs/) chiamato `<nome>.spec.js` con `test( ... )` di `@playwright/test`. Per un utente autenticato usare gli helper
di [`support/auth.js`](support/auth.js). Provare solo ciò che richiede un browser: il resto va nei [test di integrazione](../integration/README.md). Il ripristino
della baseline avviene da solo; se la specifica richiede dati di esempio, vanno nella baseline (si rigenera come sopra).
