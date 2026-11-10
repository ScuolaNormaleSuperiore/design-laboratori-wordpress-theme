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

Chi non ha accesso non vede le pagine riservate nei menu, nella ricerca, nei suggerimenti, nella mappa del sito o nelle sezioni della home. L'indirizzo diretto restituisce una pagina 404.

## Configurazione

### Prima di iniziare

- Fai un backup del database.
- Fai registrare il sito come Service Provider presso il gestore dell'IdP (Cineca per la Scuola Normale). Comunica l'Entity ID del sito, per esempio `urn:<dominio-del-sito>`, e l'indirizzo di ritorno `https://<dominio-del-sito>/wp-login.php`.
- Chiedi al gestore gli indirizzi dell'IdP, la mappatura degli attributi e il certificato pubblico di firma in formato PEM; verifica con lui l'impronta SHA-256. Chiedi anche quali utenti possono autenticarsi: con Auto Provision attivo tutti loro possono creare un account in attesa.
- Se usi una cache di pagina, escludi le pagine riservate e gli utenti collegati.
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
| Accesso solo tramite SAML | Scegli se impedire l'accesso con password ai due ruoli dell'area riservata. Il default è **No** |

Con la gestione attiva, i due ruoli vengono indirizzati alla pagina scelta dopo il login e quando aprono `/wp-admin`; la barra di amministrazione è nascosta. Chi avvia il login da un'altra pagina del sito torna a quella pagina.

Se la pagina non è scelta o non è pubblicata, la destinazione è la home e il riquadro di accesso non compare. Aggiungi allora un collegamento al login SSO, per esempio `/wp-login.php?action=wp-saml-auth`.

### Accesso solo tramite SAML

Con l'opzione attiva, **Utente in attesa** e **Utente area riservata** possono entrare soltanto con il single sign-on: login e recupero tramite password sono bloccati e i campi password sono nascosti anche all'amministratore che modifica questi utenti. Gli altri ruoli mantengono le modalità di accesso abituali.

L'opzione vale anche per gli utenti creati a mano e non cambia Auto Provision. Le password esistenti restano salvate e tornano utilizzabili disattivando l'opzione o la gestione dell'area riservata.

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

1. **Visitatore:** la pagina pubblica mostra Accedi; le pagine riservate restituiscono 404 e non compaiono in ricerca, menu, home e mappa del sito.
2. **Utente:** un nuovo account resta in attesa finché non viene assegnato a un gruppo; dopo l'approvazione vede solo i contenuti autorizzati. Se abiliti l'accesso solo SAML, verifica che password e recupero password siano bloccati.
3. **Amministratore:** l'account locale continua a entrare con la password e può gestire utenti e gruppi.
