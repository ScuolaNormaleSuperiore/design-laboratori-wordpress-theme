# Strumenti della documentazione — migrazione a Jekyll

Stato: generatore precedente rimosso; il runtime Jekyll minimo e il suo
contratto sono presenti, ma il layout, la build HTML, la ricerca e
l'anteprima del sito documentale non sono ancora disponibili.
Nessun deploy GitHub Pages è configurato da questi strumenti.

Il progetto adotterà Jekyll e il layout della documentazione ufficiale
Bootstrap Italia. I manuali originali restano in `DOC/`; questa cartella
contiene soltanto gli strumenti tecnici. Il `Gemfile` usa Ruby >= 3.3 e
Jekyll 4.4.1; il lock e' stato generato e la build minima verificata su
Windows con Ruby 4.0.9 e Bundler 4.0.23. La piattaforma Linux e' registrata
nel lock e sara' verificata con la futura CI, prima di considerare il runtime
completo su entrambe le piattaforme.
`UPSTREAM.md` fissa tag, commit, licenza e perimetro del layout Bootstrap
Italia da derivare.

## Strumenti riutilizzabili

- `site-manifest.json`: selezione tecnica provvisoria di due guide esistenti,
  REST API e Personalizzazione dello stile. Non è il menu definitivo né
  un elenco di contenuti già approvati per la pubblicazione.
- `scripts/manifest.mjs`: verifica selezione esplicita, file versionati in
  DOC, destinazioni e rifiuto di percorsi privati, traversal e symlink.
- `scripts/prepare.mjs`: copia Markdown e asset selezionati in un input
  temporaneo. Riceve l'elenco dei file DOC da Git e riscrive link semplici.
- `scripts/preview.mjs`: server HTTP locale riutilizzabile per HTML statico.
  Non genera pagine e non ha una cartella di anteprima predefinita.

I moduli Node richiedono Node.js >= 20; il comando dei test riportato sotto
usa Node.js >= 22. I test non richiedono WordPress, database o browser.

## Controlli mirati disponibili

Dalla radice del tema:

```sh
node --test --test-isolation=none tests/documentation/manifest.test.mjs tests/documentation/prepare.test.mjs
```

La modalità senza isolamento dei processi evita i limiti di avvio di
sottoprocessi presenti in alcuni ambienti WSL/sandbox e mostra tutti i
singoli casi eseguiti. Non modifica i contratti verificati.

## Limiti da risolvere prima della build Jekyll

Il preparatore è una base da adattare, non una pipeline pronta per
pubblicare. Mancano front matter e permalink Jekyll, navigazione dal
manifest, controllo completo di link/ancore e rinvii autorizzati a GitHub,
versione/ref nelle pagine, ricerca e packaging.

La riscrittura corrente usa una regex per link semplici, non un parser
Markdown completo; i link non selezionati diventano attualmente URL GitHub
senza applicare la lista `github_documents`. Anche la validazione della
directory di output va rafforzata prima di usare lo script su percorsi
arbitrari: il preparatore sostituisce ricorsivamente la destinazione.
La fase di adattamento deve risolvere questi limiti prima dell'uso ordinario.

## Anteprima statica

Dopo che sarà disponibile una build HTML, il server potrà ricevere
`DLI_DOCS_OUTPUT`, nome semplice della cartella sotto
`SETUP/Documentazione/.build/` che contiene `index.html`.
Se la variabile o la pagina iniziale mancano, termina con un messaggio
esplicito. Nessun comando di build o di avvio del sito viene promesso in
questa fase di migrazione.

Le impostazioni generiche conservate sono:

| Variabile | Significato |
| --- | --- |
| `DLI_DOCS_OUTPUT` | Cartella HTML da servire; obbligatoria, senza default. |
| `DLI_DOCS_PORT` | Porta localhost, predefinita 8085. |
| `DLI_DOCS_BASE_PATH` | Percorso URL, predefinito `/`; supporta il sottopercorso Pages. |

Il server si arresta con Ctrl+C e richiede rigenerazione esplicita dopo le
modifiche. Serve file statici via HTTP e gestisce il MIME `.mjs`.
Non promette funzionamento completo aprendo le pagine con `file://`.

## Pilota visivo temporaneo

Il pilota della Fase 3 genera un input temporaneo dalle due guide gia'
selezionate nel manifest. Non e' il comando definitivo della documentazione:
non aggiunge ricerca, non stabilisce URL pubblici e non sostituisce la futura
preparazione della Fase 4.

Da PowerShell, nella radice del tema:

```powershell
node SETUP/Documentazione/scripts/build-pilot.mjs
Push-Location SETUP/Documentazione
bundle exec jekyll build --config _config.yml --source .build/pilot-source --destination .build/pilot
Pop-Location
$env:DLI_DOCS_OUTPUT = 'pilot'
node SETUP/Documentazione/scripts/preview.mjs
```

Aprire `http://127.0.0.1:8085/` e arrestare il server con `Ctrl+C`.
Il generatore copia nell'output i file locali della distribuzione npm
Bootstrap Italia fissata; non carica CDN, analytics o ricerca esterna.

## Dipendenze e distribuzione

`vendor/`, `.bundle/`, cache Jekyll e `.build/` sono ignorati localmente; non
vi sono dipendenze Composer documentali da installare. Il Composer e il PHP
del tema rimangono indipendenti da questi strumenti.

Le regole locali `.gitattributes` escludono il tooling dagli archivi del
tema generati con Git. La futura fase di packaging dovrà verificare anche
lo script ZIP effettivo, introdurre ignore mirati per Ruby/Jekyll e produrre
un archivio separato delle sole pagine statiche pubbliche.
