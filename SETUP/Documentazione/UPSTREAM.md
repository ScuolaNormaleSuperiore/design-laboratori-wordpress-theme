# Provenienza del layout documentale

## Riferimento fissato

- Repository: `https://github.com/italia/bootstrap-italia`
- Tag: `v3.0.0-beta.7`
- Commit: `d00b89f209a08f7eb5c14fe107e7184dcf3870fa`
- Licenza del codice Bootstrap Italia: BSD-3-Clause, riportata nel file
  upstream `LICENSE`.
- Versione della dipendenza gia' presente nel tema: `bootstrap-italia`
  `3.0.0-beta.7` in `package.json` e `package-lock.json`.

Il tag e il commit sono la sola sorgente di confronto del pilota. Non usare
`main` come origine implicita e non aggiornare l'asset del tema WordPress per
costruire la documentazione.

## Struttura da derivare nel pilota

L'aspetto di riferimento e' la pagina documentale ufficiale, non la home.
La catena effettiva individuata al ref fissato e':

1. `_layouts/docs.html`: skiplink, topbar, navbar, sidebar, indice e area
   contenuto;
2. `_includes/docs-topbar.html`, `docs-navbar.html`, `docs-sidebar.html` e
   `docs-toc.html`;
3. `_includes/head.html`, `scripts.html`, `footer.html`, `favicons.html` e
   le icone richiamate dal layout;
4. `_data/nav.yml` per la navigazione; per questo progetto verra' generato
   dal manifest, non copiato come una seconda fonte editoriale;
5. `dist/css/bootstrap-italia.min.css`, `dist/js/bootstrap-italia.bundle.min.js`,
   `dist/svg/sprites.svg` e i font della stessa distribuzione.

Per il pilota si importeranno solo i file effettivamente necessari, con nomi
e identita' del progetto sostituiti. Ogni file importato sara' aggiunto qui
con percorso upstream, hash e modifica locale.

### Pilota Fase 3

Il pilota implementa in locale, adattandola al progetto, la gerarchia del
layout upstream `docs.html`: skiplink, header, menu mobile, sidebar, contenuto,
indice e footer. I template locali sono `_layouts/docs.html` e gli include
`docs-*.html`, `head.html`, `footer.html`, `scripts.html`; non sono copie
letterali dell'identita' o dei link Bootstrap Italia.

`assets/css/pilot.css` deriva le regole essenziali di navigazione, contenuto,
sidebar, indice e skiplink dai componenti SCSS della documentazione upstream.
Il suo entrypoint upstream e' `docs/assets/src/scss/docs.scss`, SHA-256
`50058caeb994071aa1b730235d590eee3090784e2dc11131389d11c477560b59`.

`scripts/build-pilot.mjs` copia **solo nell'output ignorato** `.build/pilot-source`
la distribuzione npm locale corrispondente: `dist/css`, `dist/js`, `dist/fonts`
e `dist/svg`. Nessun asset della libreria viene duplicato o versionato sotto
questo tooling. La fonte effettiva e' `node_modules/bootstrap-italia` 3.0.0-beta.7.

## Esclusioni deliberate

Non riusare nel sito del progetto: topbar con identita' del Dipartimento/AgID,
link di download e repository Bootstrap Italia, home animata, analytics,
DocSearch/Algolia, CDN e script di esempio. La v1 deve mantenere ricerca e
asset locali e non eseguire richieste obbligatorie a servizi esterni.

Il file upstream `scripts.html` include funzioni aggiuntive (animazioni,
clipboard e DocSearch) che non sono una dipendenza automatica del nostro
layout. Valutarle singolarmente nelle fasi successive.

## Runtime Jekyll upstream rilevato

Il `Gemfile` del ref fissato richiede Ruby >= 3.3 e usa Jekyll 4.4.1,
`jekyll-toc`, `webrick` e `wdm` su Windows, oltre a plugin e controlli che
non servono ancora al pilota. Il Gemfile locale mantiene soltanto il minimo
necessario a generare il layout e il relativo indice; il lock locale e'
generato separatamente su Windows con Bundler.
