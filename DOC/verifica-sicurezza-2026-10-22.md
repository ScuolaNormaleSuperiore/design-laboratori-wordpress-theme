# Verifica di sicurezza del tema — 22 ottobre 2026 (aggiornamento)

Aggiornamento della verifica [`verifica-sicurezza-2026-09-18.md`](verifica-sicurezza-2026-09-18.md): per ciascun rilievo S1-S6 è stato ricontrollato il codice attuale per capire cosa è stato corretto, cosa resta aperto e se le modifiche svolte nel frattempo (le 4 nuove sezioni home, `single-pubblicazione.php`, il pannello "Sezioni HP") introducono problemi nuovi. Nessuna correzione applicata in questo passaggio salvo dove esplicitamente indicato; nessun commit o push eseguito. Verifica di codice, non un pentest.

## Stato dei rilievi precedenti

| ID | Stato | Nota |
|---|---|---|
| S1 — Bypass password nei dettagli | **Corretto (22/10)** | `single-pubblicazione.php` aveva una nuova istanza del problema; aggiunto `post_password_required()` + `get_the_password_form()`, verificato end-to-end nel demo |
| S2 — SVG senza sanitizzazione | **Invariato** | Nessun sanitizzatore aggiunto |
| S3 — File di sviluppo raggiungibili | **Non riproducibile in produzione** | `phpinfo.php` e `demolabdb.sql` sono ancora nel repository, ma l'autore del progetto conferma direttamente che il deploy di produzione non li espone (nessuno dei due percorsi è raggiungibile sui siti reali) — rischio confinato al solo container demo locale |
| S4 — Paginazione senza limite massimo | **Corretto (22/10)** | `archive-progetti.php` allineato all'allowlist; clamp al massimo ammesso aggiunto a progetti/blog/luoghi/cerca; query delle tipologie di ricerca alleggerita |
| S5 — CAPTCHA riutilizzabile | **Corretto (22/10)** | Aggiunta `remove()` dopo `check()` in Contatti e Newsletter, verificato che il replay della stessa challenge ora fallisce |
| S6 — Colonne nascoste comunque nel JSON | **Corretto (22/10)** | Telefono ora escluso dal JSON quando nascosto, come l'email; link diretto validato con `esc_url_raw()` |

## S1 — Nuova istanza in `single-pubblicazione.php`

**Riferimento:** `single-pubblicazione.php:18, 88-127`.

Il template, creato per la nuova scheda di dettaglio delle Pubblicazioni, riprende lo stesso pattern già rilevato altrove: `apply_filters( 'the_content', $post->post_content )` senza alcun controllo preventivo di `post_password_required()`. In più, qui il problema è più esteso che nei template originali: oltre alla descrizione, anche il blocco "Autori" (nomi e link alle schede persona, righe 88-107) e il bottone "Consulta la pubblicazione" con l'URL esterno (righe 116-127) vengono mostrati incondizionatamente, prima ancora della descrizione. Su una pubblicazione protetta da password, un visitatore anonimo vede quindi autori, link esterno e corpo completo senza mai inserire la password.

**Correzione applicata (22/10):** aggiunto `$dli_password_required = post_password_required( $post );` subito dopo `get_header()`; autori, descrizione e url vengono ora azzerati a monte quando vero, e `get_the_password_form( $post )` viene mostrato al loro posto. Verificato nel demo impostando una password su una pubblicazione reale: con password impostata il form compare e i blocchi autori/descrizione/link sono assenti dall'HTML; rimossa la password, il contenuto torna visibile normalmente.

## S3 — File di sviluppo: confinati al demo locale

L'autore del progetto conferma direttamente che, sui siti di produzione reali, né `phpinfo.php` né il dump SQL locale sono raggiungibili. Il rilievo originale resta quindi valido **solo per il container demo** (`SETUP/Docker`, pensato per clonare l'intero repository sotto la document root a scopo dimostrativo) e non rappresenta un'esposizione concreta sui deployment reali. Resta comunque buona norma non distribuire questi file nel repository del tema, ma la priorità operativa scende: non richiede un intervento urgente sui siti in produzione.

## S5 — CAPTCHA: fix applicato

**Riferimento:** `page-templates/contatti.php`, `page-templates/newsletter.php`.

Aggiunta la chiamata `$dli_captcha_obj->remove( $dli_captcha_prefix )` subito dopo `check()`, in entrambi i file, indipendentemente dall'esito (valido o non valido) — così la challenge viene consumata immediatamente invece di restare riutilizzabile fino alla sua scadenza naturale. Verificato isolatamente con un CAPTCHA sintetico (stesso approccio della verifica di settembre): primo `check()` dopo la generazione restituisce `true`; dopo `remove()`, un secondo `check()` con la stessa coppia prefix/risposta (replay) restituisce `false`.

## S4 — Dettaglio per file (corretto)

Ricontrollati i quattro template "primari" citati nel rilievo originale, più i sei "altri ingressi":

| Template | Stato attuale |
|---|---|
| `page-templates/ricerca.php`, `brevetti.php`, `spinoff.php`, `eventi.php`, `notizie.php` | **Già corretti**: allowlist (`in_array` contro `DLI_POST_PER_PAGE_VALUES`) |
| `page-templates/archive-progetti.php` | **Corretto (22/10)**: convertito alla stessa allowlist degli altri cinque template (prima accettava `is_numeric()`, incluso `-1`, senza alcun limite) |
| `page-templates/progetti.php`, `blog.php`, `luoghi.php`, `cerca.php` | **Corretto (22/10)**: aggiunto `min( absint( $input ), (int) max( $dli_per_page_values ) )` dopo `FILTER_VALIDATE_INT`, così un intero positivo valido ma enorme viene comunque riportato al massimo ammesso |
| `get_contenttypes_with_search_results()` (`class-contents-manager.php:1071`) | **Corretto (22/10)**: `fields` passato da `'all'` a `'id=>post_type'` (serve solo il post_type di ogni corrispondenza) — stesso `posts_per_page => -1`, legittimo qui perché è un'aggregazione lato server su input già filtrato, non una paginazione esposta direttamente all'utente |

**Verifica:** per `archive-progetti.php` e `progetti.php` è stato aggiunto temporaneamente un marcatore HTML con il valore di `$per_page` calcolato, osservato via richieste HTTP reali al demo con `per_page=-1`, `per_page=999999`, un valore valido e uno fuori lista, poi rimosso. `archive-progetti.php`: `-1`/`999999`/non in lista → tutti riportati al default (6); valore in lista → accettato. `progetti.php`: `999999` → riportato a 96 (massimo ammesso); `-5` → default; valore in lista → accettato. `blog.php`/`luoghi.php`/`cerca.php` condividono lo stesso identico pattern, non riverificati singolarmente via HTTP. `get_contenttypes_with_search_results()` verificata in isolamento: stesso risultato (stessi post type distinti) prima e dopo il cambio di `fields`.

## S6 — Colonne nascoste e link diretto (corretto)

**Riferimento:** `template-parts/persone/view-tabella.php:60-105`.

Il telefono ora segue lo stesso pattern già usato per l'email: `$dli_tb_r_phone`/`$dli_tb_r_phone_href` restano stringa vuota quando `hide_people_table_phone` è attivo, invece di essere valorizzati incondizionatamente e finire comunque nel JSON. Il link diretto (`$dli_tb_r_link`, popolato dal campo ACF "Sito web" quando l'opzione "link diretto" è attiva) passa ora per `esc_url_raw()` prima di entrare nell'oggetto JSON.

**Verifica:** sulla pagina Persone (vista tabella) del demo, con `hide_people_table_phone` attivo, tutte le 5 righe presenti risultano con `"phone":""`; con `hide_people_table_email` disattivo le email restano correttamente popolate (nessuna regressione). Impostato temporaneamente su una persona reale un link diretto con valore `javascript:alert(1)//http://evil.test`: dopo la correzione il campo `"link"` per quella riga risulta stringa vuota (la stringa viene scartata integralmente da `esc_url_raw()`, che non ammette lo schema `javascript:`), mentre i permalink legittimi delle altre persone restano invariati. Dato di test rimosso subito dopo la verifica.

## Nessun problema nuovo rilevato nelle altre aggiunte

Controllati puntualmente, senza trovare pattern insicuri aggiuntivi:

- Le 4 nuove sezioni home (`hp-navigation-section.php`, `hp-fasi-section.php`, `hp-page-index-section.php`, `hp-tabs-section.php`): nessun output non filtrato (`echo $variabile` sempre con `esc_html`/`esc_attr`/`esc_url`), `wp_kses_post()` sul solo campo WYSIWYG "descrizione" del Pannello tab, coerente con il livello di fiducia del ruolo Super Editor che lo compila.
- `featured-contents.php` (fix larghezza colonne di questa sessione): nessun input esterno coinvolto, solo opzioni di configurazione e conteggio post.
- `page.php` (flag "Mostra le sottopagine come card"): `get_posts()` con `post_parent` sempre un ID intero del post corrente, nessun input utente.
- `inc/admin/options.php` / `inc/admin-js/options.js` (frecce di riordino "Sezioni HP"): pannello riservato a Super Editor/Amministratore già autenticati; la CSS inline aggiunta è una stringa statica, nessun dato dinamico interpolato.

## Non ricontrollato in questo passaggio

S2 è stato solo verificato come "ancora presente" con un controllo puntuale del codice indicato nella verifica di settembre, senza ripetere la prova end-to-end (upload SVG sintetico) già documentata allora: le conclusioni di merito restano quelle del documento di settembre.

## Residuo aperto

Dopo questo aggiornamento resta aperto solo **S2** (SVG senza sanitizzazione, priorità media — rischio limitato a ruoli già fidati con `upload_files`, non a visitatori anonimi). Tutti gli altri rilievi (S1, S3, S4, S5, S6) risultano corretti o non riproducibili in produzione, verificati come descritto sopra.
