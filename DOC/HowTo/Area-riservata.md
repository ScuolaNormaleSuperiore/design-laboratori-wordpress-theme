# Area riservata

Guida per l'amministratore di un sito federato: come gestire pagine e contenuti accessibili soltanto agli utenti autorizzati dell'istituto.

## Come funziona

**WP SAML Auth** autentica gli utenti presso l'Identity Provider (IdP) dell'istituto; **Groups** stabilisce quali contenuti possono leggere. Il tema gestisce la pagina di accesso e i due ruoli dell'area riservata.

Con la gestione attiva, l'utente preme **Accedi** nella pagina pubblica «Area riservata» e si autentica con il single sign-on SAML. Se la creazione automatica è attiva, al primo accesso nasce come **Utente in attesa** e legge il messaggio di attesa dell'approvazione. L'amministratore lo inserisce nei gruppi assegnati; il tema lo promuove a **Utente area riservata**.

| Creazione dell'utente | Ruolo iniziale | Approvazione |
| --- | --- | --- |
| Automatica, al primo accesso SAML | Utente in attesa | Inserimento nei gruppi e promozione automatica |
| Manuale, da parte dell'amministratore | Utente area riservata | Assegnazione dei gruppi autorizzati |

**I gruppi danno accesso ai contenuti; i ruoli distinguono gli utenti in attesa da quelli approvati.** Entrambi i ruoli sono privi dei permessi di WordPress: non consentono di usare la bacheca o modificare il proprio profilo. Nome utente ed email coincidono con l'email dell'istituto, che identifica l'account.

### Cosa vede chi

Esempio con il gruppo padre «Area riservata» e i figli «Area A» e «Area B». Chi appartiene a un figlio accede anche ai contenuti del padre; chi appartiene soltanto al padre non accede ai contenuti dei figli.

| Utente | Pagine pubbliche | Pagine di A | Pagine di A o B | Pagine del padre |
| --- | --- | --- | --- | --- |
| Visitatore o utente in attesa senza gruppi | Sì | No | No | No |
| Utente nel gruppo A | Sì | Sì | Sì | Sì |
| Utente nel gruppo B | Sì | No | Sì | Sì |
| Utente solo nel gruppo padre | Sì | No | No | Sì |
| Amministratore | Sì | Sì | Sì | Sì |

Chi non ha accesso non vede i contenuti riservati nei menu, nella ricerca, nei suggerimenti, nella mappa del sito o nelle sezioni della home. L'indirizzo diretto non restituisce più un 404, perché un contenuto riservato esiste: risponde con una pagina del tema che non mostra il titolo né il testo, e dipende da chi chiede.

| Chi apre l'indirizzo | Risposta |
| --- | --- |
| Un visitatore non collegato | **401**: «Contenuto riservato», con il pulsante **Accedi**, che porta al single sign-on e poi torna al contenuto richiesto |
| Un utente collegato senza permessi | **403**: «Non hai i permessi per vedere questo contenuto»; l'utente in attesa legge che la richiesta è in attesa di approvazione |
| Un utente con i permessi | Il contenuto |
| Un indirizzo che non esiste | 404, come prima |

La regola vale per tutti i contenuti pubblici che Groups riserva (pagine, articoli, notizie, eventi, persone, pubblicazioni e così via) e solo con la gestione dell'area riservata attiva e Groups installato; con la gestione spenta resta il 404. Le bozze e i contenuti non pubblicati non vengono mai rivelati. Una scheda che il tema disattiva apposta (per esempio una persona con la pagina di dettaglio spenta) resta un 404. La pagina di risposta non è indicizzata né messa in cache.

**Cosa si rivela.** Chi vede il 401 o il 403 capisce che a quell'indirizzo c'è un contenuto riservato, anche se non ne vede il titolo né il testo: con il 404 non lo avrebbe saputo. È una scelta voluta, per non far passare per inesistente un contenuto a cui si ha diritto e per dare un modo di accedere.

## Configurazione

### Prima di iniziare

- Fai un backup del database.
- Fai registrare il sito come Service Provider presso il gestore dell'IdP (Cineca per la Scuola Normale). Comunica l'Entity ID del sito, per esempio `urn:<dominio-del-sito>`, e l'indirizzo di ritorno `https://<dominio-del-sito>/wp-login.php`.
- Chiedi al gestore gli indirizzi dell'IdP, la mappatura degli attributi e il certificato pubblico di firma in formato PEM; verifica con lui l'impronta SHA-256. Chiedi anche quali utenti possono autenticarsi: con Auto Provision attivo tutti loro possono creare un account in attesa.
- Se usi una cache di pagina, escludi le pagine riservate e gli utenti collegati; le risposte 401 e 403 del tema inviano già le intestazioni di non-cache, ma una cache di pagina non deve servirle ad altri.
- Se usi Wordfence, concorda l'accesso durante la configurazione: login SAML falliti possono far scattare il blocco. L'elenco degli IP consentiti è in *Firewall → All Firewall Options → Allowlisted IPs*.

### 1. WP SAML Auth e ruolo predefinito

Installa e attiva **WP SAML Auth** (Pantheon). In *Impostazioni → WP SAML Auth* scegli il tipo di connessione **internal** e configura:

| Impostazione | Valore |
| --- | --- |
| Auto Provision | Attivo per creare gli utenti al primo accesso; spento per ammettere soltanto quelli creati prima dall'amministratore |
| Permit WordPress login | Attivo, per mantenere l'accesso con password dell'amministratore locale |
| Get User By | `email` |
| Base URL | `https://<dominio-del-sito>` |
| SP Entity Id | Quello registrato presso l'IdP |
| SP Assertion Consumer Service URL | `https://<dominio-del-sito>/wp-login.php` |
| IdP Entity Id e Single SignOn Service URL | Forniti dal gestore dell'IdP |
| IdP Single Logout Service URL | Vuoto; vedi [Limiti](#limiti) |
| x509 Certificate Path | Percorso del certificato PEM dell'IdP sul server |

Imposta la mappatura seguente, confermandola con il gestore dell'IdP:

| Campo WordPress | Attributo SAML |
| --- | --- |
| `user_login` e `user_email` | `urn:oid:0.9.2342.19200300.100.1.3` (mail) |
| `display_name` | `urn:oid:2.5.4.3` (cn) |
| `first_name` | `urn:oid:2.5.4.42` (givenName) |
| `last_name` | `urn:oid:2.5.4.4` (sn) |

In *Impostazioni → Generale → Ruolo predefinito del nuovo utente* scegli **Utente in attesa**. Questa impostazione vale per tutti i nuovi utenti del sito.

### 2. Groups

Installa e attiva **Groups** (itthinx).

1. In *Groups → Opzioni → Permissions* assegna all'Amministratore tutti i permessi e al Super Editor soltanto «Access Groups» e «Restrict Access»; a nessun altro ruolo. Lascia disattivato «Delete all Groups plugin data on deactivation».
2. Attiva le restrizioni per le Pagine e per i tipi di contenuto da riservare, per esempio le Notizie.
3. Crea un gruppo per ogni area. Per condividere contenuti tra tutte le aree puoi raccogliere i gruppi sotto un padre «Area riservata», usando il campo *Parent*.

**Non usare Registered per riservare contenuti:** ne fanno parte tutti gli utenti, compresi quelli in attesa.

### 3. Pagina di accesso e pagine riservate

Prepara una pagina **pubblica** «Area riservata», con un testo che spieghi a chi è rivolta. Puoi crearla a mano oppure usare *Strumenti DLI → Utilità → Ricarica i dati di attivazione*, che crea le pagine predefinite mancanti, compresa la traduzione inglese collegata tramite Polylang.

Nei siti esistenti lo strumento non modifica pagine e menu già presenti: controlla la pagina scelta nella configurazione e aggiungi il collegamento al menu se manca.

Per ogni pagina o contenuto da proteggere, scegli nel riquadro di Groups i gruppi autorizzati. Con più gruppi basta appartenere ad **almeno uno**; per tutte le aree puoi scegliere il loro gruppo padre.

**Ogni pagina va riservata esplicitamente.** La posizione nell'albero delle pagine non assegna permessi: una sottopagina senza gruppi resta pubblica anche se la madre è riservata. Controlla le restrizioni anche sulle traduzioni, soprattutto dopo un cambio dei gruppi.

### 4. Attivazione nel tema

Apri **Configurazione → Area riservata**, disponibile soltanto all'Amministratore.

| Campo | Impostazione |
| --- | --- |
| Gestione area riservata | Scegli **Sì** per attivare le funzioni del tema. Il default è **No**; con la gestione spenta gli altri campi sono disabilitati e ignorati |
| Pagina Area riservata | Seleziona la pagina pubblica preparata prima. Il tema vi mostra **Accedi**, oppure il nome dell'utente e **Esci**, nella lingua corrente |
| Accesso solo tramite SAML | Scegli se impedire l'accesso con password ai due ruoli dell'area riservata. Il default è **Sì**; la regola vale solo se il plugin WP SAML Auth è attivo |

Con la gestione attiva, i due ruoli vengono indirizzati alla pagina scelta dopo il login e quando aprono `/wp-admin`; la barra di amministrazione è nascosta. Chi avvia il login da un'altra pagina del sito torna a quella pagina.

Se la pagina non è scelta o non è pubblicata, la destinazione è la home e il riquadro di accesso non compare. Aggiungi allora un collegamento al login SSO, per esempio `/wp-login.php?action=wp-saml-auth`.

### Schermata di login

Con la gestione attiva, la schermata di accesso di WordPress (`/wp-login.php`) si presenta con l'identità del sito:

- il **logo** è quello di «Logo header» in Configurazione → Opzioni di base, oppure il logo predefinito del tema se non ne hai scelto uno, al posto del logo WordPress; cliccandolo si va alla home e il suo nome accessibile è il nome del sito;
- i **testi** di WP SAML Auth sono in italiano: «Autenticati con le credenziali istituzionali:», il pulsante «Accedi» e, sotto, «Oppure accedi con le credenziali di WordPress:» (compare solo se Permit WordPress login è attivo). In inglese: «Sign in with your institutional credentials:», «Login» (come gli altri pulsanti di accesso del tema) e «Or sign in with your WordPress credentials:».

Con la gestione spenta il login resta quello di WordPress, senza modifiche. La modifica dei testi usa il filtro `wp_saml_auth_login_strings` del plugin: se una versione molto vecchia non lo avesse, i testi resterebbero in inglese ma il login funzionerebbe.

### Accesso solo tramite SAML

Con l'opzione su **Sì**, **Utente in attesa** e **Utente area riservata** possono autenticarsi soltanto con il single sign-on SAML: login e recupero tramite password sono bloccati, una password non può essere data a questi utenti tramite la REST API e i campi password sono nascosti anche all'amministratore che modifica questi utenti. Gli altri ruoli mantengono le modalità di accesso abituali.

**La regola vale solo se il plugin WP SAML Auth è attivo.** Se il plugin non è attivo non c'è un single sign-on a cui mandare gli utenti, e bloccare la password li lascerebbe senza alcun modo di entrare: in quel caso la regola non si applica e l'accesso con password resta possibile. La scheda lo segnala con l'avviso «WP SAML Auth non è attivo: la regola non è applicata». Attenzione: il tema riconosce la **presenza** del plugin, non che il single sign-on **funzioni**. Con il plugin attivo ma configurato male (per esempio con un certificato mancante) e l'opzione su **Sì**, i due ruoli restano senza modo di entrare: l'uscita è mettere l'opzione su **No**; gli amministratori non sono toccati dalla regola.

**Il default è Sì.** Se la scheda non è mai stata salvata l'opzione conta come **Sì**; un valore salvato come **No** resta tale. Un utente creato a mano con una password può quindi accedere con la password solo se l'opzione è su **No** (o se il plugin non è attivo).

L'opzione vale anche per gli utenti creati a mano e non cambia Auto Provision. Le password esistenti restano salvate e tornano utilizzabili disattivando l'opzione, la gestione dell'area riservata o il plugin.

L'opzione non cambia ciò che i due ruoli possono fare in WordPress: in nessun caso possono modificare il proprio profilo (e quindi la propria password), perché non hanno permessi di WordPress. Non copre invece le application password, un link di reimpostazione della password già emesso e l'email «imposta la password» inviata a un utente creato a mano.

## Gestire gli utenti

Per approvare un nuovo utente, in *Utenti* filtra per **Utente in attesa**, apri la scheda e assegnagli i gruppi autorizzati. Con la gestione attiva il tema lo promuove automaticamente; Registered non conta.

Per creare un utente prima del suo accesso SAML, usa *Utenti → Aggiungi nuovo*, inserisci nome utente ed email uguali all'email restituita dall'IdP, scegli **Utente area riservata** e assegna i gruppi.

Per revocare l'accesso ai contenuti riservati, rimuovi l'utente da tutti i gruppi che lo autorizzano. Il solo cambio di ruolo non revoca i permessi assegnati da Groups.

## Limiti

- **Allegati:** i file nella Libreria media restano accessibili dal loro indirizzo diretto. Non caricare file riservati.
- **Uscita:** Esci chiude la sessione WordPress, non quella dell'IdP. Un nuovo accesso può non richiedere le credenziali; su un computer condiviso chiudi il browser.
- **Filtri degli elenchi:** categorie o anni usati soltanto da contenuti riservati possono comparire nei filtri.

## Privacy

Il sito conserva nome ed email ricevuti dall'IdP e appartenenze ai gruppi gestite sul sito. L'informativa deve descrivere finalità, conservazione e accesso a questi dati. Per le richieste degli utenti usa *Strumenti → Esporta/Cancella dati personali*.

## Verifiche dopo la configurazione

1. **Visitatore:** la pagina pubblica mostra Accedi; i contenuti riservati restituiscono 401 con il pulsante Accedi (e tornano al contenuto dopo il login) e non compaiono in ricerca, menu, home e mappa del sito; un utente collegato senza permessi riceve 403.
2. **Utente:** un nuovo account resta in attesa finché non viene assegnato a un gruppo; dopo l'approvazione vede solo i contenuti autorizzati. Se abiliti l'accesso solo SAML, verifica che password e recupero password siano bloccati con il plugin WP SAML Auth attivo, e che tornino utilizzabili se lo disattivi.
3. **Amministratore:** l'account locale continua a entrare con la password e può gestire utenti e gruppi.
