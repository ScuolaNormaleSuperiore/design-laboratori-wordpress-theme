# Importare pagine da CSV

Usa `Strumenti DLI -> Importa -> Pagine` per creare un albero di pagine fino a tre livelli. Lo strumento è destinato a chi ha i permessi di configurazione del sito e di pubblicazione delle pagine.

## Prima di iniziare

Scarica dalla schermata il modello CSV oppure gli esempi italiano e inglese. Gli esempi creano contenuti dimostrativi e non vanno importati su un sito di produzione. Fai un backup prima dell'importazione reale.

L'intestazione richiesta è:

```text
level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards
```

Le colonne facoltative sono `page_key`, `excerpt` e `slug`. Ogni riga contiene un solo titolo, in uno dei tre livelli. `menu_order` stabilisce l'ordine; i due flag accettano solo `1` o `0`.

## Procedura

1. Seleziona il file CSV e la lingua.
2. Lascia attiva la simulazione e leggi l'intero report.
3. Correggi ogni errore segnalato; l'importazione non deve avere errori.
4. Se necessario, attiva il testo Lorem ipsum solo per prove locali.
5. Conferma l'importazione e verifica pagine, gerarchia, menu e home.

Per un sito IT/EN importa un file per lingua e usa la stessa `page_key` nelle righe corrispondenti: così le traduzioni vengono collegate. La chiave ammette minuscole, numeri, trattini e underscore, fino a 64 caratteri.

## Limiti e problemi frequenti

L'importatore accetta al massimo 200 pagine. Crea solo pagine nuove: non aggiorna, adotta o cancella pagine esistenti. Un indirizzo già usato blocca l'intera importazione. Se ripeti un file già importato, le pagine riconosciute vengono ignorate e non duplicate.

Controlla che ogni sottopagina segua una pagina del livello superiore. Dopo l'importazione, verifica che `Promuovi in Home` e `Mostra sottopagine come card` producano il risultato desiderato nelle sezioni della home.
