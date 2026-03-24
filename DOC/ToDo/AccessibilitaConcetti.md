# Accessibilità Web — Concetti e Meccanismi

Documento di riferimento per comprendere gli errori di accessibilità trovati nel tema. Per ogni problema si spiega il meccanismo sottostante, la regola WCAG violata e il modo corretto di gestirlo.

---

## 1. `aria-labelledby` — Come funziona davvero

### Il problema trovato nel codice

```html
<svg class="icon" role="img" aria-labelledby="Arrow right">
    <title>Arrow right</title>
</svg>
```

`aria-labelledby="Arrow right"` **non** assegna il testo "Arrow right" come etichetta. L'attributo si aspetta l'**ID di un elemento HTML esistente nel DOM**, non testo libero.

Il lettore di schermo cerca `document.getElementById("Arrow right")` — non trova nulla — e ignora il nome accessibile dell'SVG.

### Come funziona `aria-labelledby`

`aria-labelledby` costruisce il nome accessibile raccogliendo il `textContent` degli elementi referenziati. Si possono anche concatenare più ID separati da spazio:

```html
<span id="first">Blue</span> <span id="second">Widget</span>
<img aria-labelledby="first second" />
<!-- Il lettore di schermo annuncia: "Blue Widget" -->
```

### Differenza con `aria-label`

| | `aria-label` | `aria-labelledby` |
|---|---|---|
| Contiene | Il testo direttamente | L'`id` di un elemento |
| Testo visibile | No (solo per AT) | Sì (il testo referenziato è nel DOM) |
| Molteplici sorgenti | No | Sì (più ID separati da spazio) |

### Il modo corretto per gli SVG

**SVG informativo** (comunica qualcosa all'utente):
```html
<svg role="img" aria-labelledby="svg-arrow-title">
    <title id="svg-arrow-title">Vai alla pagina successiva</title>
    <use href="sprites.svg#it-arrow-right"></use>
</svg>
```

Oppure, più semplice:
```html
<svg role="img" aria-label="Vai alla pagina successiva">
    <use href="sprites.svg#it-arrow-right"></use>
</svg>
```

**SVG decorativo** (l'informazione è già nel testo del link/bottone che lo contiene):
```html
<a href="/notizie/titolo">
    Leggi di più
    <svg aria-hidden="true" focusable="false">
        <use href="sprites.svg#it-arrow-right"></use>
    </svg>
</a>
```
Il testo del link è già "Leggi di più": l'icona freccia non aggiunge informazione, quindi si nasconde con `aria-hidden="true"`. Nota anche `focusable="false"` che serve su IE/Edge per impedire che l'SVG riceva il focus.

### Regola pratica

> Se l'SVG si trova dentro un `<a>` o `<button>` che ha già testo visibile → `aria-hidden="true"`.
> Se l'SVG è l'unico contenuto significativo → `role="img"` + `aria-label="descrizione"`.

**Criterio WCAG:** 1.1.1 Non-text Content (Livello A), 4.1.2 Name, Role, Value (Livello A)

---

## 2. `aria-hidden` — Quando usarlo e quando no

### Il problema trovato nel codice

```html
<img src="avatar.jpg"
     alt="Mario Rossi, Ricercatore"
     aria-hidden="true">
```

Questo codice è contraddittorio: l'`alt` descrive l'immagine come informativa, ma `aria-hidden="true"` la nasconde completamente al lettore di schermo. **`aria-hidden` ha la precedenza**: il lettore di schermo non legge né l'immagine né il suo `alt`.

### Come funziona `aria-hidden`

`aria-hidden="true"` rimuove l'elemento (e tutti i suoi discendenti) dall'**accessibility tree** — la rappresentazione della pagina che il lettore di schermo utilizza. L'elemento rimane visibile sullo schermo ma è come se non esistesse per chi usa uno screen reader.

**Regola critica:** Non usare mai `aria-hidden="true"` su elementi che ricevono focus (link, button, input). L'elemento diventerebbe navigabile da tastiera ma silenzioso: l'utente ci finisce sopra senza sentire nulla.

### Immagine decorativa vs informativa

**Immagine decorativa** — non aggiunge informazione, è già spiegata dal testo circostante:
```html
<!-- Opzione 1: alt vuoto (standard HTML) -->
<img src="avatar.jpg" alt="">

<!-- Opzione 2: aria-hidden (equivalente per i casi semplici) -->
<img src="avatar.jpg" alt="" aria-hidden="true">
```

**Immagine informativa** — comunica qualcosa che non è detto altrove:
```html
<!-- alt descrittivo, NIENTE aria-hidden -->
<img src="avatar.jpg" alt="Mario Rossi, Ricercatore Senior">
```

### Come decidere nel caso dell'avatar persona

L'avatar in `sezione-persone.php` appare in una card che contiene già il nome e il ruolo della persona come testo visibile. In questo caso l'immagine è **decorativa rispetto al contesto**: il nome è già leggibile. La soluzione corretta è:

```html
<img src="<?php echo esc_url( $avatar ); ?>"
     alt=""
     aria-hidden="true">
```

Se invece la card non avesse il nome come testo, l'immagine sarebbe informativa e bisognerebbe togliere `aria-hidden` e tenere l'`alt` con il nome.

**Criterio WCAG:** 1.1.1 Non-text Content (Livello A)

---

## 3. Alt text per i CAPTCHA

### Il problema trovato nel codice

```html
<img src="captcha.png" alt="captcha">
```

L'`alt="captcha"` descrive il **tipo** di immagine, non il suo **scopo**. Un utente con screen reader sente "immagine, captcha" e non capisce cosa deve fare.

### Cosa dice WCAG 1.1.1 sui CAPTCHA

WCAG riconosce che i CAPTCHA visivi sono intrinsecamente inaccessibili (non puoi mettere come `alt` la soluzione, altrimenti i bot la leggerebbero). Il criterio prevede un'eccezione specifica con due requisiti:

1. L'`alt` deve descrivere lo **scopo** del CAPTCHA (non la soluzione, non il tipo).
2. Devono esistere **almeno due modalità di input** per sensi diversi (visivo + audio, oppure visivo + domanda logica).

### Alt text corretto

```html
<img src="captcha.png"
     alt="Immagine di verifica: inserisci i caratteri visualizzati nel campo sottostante">
```

### Soluzione completa accessibile

```html
<!-- Modalità 1: visiva -->
<img src="captcha.png"
     alt="Immagine di verifica sicurezza: inserisci i caratteri mostrati">

<!-- Modalità 2: alternativa audio (ideale) -->
<a href="captcha-audio.mp3" aria-label="Ascolta il CAPTCHA audio">
    <svg aria-hidden="true"><use href="#it-hearing"></use></svg>
    Ascolta
</a>

<input type="text"
       id="captcha-field"
       aria-label="Inserisci i caratteri della verifica">
```

In assenza dell'audio CAPTCHA (come nel plugin Really Simple Captcha), almeno l'`alt` deve essere descrittivo dello scopo.

**Criterio WCAG:** 1.1.1 Non-text Content — Eccezione CAPTCHA (Livello A)

---

## 4. Link "Leggi di più" — Il problema del link purpose

### Il problema trovato nel codice

```html
<a class="read-more" href="/notizia/titolo-articolo">
    <span>Leggi di più</span>
</a>
```

Un utente di screen reader che naviga i link della pagina sente: "Leggi di più. Leggi di più. Leggi di più." Non sa a cosa si riferisce nessuno di questi link.

### Come funziona WCAG 2.4.4

WCAG 2.4.4 (Link Purpose in Context) permette due approcci:

- **Link only**: il testo del link da solo è sufficiente a capire la destinazione.
- **In context**: il testo del link + il testo circostante (heading della card, paragrafo contenitore, elemento lista) insieme rendono chiaro lo scopo.

"Leggi di più" non soddisfa né l'uno né l'altro: anche con il contesto visivo, il lettore di schermo che naviga in modalità lista-link estrae solo il testo del link.

### Attenzione a WCAG 2.5.3 (Label in Name)

Se si usa `aria-label`, questo **deve contenere il testo visibile** del link. Quindi `aria-label="Dettagli articolo"` su un link con testo "Leggi di più" **viola** WCAG 2.5.3, perché gli utenti di voice control che dicono "clicca Leggi di più" non troverebbero il link.

Corretto: `aria-label="Leggi di più su [titolo articolo]"` — contiene "Leggi di più".

### Soluzioni corrette

**Opzione 1 — `aria-labelledby` sull'heading della card (preferita):**
```html
<article>
    <h3 id="card-title-<?php echo $dli_id; ?>">
        <?php echo esc_html( $dli_title ); ?>
    </h3>
    <p><?php echo esc_html( $dli_excerpt ); ?></p>
    <a href="<?php echo esc_url( $dli_link ); ?>"
       aria-labelledby="card-title-<?php echo $dli_id; ?>">
        Leggi di più
    </a>
</article>
<!-- Il lettore di schermo annuncia il titolo dell'articolo come nome del link -->
```

**Opzione 2 — `aria-label` che include il testo visibile:**
```html
<a href="<?php echo esc_url( $dli_link ); ?>"
   aria-label="Leggi di più su <?php echo esc_attr( $dli_title ); ?>">
    Leggi di più
</a>
```

**Opzione 3 — testo visivamente nascosto dentro il link:**
```html
<a href="<?php echo esc_url( $dli_link ); ?>">
    Leggi di più
    <span class="visually-hidden">
        su <?php echo esc_html( $dli_title ); ?>
    </span>
</a>
```

**Criterio WCAG:** 2.4.4 Link Purpose in Context (Livello A), 2.5.3 Label in Name (Livello A)

---

## 5. Heading hierarchy — Perché la sequenza conta

### Il problema trovato nel codice

In `single-evento.php` la struttura è approssimativamente:
```
<h2> Titolo evento
  <h3> Dettagli (sidebar)
    <h4> Date e orari
    <h3 class="h4"> Luogo     ← h3 con stile h4: semantica e aspetto discordano
    <h3 class="h4"> Contatti
```

### Come i lettori di schermo usano gli heading

Gli heading HTML (h1–h6) creano un **outline del documento**, equivalente all'indice di un libro. I lettori di schermo offrono una modalità di navigazione dedicata: l'utente può saltare di heading in heading (tasto H in NVDA/JAWS) o visualizzare l'elenco degli heading della pagina per orientarsi.

Quando la gerarchia salta livelli (h2 → h4), l'outline diventa incoerente: l'utente si aspetta h3 come figlio di h2 e non trova nulla, poi trova h4 come "figlio" di h2, il che logicamente non ha senso.

### La regola

- **Non saltare livelli in avanti**: dopo un `<h2>` può venire solo `<h3>`, non `<h4>`.
- **Si può risalire**: dopo un `<h4>` si può tornare a `<h2>` (fine di una sezione, inizio di un'altra).
- **Un solo `<h1>` per pagina** che descrive l'argomento principale.
- **Usare le classi per lo stile**, non per la semantica: `<h3 class="h4">` è visivamente un h4 ma semanticamente un h3 — va bene se la gerarchia è corretta; va male se serve solo per far sembrare un h4 quello che è concettualmente un h2.

### Struttura corretta per pagina con sidebar

```html
<!-- Layout a due colonne: contenuto + sidebar -->
<h1>Titolo pagina (o h2 se h1 è nel template globale)</h1>

<main>
    <h2>Prima sezione del contenuto</h2>
    <p>...</p>
    <h2>Seconda sezione</h2>
    <h3>Sotto-sezione</h3>
</main>

<aside>
    <!-- La sidebar ha la propria gerarchia indipendente -->
    <h2>Dettagli evento</h2>
    <h3>Date e orari</h3>
    <h3>Luogo</h3>
    <h3>Contatti</h3>
</aside>
```

**Criterio WCAG:** 1.3.1 Info and Relationships (Livello A)

---

## 6. ARIA Landmark Roles — banner, navigation, main

### Il problema trovato nel codice

```html
<header role="navigation">
    ...
</header>
```

### Come funzionano i landmark

I landmark sono "regioni" della pagina che i lettori di schermo usano per navigare rapidamente (tasto D in NVDA per passare al prossimo landmark). Ogni elemento HTML semantico ha un landmark implicito:

| Elemento HTML | Landmark implicito | Scopo |
|---|---|---|
| `<header>` (figlio di body) | `banner` | Logo, nome sito, ricerca globale |
| `<nav>` | `navigation` | Menu di navigazione |
| `<main>` | `main` | Contenuto principale |
| `<aside>` | `complementary` | Sidebar, contenuti correlati |
| `<footer>` (figlio di body) | `contentinfo` | Copyright, privacy, link footer |

### Perché `<header role="navigation">` è sbagliato

`role="navigation"` **sovrascrive** il landmark implicito `banner`. Il browser/AT tratta `<header>` come se fosse un `<nav>`, perdendo completamente il landmark `banner`. Risultato: la pagina ha una navigazione in più (confondente) e nessun banner.

La struttura corretta è separare i due concetti:

```html
<header>               <!-- landmark: banner -->
    <img src="logo.png" alt="Nome laboratorio">
    <nav aria-label="Menu principale">   <!-- landmark: navigation -->
        <ul>...</ul>
    </nav>
</header>
```

### Quando più nav coesistono sulla stessa pagina

Se ci sono più `<nav>`, ognuna deve avere un `aria-label` diverso per distinguerle:

```html
<nav aria-label="Menu principale">...</nav>
<nav aria-label="Breadcrumb">...</nav>
<nav aria-label="Menu secondario">...</nav>
```

**Criterio WCAG:** 1.3.1 Info and Relationships (Livello A), 2.4.1 Bypass Blocks (Livello A)

---

## 7. ID duplicati e `aria-controls` — Perché l'unicità degli ID è fondamentale

### Il problema trovato nel codice

**Caso 1 — `aria-controls` sbagliato** (`sezione-video.php`):
```html
<!-- Il trigger dichiara di controllare "transcription" -->
<button aria-controls="transcription">Mostra trascrizione</button>

<!-- Ma il pannello reale ha id="transcription9" -->
<div id="transcription9" class="collapse">...</div>
```

**Caso 2 — ID duplicati** (`secondary-menu.php`):
```html
<!-- Ogni dropdown nel menu usa lo stesso ID fisso -->
<button id="mainNavDropdown1" aria-expanded="false">Ricerca</button>
<ul aria-labelledby="mainNavDropdown1">...</ul>

<button id="mainNavDropdown1" aria-expanded="false">Persone</button>  <!-- stesso id! -->
<ul aria-labelledby="mainNavDropdown1">...</ul>
```

### Perché gli ID duplicati rompono ARIA

`aria-controls`, `aria-labelledby`, `aria-describedby` e `aria-owns` funzionano tutti allo stesso modo: il browser esegue `document.getElementById(valore)` e usa il primo elemento trovato. Se ci sono due elementi con lo stesso `id`, le relazioni ARIA delle occorrenze successive puntano all'elemento sbagliato.

L'HTML spec dichiara che gli `id` devono essere **unici nel documento**. Con `id` duplicati:
- `aria-labelledby="mainNavDropdown1"` sul secondo dropdown referenzia il **primo** bottone, non il suo.
- Il lettore di schermo legge l'etichetta sbagliata per ogni dropdown tranne il primo.
- `aria-controls="transcription"` non trova il pannello `transcription9` e la relazione è spezzata: il lettore di schermo non sa quale elemento viene espanso/collassato.

### Come funziona `aria-controls`

`aria-controls` serve a dichiarare che un elemento interattivo (button, link) controlla un altro elemento del DOM. Lo screen reader usa questa relazione per annunciare all'utente "questo bottone controlla il pannello X" e permette di saltare direttamente al pannello controllato.

```html
<!-- Corretto: id e aria-controls allineati -->
<button aria-controls="panel-trascrizione" aria-expanded="false">
    Mostra trascrizione
</button>
<div id="panel-trascrizione" class="collapse">
    <p>Testo della trascrizione...</p>
</div>
```

### Come generare ID unici in PHP

Quando un partial viene incluso più volte, l'ID deve essere generato dinamicamente:

```php
// Opzione 1: uniqid (sufficiente per la maggior parte dei casi)
$dli_panel_id = 'panel-' . uniqid();

// Opzione 2: contatore passato come argomento al partial
// Nel template chiamante:
get_template_part( 'template-parts/common/sezione-video', null, array( 'index' => $i ) );

// Nel partial:
$dli_index    = isset( $args[0] ) ? (int) $args[0] : 0;
$dli_panel_id = 'panel-video-' . $dli_index;
```

```html
<button aria-controls="<?php echo esc_attr( $dli_panel_id ); ?>">
    Trascrizione
</button>
<div id="<?php echo esc_attr( $dli_panel_id ); ?>">...</div>
```

**Criterio WCAG:** 4.1.2 Name, Role, Value (Livello A), 4.1.1 Parsing (Livello A)

---

## 8. `<a href="#">` con `onclick` — Perché rompe la navigazione da tastiera

### Il problema trovato nel codice

```php
<a href="#" onclick="redirectToPage('<?php echo $url; ?>')">
    Vai alla pagina
</a>
```

### Perché è inaccessibile

Un link con `href="#"` si comporta in modo problematico per più motivi:

1. **Senza JavaScript** il link non fa nulla (o salta all'inizio della pagina). Se JS non carica o è disabilitato, l'utente è bloccato.
2. **Con screen reader + tastiera**: l'utente preme Invio sul link, `onclick` scatta, ma se `onclick` chiama solo JS senza navigare davvero, il focus non si sposta — l'utente non sa cosa è successo.
3. **`href="#"`** annuncia il link come "hash link" ad alcuni screen reader, creando aspettative sbagliate sulla navigazione.
4. **Il pattern `href="#"` con solo JS** è semanticamente un **bottone**, non un link. I link navigano verso URL, i bottoni eseguono azioni.

### La distinzione link vs bottone

| Elemento | Semantica | Quando usarlo |
|---|---|---|
| `<a href="url">` | Link | Naviga a un'altra URL o risorsa |
| `<button>` | Bottone | Esegue un'azione (apri modale, filtra, invia form) |
| `<a href="#">` + onclick | Nessuna semantica chiara | **Da evitare** |

### Soluzioni corrette

**Se l'azione è una navigazione** → usare un link vero con URL reale:
```html
<a href="<?php echo esc_url( $dli_page_url ); ?>">
    Vai alla pagina
</a>
```

**Se l'azione è JS-only** (es. aprire un filtro, eseguire una ricerca) → usare `<button>`:
```html
<button type="button" data-target="<?php echo esc_attr( $dli_target ); ?>">
    Applica filtro
</button>
```

**Se serve navigazione JS progressiva** (funziona con e senza JS) → URL reale con JS che intercetta:
```html
<a href="<?php echo esc_url( $dli_page_url ); ?>"
   data-js-navigate>
    Vai alla pagina
</a>
```
```js
document.querySelectorAll('[data-js-navigate]').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        // logica JS
    });
});
```

**Criterio WCAG:** 2.1.1 Keyboard (Livello A), 4.1.2 Name, Role, Value (Livello A)

---

## 9. `<input type="text">` vs `<textarea>` — Semantica dei campi form

### Il problema trovato nel codice

```html
<!-- Campo "messaggio" nella form contatti implementato come input singola riga -->
<input type="text" name="messaggio" id="messaggio" value="...">
```

### Perché è un problema di accessibilità

Gli assistive technology annunciano il tipo di campo quando l'utente ci arriva. `<input type="text">` viene annunciato come "campo di testo, riga singola". L'utente capisce che può scrivere solo una riga breve — e non tenterà di comporre un messaggio lungo.

`<textarea>` viene annunciato come "area di testo, multiriga", segnalando chiaramente che è atteso un testo esteso. Inoltre:

- `sanitize_text_field()` (usato su `input`) rimuove i ritorni a capo — un messaggio con invii a capo verrebbe appiattito su una riga prima di essere salvato.
- `sanitize_textarea_field()` (corretto per `textarea`) preserva i ritorni a capo.

### Codice corretto

```html
<textarea
    name="messaggio"
    id="messaggio"
    rows="6"
    aria-required="true"
    aria-describedby="messaggio-hint"
><?php echo esc_textarea( $dli_message ); ?></textarea>
<p id="messaggio-hint" class="form-text">
    Descrivi la tua richiesta in dettaglio.
</p>
```

In PHP, sostituire la sanitizzazione:
```php
// Prima (sbagliato per testo multiriga):
$dli_message = sanitize_text_field( wp_unslash( $_POST['messaggio'] ) );

// Dopo (corretto):
$dli_message = sanitize_textarea_field( wp_unslash( $_POST['messaggio'] ) );
```

**Criterio WCAG:** 1.3.1 Info and Relationships (Livello A), indirettamente 3.3.2 Labels or Instructions (Livello A)

---

## Riepilogo: Issue trovate e meccanismo

| Issue | File | Meccanismo | Criterio WCAG |
|---|---|---|---|
| `aria-labelledby` referenzia stringhe non-ID | carousel.php, archive.php, sezione-box-notizia.php, contatti.php, newsletter.php, sezione-progetti.php | `aria-labelledby` vuole un `id`, non testo | 1.1.1, 4.1.2 (A) |
| `aria-hidden` su immagine informativa | sezione-persone.php | `aria-hidden` cancella tutto dall'AT, ha precedenza su `alt` | 1.1.1 (A) |
| Alt text CAPTCHA generico | contatti.php, newsletter.php | L'`alt` deve descrivere lo **scopo**, non il tipo | 1.1.1 eccezione CAPTCHA (A) |
| Link "Leggi di più" senza contesto | carousel.php, sezione-box-notizia.php, sezione-box-evento.php, archive.php | I link list di AT estraggono solo il testo del link | 2.4.4 (A), 2.5.3 (A) |
| Heading hierarchy saltata | single-evento.php | L'outline documento deve essere progressivo | 1.3.1 (A) |
| `<header role="navigation">` | header.php | `role` esplicito sovrascrive il landmark implicito | 1.3.1 (A), 2.4.1 (A) |
| Accordion `aria-controls` errato / ID duplicati | sezione-video.php, secondary-menu.php, hp-video-section.php | `aria-controls` e le relazioni ARIA vogliono `id` unici e corretti | 4.1.2 (A), 4.1.1 (A) |
| `<a href="#">` + onclick senza keyboard handler | persone.php, footer.php | `href="#"` non è una navigazione; le azioni JS vanno su `<button>` | 2.1.1 (A), 4.1.2 (A) |
| `<input type="text">` al posto di `<textarea>` | contatti.php | Il tipo di campo annuncia la semantica; testo multiriga richiede `<textarea>` | 1.3.1 (A) |

---

## Fonti

- [WebAIM: Introduction to ARIA](https://webaim.org/techniques/aria/)
- [WebAIM: Decoding Label and Name for Accessibility](https://webaim.org/articles/label-name/)
- [WebAIM: Alternative Text](https://webaim.org/techniques/alttext/)
- [WebAIM: Headings](https://webaim.org/techniques/headings/)
- [WebAIM: Semantic Structure](https://webaim.org/techniques/semanticstructure/)
- [W3C WAI: Page Structure — Regions](https://www.w3.org/WAI/tutorials/page-structure/regions/)
- [W3C WAI: Page Structure — Headings](https://www.w3.org/WAI/tutorials/page-structure/headings/)
- [W3C WAI: Understanding WCAG 1.1.1](https://www.w3.org/WAI/WCAG21/Understanding/non-text-content)
- [W3C WAI: Understanding WCAG 2.4.4](https://www.w3.org/WAI/WCAG21/Understanding/link-purpose-in-context)
- [W3C WAI: Understanding WCAG 4.1.2](https://www.w3.org/WAI/WCAG21/Understanding/name-role-value)
- [W3C WAI: Understanding WCAG 2.1.1](https://www.w3.org/WAI/WCAG21/Understanding/keyboard)
- [W3C WAI: Understanding WCAG 4.1.1](https://www.w3.org/WAI/WCAG21/Understanding/parsing)
- [WebAIM: Keyboard Accessibility](https://webaim.org/techniques/keyboard/)
- [WebAIM: Creating Accessible Forms](https://webaim.org/techniques/forms/)
