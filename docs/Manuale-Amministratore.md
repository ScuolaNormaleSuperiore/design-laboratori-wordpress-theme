# Manuale Amministratore — Design Laboratori Italia

Guida di riferimento per il ruolo **Amministratore** di WordPress: permessi, pannello di configurazione completo, strumenti riservati, installazione e note di architettura per chi valuta il tema in ottica di riuso.

## 1. Ruoli e permessi

Il tema definisce (`inc/classes/class-authmanager.php`) un ruolo **Super Editor** = Editor + due capability aggiuntive:
- `edit_theme_options` (voce **Aspetto**, necessaria per gestire i menu);
- `dli_edit_site_configuration` (accesso alla maggior parte di **WP → Configurazione**).

L'**Amministratore** (capability nativa `manage_options`) ha accesso a tutto, incluse **3 schede riservate** che il Super Editor non vede:

| Scheda | Perché è riservata |
|---|---|
| **Altro** | Contiene lo switch "Stile del sito" (standard/personalizzato): tocca i colori/l'identità visiva di tutto il sito |
| **Indico** | Importazione eventi da un endpoint esterno |
| **Iris** | Importazione brevetti da un endpoint esterno |

Tutte le altre schede (contenuti, Layout HP, Sezioni HP, Avvisi in Home, Social, Opzioni di base) sono gestibili anche dal Super Editor — per il dettaglio operativo di ciascuna vedi il [Manuale Utente](Manuale-Utente.md).

## 2. Pannello di configurazione: mappa completa (`WP → Configurazione`)

| Scheda | Cosa configura | Permesso |
|---|---|---|
| Opzioni di base | Nome/tagline del laboratorio (IT/EN), indirizzo/contatti/PEC, ente di appartenenza, loghi (header/footer/mobile, visibilità) | Super Editor |
| Avvisi in Home | Messaggi di avviso ripetibili (colore, testo max 140 caratteri, link) | Super Editor |
| **Sezioni HP** | Attiva/disattiva e ordina le sezioni della home (drag&drop); per riga: titolo sezione sì/no, sfondo bianco/grigio, e campo "Pagina" quando la riga è "Indice di una pagina" | Super Editor |
| Layout HP | Hero principale, carousel, le 3 sezioni "Pagine in evidenza" / "Navigazione per pagine" / "Pannello tab" (titoli bilingue + contenuti), "Indice di una pagina" (vedi §3), box di Contenuti in evidenza, Video | Super Editor |
| Laboratorio | Etichetta e descrizione della pagina "Il laboratorio" (IT/EN) | Super Editor |
| Persone | Testi introduttivi, filtri (struttura/tag), modalità vista (chip/tabella) | Super Editor |
| Indirizzi di ricerca | Testi introduttivi | Super Editor |
| Progetti | Testi introduttivi, paginazione, etichette filtro tag | Super Editor |
| Pubblicazioni | Testi introduttivi della pagina elenco | Super Editor |
| Brevetti | Testi introduttivi | Super Editor |
| Spin-off | Testi introduttivi | Super Editor |
| Risorse tecniche | Testi introduttivi | Super Editor |
| Sponsors | Numero di elementi per riga nel grid home | Super Editor |
| Luoghi | Testi introduttivi | Super Editor |
| Novità (Notizie) | Testi introduttivi | Super Editor |
| Eventi | Testi introduttivi | Super Editor |
| Blog | Testi introduttivi | Super Editor |
| Socialmedia | Attiva/disattiva le icone social in footer e i relativi URL (Facebook, YouTube, Instagram, Twitter/X, LinkedIn, GitHub, Pinterest, Mastodon) | Super Editor |
| **Indico** | Attivazione, URL e debug dell'importazione automatica eventi | **Amministratore** |
| **Iris** | Attivazione, URL endpoint e debug dell'importazione automatica brevetti | **Amministratore** |
| **Altro** | **Stile del sito** (vedi §4) | **Amministratore** |

## 3. Sezione "Indice di una pagina" — come funziona la ripetibilità

A differenza delle altre sezioni home, "Indice di una pagina" non ha testi propri in Layout HP: il titolo mostrato è sempre quello della pagina scelta. Per usarla:
1. In **Sezioni HP**, aggiungi una riga, scegli "Indice di una pagina" come Sezione.
2. Compare un campo aggiuntivo "Pagina": scegli la pagina di cui mostrare la struttura (colonne = le sue sottopagine dirette; ogni colonna elenca le sottopagine di quella sottopagina).
3. Per mostrarne più di una in home con pagine diverse, ripeti i passi 1-2 su un'altra riga: ogni riga è indipendente, incluso lo sfondo.

## 4. Stile del sito (solo Amministratore)

In **WP → Configurazione → Altro**:
- **Stile Bootstrap Italia standard**: nessun colore di brand, nessuna immagine decorativa negli hero — solo il tema Bootstrap Italia "di fabbrica".
- **Stile personalizzato**: applica i colori Ottanio/Dark Blue del brand (`assets/css/custom-colors.css`) e mostra le immagini decorative negli hero privi di foto reale.

Il cambio è istantaneo e globale, non richiede build.

## 5. Importazione dati (solo Amministratore)

- **Eventi da Indico**: endpoint REST dedicato (`custom/v1/indico-import`), configurazione URL/attivazione in **Configurazione → Indico**.
- **Brevetti da IRIS**: endpoint REST dedicato (`custom/v1/iris-patent-import`), configurazione in **Configurazione → Iris**. Guida dettagliata: `DOC/HowTo/Import brevettiI da RIS.md`.

Dopo ogni importazione, verificare su un campione: titoli, date, link e immagini.

## 6. Plugin richiesti

Il tema segnala in bacheca i plugin mancanti. Obbligatori per il funzionamento:
- Advanced Custom Fields
- Polylang (gestione multilingua)
- ACF OpenStreetMap Field (mappa nei Luoghi)
- Better Aria Label Support
- WP Mail SMTP (invio email affidabile)
- Really Simple CAPTCHA (form di contatto)

## 7. Installazione e primo avvio (riepilogo)

1. Copiare il tema in `wp-content/themes/` e attivarlo (crea automaticamente contenuti e menu di default).
2. Installare i plugin richiesti dal popup mostrato all'attivazione.
3. Configurare Polylang (lingua IT come default, EN come secondaria).
4. `WP → Strumenti DLI → Utilità → Ricarica i dati di attivazione` per rigenerare pagine/menu/tassonomie.
5. In **Aspetto → Menu**, assegnare e salvare le 5 posizioni di menu.
6. Compilare **Configurazione → Opzioni di base** e le sezioni Home desiderate.

Dettagli completi, requisiti software e demo Docker: `README.md` alla radice del progetto.

## 8. Note di architettura (per chi valuta il riuso)

- **Content type**: registrati in `inc/classes/class-*manager.php`, con campi ACF dichiarati via `acf_add_local_field_group()` (non da export JSON per i più recenti, es. i due flag aggiunti alla scheda Pagina). Elenco slug e costanti in `config-lab.php`.
- **Sezioni home**: catalogo dichiarativo `DLI_HP_SECTIONS` (`config-lab.php`) — ogni voce ha `id`, `name` (etichetta in admin) e `template` (file in `template-parts/home/`). L'elenco/ordine/attivazione effettivo è salvato come opzione CMB2 ripetibile (`site_sections`, option `homepage_sections`); `home.php` itera questa lista e passa l'intera riga di configurazione al template-part, quindi **aggiungere campi per-sezione non richiede modifiche a `home.php`**.
- **Flag di visibilità**: pattern ricorrente `true_false` ACF `promuovi_in_home` / `promuovi_in_carousel` su quasi tutti i content type, più i due flag dedicati sulla scheda Pagina (`promuovi_in_home`, `mostra_sottopagine_a_card` — `inc/classes/class-pagemanager.php`).
- **Pubblicazioni con pagina di dettaglio**: controllato dalla costante `DLI_PUBLICATION_HAS_DETAIL` (`config-lab.php`) — quando `true` abilita permalink, visibilità in ricerca/REST e il template `single-pubblicazione.php`.
- **Permessi**: due capability distinte, `dli_edit_site_configuration` (Super Editor + Admin) e l'uso diretto di `manage_options` sulle 3 schede riservate — vedi `inc/admin/options.php` per il dettaglio campo per campo.
- **Pagine e menu**: la sidebar "Pagine collegate" e le sezioni "Navigazione per pagine"/"Indice di una pagina" leggono la gerarchia nativa di WordPress (`post_parent`), indipendente dai menu di navigazione (`wp_nav_menu`), che sono invece gestiti dall'utente in **Aspetto → Menu** su 5 location separate (`menu-lab`, `menu-right`, `menu-header-right`, `menu-links`, `menu-footer`).

## 9. Troubleshooting rapido

| Sintomo | Verifica |
|---|---|
| Una sezione home non appare | È abilitata in "Sezioni HP"? Ha contenuti reali con il flag richiesto attivo? |
| Una pagina non compare in "Pagine in evidenza"/"Navigazione per pagine" | Flag `Promuovi in Home` attivo? Per "Navigazione per pagine": la pagina ha almeno una sottopagina? |
| Colori del brand assenti | `Configurazione → Altro → Stile del sito` è su "personalizzato"? (solo Amministratore) |
| Pubblicazione non cliccabile dall'elenco | `DLI_PUBLICATION_HAS_DETAIL` è `true` in `config-lab.php`? (richiede intervento tecnico) |
| Import Indico/IRIS non parte | Endpoint/attivazione configurati nella relativa scheda (solo Amministratore)? |
