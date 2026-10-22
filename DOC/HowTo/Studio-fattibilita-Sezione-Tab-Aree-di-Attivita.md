# Studio di fattibilità: sezione home "Aree di attività" (tab)

**Stato:** analisi, nessuna implementazione. Documento non incluso nei commit (locale).

## 1. Riferimento grafico

`sf-index.html`, sezione `#blocco-tab` (righe 702-840): componente ufficiale Bootstrap Italia 3 **Tabs, variante "Con controllo pannelli, testuali"** — `ul.nav.nav-tabs[role=tablist]` con i link dei tab (`data-bs-toggle="tab"`) e `div.tab-content` con i pannelli (`role=tabpanel`, `aria-labelledby`). **Nessun JavaScript aggiuntivo**: tastiera (frecce), focus e `aria-selected` sono già gestiti dal bundle Bootstrap Italia già incluso nel tema — confermato anche dal commento originale del prototipo.

Contenuto di esempio nel prototipo: 3 tab ("Ricerca", "Persone", "Terza missione"), sfondo bianco (la sezione "Il laboratorio" che precede è grigia). Ogni pannello ha:
- un paragrafo introduttivo (il tab "Persone" ne ha **tre**, nessun link);
- opzionalmente, una lista BI3 "Azioni, con freccia" (`it-list`, la stessa già usata in "Le sezioni del sito"/"Indice di una pagina") con 0 o più link in fondo al pannello.

Il commento del prototipo stesso descrive già l'intento gestionale: *"Da backoffice: titolo della sezione e, per ogni tab, etichetta (una o due parole), testo e link; almeno 2 tab, uno solo attivo."* — coincide con quanto descritto nella richiesta.

## 2. Modello dati proposto

Stesso pattern già usato per "Sezioni HP" e per i "Box 1/2/3" di Contenuti in evidenza: un **campo CMB2 di tipo `group`, ripetibile** (`repeatable => true`), con un sotto-campo per voce:

| Campo | Tipo CMB2 | Corrisponde a |
|---|---|---|
| `Etichetta tab` | `text` | Testo del pulsante tab (breve, "una o due parole" come da prototipo) |
| `Descrizione` | `wysiwyg` (editor ridotto, come già usato per `testo_pubblicazioni` ecc.) | Il/i paragrafo/i del pannello — un editor permette più paragrafi senza bisogno di un campo a parte (copre anche il caso "Persone", 3 paragrafi) |
| `Pagine collegate` | **`pw_multiselect`** (select2 multiplo) | I link "in calce" al pannello, **zero o più** — risolve esattamente la richiesta "link a contenuti da mettere in calce al singolo tab" |

### Perché `pw_multiselect` e non un nuovo gruppo annidato
La richiesta implica, per ogni tab, una **lista** di link (nel prototipo: 3 per "Ricerca", 0 per "Persone", 3 per "Terza missione") — non un singolo link. Le alternative erano:
1. **Gruppo CMB2 annidato dentro un gruppo** (un repeatable "Link" dentro il repeatable "Tab"): tecnicamente possibile ma CMB2 gestisce i gruppi annidati in modo storicamente fragile (problemi noti di UI/riordino su più livelli) — rischio tecnico non banale per un beneficio marginale.
2. **Campo testo libero** ("Etichetta|URL" una riga per link): robusto ma scomodo da editare e non collegato a contenuti reali del sito.
3. **`pw_multiselect`** (select2, già vendorizzato e attivo nel tema — `inc/vendor/cmb-field-select2-master/`, hook già registrato in `inc/cmb2.php`): un **singolo campo per riga del gruppo**, nessun annidamento, l'admin seleziona N pagine con un normale multi-select con ricerca. **Scelta consigliata.**

Il campo userebbe lo stesso helper già scritto per "Cosa puoi fare"/"Pagine in evidenza" e "Indice di una pagina" (`dli_get_pages_for_select()`, `inc/utils.php`) per popolare le opzioni — nessun nuovo codice di supporto lato PHP per questo. L'etichetta di ogni link nell'`it-list` sarebbe il titolo della pagina selezionata (stesso criterio già in uso altrove), il link il suo permalink.

Nota: nel prototipo i link del tab "Ricerca" puntano agli **archivi di content type** (Progetti, Attività di ricerca, Pubblicazioni) — che nel nostro tema sono comunque normali Pagine WordPress con un page-template assegnato, quindi rientrano perfettamente nello stesso selettore "scegli una pagina" già costruito, senza bisogno di un meccanismo diverso per distinguere "pagina generica" da "pagina archivio".

## 3. Rendering

Nuovo template-part (es. `template-parts/home/hp-tabs-section.php`) e nuova voce nel catalogo `DLI_HP_SECTIONS` (`config-lab.php`), esattamente come le ultime tre sezioni create in questa sessione. Grazie alla generalizzazione già fatta in `home.php` (passaggio dell'intera riga di configurazione al template-part, introdotta con "Indice di una pagina"), **non serve alcuna modifica a `home.php`**.

Per ogni tab in configurazione, generare:
- un `<li role="presentation">` nella `ul.nav.nav-tabs` con id univoco (es. `tab-{indice}` o uno slug derivato dall'etichetta);
- il pannello corrispondente in `div.tab-content`, con la lista `it-list` delle pagine collegate (se presenti) sotto il testo, **omessa interamente se il campo "Pagine collegate" è vuoto** (comportamento del tab "Persone").
- il **primo** tab della lista è quello attivo di default (`active`/`show`), nessun campo admin dedicato per sceglierlo — stessa logica "l'ordine nella lista è l'ordine di rendering" già usata per "Sezioni HP".

CSS: nessuna regola nuova prevista a priori (il componente Tabs ufficiale è già compilato nel bundle, verificato nella comparazione fatta settimane fa), salvo eventuali rifiniture dopo il primo test visivo (es. le stesse regole già applicate altrove per spegnere sottolineatura/hover di default sulle liste `it-list`, da estendere a `#blocco-tab` se necessario — le regole CSS di "Le sezioni del sito"/"Indice di una pagina" in questo tema sono già scritte in modo da poter includere `#blocco-tab` senza modifiche, lo avevamo previsto per coerenza col prototipo).

## 4. Effort stimato: basso

Non richiede nulla di nuovo a livello di infrastruttura: riusa interamente il pattern "gruppo ripetibile" già rodato tre volte in questa sessione, l'helper di selezione pagine già scritto, il campo `pw_multiselect` già vendorizzato e attivo, il componente CSS/JS Tabs già incluso nel bundle Bootstrap Italia. Il lavoro reale è: definizione dei campi in `inc/admin/options.php`, nuovo template-part, nuova voce di catalogo — stessa mole di lavoro già fatta per "Indice di una pagina".

## 5. Domande aperte

1. **Bilingue (IT/EN)**: le altre due sezioni testuali libere ("Pagine in evidenza", "Navigazione per pagine") hanno titolo/sottotitolo con varianti `_eng`; i "Box" di Contenuti in evidenza invece no (solo IT). Per "Etichetta tab" e "Descrizione" vuoi la coppia IT/EN per ciascuna voce del gruppo (raddoppia i campi per tab), o va bene mono-lingua per ora?
2. **Vincolo "almeno 2 tab"**: il prototipo lo consiglia per motivi di design (un solo tab non avrebbe senso come componente a tab). Lo lasciamo come semplice indicazione in descrizione del campo (nessun blocco tecnico), o serve una validazione che impedisca di salvare con un solo tab attivo?
3. **Tab attivo di default**: proponiamo "il primo della lista" (nessun campo dedicato) — va bene, o preferisci poter scegliere quale tab parte aperto indipendentemente dall'ordine?
4. **Etichetta del link nell'`it-list`**: proponiamo il titolo della pagina collegata (coerente con le altre due sezioni) — oppure vuoi poter personalizzare il testo del link indipendentemente dal titolo reale della pagina?
