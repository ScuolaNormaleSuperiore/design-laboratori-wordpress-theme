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


## Utenti di prova

La baseline (`fixtures/baseline.sql`) contiene questi utenti. La password è la stessa per tutti ed è volutamente
banale: il sito è un sito di prova locale, non raggiungibile da fuori. **Non riusare mai queste credenziali su
un sito vero.**

| Utente | Password | Ruolo | A cosa serve |
|---|---|---|---|
| `e2e-admin` | `password` | Amministratore | tutto, compresa la pagina Configurazione |
| `e2e-super-editor` | `password` | Super Editor (`dli_super_editor`) | può aprire la pagina Configurazione |
| `e2e-editor` | `password` | Editor | non può aprire la pagina Configurazione: il lato negativo dei test sui permessi |


## Baseline

`fixtures/baseline.sql` è un dump del database di `dli-wp-e2e` in uno stato noto: tema, ACF, ACF OpenStreetMap
Field e Polylang attivi, permalink `/%postname%/`, lingue `it` ed `en`, le 42 pagine e le voci di menu create
dall'attivazione del tema, i tre utenti qui sopra e nessun altro contenuto. Non contiene dati personali reali,
transient né sessioni.


### Ripristino automatico

`composer test:e2e` ripristina la baseline **prima e dopo** la corsa (`global-setup.js` e `global-teardown.js`,
che chiamano `support/reset.js`, che lancia `support/reset-baseline.php`). Lo script PHP legge i dati di
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

Sono dodici test. Non coprono l'invio riuscito del form dei contatti, che richiede il plugin CAPTCHA e un server di posta. La copertura vera (opzioni condizionali
delle persone, selettore Schede/Tabella, cambio lingua, manifest) è da scrivere.


## Come funziona

- `specs/`: i file di test (`*.spec.js`).
- `support/auth.js`: gli helper di login (`loginAsAdmin`, `loginAsSuperEditor`, `loginAsEditor`).
- `support/reset.js` e `support/reset-baseline.php`: il ripristino della baseline.
- `global-setup.js` e `global-teardown.js`: lanciano il ripristino prima e dopo i test e fanno un login di
  riscaldamento (il primo accesso a `wp-admin` dopo il ripristino è lento).
- `fixtures/baseline.sql`: la baseline.
- `test-results/` e `playwright-report/`: risultati generati, ignorati da Git.
- La configurazione è in `playwright.config.js` (radice del tema): un solo worker perché i test condividono lo
  stesso sito, timeout di 60 s per test e di 15 s per le asserzioni (login e prima pagina di amministrazione sono
  lenti su un sito locale), un nuovo tentativo per test.

**Test «flaky».** Con un sito Local su Windows il login a volte non parte (circa 1 su 40-80) oppure il browser
segnala `net::ERR_NETWORK_CHANGED`. Il nuovo tentativo di Playwright lo assorbe e il test compare come «flaky»
nel riepilogo: non è un errore del tema, ma se i «flaky» diventano frequenti vale la pena indagare.


## Come aggiungere una specifica

Creare `tests/e2e/specs/<nome>.spec.js` con `test( ... )` di `@playwright/test`. Per un utente autenticato usare gli helper
di `support/auth.js`. Provare solo ciò che richiede un browser: il resto va nei test di integrazione. Il ripristino
della baseline avviene da solo; se la specifica richiede dati di esempio, vanno nella baseline (si rigenera come sopra).
