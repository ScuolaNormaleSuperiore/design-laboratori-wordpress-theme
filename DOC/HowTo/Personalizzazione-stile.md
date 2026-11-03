# Personalizzazione dello stile del tema

In *WP → Configurazione → Altro → Stile del sito* è possibile scegliere tra:

- **Stile Bootstrap Italia standard**: colori e font predefiniti della
  libreria Bootstrap Italia.
- **Stile personalizzato**: CSS base di Bootstrap Italia con gli override di
  colore del brand del tema.

Il foglio `assets/css/main.css` viene caricato con entrambi gli stili e
contiene le regole specifiche del tema.

## Personalizzare i colori

Bootstrap Italia 3 usa custom property CSS con prefisso `--bsi-`. Per
modificare i colori dello stile personalizzato intervenire in
[`assets/css/custom-colors.css`](../../assets/css/custom-colors.css): il file
viene caricato dopo il CSS base solo quando è selezionato lo **Stile
personalizzato**. Gli override del brand restano quindi separati dal CSS
generato della libreria.

Ad esempio, le proprietà `--bsi-color-background-primary`,
`--bsi-color-link` e `--bsi-icon-primary` definiscono rispettivamente il
colore primario, quello dei collegamenti e quello delle icone. Quando si
modificano i colori, verificare in particolare contrasto, stati hover e testo
su sfondo colorato.

## Personalizzare i font

Il tema non sovrascrive oggi i font di Bootstrap Italia 3: rimane in uso
Titillium Web. Se necessario, è possibile definire in `custom-colors.css` le
custom property dei font di Bootstrap Italia (per esempio
`--bsi-font-sans`, `--bsi-font-serif` e `--bsi-font-monospace`) e verificare
la resa di titoli, testi e componenti. Questa modifica si applica soltanto
allo **Stile personalizzato**.

Risorse utili:

- [Personalizzazione di Bootstrap Italia](https://italia.github.io/bootstrap-italia/docs/come-iniziare/personalizzazione-della-libreria)
- [Tool per la codifica dei colori](https://rgb.to/)
- [Google Fonts](https://fonts.google.com/)

## Rigenerare il CSS di Bootstrap Italia

Il file Sass
[`assets/scss/bootstrap-italia-custom.scss`](../../assets/scss/bootstrap-italia-custom.scss)
genera il CSS base
[`assets/css/bootstrap-italia-custom.min.css`](../../assets/css/bootstrap-italia-custom.min.css).
Non usare il CSS generato al posto degli override del brand.

Preparare l'ambiente locale con:

```sh
npm ci
```

Rigenerare il CSS compilato con il comando adatto al sistema operativo solo
dopo un aggiornamento di Bootstrap Italia, oppure quando
`bootstrap-italia-custom.scss` contiene una personalizzazione SCSS realmente
supportata dalla versione della libreria in uso:

```sh
npm run update_layout_win
# oppure
npm run update_layout_linux
```

Le vecchie variabili Sass di colore e font presenti nell'entrypoint non hanno
effetto con Bootstrap Italia 3. Per modificare il brand intervenire invece in
`assets/css/custom-colors.css`; per le regole specifiche del tema intervenire
in `assets/css/main.css`. Nessuno di questi due file richiede di eseguire
`update_layout_win` o `update_layout_linux`.

Per aggiornare la libreria Bootstrap Italia seguire la guida
[Come aggiornare Bootstrap Italia](HowTo%20Aggiornare%20Bootstrap%20Italia.md).
