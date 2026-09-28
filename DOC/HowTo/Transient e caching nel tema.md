# Transient e caching nel tema

## Cosa sono i transient

I transient sono un meccanismo **nativo** di WordPress (non un'aggiunta di questo
tema) per salvare temporaneamente un valore con una scadenza, evitando di
ricalcolarlo ad ogni richiesta. Tre funzioni:

```php
set_transient( 'mia_chiave', $valore, 3600 );  // salva per 1 ora
$valore = get_transient( 'mia_chiave' );       // false se scaduto/assente
delete_transient( 'mia_chiave' );              // invalida manualmente
```

Per impostazione predefinita i transient vengono salvati nella tabella
`wp_options`. Se il sito ha un object cache esterno configurato (Redis,
Memcached), WordPress li salva lì automaticamente, in modo trasparente per chi
scrive il codice.

## Come si usano di solito

Il pattern standard è: **leggi la cache, se manca calcola e salva, altrimenti
restituisci il valore cachato**.

```php
function get_dato_costoso() {
    $cached = get_transient( 'chiave_dato_costoso' );
    if ( false !== $cached ) {
        return $cached;
    }
    $valore = /* query o calcolo pesante */;
    set_transient( 'chiave_dato_costoso', $valore, DAY_IN_SECONDS );
    return $valore;
}
```

L'invalidazione va gestita esplicitamente: o si lascia scadere il transient da
solo (TTL breve), oppure si cancella con `delete_transient()` nel momento in
cui il dato sottostante cambia (tipicamente agganciandosi a un hook come
`save_post`).

### Come si aggiorna in pratica: la sequenza

Il punto spesso poco chiaro è **quando** la cache viene effettivamente
ricalcolata. Non appena si salva il contenuto: la cache viene solo *svuotata*,
il ricalcolo avviene alla richiesta successiva. Sequenza completa:

1. **Prima visita alla pagina** → `get_transient()` non trova nulla (`false`)
   → si esegue la query/calcolo pesante → il risultato viene salvato con
   `set_transient()` → la pagina viene mostrata.
2. **Visite successive**, finché il contenuto non cambia → `get_transient()`
   trova il valore salvato al passo 1 e lo restituisce subito: **nessuna
   query pesante viene rieseguita**.
3. **Un operatore aggiunge o modifica un contenuto** che alimenta quel dato
   (es. pubblica un nuovo elemento) → scatta l'hook a cui è agganciata
   `delete_transient()` (nel nostro caso `save_post` o una sua variante) →
   la cache viene **svuotata immediatamente**, ma **non ricalcolata in quel
   momento**: la scrittura del contenuto non richiama la query pesante.
4. **Prima visita successiva alla pagina** (di un qualsiasi visitatore) →
   `get_transient()` non trova più nulla (perché è stata svuotata al passo 3)
   → si rientra nel caso del passo 1: si ricalcola e si ripopola la cache.

In altre parole: **salvare un contenuto invalida la cache, ma è la richiesta
successiva alla pagina a farla ricalcolare**, non l'operazione di salvataggio
in sé. Se nessuno visita più quella pagina, il dato resta "vuoto" (da
ricalcolare) finché non arriva una richiesta, oppure finché non scade da solo
al termine del TTL (che quindi funge anche da rete di sicurezza se l'hook di
invalidazione, per qualche motivo, non dovesse scattare).

## Come li usiamo in questo progetto

Ad oggi il tema usa i transient in due punti, entrambi introdotti per
correggere pattern di query N+1/non necessarie segnalati durante la code
review (vedi `AGENTS/ISSUES_RESOLVED.md`):

| Funzione | File | Cosa cachea | Chiave | Invalidata su |
|---|---|---|---|---|
| `dli_get_all_place_types_with_results()` | `inc/utils.php` | Tipi di luogo con almeno un contenuto pubblicato | `dli_place_types_with_results` | `save_post_{PLACE_POST_TYPE}` |
| `dli_get_all_categories_by_ct()` | `inc/utils.php` | Categorie/termini con almeno un contenuto pubblicato, per tassonomia + post type | hash di tassonomia+post type+stato (`dli_get_categories_by_ct_cache_key()`) | `save_post` generico, filtrato per post type tramite la mappa `DLI_CATEGORIES_BY_CT_TAXONOMY_PER_POST_TYPE` in `inc/actions.php` |

Entrambe usano un TTL di 24 ore (`DAY_IN_SECONDS`) come rete di sicurezza,
oltre all'invalidazione esplicita al salvataggio dei contenuti interessati.
Non esiste al momento un layer di caching generale nel tema: è un intervento
puntuale sulle due funzioni segnalate come costose, non un'infrastruttura
riusabile per altri scopi.

### Cosa succede quando si aggiunge un nuovo contenuto: `dli_get_all_place_types_with_results()`

Caso concreto: un operatore pubblica un nuovo `luogo` di un tipo che finora
non aveva alcun contenuto (quindi non compariva nei filtri della pagina
`/luoghi/`).

1. Un visitatore apre `/luoghi/` **prima** della pubblicazione: la funzione
   calcola i tipi di luogo con risultati (senza quello nuovo, che ancora non
   esiste) e salva il risultato nel transient `dli_place_types_with_results`.
2. L'operatore pubblica il nuovo luogo. WordPress lancia l'azione
   `save_post_{PLACE_POST_TYPE}` (cioè `save_post_luogo`), a cui è agganciata
   `dli_invalidate_place_types_with_results_cache()`: questa funzione fa solo
   `delete_transient( 'dli_place_types_with_results' )`. **In questo istante
   non viene ricalcolato nulla**, solo cancellato il valore vecchio.
3. Un visitatore riapre `/luoghi/` dopo la pubblicazione: `get_transient()`
   non trova più nulla, la funzione riesegue `get_terms()` e questa volta
   include anche il nuovo tipo di luogo (che ora ha un contenuto pubblicato),
   e il risultato aggiornato viene salvato di nuovo in cache.

Effetto pratico per l'operatore: il nuovo filtro compare "alla prima
richiesta utile dopo il salvataggio", non istantaneamente mentre sta ancora
compilando il contenuto — ma comunque alla primissima visita successiva, non
dopo ore.

### Cosa succede quando si aggiunge un nuovo contenuto: `dli_get_all_categories_by_ct()`

Caso concreto: un operatore pubblica un nuovo `brevetto` associato a
un'area tematica che finora non aveva alcun brevetto pubblicato.

1. Un visitatore apre `/brevetti/` prima della pubblicazione: la cache per la
   combinazione (`THEMATIC_AREA_TAXONOMY`, `PATENT_POST_TYPE`, `publish`)
   viene popolata senza quell'area tematica.
2. L'operatore pubblica il brevetto. WordPress lancia l'azione generica
   `save_post` (si attiva per **qualsiasi** tipo di contenuto salvato, non
   solo i brevetti) → `dli_invalidate_categories_by_ct_cache( $post_id )`
   controlla il tipo del post appena salvato tramite `get_post_type()`; se è
   uno dei tipi presenti nella mappa `DLI_CATEGORIES_BY_CT_TAXONOMY_PER_POST_TYPE`
   (qui: `brevetto` → `THEMATIC_AREA_TAXONOMY`), calcola la stessa chiave
   usata in lettura e la cancella con `delete_transient()`. Se il post
   salvato fosse invece, per esempio, una pagina qualsiasi non presente in
   quella mappa, la funzione si ferma subito e non tocca nessuna cache.
3. Un visitatore riapre `/brevetti/`: la cache per quella combinazione non
   c'è più, viene ricalcolata e questa volta include anche la nuova area
   tematica.

Punto da tenere a mente: l'hook `save_post` generico scatta ad ogni
salvataggio nel sito (post, pagine, brevetti, eventi, ecc.), ma il controllo
sulla mappa fa sì che il lavoro di invalidazione vero e proprio (calcolo
chiave + `delete_transient`) avvenga solo per i tipi effettivamente
cachati — per tutti gli altri tipi di contenuto la funzione esce subito senza
effetti.

## Vantaggi

- Riduce query ripetute e identiche ad ogni caricamento di pagina pubblica,
  a costo di codice minimo (nessuna dipendenza esterna, API già presente in
  WordPress core).
- Se il sito ha un object cache esterno, il transient lo sfrutta
  automaticamente senza modifiche al codice.
- L'invalidazione mirata su hook (`save_post`) mantiene il dato coerente
  senza dover aspettare la scadenza del TTL.

## Svantaggi e rischi

- **Coerenza approssimata**: fra il momento in cui il dato cambia e il
  momento in cui l'hook di invalidazione scatta c'è una finestra in cui la
  cache potrebbe restituire un valore leggermente vecchio (mitigato qui
  dall'invalidazione su `save_post`, ma resta un rischio se il dato cambia
  per vie diverse dal salvataggio di un post, es. modifica diretta a DB).
- **Costo nascosto su `wp_options`**: senza un object cache esterno, ogni
  transient è una riga in `wp_options`; un uso massiccio o TTL troppo lunghi
  su tabelle già grandi può degradare le performance invece di migliorarle.
- **Debug meno immediato**: un valore "sbagliato" mostrato in pagina può
  essere un dato di cache non invalidato correttamente, non un bug nella
  logica di calcolo — va sempre verificato/svuotato il transient durante il
  debug prima di sospettare altro.
- **Manutenzione della mappa di invalidazione**: per `dli_get_all_categories_by_ct()`
  l'invalidazione dipende da una mappa post-type → tassonomia mantenuta a
  mano in `inc/actions.php`; se in futuro la funzione viene chiamata con una
  nuova combinazione tassonomia/post-type non presente in quella mappa, la
  relativa cache non verrà mai invalidata automaticamente.
