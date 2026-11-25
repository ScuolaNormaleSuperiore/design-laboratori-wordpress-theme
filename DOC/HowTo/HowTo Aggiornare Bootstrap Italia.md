# Come aggiornare la libreria Bootstrap Italia

## Procedura

Questa procedura mantiene allineati la dipendenza npm e gli asset versionati che il tema carica da `assets/bootstrap-italia/`. Non copiare manualmente file da archivi ZIP nella cartella degli asset.

1. Dalla radice del tema, installare la versione desiderata in modo esplicito:

   ```powershell
   npm install bootstrap-italia@<nuova_versione> --save-exact
   ```

   Il comando aggiorna `package.json` e `package-lock.json`. Per installare le versioni già registrate nel lockfile su una nuova copia del repository, usare invece `npm ci`.

2. Aggiornare `expectedVersion` in `SETUP/npm_scripts/sync-bootstrap-italia.mjs` alla stessa versione dichiarata in `package.json`. Il controllo impedisce di copiare una distribuzione diversa da quella attesa.

3. Sincronizzare nel tema la distribuzione installata:

   ```powershell
   npm run assets:sync
   ```

   Il comando copia CSS, JavaScript, font e sprite SVG da `node_modules/bootstrap-italia/dist/` a `assets/bootstrap-italia/`.

4. Rigenerare **sempre** il CSS compilato del tema:

   ```powershell
   npm run update_layout_win
   ```

   In ambiente Linux il comando equivalente è `npm run update_layout_linux`. Basta lanciarlo una volta: compila `assets/scss/bootstrap-italia-custom.scss` in `assets/css/compiled/` e copia il risultato in `assets/css/`.

   Il passo serve anche se il proprio sito usa lo stile predefinito. Il tema sceglie il CSS di Bootstrap Italia in base all'opzione «Stile» della configurazione: con lo stile predefinito carica `assets/bootstrap-italia/css/bootstrap-italia.min.css`, aggiornato dal passo 3; con lo stile personalizzato carica `assets/css/bootstrap-italia-custom.min.css`, una copia completa del CSS della libreria che solo questo comando rigenera. Il JavaScript viene invece sempre da `assets/bootstrap-italia/`. Saltando questo passo, i siti con lo stile personalizzato avrebbero il JavaScript della nuova versione con il CSS della versione precedente.

5. Verificare la versione installata:

   ```powershell
   node -p "require('./node_modules/bootstrap-italia/package.json').version"
   ```

   Verificare inoltre che `assets/bootstrap-italia/version.js` e il bundle JavaScript dichiarino la stessa versione.

6. Controllare il diff e verificare il sito con hard refresh: nessun 404 per CSS, JavaScript, font o sprite SVG, nessun errore JavaScript e componenti interattivi funzionanti (header, menu mobile, modali, dropdown, carousel e cookie banner).

7. Committare `package.json`, `package-lock.json`, `SETUP/npm_scripts/sync-bootstrap-italia.mjs`, gli asset modificati in `assets/bootstrap-italia/` e i CSS rigenerati in `assets/css/` (`bootstrap-italia-custom.min.css` e la sua mappa).

## Aggiornamenti proposti da Dependabot

Dependabot apre una pull request quando esce una nuova versione di Bootstrap Italia, ma quella pull request aggiorna soltanto `package.json` e `package-lock.json` (il passo 1). Non va unita da sola, perché lascerebbe gli asset del tema alla versione precedente: si esegue invece in locale l'intera procedura, con la versione proposta, e si fa il push su `dev`. Dependabot riconosce che la dipendenza è già aggiornata e chiude la pull request da solo; se resta aperta, la si chiude a mano indicando il commit che la sostituisce. Non usare i comandi `@dependabot ignore`, che bloccherebbero anche gli aggiornamenti futuri.

## Nota su Bootstrap Italia 3

Bootstrap Italia 3 usa custom property CSS `--bsi-*` per la tematizzazione. Gli override del brand del tema sono in `assets/css/custom-colors.css`; la rigenerazione Sass non sostituisce tali override.
