# Importare da file CSV

In `Strumenti DLI -> Importa` alcuni contenuti si creano da un file CSV preparato con Excel, LibreOffice o un editor di testo. Questa guida descrive prima il formato del file, poi ogni importazione.

## Formato del file

Le regole di questa sezione valgono per tutte le importazioni da file: pagine e persone.

- **Codifica UTF-8.** Un file in un'altra codifica viene rifiutato. In Excel scegli *Salva con nome → CSV UTF-8 (delimitato da virgole)* e poi controlla il separatore; in LibreOffice scegli il set di caratteri *Unicode (UTF-8)*.
- **Separatore punto e virgola (`;`).** La virgola non separa le colonne: fa parte del testo della cella.
- **Virgolette facoltative.** Servono solo quando una cella contiene un punto e virgola, un a capo o delle virgolette; le virgolette dentro la cella si scrivono doppie (`""`). Excel e LibreOffice le aggiungono da soli quando servono.
- **Intestazione nella prima riga.** I nomi delle colonne si possono scrivere in maiuscolo o in minuscolo e in qualsiasi ordine.
- **Colonne obbligatorie.** Se ne manca una, il file viene rifiutato prima di iniziare e il messaggio indica quale. Una colonna prevista ripetuta due volte rifiuta il file.
- **Altre colonne.** Le colonne non previste vengono ignorate, anche se contengono dati, ed elencate nel report alla voce *Colonne ignorate*: controlla l'elenco, perché un nome scritto male (per esempio `pagekey` invece di `page_key`) finisce tra le colonne ignorate.
- **Righe vuote.** Le righe senza dati nelle colonne previste, comprese quelle fatte di soli `;`, vengono saltate. I numeri di riga dei messaggi sono quelli del file.
- **Righe corte.** Una riga che non arriva all'ultima colonna prevista è un errore di quella riga.
- **Limiti.** Al massimo 200 righe con dati e 2 MB.

## Pagine

`Strumenti DLI -> Importa -> Pagine` crea un albero di pagine fino a tre livelli. Lo strumento è destinato a chi ha i permessi di configurazione del sito e di pubblicazione delle pagine.

### Prima di iniziare

Scarica dalla schermata il modello CSV oppure gli esempi italiano e inglese. Gli esempi creano contenuti dimostrativi e non vanno importati su un sito di produzione. Fai un backup prima dell'importazione reale.

Le colonne obbligatorie sono:

```text
level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards
```

Ogni riga contiene un solo titolo, in uno dei tre livelli. `menu_order` stabilisce l'ordine tra le pagine dello stesso livello; i due flag accettano solo `1` o `0`.

Le colonne facoltative sono:

- `page_key`: la chiave che collega le traduzioni (vedi sotto);
- `excerpt`: il riassunto della pagina;
- `slug`: l'indirizzo, se diverso da quello ricavato dal titolo.

### Procedura

1. Seleziona il file CSV e la lingua.
2. Lascia attiva la simulazione e leggi l'intero report, comprese le eventuali colonne ignorate.
3. Correggi ogni errore segnalato; l'importazione non deve avere errori.
4. Se necessario, attiva il testo Lorem ipsum solo per prove locali.
5. Conferma l'importazione e verifica pagine, gerarchia, menu e home.

### Pagine in più lingue: `page_key`

Per un sito IT/EN prepara un file per lingua e importa ciascuno scegliendo la sua lingua. La `page_key` è una chiave che scegli tu: scrivi la stessa chiave nella riga italiana e nella riga inglese della stessa pagina, e l'importazione collega le due pagine come traduzioni. Non è l'ID della pagina né il suo indirizzo, quindi puoi preparare i due file prima di importarli, importarli in qualsiasi ordine e usarli su siti diversi.

```text
level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards;page_key
Dati della ricerca;;;2;1;1;research-data
```

```text
level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards;page_key
Research Data;;;2;1;1;research-data
```

La chiave è facoltativa; quando c'è, ha da 1 a 64 caratteri tra lettere minuscole, numeri, trattini e underscore, e non può ripetersi nello stesso file. Il valore non viene convertito: `Ricerca` non è una chiave valida, `ricerca` sì. Il report mostra la chiave di ogni pagina nella colonna `page_key`.

La chiave resta salvata nella pagina creata, in un campo nascosto: per questo collega soltanto pagine create dall'importazione, non pagine create a mano.

### Limiti e problemi frequenti

L'importatore accetta al massimo 200 pagine. Crea solo pagine nuove: non aggiorna, adotta o cancella pagine esistenti. Un indirizzo già usato blocca l'intera importazione. Se ripeti un file già importato, le pagine riconosciute vengono ignorate e non duplicate.

Controlla che ogni sottopagina segua una pagina del livello superiore. Dopo l'importazione, verifica che `Promuovi in Home` e `Mostra sottopagine come card` producano il risultato desiderato nelle sezioni della home.

## Persone

`Strumenti DLI -> Importa -> Persone` crea o aggiorna le schede delle persone. Lo strumento è destinato a chi ha i permessi di configurazione del sito.

### Prima di iniziare

Scarica dalla schermata il CSV di esempio: contiene tutte le colonne e due persone dimostrative. Fai un backup prima dell'importazione reale.

Le colonne obbligatorie sono `nome`, `cognome` ed `email`. Le colonne facoltative sono:

| Colonna | Contenuto | Se manca o è vuota |
|---|---|---|
| `titolo` | Titolo della persona: `Cav.`, `Comm.`, `Dott.`, `Dott.ssa`, `Dr.`, `Prof.`, `Prof.ssa`, `Ing.`, `Avv.`, `Rag.`, `Sig.`, `Sig.ra` | Nessun titolo |
| `telefono` | Numero di telefono | Vuoto |
| `tipologia_persona` | Slug di una o più tipologie già esistenti (vedi sotto) | Nessuna tipologia |
| `struttura` | Slug di una o più strutture già esistenti (vedi sotto) | Nessuna struttura |
| `stato` | `publish` (pubblicata) o `draft` (bozza) | `publish` |
| `sito_web` | Indirizzo del sito personale | Vuoto |
| `body` | Testo della scheda: testo semplice o HTML di base (paragrafi, grassetto, corsivo) | Vuoto |
| `escludi_da_elenco` | `1` per non mostrare la persona negli elenchi, `0` altrimenti | `0` |
| `disattiva_pagina_dettaglio` | `1` per disattivare la pagina della persona, `0` altrimenti | `0` |

In `tipologia_persona` e `struttura` più valori si separano con la barra verticale, per esempio `prima-struttura|seconda-struttura`. Le tipologie e le strutture che non esistono vengono ignorate. Un testo con punti e virgola o su più righe, come spesso il `body`, va tra virgolette (Excel e LibreOffice lo fanno da soli).

### Procedura

1. Seleziona il file CSV.
2. Scegli la modalità di importazione. La persona viene riconosciuta dalla sua `email`:
   - `upsert`: crea le persone nuove e aggiorna quelle esistenti;
   - `solo nuovi` e `ignora esistenti`: crea solo le persone nuove e salta quelle esistenti;
   - `aggiorna esistenti`: aggiorna solo le persone esistenti e salta quelle nuove.
3. Lascia la modalità di esecuzione su `dry_run` (simulazione) e leggi il report, comprese le eventuali colonne ignorate.
4. Correggi le righe con errori, poi esegui con `commit`.

**Attenzione agli aggiornamenti.** Quando una persona esiste già, l'importazione riscrive tutti i suoi campi con i valori del file: una colonna facoltativa assente o una cella vuota cancella il valore presente sul sito (per esempio il telefono, le strutture o il testo della scheda). Per `stato` vale questo: se la colonna manca, la persona viene pubblicata (`publish`); se c'è ma la cella è vuota o ha un valore diverso da `publish` e `draft`, lo stato attuale non cambia. Per aggiornare solo alcune persone, metti nel file tutte le loro colonne con i valori completi.

### Errori

Un file che non rispetta il formato (codifica, colonne obbligatorie, dimensione, numero di righe) viene rifiutato prima di iniziare e nessuna persona viene creata. Gli errori di una riga invece riguardano solo quella riga: una riga senza nome, cognome o email valida, una riga troppo corta o un'email che appartiene a più persone vengono segnalate nel report con il loro numero, e le altre righe vengono elaborate.
