# Come aggiornare la libreria Bootstrap Italia

## Procedura

1. Scaricare la versione aggiornata di **Bootstrap Italia** da questo link: https://italia.github.io/bootstrap-italia/docs/come-iniziare/introduzione
2. Scompattare il contenuto del file zip.
3. Svuotare la cartella **"design-laboratori-wordpress-theme\assets\bootstrap-italia"** all'interno del tema.
4. Copiare dentro **"design-laboratori-wordpress-theme\assets\bootstrap-italia"** il contenuto della cartella bootstrap-italia appena scaricata.
5. Modificare il file ***packages.json*** e aggiornare la voce:
```
"dependencies": {
    "bootstrap-italia": "^<nuova_versione>"
  },
```
6. Modificare anche il file *README.md* alla riga:
```
Il progetto nasce da un fork del tema [**Design Scuole Italia**](https://developers.italia.it/it/software/istsc_blps020006-italia-design-scuole-wordpress-theme.html) ed utilizza la libreria [***Bootstrap Italia <nuova_versione>***]
```
7. Entrare con la shell nella directory principale del template: ***design-laboratori-wordpress-theme***.
8. Eseguire il comando ***npm install***
9. Eseguire il comando ***npm run update_layout_win*** o ***npm run update_layout_linux***. Questo comando produce un nuovo file *bootstrap-italia-custom.min* che va a sovrascrivere quello esistente.
10. Verificare che sia caricata la versione corretta di ***Bootstrap Italia***.
