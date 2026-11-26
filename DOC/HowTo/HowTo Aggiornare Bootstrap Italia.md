# Come aggiornare la libreria Bootstrap Italia

## Procedura

Tutti i comandi si lanciano dalla radice del tema. Non copiare file da archivi ZIP in `assets/bootstrap-italia/`.

1. Installare la nuova versione (aggiorna `package.json` e `package-lock.json`):

   ```powershell
   npm install bootstrap-italia@<nuova_versione> --save-exact
   ```

2. In `SETUP/npm_scripts/sync-bootstrap-italia.mjs` portare `expectedVersion` alla stessa versione: lo script del passo 3 si rifiuta di copiare una versione diversa. La sincronizzazione esclude `fonts/Titillium_Sans_Pro/`, rimosso dal tema; i font distribuiti sono Titillium Web, Lora e Roboto Mono.

3. Copiare la libreria nel tema (da `node_modules/bootstrap-italia/dist/` a `assets/bootstrap-italia/`):

   ```powershell
   npm run assets:sync
   ```

4. Rigenerare il CSS dello stile personalizzato, **sempre** (su Linux `npm run update_layout_linux`):

   ```powershell
   npm run update_layout_win
   ```

   Con lo stile personalizzato il tema carica `assets/css/bootstrap-italia-custom.min.css`, una copia completa del CSS della libreria che solo questo comando aggiorna: senza, quei siti avrebbero il JavaScript nuovo con il CSS vecchio.

5. Controllare che i quattro file dichiarino la nuova versione (quattro righe con lo stesso numero):

   ```powershell
   Select-String -Pattern '(?:"version":\s*"|BOOTSTRAP_ITALIA_VERSION = ''|--bootstrap-italia-version:\s*'')([^''"]+)' -List -Path node_modules/bootstrap-italia/package.json, assets/bootstrap-italia/version.js, assets/bootstrap-italia/css/bootstrap-italia.min.css, assets/css/bootstrap-italia-custom.min.css | ForEach-Object { "$($_.Filename): $($_.Matches[0].Groups[1].Value)" }
   ```

   Su Linux:

   ```sh
   grep -m1 -oE "(\"version\": \"|BOOTSTRAP_ITALIA_VERSION = '|--bootstrap-italia-version: ')[^'\"]+" node_modules/bootstrap-italia/package.json assets/bootstrap-italia/version.js assets/bootstrap-italia/css/bootstrap-italia.min.css assets/css/bootstrap-italia-custom.min.css
   ```

6. Provare il sito con un aggiornamento forzato della pagina, con lo stile predefinito e con quello personalizzato: nessun 404 per CSS, JavaScript, font o icone, nessun errore JavaScript, e header, menu mobile, modali, dropdown, carousel e banner dei cookie funzionanti.

7. Committare `package.json`, `package-lock.json`, `SETUP/npm_scripts/sync-bootstrap-italia.mjs`, `assets/bootstrap-italia/` e `assets/css/bootstrap-italia-custom.min.css` con la sua mappa (non `assets/css/compiled/`, ignorata da Git).

## Aggiornamenti proposti da Dependabot

Dependabot apre una pull request quando esce una nuova versione di Bootstrap Italia, ma quella pull request aggiorna soltanto `package.json` e `package-lock.json` (il passo 1). Non va unita da sola, perché lascerebbe gli asset del tema alla versione precedente: si esegue invece in locale l'intera procedura, con la versione proposta, e si fa il push su `dev`. Dependabot riconosce che la dipendenza è già aggiornata e chiude la pull request da solo; se resta aperta, la si chiude a mano indicando il commit che la sostituisce. Non usare i comandi `@dependabot ignore`, che bloccherebbero anche gli aggiornamenti futuri.

## Nota su Bootstrap Italia 3

Bootstrap Italia 3 usa custom property CSS `--bsi-*` per la tematizzazione. Gli override del brand del tema sono in `assets/css/custom-colors.css`; la rigenerazione Sass non sostituisce tali override.
