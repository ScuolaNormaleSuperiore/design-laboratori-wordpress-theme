# Area riservata

Questa guida spiega come configurare in un sito federato un'area riservata: pagine e contenuti visibili solo a chi si è autenticato con le credenziali dell'istituto (single sign-on SAML) ed è stato inserito in un gruppo. È rivolta all'amministratore del sito.

## Come funziona

| Componente | Che cosa fa |
| --- | --- |
| Plugin **WP SAML Auth** (Pantheon) | Login con l'Identity Provider (IdP) dell'istituto; crea l'utente al primo accesso |
| Plugin **Groups** (itthinx) | Gruppi di utenti; decide chi vede ogni pagina o contenuto riservato |
| Ruolo **Utente in attesa** (`dli_pending_user`) | Ruolo di chi si è appena registrato e non è ancora stato approvato. Nessun permesso |
| Ruolo **Utente area riservata** (`dli_reserved_area_user`) | Ruolo di un utente approvato. Nessun permesso di WordPress: non entra nella bacheca e non modifica il proprio profilo |
| Scheda **Configurazione → Area riservata** del tema | Attiva le funzioni del tema per i due ruoli, sceglie la pagina di accesso e il blocco delle password |

Il flusso è questo:

1. La persona apre la pagina pubblica «Area riservata» e preme **Accedi**.
2. Si autentica presso l'IdP dell'istituto. Al primo accesso WordPress crea il suo utente come **Utente in attesa** e la riporta sulla pagina «Area riservata», dove legge che la richiesta è in attesa di approvazione.
3. L'amministratore la inserisce in uno o più gruppi. Il tema la promuove a **Utente area riservata**.
4. Da quel momento vede le pagine dei suoi gruppi.

Il nome utente e l'email sono l'indirizzo email della persona, che è la chiave dell'utente e non cambia.

## Prima di iniziare

- Fai un backup del database.
- Fai registrare il sito come Service Provider presso chi gestisce l'IdP (per la Scuola Normale, Cineca). Servono l'Entity ID del sito (per esempio `urn:<dominio-del-sito>`) e l'indirizzo di ritorno `https://<dominio-del-sito>/wp-login.php`. Chiedi anche quali utenti l'IdP autentica: con la creazione automatica degli utenti, chiunque l'IdP accetti può creare un Utente in attesa.
- Se il sito usa Wordfence, aggiungi il tuo indirizzo IP in *Wordfence → Firewall → All Firewall Options → Allowlisted IPs*: una configurazione SAML sbagliata produce login falliti che possono far scattare il blocco del sito.
- Il sito non deve avere una cache di pagina che serva le pagine riservate a utenti diversi. Se ne aggiungi una, escludi le pagine riservate e gli utenti collegati.

## 1. WP SAML Auth

Installa e attiva WP SAML Auth, poi apri *Impostazioni → WP SAML Auth*. Tipo di connessione: **internal**.

| Impostazione | Valore |
| --- | --- |
| Auto Provision | Attivo per creare l'utente al primo accesso; spento se gli utenti vanno creati prima a mano (chi non è censito viene rifiutato) |
| Permit WordPress login | Attivo: l'amministratore locale continua a entrare con la password |
| Get User By | `email` |
| Base URL | `https://<dominio-del-sito>` |
| SP Entity Id | quello registrato presso l'IdP |
| SP Assertion Consumer Service URL | `https://<dominio-del-sito>/wp-login.php` |
| IdP Entity Id e Single SignOn Service URL | forniti dall'IdP (pre-produzione e produzione hanno valori diversi) |
| IdP Single Logout Service URL | vuoto (vedi «Limiti») |
| x509 Certificate Path | percorso del file del certificato dell'IdP, per esempio `wp-content/saml/idp.crt` |

Mappatura degli attributi:

| Campo WordPress | Attributo SAML |
| --- | --- |
| `user_login` | `urn:oid:0.9.2342.19200300.100.1.3` (mail) |
| `user_email` | `urn:oid:0.9.2342.19200300.100.1.3` (mail) |
| `display_name` | `urn:oid:2.5.4.3` (cn) |
| `first_name` | `urn:oid:2.5.4.42` (givenName) |
| `last_name` | `urn:oid:2.5.4.4` (sn) |

**Il certificato dell'IdP.** È il certificato pubblico con cui l'IdP firma le risposte; si trova nel metadata dell'IdP (XML pubblico), nell'elemento `<ds:X509Certificate>` del `KeyDescriptor` di firma dell'`IDPSSODescriptor`. Il file deve essere in formato PEM: la riga `-----BEGIN CERTIFICATE-----`, il contenuto base64 in righe da 64 caratteri, la riga `-----END CERTIFICATE-----`. Non salvare l'XML del metadata come certificato. Controlla l'impronta SHA-256 del file con chi gestisce l'IdP prima di usarlo. Un file vuoto o sbagliato non dà errori evidenti: il login ripresenta il modulo e nel log di WordPress compare `idp_cert_or_fingerprint_not_found_and_required`.

## 2. Ruolo dei nuovi utenti

In *Impostazioni → Generale → Ruolo predefinito del nuovo utente* scegli **Utente in attesa**. WP SAML Auth dà questo ruolo agli utenti che crea. L'impostazione vale per tutti i nuovi utenti del sito.

Se crei un utente a mano (con Auto Provision spento, o per una persona già nota), scegli direttamente **Utente area riservata**.

## 3. Groups

Installa e attiva Groups.

1. In *Groups → Opzioni → Permissions* lascia all'Amministratore tutti i permessi; al Super Editor dai solo «Access Groups» e «Restrict Access»; a nessun altro ruolo. Non attivare «Delete all Groups plugin data on deactivation».
2. In *Groups → Opzioni* attiva le restrizioni per le Pagine e per i tipi di contenuto che vuoi riservare (per esempio le Notizie).
3. In *Groups → Gruppi* crea un gruppo per ogni area: «Area A», «Area B»… (i nomi dipendono dal sito). Puoi creare un gruppo padre «Area riservata» e mettere le aree come suoi figli (campo *Parent* del gruppo). In Groups l'accesso **sale** lungo la gerarchia:
   - chi è in un gruppo figlio («Area A» o «Area B») vede anche le pagine riservate al gruppo padre «Area riservata»: è il modo più semplice per riservare una pagina a **tutte** le aree;
   - chi è solo nel gruppo padre non vede le pagine riservate ai figli. Per questo gli utenti si inseriscono nei gruppi delle aree, non nel gruppo padre.
4. **Non riservare mai una pagina al gruppo «Registered»**: ne fanno parte tutti gli utenti, compresi quelli in attesa.

## 4. Scheda Configurazione → Area riservata

La scheda è visibile solo all'Amministratore.

| Campo | Effetto |
| --- | --- |
| Gestione area riservata | **No** per default. Con **Sì** il tema attiva le funzioni descritte sotto. Con **No** gli altri campi sono disabilitati e ignorati |
| Pagina Area riservata | La pagina pubblica di accesso (consigliata, vedi [Pagine riservate](#5-pagine-riservate)). Riceve il riquadro «Accesso all'area riservata» ed è la pagina in cui arrivano i due ruoli dopo il login. Se non è scelta, si arriva alla home e il pulsante Accedi non compare |
| Accesso solo tramite SAML | **No** per default. Con **Sì** gli Utenti in attesa e gli Utenti area riservata possono entrare solo con il single sign-on. Vedi [Accesso solo tramite SAML](#accesso-solo-tramite-saml) |

Con la gestione attiva, per gli Utenti in attesa e gli Utenti area riservata:

- dopo il login si arriva alla pagina «Area riservata»; chi ha premuto Accedi da un'altra pagina del sito torna a quella pagina;
- chi apre la bacheca (`/wp-admin`) viene riportato alla pagina «Area riservata»;
- la barra di amministrazione è nascosta;
- un Utente in attesa inserito in un gruppo diventa Utente area riservata.

### Accesso solo tramite SAML

L'impostazione non stabilisce come vengono creati gli utenti, ma **come entrano**. Vale solo per chi ha il ruolo Utente in attesa o Utente area riservata, comunque sia stato creato (dal single sign-on o a mano dall'amministratore).

Con **Sì**, per questi utenti:

- il login con nome utente (o email) e password viene rifiutato con il messaggio «Per questo account l'accesso è possibile solo con il single sign-on dell'istituto», anche se l'utente ha una password;
- «Password dimenticata?» non funziona;
- nel profilo non compaiono i campi della password, neanche quando lo apre l'amministratore da *Utenti → Modifica*;
- la REST API rifiuta con un errore 403 una password inviata per loro, sia creando un utente con uno di questi ruoli sia modificandone uno esistente.

L'unico modo per entrare è il pulsante **Accedi** della pagina «Area riservata» (o il login SSO di WP SAML Auth).

Cosa **non** cambia:

- **Gli altri ruoli.** Amministratori, editor, autori e gli altri utenti del sito entrano con la password come prima.
- **La creazione a mano.** L'amministratore può ancora creare da *Utenti → Aggiungi nuovo* un utente con un ruolo dell'area riservata, per esempio per abilitarlo prima del suo primo accesso. WordPress gli assegna comunque una password, che però non serve. L'utente entra con il single sign-on solo se WP SAML Auth riconosce il suo account. Per questo crealo con la **stessa email** (o lo stesso nome utente) che arriva dall'IdP, secondo il campo scelto in *Get User By*.
- **La creazione automatica.** La creazione degli utenti al primo accesso dipende solo da *Auto Provision* di WP SAML Auth, non da questa impostazione.
- **Le password salvate.** Non vengono cancellate: se riporti l'impostazione a **No** (o spegni la gestione dell'area riservata), tornano valide.

Con **No** gli utenti dell'area riservata che hanno una password possono entrare anche con quella, oltre che con il single sign-on.

## 5. Pagine riservate

1. **Consigliato.** Usa una pagina pubblica di atterraggio **Area riservata** e sceglila nel campo «Pagina Area riservata» della scheda. È la pagina in cui l'utente arriva subito dopo il login. Il riquadro con il pulsante **Accedi** (o «Sei collegato come… / Esci») compare prima del testo.
   - **Il modo più rapido** è *Strumenti DLI → Utilità → Ricarica i dati di attivazione*. Lo strumento crea le pagine predefinite che mancano, tra cui «Area riservata» (`/area-riservata`) e la traduzione «Reserved area» (`/reserved-area`), già collegate in Polylang. Quando crea la pagina, se il campo «Pagina Area riservata» è vuoto, ci mette la pagina italiana. Non modifica le pagine che esistono già e non cambia una pagina già scelta. Se esiste già una pagina con indirizzo `/area-riservata`, non ne crea un'altra e non compila il campo: sceglila tu nella scheda.
   - Il campo si può sempre lasciare su **«Nessuna (home del sito)»**: i ricaricamenti successivi non lo modificano (vedi sotto *Senza una pagina scelta*).
   - **Nei siti nuovi** la pagina è collegata anche nel menu footer, in italiano e in inglese. Nei siti esistenti lo strumento non modifica il menu footer: se vuoi il collegamento, aggiungilo a mano in *Aspetto → Menu*.
   - In alternativa crea la pagina a mano, con la traduzione se il sito è in due lingue (il tema usa la pagina nella lingua corrente), e sceglila nella scheda.
   - Sostituisci il testo provvisorio con uno che spiega a chi è rivolta l'area. Non riservare la pagina a un gruppo: deve vederla anche chi non ha ancora fatto il login.
   - Lo strumento crea la pagina anche nei siti che non usano l'area riservata: lì, con la gestione spenta, è una pagina normale senza pulsante. Se non serve, mettila in bozza e toglila dal menu footer.
2. Crea le pagine riservate, per esempio come sottopagine: «Area A», «Area B».
3. In ogni pagina riservata, nel riquadro di Groups, scegli il gruppo o i gruppi che la vedono. Se scegli più gruppi, la pagina la vede chi è in **almeno uno** di essi: per una pagina comune ad «Area A» e «Area B» scegli entrambi i gruppi, oppure il gruppo padre «Area riservata», che vale per tutte le aree figlie.
   **È il gruppo a proteggere la pagina, non la sua posizione nell'albero delle pagine.** Una pagina messa sotto la pagina «Area riservata» senza un gruppo nel riquadro di Groups resta pubblica e la vedono tutti, anche i visitatori. Allo stesso modo una sottopagina non eredita il gruppo della pagina madre: va riservata anche lei.
4. **Traduzioni**: la traduzione creata dal pulsante di Polylang eredita il gruppo. Se cambi i gruppi dopo aver creato la traduzione, controlla anche la traduzione.

**Senza una pagina scelta** (campo vuoto, oppure pagina cestinata o non pubblicata) l'area riservata funziona lo stesso, con queste differenze:

- dopo il login, e quando aprono `/wp-admin`, gli Utenti in attesa e gli Utenti area riservata arrivano alla **home** nella lingua corrente;
- il riquadro con il pulsante **Accedi** non compare in nessuna pagina: per entrare servono un collegamento al login SSO aggiunto a mano (per esempio in un menu: `/wp-login.php?action=wp-saml-auth`) o la pagina di login di WordPress;
- l'Utente in attesa non legge il messaggio che lo avvisa di attendere l'approvazione e non ha il pulsante **Esci** nella pagina;
- chi preme Accedi partendo da una pagina del sito torna comunque a quella pagina.

Le pagine riservate non compaiono a chi non ha accesso: menu, ricerca, suggerimenti di ricerca, mappa del sito, card e sezioni della home che elencano pagine. Chi apre l'indirizzo diretto riceve una pagina 404.

## 6. Approvare un utente

1. In *Utenti* filtra per ruolo **Utente in attesa**.
2. Apri l'utente e, nel riquadro di Groups, inseriscilo nei gruppi che gli spettano.
3. Il ruolo passa da solo a **Utente area riservata** (con la gestione attiva). Il gruppo Registered non conta.

Per togliere l'accesso, togli l'utente dai gruppi; per bloccarlo del tutto, eliminalo o cambiagli ruolo.

## Cosa vede chi

Esempio con il gruppo padre «Area riservata» e i figli «Area A» e «Area B»:

| Utente | Pagine senza gruppo | Pagine del gruppo Area A | Pagine dei gruppi Area A e Area B | Pagine del gruppo padre Area riservata |
| --- | --- | --- | --- | --- |
| Visitatore | Sì | No | No | No |
| Utente in attesa senza gruppi | Sì | No | No | No |
| Utente nel gruppo Area A | Sì | Sì | Sì | Sì |
| Utente nel gruppo Area B | Sì | No | Sì | Sì |
| Utente solo nel gruppo padre | Sì | No | No | Sì |
| Amministratore | Sì | Sì | Sì | Sì |

«Pagine senza gruppo» comprende anche le pagine messe sotto la pagina «Area riservata» a cui non è stato assegnato un gruppo.

## Limiti

- **Allegati.** Un file caricato nella Libreria media è raggiungibile da chiunque conosca il suo indirizzo, anche se è inserito in una pagina riservata. Non caricare file riservati.
- **Uscita.** «Esci» chiude la sessione di WordPress, non quella dell'IdP: finché il browser resta aperto, un nuovo accesso può non chiedere la password. Su un computer condiviso chiudi il browser.
- **Filtri degli elenchi.** Nei filtri per categoria e per anno può comparire una categoria o un anno usati solo da contenuti riservati (non il loro titolo).
- **Utenti creati prima.** Il tema dà agli utenti creati da WP SAML Auth un indirizzo pubblico neutro (`utente-<ID>`) al posto di quello ricavato dall'email. Gli utenti creati prima di questa versione lo mantengono finché non viene cambiato (per esempio con WP-CLI: `wp user update <ID> --user_nicename=utente-<ID>`).
- **Ruoli già esistenti.** I due ruoli vengono creati solo se mancano e non vengono mai riscritti.

## Privacy

Il sito conserva email, nome e appartenenza ai gruppi ricevuti dall'IdP. L'informativa privacy del sito deve indicare titolare, finalità, durata della conservazione e chi vede questi dati. Per esportare o cancellare i dati di una persona usa gli strumenti di *Strumenti → Esporta/Cancella dati personali* di WordPress.

## Verifiche dopo la configurazione

1. Da una finestra privata, senza login: la pagina «Area riservata» mostra il pulsante Accedi; una pagina riservata dà 404 e non compare in menu, ricerca, home e mappa del sito.
2. Login con un account dell'istituto mai usato: l'utente nasce Utente in attesa, torna sulla pagina «Area riservata» e legge il messaggio di attesa; `/wp-admin` lo riporta alla pagina.
3. Inserisci l'utente nel gruppo A: diventa Utente area riservata e vede le pagine del gruppo A, non quelle del B.
4. L'amministratore locale entra ancora con la password.
5. Con «Accesso solo tramite SAML» attivo: il login con password di un utente dell'area riservata viene rifiutato, «password dimenticata» non funziona e nel suo profilo non ci sono i campi della password; lo stesso utente entra con il pulsante Accedi; un editor entra ancora con la password.
