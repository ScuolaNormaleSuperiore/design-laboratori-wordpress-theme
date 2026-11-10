# Manuale Amministratore

Versione: 1.0  
Data: 2026-11-03  
Tema: `design-laboratori-wordpress-theme` (2.0.5)

## 1. Scopo del manuale
Guida di riferimento per l'**Amministratore** di WordPress: permessi, schede riservate del pannello di configurazione, strumenti, plugin richiesti e note di architettura per chi valuta il tema in ottica di riuso. Per l'uso quotidiano dei contenuti vedi il [Manuale Operatore](manuale-operatore.md); per ogni campo del pannello di configurazione vedi il [Manuale Utente del Pannello Configurazione](Manuale-Utente-Pannello-Configurazione.md).

## 2. Ruoli e permessi
Il tema aggiunge il ruolo **Super Editor** (`dli_super_editor`), basato su Editor, con due permessi in più (`inc/classes/class-dli-authorizationmanager.php`):

- `edit_theme_options`: la voce **Aspetto** del backoffice, necessaria per gestire i menu;
- `dli_edit_site_configuration`: l'accesso a **WP → Configurazione** (quasi tutte le sue schede). Lo riceve anche l'Amministratore.

L'**Amministratore** (permesso nativo `manage_options`) ha accesso a tutto, comprese tre schede che il Super Editor non vede:

| Scheda | Perché è riservata |
|---|---|
| **Altro** | Contiene lo «Stile del sito» (standard o personalizzato): cambia colori e identità visiva di tutto il sito |
| **Indico** | Importazione degli eventi da un endpoint esterno |
| **Iris** | Importazione dei brevetti da un endpoint esterno |
| **Area riservata** | Accesso con single sign-on e regole sulle password degli utenti dell'area riservata |

Un Redattore o un Editor «semplice» lavora solo sui contenuti, senza accesso alla configurazione né ai menu.

Per l'area riservata il tema aggiunge due ruoli senza permessi di WordPress: **Utente in attesa** (`dli_pending_user`), chi si è registrato con il single sign-on e non è ancora stato approvato, e **Utente area riservata** (`dli_reserved_area_user`), chi è stato approvato. Nessuno dei due entra nella bacheca o modifica il proprio profilo. Chi vede le pagine riservate lo decide il plugin Groups: vedi [Area riservata](../HowTo/Area-riservata.md).

## 3. Pannello di configurazione (`WP → Configurazione`)
Tutte le schede, campo per campo, sono nel [Manuale Utente del Pannello Configurazione](Manuale-Utente-Pannello-Configurazione.md). In sintesi:

- **Super Editor e Amministratore:** Opzioni di base, Avvisi in Home, Sezioni HP, Layout HP, Laboratorio, Persone, Indirizzi di ricerca, Progetti, Pubblicazioni, Brevetti, Spin-off, Risorse tecniche, Sponsors, Luoghi, Novità, Eventi, Blog, Socialmedia.
- **Solo Amministratore:** Indico, Iris, Altro.

### 3.1 Sezioni della home
La home è composta dalle sezioni scelte in **Sezioni HP** (gruppo ripetibile: attivazione, ordine, titolo visibile e, dove previsti, sfondo e limiti per riga). Le sezioni disponibili sono quelle del catalogo `DLI_HP_SECTIONS` (`config-lab.php`):

| Sezione | Sfondo di default | Campi della riga oltre ad attivazione e titolo |
|---|---|---|
| Hero principale | — | — |
| Descrizione del sito | — | — |
| Carousel principale | — | — |
| Contenuti in evidenza | — | — |
| Card di navigazione | grigio chiaro | sfondo, numero massimo di elementi, livello delle pagine |
| Navigazione per pagine | grigio chiaro | sfondo, numero massimo di elementi e di sottopagine |
| Indice di una pagina | bianco | sfondo, pagina, numero massimo di elementi e di sottopagine |
| Pannello tab | bianco | sfondo |

Le altre sezioni della home (eventi, notizie, pubblicazioni, articoli, banner, sponsor, video) hanno la stessa riga di attivazione, ordine e titolo. Ogni riga, anche chiusa, mostra il nome della sezione scelta e le frecce per spostarla. I campi di una sezione che dipendono dalla riga (pagina, indirizzo del video, limiti, sfondo) sono salvati nella riga; i testi e i contenuti (titolo, sottotitolo, tab, box) sono in **Layout HP**. L'elenco che fa vedere «quali pagine compaiono in quale sezione», con un esempio, è nel manuale di configurazione, scheda Layout HP.

Una sezione che dovrebbe comparire e non compare: controlla che sia attiva in Sezioni HP, che ci siano pagine o contenuti che rispettano i criteri della tabella (pubblicati, senza password, nella lingua corrente, con `Promuovi in Home` dove serve) e che la sezione non sia vuota.

## 4. Stile del sito (solo Amministratore)
In **WP → Configurazione → Altro**:

- **Stile Bootstrap Italia standard:** usa i colori e i font predefiniti della libreria, insieme alle regole grafiche specifiche del tema.
- **Stile personalizzato:** applica anche i colori del brand definiti in `assets/css/custom-colors.css`.

Le immagini decorative degli hero restano presenti in entrambi gli stili,
secondo il template della pagina; le foto reali dei contenuti mantengono la
precedenza dove previste. La guida [Personalizzazione dello stile](../HowTo/Personalizzazione-stile.md)
spiega come modificare colori e font.

Il cambio è immediato e vale per tutto il sito, senza ricostruire nulla.

## 5. Importazione dei dati (solo Amministratore)
- **Eventi da Indico:** configurazione e attivazione in **Configurazione → Indico**.
- **Brevetti da IRIS:** configurazione e attivazione in **Configurazione → Iris**. Guida: [Import brevetti da IRIS](../HowTo/Import%20brevettiI%20da%20RIS.md).

Gli import si avviano anche dagli endpoint REST dedicati (`POST custom/v1/indico-import` e `POST custom/v1/iris-patent-import`), disponibili solo quando l'opzione «Abilita REST API» è attiva e con autenticazione HTTP Basic di un utente con `manage_options`. Le credenziali non vanno mai scritte in un documento o in un registro. Dopo ogni importazione controlla su un campione titoli, date, link e immagini.

## 6. Plugin richiesti
Il tema avvisa in bacheca se manca uno dei plugin obbligatori (`SUGGESTED_PLUGINS`, `config-lab.php`):

- Advanced Custom Fields
- ACF OpenStreetMap Field (la mappa nei Luoghi)
- Polylang (multilingua; senza Polylang il tema non si attiva)
- WP Mail SMTP (invio affidabile delle e-mail)
- Really Simple CAPTCHA (moduli di contatto e newsletter)

La libreria dei campi di configurazione (CMB2) è inclusa nel tema in `inc/vendor/` e non si installa a parte.

## 7. Installazione e primo avvio
I passi (copia del tema, attivazione, plugin, Polylang, dati di attivazione, menu, configurazione) sono nel capitolo «Installazione e configurazione del tema» del `README.md`. Strumenti utili in **WP → Strumenti DLI**:

- **Utilità → Ricarica i dati di attivazione:** crea pagine, menu e tassonomie predefinite che mancano, compresa la pagina «Area riservata» (vedi [Area riservata](../HowTo/Area-riservata.md)). Non modifica le pagine e i menu che esistono già;
- **Importa → Pagine:** crea l'albero delle pagine del sito (fino a tre livelli e 200 pagine) da un file CSV, con simulazione e conferma prima della scrittura. Dalla stessa pagina si scaricano un modello vuoto e due esempi dimostrativi (italiano e inglese), che servono anche a provare le sezioni della home che elencano pagine: non importarli su un sito di produzione. Le pagine create sono pubblicate subito, e non si modificano né si cancellano pagine già esistenti: un indirizzo già usato blocca l'importazione. Prima di importare su un sito reale conviene fare un backup;
- **Importa → Persone, Indico, Iris:** avvio manuale degli altri import.

## 8. Note di architettura (per chi valuta il riuso)
La descrizione completa è in `AGENTS/ARCHITECTURE.md`. In breve:

- **Contenuti:** ogni tipo (persone, progetti, eventi, notizie, pubblicazioni, brevetti, luoghi, spin-off, risorse tecniche, indirizzi di ricerca) è registrato da un manager in `inc/classes/`, con campi ACF dichiarati in PHP. Gli elenchi sono pagine con un modello (`page-templates/`), non archivi di WordPress.
- **Sezioni della home:** il catalogo `DLI_HP_SECTIONS` dice quali sezioni esistono e quale file le stampa (`template-parts/home/`); l'elenco, l'ordine e l'attivazione effettivi sono salvati nell'opzione `homepage_sections`. `home.php` passa al modello l'intera riga: un campo nuovo per riga non richiede modifiche a `home.php`.
- **Pagine nelle sezioni:** le sezioni che elencano pagine leggono la gerarchia nativa di WordPress (pagina genitore) e il campo `Promuovi in Home`, non i menu. Gli helper sono in `inc/pages.php`; gli aiuti puri (senza WordPress) in `inc/home-sections.php` e `inc/pagination.php`.
- **Menu:** cinque posizioni (`menu-lab`, `menu-right`, `menu-header-right`, `menu-links`, `menu-footer`) in **Aspetto → Menu**, ciascuna con la propria variante in inglese. Una voce con sottovoci diventa un menu a tendina; il markup è condiviso da `template-parts/header/menu-nav-item.php`.
- **Lingue:** Polylang, con l'italiano come lingua predefinita. Il codice usa i wrapper `dli_*`, non le funzioni di Polylang.
- **Permessi:** due livelli, `dli_edit_site_configuration` (Super Editor e Amministratore) e `manage_options` (le tre schede riservate), in `inc/admin/options.php`.
- **Test e controlli:** unit, integrazione ed E2E (`tests/`), PHPCS e PHPStan di livello 5; i comandi sono in `AGENTS/ARCHITECTURE.md`.

## 9. Troubleshooting rapido

| Sintomo | Verifica |
|---|---|
| Una sezione della home non compare | È attiva in Sezioni HP? Ha pagine o contenuti che rispettano i criteri (pubblicati, senza password, lingua corrente, `Promuovi in Home`)? |
| Una pagina non è in «Card di navigazione» | `Promuovi in Home` è acceso? È una pagina di primo livello, o la riga ha un livello più ampio? |
| Una pagina non è in «Navigazione per pagine» | `Promuovi in Home` è acceso e la pagina ha almeno una sottopagina pubblicata? |
| «Indice di una pagina» manca in inglese | La pagina scelta ha la traduzione inglese pubblicata? |
| Colori del brand assenti | In Configurazione → Altro lo stile è «personalizzato»? (solo Amministratore) |
| Una pubblicazione non porta alla scheda | Il campo `Link dettaglio` della pubblicazione: scheda, sito esterno o allegato |
| L'import da Indico o da IRIS non parte | Attivazione e indirizzo nella scheda giusta? (solo Amministratore) |
| Il menu non cambia nel sito | Il menu è assegnato alla posizione giusta e salvato? |

## 10. Aggiornare un sito esistente alla versione 2.0.4
**Non serve nessuna migrazione dei dati** e nessuno script: il tema legge i dati già salvati con valori predefiniti e non li riscrive. Dopo l'aggiornamento conviene salvare una volta i menu e ricompilare le traduzioni con Loco Translate. Che cosa succede, campo per campo:

| Cosa c'era | Cosa succede |
|---|---|
| Righe di `Sezioni HP` salvate prima dei campi nuovi (sfondo, pagina, numero di elementi, livello) | Restano com'erano: senza `Sfondo sezione` vale lo sfondo di default della sezione; senza limiti valgono quelli predefiniti; un valore non valido è ignorato. Aprire e salvare la configurazione non aggiunge righe e non cambia quelle esistenti. |
| Le quattro sezioni nuove (`Card di navigazione`, `Navigazione per pagine`, `Indice di una pagina`, `Pannello tab`) | Non compaiono finché non aggiungi la riga in `Sezioni HP`. |
| Campi nuovi della scheda Pagina (`Promuovi in Home`, `Mostra le sottopagine come card`) | Spenti per tutte le pagine esistenti. Il riassunto delle pagine è ora disponibile, ma resta vuoto. |
| Testi in inglese mancanti (titoli, sottotitoli, tab, box) | Si usa il testo italiano: per i tab, l'etichetta e la descrizione; per il titolo di un box, il titolo italiano o, se vuoto, il nome del tipo. |
| Pagina scelta in una riga o in un tab che diventa bozza, protetta da password, cancellata o senza traduzione inglese | Non compare nel sito nella lingua in cui non è visibile (se è la pagina di `Indice di una pagina`, non compare la sezione). Una pagina esistente che diventa bozza o protetta resta nel selettore con l'indicazione «(non visibile)»; una pagina eliminata definitivamente non è più elencata. La sola mancanza della traduzione inglese non cambia la scelta italiana. |
| Box di `Contenuti in evidenza` con il vecchio campo `Template del box` | Il valore resta nel database ma non viene più letto: il layout lo decide il tipo di contenuto. |
| Pubblicazioni esistenti | Ottengono la scheda dedicata, all'indirizzo `/pubblicazione/<nome>/`: le regole degli indirizzi si aggiornano da sole alla prima visita, senza salvare i permalink. Il campo `Link dettaglio` vale «scheda» per tutte finché non lo cambi. Una descrizione vuota o `.` non mostra il blocco. |
| Pubblicazioni e API REST | Le pubblicazioni sono ora leggibili da `/wp-json/wp/v2/pubblicazione`, come gli altri contenuti, a meno che l'opzione «Abilita REST API» sia spenta (vale per chi non ha fatto l'accesso). |
| Costante `DLI_PUBLICATION_HAS_DETAIL` in `config-lab.php` | Rimossa: la scheda dedicata è sempre attiva. Un sito che l'avesse messa a `false` perde quella possibilità, e si sceglie per pubblicazione con `Link dettaglio`. |
| Classi PHP rinominate: `DLI_Page_Manager`, `DLI_Publication_Manager`, `DLI_ContentsManager` (con `DLI_OG_Wrapper` in un file a parte), `DLI_LabManager`, `DLI_Main_Menu_Walker` | I nomi di classe non sono salvati nel database. Un child theme o uno snippet che usasse i vecchi nomi va aggiornato. I walker `Menu_Right_Walker` e `Header_Menu_Walker`, mai usati, sono stati eliminati. |
| Plugin Better Aria Label Support | Non è più richiesto. |

Per i contenuti del sito di sviluppo Laboratorio1 (rinomina interna di «Card di navigazione») è stato usato uno script monouso, non necessario sugli altri siti: la sezione non era mai stata rilasciata.

### 10.1 Dalla versione 2.0.4 alla 2.0.5
Anche qui **nessuna migrazione dei dati**: i valori salvati restano com'erano.

- **Libreria dei campi (CMB2):** aggiornata da 2.11.0 a 2.13.5, con correzioni di sicurezza e di salvataggio (campi ripetibili, caselle con valore `0`, editor di testo dentro i gruppi) e la compatibilità con PHP 8.2–8.4. Dopo l'aggiornamento conviene aprire una volta **Configurazione**, controllare che i campi si vedano e si salvino, e svuotare la cache del browser se l'amministrazione appare con stili o comportamenti vecchi.
- **Salvataggio della configurazione:** una pagina di **Configurazione** si può salvare soltanto con il permesso della pagina (Super Editor e Amministratore; `manage_options` per le schede riservate), anche per una richiesta che non passa dalla pagina. Non cambia nulla per chi usa il pannello normalmente.

