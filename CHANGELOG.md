# Change Log

Tutte le modifiche notevoli a questo progetto saranno documentate in questo file.

Il formato è basato su [Keep a Changelog](http://keepachangelog.com/)
e questo progetto segue [Semantic Versioning](http://semver.org/).


TAGS: Aggiunto (Added), Modificato (Changed), Deprecato (Deprecated), Rimosso (Removed), Corretto (Fixed), Sicurezza (Security).


## [Non rilasciato]

### Aggiunto
- Aggiunta integrazione con Brevo.

### Sicurezza
- Aggiunta gestione nonce nelle form: contatti e ricerca.
- Corretta gestione sanitize input nelle form: contatti e ricerca.
- Reso meno specifico il testo del messaggio di login errato.
- Nascosto tag "generator" nel sorgente delle pagine del sito.
- Modificata la url della pagina di login.
- Verificare opportunità di usare Content Security Policy (CSP).
- DISALLOW_FILE_EDIT.

### Modificato
- Aggiornata libreria Bootstrap Italia alla versione 2.8.2.
- Corretto codice in base ai warning generati da PHP 8.2 soprattutto sulle librerie CMB2.


## [1.2.5] - 2024-03-01


