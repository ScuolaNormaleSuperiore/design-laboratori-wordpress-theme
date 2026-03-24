# Sezione "Word Cloud" in Home Page

Documento di lavoro per valutare l'introduzione in homepage di una striscia dedicata ai temi chiave del sito, rappresentati tramite una word cloud o una variante più ordinata e controllata della stessa.

L'obiettivo non e' solo estetico: la sezione dovrebbe aiutare l'utente a capire rapidamente gli argomenti principali trattati dal sito e offrire un accesso immediato a contenuti, archivi o landing page tematiche.

---

## 1. Obiettivo della sezione

La nuova sezione potrebbe comparire in home come una fascia orizzontale, coerente con Bootstrap Italia e con il linguaggio visivo del sito, composta da parole o espressioni chiave:

- cliccabili;
- visualizzate con pesi tipografici diversi;
- ordinate in modo leggibile;
- collegate a contenuti reali del sito.

### Obiettivi possibili

- Evidenziare i temi principali del laboratorio o del sito federato.
- Facilitare l'esplorazione rapida per argomento.
- Rafforzare la percezione editoriale della home page.
- Creare un punto di accesso alternativo rispetto ai menu tradizionali.

### Vincoli da tenere presenti

- La sezione deve restare leggibile su mobile.
- Non dovrebbe sembrare casuale o "disordinata".
- Deve essere accessibile: link chiari, focus visibile, buon contrasto, niente interazioni incomprensibili.
- Le parole mostrate devono avere senso per l'utente, non solo per il CMS.

---

## 2. Possibili forme di presentazione

La "word cloud" non deve per forza essere una nuvola libera e caotica. Ci sono varie opzioni.

### Scenario A - Word cloud editoriale ordinata

Parole con dimensioni diverse, distribuite su piu' righe con `flex-wrap`, senza posizionamento assoluto casuale.

Caratteristiche:

- effetto visivo da word cloud;
- maggiore controllo sul layout;
- buona compatibilita' con Bootstrap Italia;
- facile gestione responsive.

Pro:

- leggibile;
- elegante;
- semplice da implementare;
- basso rischio di problemi accessibilita'/responsive.

Contro:

- meno "spettacolare" di una nuvola libera;
- se i pesi sono troppo uniformi, puo' sembrare un semplice insieme di chip.

### Scenario B - Chip cloud / tag cloud istituzionale

Le parole sono mostrate come chip o pillole con piccole variazioni di peso tipografico.

Caratteristiche:

- aspetto molto ordinato;
- forte coerenza con interfacce pubbliche e istituzionali;
- navigazione molto chiara.

Pro:

- accessibile;
- prevedibile;
- molto solida su mobile;
- semplice da mantenere.

Contro:

- meno distintiva dal punto di vista grafico;
- rischia di assomigliare a una lista tag standard.

### Scenario C - Word cloud "libera" con posizionamento dinamico

Le parole vengono posizionate con logica piu' libera, simulando una vera nuvola.

Caratteristiche:

- impatto visivo alto;
- richiede piu' logica di rendering;
- puo' richiedere JS lato client o pre-rendering.

Pro:

- se ben realizzata, e' piu' caratterizzante;
- puo' rendere la home piu' memorabile.

Contro:

- piu' fragile lato responsive;
- piu' difficile da rendere accessibile;
- rischio di sovrapposizioni o gerarchie poco chiare;
- meno coerente con un contesto pubblico/istituzionale.

### Raccomandazione iniziale

Per questo progetto partirei con lo Scenario A oppure B.

Motivo:

- hanno un buon equilibrio tra impatto e controllo;
- si integrano meglio con Bootstrap Italia;
- non introducono complessita' inutile nella prima versione.

---

## 3. Come recuperare le parole

Le possibilita' principali emerse finora sono tre, ma conviene espanderle in scenari piu' concreti.

### Scenario 1 - Parole impostate manualmente nel backoffice

Un redattore imposta da backend un elenco di voci con:

- etichetta;
- link;
- peso;
- eventuale ordine;
- eventuale descrizione accessibile.

#### Come funzionerebbe

Si potrebbe usare un repeater o un gruppo campi dedicato alla homepage, per esempio:

- `label`
- `url`
- `weight` da 1 a 5
- `target` o tipo destinazione
- `aria_label` opzionale

#### Pro

- massimo controllo editoriale;
- parole sempre sensate;
- niente rumore statistico;
- facile allineare la sezione alla comunicazione istituzionale;
- consente di mettere in evidenza temi strategici anche se poco frequenti nei contenuti.

#### Contro

- richiede manutenzione manuale;
- non si aggiorna automaticamente;
- il redattore deve ricordarsi di tenerla coerente con il sito.

#### Quando ha senso

- se la homepage e' molto editoriale;
- se i temi da evidenziare sono pochi e stabili;
- se serve forte controllo comunicativo.

---

### Scenario 2 - Uso dei tag WordPress

La cloud viene generata leggendo i tag esistenti nel sito.

#### Come funzionerebbe

Si potrebbero recuperare i tag:

- piu' usati in assoluto;
- piu' usati in un certo tipo di contenuto;
- solo quelli appartenenti a contenuti pubblicati;
- eventualmente filtrati da una whitelist.

Il peso della parola potrebbe dipendere da:

- numero totale di contenuti associati al tag;
- numero di contenuti recenti associati al tag;
- peso editoriale aggiuntivo definito manualmente.

#### Pro

- aggiornamento automatico;
- semantica gia' presente in WordPress;
- link naturali agli archivi dei tag;
- implementazione relativamente semplice se il tagging e' gia' ben usato.

#### Contro

- i tag potrebbero essere sporchi, incoerenti o ridondanti;
- rischio sinonimi duplicati o varianti poco utili;
- i tag piu' usati non sono sempre i piu' interessanti;
- puo' emergere terminologia troppo tecnica o poco chiara per l'utente finale.

#### Quando ha senso

- se i tag sono gia' ben governati;
- se il sito ha una strategia tassonomica chiara;
- se si vuole una cloud viva e auto-aggiornata.

#### Nota implementativa (specifica per questo tema)

Il meccanismo e' gia' operativo nella pagina Progetti: i tag `post_tag` appaiono come chip filtro Bootstrap Italia e generano URL del tipo `/progetti/?level=machine-learning`. La word cloud in home potrebbe riusare la stessa logica di destinazione (vedi Opzione E in §5) senza scrivere nuove query o nuovi template di archivio.

---

### Scenario 3 - Conteggio delle parole nei testi

Le parole vengono estratte automaticamente da titoli, excerpt o contenuti dei post/pagine e contate per frequenza.

#### Come funzionerebbe

Flusso tipico:

1. si raccolgono i testi da analizzare;
2. si normalizzano in minuscolo;
3. si rimuovono punteggiatura, stopword e parole troppo corte;
4. si uniscono eventuali varianti o sinonimi;
5. si calcola la frequenza;
6. si scelgono le prime N parole.

#### Varianti possibili

- contare solo i titoli;
- contare titolo + excerpt;
- contare tutto il contenuto;
- limitarsi ai contenuti piu' recenti;
- usare liste di esclusione e liste di priorita'.

#### Pro

- e' il metodo piu' "automatico";
- puo' far emergere temi reali anche senza tassonomie curate;
- si aggiorna insieme ai contenuti.

#### Contro

- produce facilmente rumore;
- richiede pulizia linguistica, specialmente in italiano;
- puo' far emergere parole poco utili come concetti troppo generici o parole tecniche isolate;
- e' il metodo meno editoriale;
- il link associato alla parola non e' ovvio: serve una strategia aggiuntiva.

#### Quando ha senso

- se si vuole un esperimento o un prototipo;
- se non esiste una tassonomia affidabile;
- se si e' disposti a introdurre una fase di normalizzazione abbastanza curata.

---

### Scenario 4 - Soluzione ibrida: parole automatiche con controllo editoriale

Il sistema propone automaticamente una lista di parole, ma il backend consente di:

- nascondere termini indesiderati;
- fissare alcune parole obbligatorie;
- correggere label;
- ridefinire pesi e link;
- limitare il numero finale di voci.

#### Pro

- buon equilibrio tra automazione e controllo;
- riduce il lavoro redazionale puro;
- consente di correggere anomalie senza riscrivere tutto.

#### Contro

- e' piu' complesso da implementare;
- richiede una UX backend ben pensata;
- bisogna decidere bene chi prevale tra dato automatico e scelta editoriale.

#### Quando ha senso

- se la sezione deve evolvere nel tempo;
- se il sito ha molti contenuti e si vuole evitare gestione totalmente manuale;
- se esiste una redazione che puo' fare supervisione ma non curare ogni voce da zero.

---

### Scenario 5 - Tassonomie custom o temi controllati

Anziche' usare i tag standard, si introduce o si riusa una tassonomia piu' curata, per esempio "temi", "ambiti", "linee di ricerca", "argomenti".

#### Pro

- maggiore coerenza semantica;
- termini piu' stabili;
- ottimo controllo sull'esperienza utente;
- link naturali a pagine archivio tematiche.

#### Contro

- richiede disegno informativo a monte;
- puo' richiedere revisione dei contenuti gia' pubblicati;
- lavoro editoriale iniziale non banale.

#### Quando ha senso

- se si vuole una soluzione robusta e duratura;
- se il sito crescera' ancora;
- se si vuole evitare l'imprevedibilita' dei tag liberi.

---

## 4. Come calcolare il peso visivo delle parole

Indipendentemente dalla fonte, conviene evitare differenze troppo estreme. Meglio usare classi discrete.

### Esempio di livelli

- `xs`
- `sm`
- `md`
- `lg`
- `xl`

### Possibili criteri di assegnazione

#### Metodo lineare

Il numero di occorrenze viene mappato in modo diretto sulle classi.

Pro:

- semplice;
- prevedibile.

Contro:

- se c'e' un forte squilibrio tra le frequenze, quasi tutte le parole finiscono nelle classi basse.

#### Metodo a quantili

Le parole vengono distribuite in gruppi con numerosita' simile.

L'implementazione pratica e' semplice: si ordina l'array per conteggio, lo si divide in N parti uguali con `array_chunk`, e ogni parte riceve una classe CSS (`wc-s1` … `wc-s5`). Non serve alcuna formula matematica complessa.

Pro:

- equilibrio visivo migliore;
- cloud piu' leggibile.

Contro:

- la differenza numerica reale tra parole potrebbe non essere percepibile.

#### Metodo editoriale

Il peso e' deciso manualmente o corretto manualmente.

Pro:

- massimo controllo.

Contro:

- meno automatico.

### Raccomandazione

Per la prima versione userei:

- massimo 12-16 parole;
- 4 o 5 livelli di peso;
- nessuna parola gigantesca;
- differenze tipografiche moderate.

---

## 5. A cosa linkare le parole

Questa e' una decisione centrale. Una parola senza destinazione chiara rischia di essere solo decorativa.

### Opzione A - Archivio tag

Esempio:

- `Design` -> archivio del tag `design`

Pro:

- semplice;
- naturale se si usano i tag WordPress.

Contro:

- la pagina archivio puo' essere povera o poco curata;
- se il tagging e' disordinato, la destinazione risulta debole.

### Opzione B - Archivio di tassonomia custom

Esempio:

- `Sostenibilita'` -> archivio della tassonomia "temi"

Pro:

- semantica piu' forte;
- esperienza piu' ordinata.

Contro:

- richiede tassonomia ben progettata.

### Opzione C - Ricerca prefiltrata

Esempio:

- `Materiali` -> risultati di ricerca con query "materiali"

Pro:

- implementazione semplice;
- non richiede tassonomie forti.

Contro:

- risultati meno stabili;
- rischio rumore nei risultati;
- UX spesso inferiore a una vera landing o archivio tematico.

### Opzione D - Landing page dedicate

Esempio:

- `Innovazione` -> pagina editoriale dedicata al tema

Pro:

- destinazione ricca;
- forte controllo editoriale;
- buona per temi strategici.

Contro:

- richiede creazione e manutenzione delle landing;
- non sempre sostenibile per molte parole.

### Opzione E - Pagine archive di CPT filtrate

Esempio:

- `Prototipazione` -> archivio progetti/notizie/eventi con filtro attivo

Pro:

- navigazione piu' contestuale;
- utile se si vuole portare l'utente a contenuti gia' organizzati.

Contro:

- servono filtri robusti;
- il significato del link deve restare chiaro.

### Raccomandazione

Ordine di preferenza:

1. landing page dedicate, se esistono;
2. tassonomie curate;
3. archivi tag, se ben mantenuti;
4. ricerca prefiltrata, solo come fallback.

---

## 6. Scenari completi di implementazione

Qui sotto alcuni scenari realistici, con pro e contro concreti.

### Scenario completo 1 - Manuale puro

Le parole sono inserite nel backoffice, con link e peso definiti a mano.

Pro:

- qualita' alta;
- controllo totale;
- rischio basso.

Contro:

- aggiornamento manuale;
- dipendenza dal lavoro redazionale.

Valutazione:

- ottimo per una prima release.

---

### Scenario completo 2 - Tag WordPress puri

La cloud mostra automaticamente i tag piu' usati e linka ai rispettivi archivi.

Pro:

- implementazione rapida;
- aggiornamento automatico.

Contro:

- qualita' molto dipendente dallo stato attuale dei tag;
- facile ottenere parole ridondanti o deboli.

Valutazione:

- adatto solo se il tagging e' gia' ordinato.

---

### Scenario completo 3 - Conteggio parole dei testi + ricerca

Si estraggono le parole piu' frequenti dai contenuti e si linka a una ricerca interna.

Pro:

- molto automatico;
- nessun lavoro redazionale iniziale.

Contro:

- rumore alto;
- risultati spesso poco eleganti;
- rischio di sezione tecnicamente interessante ma editorialmente debole.

Valutazione:

- utile per prova interna, non come prima scelta per produzione.

---

### Scenario completo 4 - Manuale + suggerimenti automatici

Il sistema calcola parole candidate da tag o contenuti, ma la home mostra solo quelle approvate o corrette da backend.

Pro:

- buon compromesso tra vitalita' e controllo;
- piu' sostenibile nel medio periodo.

Contro:

- richiede piu' sviluppo e piu' backend UX.

Valutazione:

- probabilmente la soluzione migliore nel medio termine.

---

### Scenario completo 5 - Tassonomia "Temi" dedicata

Si definisce una tassonomia specifica per i temi da usare in home e negli archivi, con una gestione rigorosa.

Pro:

- soluzione solida;
- coerente;
- molto riusabile in futuro.

Contro:

- lavoro iniziale maggiore;
- richiede revisione contenuti e governance editoriale.

Valutazione:

- ottima se il sito punta a crescere e strutturarsi meglio nel tempo.

---

## 7. Aspetti di UX e accessibilita'

Qualunque scenario venga scelto, la sezione dovrebbe rispettare alcune regole:

- tutte le parole devono essere realmente cliccabili oppure tutte non cliccabili; evitare ambiguita';
- il contrasto deve essere sufficiente;
- il focus tastiera deve essere ben visibile;
- il titolo della sezione deve spiegare lo scopo, per esempio "Temi del laboratorio" o "Esplora per argomento";
- le parole molto grandi non devono rompere il layout su mobile;
- i link devono avere destinazioni prevedibili;
- se si usano parole poco autoesplicative, conviene aggiungere `aria-label` piu' descrittivi; tuttavia il valore di `aria-label` deve sempre contenere il testo visibile del link (criterio WCAG 2.5.3 Label in Name): ad esempio, su una parola visibile "Prototipazione" e' corretto `aria-label="Prototipazione — archivio progetti"` e scorretto `aria-label="Vai all'archivio progetti di prototipazione"` perche' non include la parola visibile (gli utenti di voice control che dicono "clicca Prototipazione" non troverebbero il link).

Meglio evitare:

- posizionamento completamente casuale;
- animazioni inutili;
- parole decorative senza un ruolo chiaro;
- differenze di grandezza troppo estreme;
- destinazioni eterogenee senza logica editoriale.

### Nota sul caching

Se si usa lo Scenario 2 (tag automatici) o qualsiasi fonte dinamica, la query di conteggio tag viene eseguita ad ogni caricamento della home. Conviene cachare il risultato con i WordPress transient (es. durata 1 ora), dato che i conteggi non cambiano in tempo reale e la home e' la pagina piu' visitata del sito.

---

## 7b. Integrazione con il sistema sezioni della home page

Il tema gestisce le sezioni della home tramite un sistema configurabile definito in `config-lab.php` (costante `DLI_HP_SECTIONS`) e attivabile dal pannello admin *Configurazione → Sezioni HP*. La nuova sezione word cloud si integrerebbe in questo sistema esattamente come le sezioni gia' esistenti (eventi, notizie, pubblicazioni, sponsor, ecc.):

- aggiunta di un nuovo entry in `DLI_HP_SECTIONS` con id `wordcloud_section`;
- creazione del template dedicato in `template-parts/home/hp-wordcloud-section.php`;
- attivazione, disattivazione e riordinamento dalla stessa interfaccia admin gia' usata per le altre sezioni, senza modifiche ulteriori al sistema;
- supporto bilingue automatico tramite Polylang: se si usano i tag WordPress come fonte, i termini sono gia' tradotti dall'installazione Polylang esistente.

---

## 8. Raccomandazione pratica per una prima versione

Se dovessi scegliere oggi una strategia concreta per partire, proporrei questo ordine:

### Prima scelta

Manuale puro da backend.

Perche':

- consente di definire bene il tono editoriale;
- permette di validare rapidamente l'utilita' della sezione;
- non dipende dalla qualita' attuale di tag o tassonomie.

### Seconda scelta

Tag WordPress, ma solo dopo una verifica della qualita' del tagging esistente.

### Terza scelta

Soluzione ibrida con suggerimenti automatici e controllo editoriale.

### Percorso di evoluzione consigliato

Le tre scelte non sono alternative indipendenti ma tappe progressive. Partire dalla versione manuale non preclude le evoluzioni successive:

- v1: manuale da backend — qualita' alta, zero dipendenze;
- v2: tag automatici con blacklist editoriale — aggiornamento automatico, manutenzione ridotta;
- v3: ibrido con suggerimenti automatici e approvazione — equilibrio ottimale tra vitalita' e controllo.

Ogni tappa e' autonoma e non richiede di riscrivere la precedente: si estende il template e le opzioni di configurazione.

### Scelta sconsigliata per partire

Conteggio diretto delle parole dei testi come sorgente primaria di produzione.

Motivo:

- troppo rumore;
- troppo lavoro di pulizia;
- associazione dei link poco naturale.

---

## 9. Punti da chiarire prima di sviluppare

Queste sono le domande che varrebbe la pena approfondire prima di implementare davvero la sezione.

### Strategia editoriale

- La sezione deve rappresentare i temi istituzionali del sito o i temi piu' frequenti nei contenuti?
- Deve essere stabile nel tempo o cambiare spesso?
- Le parole devono essere singoli termini o sono ammesse espressioni composte come "ricerca applicata"?

### Destinazioni dei link

- Le parole dovrebbero portare a landing editoriali, archivi tag, tassonomie, risultati di ricerca o pagine filtro?
- Esistono gia' destinazioni tematiche adeguate oppure andrebbero create?

### Ambito dei contenuti

- La cloud deve riflettere tutto il sito oppure solo alcuni contenuti, come notizie, progetti o eventi?
- I contenuti in piu' lingue devono influenzare insieme la cloud oppure serve una versione per lingua?

### Governo del dato

- I tag WordPress attuali sono abbastanza ordinati da poter essere mostrati in home?
- Esiste gia' una tassonomia tematica riusabile?
- Chi manterrebbe la sezione nel tempo se fosse manuale?

### UX e design

- La sezione deve essere una vera word cloud o una variante piu' ordinata tipo chip cloud?
- Quante parole massime vogliamo mostrare?
- La sezione deve apparire sempre in home o essere facoltativa?

---

## 10. Conclusione

La nuova sezione puo' essere una buona aggiunta alla homepage se viene trattata come strumento di navigazione editoriale e non solo come effetto grafico.

La soluzione piu' semplice e robusta per iniziare e':

- parole gestite manualmente nel backoffice;
- peso visivo controllato;
- link verso destinazioni curate;
- layout ordinato e pienamente compatibile con Bootstrap Italia.

Nel medio periodo, se il progetto lo richiedera', si potra' evolvere verso:

- tag WordPress ben governati;
- tassonomie tematiche dedicate;
- oppure una soluzione ibrida con suggerimenti automatici e revisione editoriale.
