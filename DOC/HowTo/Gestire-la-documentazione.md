# Gestire la documentazione web

Questa guida descrive come aggiornare il sito documentale di Design Laboratori
Italia. Le guide pubbliche hanno un'unica sorgente nella cartella `DOC/`; i
file sotto `SETUP/Documentazione/` contengono solo il tooling Jekyll, i layout
e le impostazioni di build.

## Prima di iniziare

Usa una copia del repository aggiornata e controlla il worktree prima di
modificare file condivisi:

```sh
git status --short
```

Per costruire il sito su Windows servono Node.js 20 o successivo, Ruby 3.3 o
successivo e Bundler. Le dipendenze Ruby sono fissate in
`SETUP/Documentazione/Gemfile.lock`; non usare `bundle update` per un normale
aggiornamento editoriale.

Da PowerShell, se Ruby non è già nel `PATH` della sessione, aggiungi la sua
cartella `bin` e verifica gli strumenti:

```powershell
$env:Path = 'C:\percorso\di\Ruby\bin;' + $env:Path
ruby --version
bundle --version
```

Poi, dalla cartella `SETUP/Documentazione`, installa le gemme bloccate e torna
alla radice del tema:

```powershell
bundle install
Set-Location ..\..
```

## Aggiungere o aggiornare una guida

1. Scrivi o modifica il Markdown sotto `DOC/`. Ogni guida ha un solo titolo di
   primo livello (`#`).
2. Aggiungi la pagina al campo `pages` di
   `SETUP/Documentazione/site-manifest.json`: indica sorgente, destinazione,
   titolo, descrizione, gruppo e ordine. La destinazione è un percorso
   minuscolo, con trattini o underscore, che termina in `.md`.
3. Nei collegamenti Markdown usa il percorso relativo al file sorgente sotto
   `DOC/`, non l'URL finale. Durante la build il preparatore trasforma i link
   fra pagine selezionate negli URL del sito; le guide non selezionate restano
   collegamenti al repository.
4. Inserisci nel manifest solo contenuti approvati per la pubblicazione. Non
   aggiungere file da `DEV/`, credenziali, dati personali, dump o materiale
   temporaneo.

Il manifest accetta solo sorgenti versionate. Prima della build, registra le
nuove guide in Git insieme al manifest; controlla sempre il diff prima del
commit.

## Immagini e screenshot

Usa dati fittizi e una configurazione coerente con la versione del tema
documentata. Per ogni immagine conserva una breve procedura testuale: uno
screenshot non sostituisce istruzioni accessibili. Dai ai file nomi
descrittivi, ritaglia le sole aree utili e scrivi un testo alternativo che
spieghi l'informazione mostrata.

Non pubblicare schermate con persone riconoscibili, indirizzi e-mail, token,
password, chiavi, dati di contatto o altri dati personali. Se un'immagine
mostra una configurazione amministrativa, verifica prima di acquisirla che
non contenga segreti o contenuti reali.

## Costruire, controllare e vedere il sito

Dalla radice del tema esegui:

```powershell
npm run docs:build
npm run docs:check
npm run docs:preview
```

`docs:build` genera l'HTML in `build/documentazione`; `docs:check` controlla
i link e le ancore interne dell'output; `docs:preview` rigenera il sito e
avvia il server locale su `http://127.0.0.1:8085/`. Arresta il server con
`Ctrl+C`.

Durante la scrittura delle guide usa invece `npm run docs:watch`: avvia lo
stesso server e rigenera la documentazione dopo ogni modifica a `DOC/` o al
tooling in `SETUP/Documentazione/`. Dopo il messaggio di aggiornamento basta
ricaricare la pagina nel browser.

Prima di proporre una modifica, verifica almeno la home, una pagina profonda,
i menu a schermo stretto e la ricerca. Controlla inoltre tastiera, focus,
titoli, testi alternativi e leggibilità di tabelle e blocchi di codice.

## Preparare una modifica

I risultati della build e lo ZIP documentale sono file generati e non vanno
aggiunti a Git. Prima del commit limita lo staging ai file modificati e
controlla il contenuto:

```powershell
git add <file-documentazione> SETUP/Documentazione/site-manifest.json
git diff --cached --check
git diff --cached --stat
```

La pubblicazione su GitHub Pages, gli artefatti CI e il rollback saranno
documentati dopo l'attivazione della procedura di rilascio della Fase 6.
