# Generatore della documentazione — pilota tecnico

Stato: pilota Daux funzionante su fixture; **non è ancora il sito documentale**.
Le fixture sotto `tests/documentation/` non vanno pubblicate. Nessun deploy
GitHub Pages è configurato o autorizzato da questi comandi.

## Requisiti isolati

- PHP >= 8.4, estensioni XML e mbstring; intl consigliata.
- Composer; usare il lock presente, senza aggiornamenti automatici.
- Node.js >= 20 per validatore, anteprima e test.
- Playwright/Chromium già presenti nel normale ambiente di sviluppo per i
  soli test browser. Non occorre WordPress o un database.

Verificato il 2026-11-03 con PHP Windows 8.4.25, Composer 2.8.10,
Node 24.13.1 e Daux **0.26.1**. Il requisito PHP riguarda soltanto la
generazione delle guide, non il tema installato sui siti.

I comandi seguenti partono dalla radice del repository. Su Windows usare
PowerShell con PHP 8.4, Composer e Node nel PATH della sessione. Se serve,
aggiungere soltanto alla sessione la cartella del PHP disponibile:

```powershell
$env:Path = 'C:\SPAZIODATI\PORTABLES\php84;' + $env:Path
```

## Installazione riproducibile

```sh
composer --working-dir=SETUP/Documentazione install --no-interaction --prefer-dist
composer --working-dir=SETUP/Documentazione check-platform-reqs
```

Il lock documentale è separato da quello del tema. `vendor/` e `.build/`
di questa cartella sono ignorati; le regole locali di export escludono il
tooling dagli archivi del tema. Non vengono modificate le dipendenze in radice.

Nel primo avvio Windows il bundle CA del PHP locale non validava Packagist.
La verifica è riuscita usando il bundle già installato con Git, solo per
la sessione/comando. Se si incontra lo stesso problema e Git è installato
in quel percorso, in PowerShell:

```powershell
$env:COMPOSER_CAFILE = 'C:\Program Files\Git\mingw64\etc\ssl\certs\ca-bundle.crt'
```

Non disabilitare TLS o la verifica dei certificati. Il percorso dipende
dalla macchina; non è un requisito generale del progetto.

## Generare e controllare il pilota

```sh
node --test tests/documentation/manifest.test.mjs
php SETUP/Documentazione/vendor/bin/daux generate --source=tests/documentation/fixtures/daux --destination=SETUP/Documentazione/.build/pilot --configuration=SETUP/Documentazione/config.json --no-cache
node --test tests/documentation/pilot.test.mjs
```

I test del manifest verificano selezione esplicita, sorgenti DOC versionate,
spazi/accenti, PDF selezionati, esclusione di percorsi privati, symlink,
traversal e destinazioni duplicate/non ammesse. Il validatore è una funzione
isolata: il preparatore deve ancora collegarla all'elenco Git dei file reali.

I due test browser servono l'HTML generato su una porta temporanea di
localhost: radice e sottopercorso Pages. Verificano navigazione, ancora,
immagine e ricerca con risultato atteso; rifiutano risorse esterne e rilevano
errori HTTP/JavaScript. Non usano la configurazione Playwright WordPress,
non effettuano login e non ripristinano database.

## Anteprima manuale

Dopo la generazione:

```sh
node SETUP/Documentazione/scripts/preview.mjs
```

Aprire <http://127.0.0.1:8085/>. Arrestare con Ctrl+C. Rigenerare dopo
ogni modifica: non c'è hot reload. `DLI_DOCS_PORT` permette di scegliere
una porta libera; con `DLI_DOCS_BASE_PATH=/design-laboratori-wordpress-theme/`
il sito è disponibile sotto quel percorso, non alla radice. Le variabili
sono di sessione: PowerShell usa `$env:DLI_DOCS_PORT = '8086'`, Linux
`DLI_DOCS_PORT=8086 node SETUP/Documentazione/scripts/preview.mjs`.

Il server serve soltanto l'output statico e gestisce il MIME `.mjs`.
Non promette la ricerca aprendo i file con `file://`.

## Cosa manca nel Lotto 1

1. Manifest delle guide approvate e preparatore: mapping di nomi con
   spazi/accenti, immagini/PDF, link/ancore, rinvii espliciti a GitHub per
   sorgenti non pubblicate e link di modifica verso il file originale.
2. Versione/ref e avviso di anteprima su tutte le pagine; selezione ed
   esclusioni verificate sull'output reale, non soltanto sulle fixture.
3. Controllo completo di link e ancore, ripetibilità della build e download PDF.
4. Comandi finali `docs:*`, output `build/documentazione*/` e packaging
   verificato su un archivio reale, dopo coordinamento dei file in radice.

L'output del primo esperimento è volutamente confinato in `.build/pilot/`
per non toccare gli ignore condivisi mentre è in corso il piano import CSV.
L'esecuzione è stata verificata su Windows; i comandi sono portabili, ma
il runtime PHP/Node WSL di questa macchina non era utilizzabile e non si
dichiara una verifica Linux già eseguita.
