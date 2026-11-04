# Strumenti della documentazione — migrazione a Jekyll

Stato: generatore precedente rimosso; **Jekyll e il nuovo layout non sono
ancora configurati in questo repository**. La build HTML, la ricerca e
l'anteprima del sito documentale non sono attualmente disponibili.
Nessun deploy GitHub Pages è configurato da questi strumenti.

Il progetto adotterà Jekyll e il layout della documentazione ufficiale
Bootstrap Italia. I manuali originali restano in `DOC/`; questa cartella
contiene soltanto gli strumenti tecnici. Versioni Ruby/Jekyll/Bundler,
Gemfile, lock e comandi di build saranno introdotti nella fase successiva.
La prova temporanea Windows riferita dall'operatore non sostituisce la
verifica del futuro sito del progetto.

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

## Dipendenze e distribuzione

`vendor/` e `.build/` sono ignorati localmente; non vi sono dipendenze
Composer documentali da installare. Il Composer e il PHP del tema rimangono
indipendenti da questi strumenti.

Le regole locali `.gitattributes` escludono il tooling dagli archivi del
tema generati con Git. La futura fase di packaging dovrà verificare anche
lo script ZIP effettivo, introdurre ignore mirati per Ruby/Jekyll e produrre
un archivio separato delle sole pagine statiche pubbliche.
