# Manuale Utente Pannello Configurazione

## 1. Scopo
Questo manuale descrive il pannello di configurazione del tema (menu `Configurazione`) e spiega, tab per tab, a cosa serve ogni sezione e quali campi compilare.

## 2. Che cos'e' il pannello configurazione
Il pannello configurazione e' un insieme di tab nelle opzioni tema (`dli_options`) usato per governare:
- dati istituzionali del laboratorio
- layout e sezioni della homepage
- testi introduttivi delle pagine archivio dei content type
- integrazioni esterne (Indico, IRIS)
- impostazioni tecniche generali (stile, newsletter, analytics, API, SEO)

Nota: alcuni tab sono visibili solo ad utenti con permessi amministrativi.

## 3. Dove si gestisce nel backoffice
- Menu principale: `Configurazione`
- Navigazione: tab orizzontali (`Opzioni di base`, `Avvisi in Home`, `Sezioni HP`, ecc.)
- Salvataggio: pulsante `Salva` nella singola pagina tab

## 4. Tab e campi principali

### 4.1 Tab `Opzioni di base` (`dli_options`)
Funzione: configura identita' e contatti principali del laboratorio (header/footer e dati istituzionali).

Campi principali:
- `Tipologia`
  tipo: `text`
  obbligatorio: `Si`
  funzione: categoria/tipologia del laboratorio.
- `Nome Laboratorio`
  tipo: `text`
  obbligatorio: `Si`
  funzione: nome ufficiale del laboratorio.
- `Nome Laboratorio EN`
  tipo: `text`
  funzione: variante inglese del nome.
- `Tagline`
  tipo: `text`
  funzione: sottotitolo sintetico del laboratorio.
- `Tagline EN`
  tipo: `text`
  funzione: variante inglese della tagline.
- `Etichetta contatto`
  tipo: `text`
  funzione: etichetta contatti footer (override del nome laboratorio).
- `Citta'`
  tipo: `text`
  obbligatorio: `Si`
  funzione: localita' principale del laboratorio.
- `Indirizzo`
  tipo: `text`
  obbligatorio: `Si`
  funzione: indirizzo fisico del laboratorio.
- `Email`
  tipo: `text`
  obbligatorio: `Si`
  funzione: email contatti principale.
- `PEC`
  tipo: `text`
  funzione: casella PEC istituzionale.
- `Telefono`
  tipo: `text`
  obbligatorio: `Si`
  funzione: recapito telefonico principale.
- `Ente padre`
  tipo: `text`
  obbligatorio: `Si`
  funzione: nome dell'ente di appartenenza.
- `Url ente padre`
  tipo: `text`
  obbligatorio: `Si`
  funzione: link all'ente padre.
- `Visualizza il logo nello header`
  tipo: `radio_inline`
  funzione: abilita/disabilita il logo in testata.
- `Logo header`
  tipo: `file`
  funzione: logo principale del laboratorio.
- `Visualizza il logo nel footer`
  tipo: `radio_inline`
  funzione: abilita/disabilita il logo nel footer.
- `Logo footer`
  tipo: `file`
  funzione: logo specifico footer.
- `Logo per mobile`
  tipo: `file`
  funzione: logo alternativo nel menu mobile.

### 4.2 Tab `Avvisi in Home` (`home_messages`)
Funzione: gestisce messaggi di avviso mostrati in homepage.

Campi principali (gruppo ripetibile `messages`):
- `Selezione colore del messaggio`
  tipo: `radio_inline`
  funzione: stile visivo dell'avviso (`danger/success/warning/info`).
- `Testo`
  tipo: `textarea_small`
  funzione: testo dell'avviso (max 140 caratteri).
- `Collegamento`
  tipo: `text_url`
  funzione: link di approfondimento associato al messaggio.

### 4.3 Tab `Sezioni HP` (`homepage_sections`)
Funzione: controlla quali sezioni homepage sono attive e il loro ordine.

Campi principali (gruppo ripetibile `site_sections`):
- `Sezione`
  tipo: `select`
  funzione: identificativo della sezione da mostrare.
- `Abilita la sezione`
  tipo: `radio_inline`
  funzione: attiva/disattiva la sezione in home.
- `Mostra il titolo della sezione`
  tipo: `radio_inline`
  funzione: mostra/nasconde il titolo della sezione.

### 4.4 Tab `Layout HP` (`homepage`)
Funzione: configura hero, carousel e box contenuti in evidenza della homepage.

Campi principali:
- `Dimensione hero`
  tipo: `select`
  funzione: imposta hero grande/piccolo.
- `Titolo hero` / `Titolo hero ENG`
  tipo: `text`
  funzione: titolo della hero in IT/EN.
- `Testo hero` / `Testo hero ENG`
  tipo: `textarea`
  funzione: testo hero (max 140 caratteri).
- `Label bottone` / `Label bottone ENG`
  tipo: `text`
  funzione: etichetta CTA hero.
- `Url`
  tipo: `text`
  funzione: link CTA hero.
- `Immagine di sfondo`
  tipo: `file`
  funzione: background della hero.
- `Selezione Automatica` (carousel)
  tipo: `radio_inline`
  funzione: usa automaticamente contenuti promossi in carousel.
- `Selezione articoli`
  tipo: `custom_attached_posts`
  funzione: selezione manuale contenuti carousel (`post`, `notizia`, `evento`, `pubblicazione`).
- `featured_contents_*` (Box 1/2/3)
  tipo: `group` con campi `label`, `tipo contenuto`, `template`
  funzione: configura i tre box dei contenuti in evidenza.

### 4.5 Tab `Laboratorio` (`il_laboratorio`)
Funzione: testi della pagina panoramica "Il laboratorio".

Campi principali:
- `Etichetta` / `Etichetta ENG`
  tipo: `text`
  funzione: titolo sezione in IT/EN.
- `Descrizione` / `Descrizione ENG`
  tipo: `wysiwyg`
  funzione: testo introduttivo pagina laboratorio in IT/EN.

### 4.6 Tab `Blog` (`blog`)
Funzione: testo introduttivo della pagina blog.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: descrizione intro pagina blog (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: descrizione intro pagina blog (EN).

### 4.7 Tab `Novita'` (`notizie`)
Funzione: testi e parametri della pagina panoramica notizie/eventi collegati.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo introduttivo sezione novita' (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo introduttivo sezione novita' (EN).
- `Numero pagine collegate`
  tipo: `text_small` (numerico)
  funzione: limite massimo di notizie/eventi collegabili a una pagina.

### 4.8 Tab `Eventi` (`eventi`)
Funzione: testi introduttivi della pagina panoramica eventi.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo intro eventi (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo intro eventi (EN).

### 4.9 Tab `Persone` (`persone`)
Funzione: configura testi, filtri e opzioni di visualizzazione archivio persone.

Campi principali:
- `Descrizione Sezione Persone`
  tipo: `wysiwyg`
  funzione: testo introduttivo pagina persone (IT).
- `Descrizione Sezione Persone ENG`
  tipo: `wysiwyg`
  funzione: testo introduttivo pagina persone (EN).
- `Nascondi icona`
  tipo: `radio_inline`
  funzione: mostra/nasconde icona persona in elenco.
- `Modalita' scelta struttura`
  tipo: `select`
  funzione: filtro struttura con chip/select/disabilitato.
- `Filtra per TAG`
  tipo: `radio_inline`
  funzione: abilita filtro per tag.
- `Etichetta 'Seleziona TAG'` (+ ENG)
  tipo: `text`
  funzione: personalizza etichetta filtro.
- `Etichetta 'Tutti i TAG'` (+ ENG)
  tipo: `text`
  funzione: personalizza etichetta filtro globale.
- `Visualizza etichetta Dettagli`
  tipo: `radio_inline`
  funzione: mostra/nasconde label "Dettagli" nel dettaglio persona.

### 4.10 Tab `Pubblicazioni` (`pubblicazioni`)
Funzione: testi introduttivi della pagina panoramica pubblicazioni.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo intro pubblicazioni (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo intro pubblicazioni (EN).

### 4.11 Tab `Brevetti` (`brevetti`)
Funzione: testi introduttivi della pagina panoramica brevetti.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo intro brevetti (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo intro brevetti (EN).

### 4.12 Tab `Spin-off` (`spinoff`)
Funzione: testi introduttivi della pagina panoramica spin-off.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo intro spin-off (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo intro spin-off (EN).

### 4.13 Tab `Progetti` (`progetti`)
Funzione: testi introduttivi e opzioni filtro/paginazione della pagina progetti.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo intro progetti (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo intro progetti (EN).
- `Modalita' di paginazione`
  tipo: `select`
  funzione: mostra tutti i risultati o attiva paginazione.
- `Numero di elementi mostrati`
  tipo: `select`
  funzione: quanti elementi mostrare per pagina.
- `Etichetta 'Seleziona TAG'` (+ ENG)
  tipo: `text`
  funzione: personalizza label del filtro tag.
- `Etichetta 'Tutti i TAG'` (+ ENG)
  tipo: `text`
  funzione: personalizza label del filtro globale.
- `Visualizza etichetta Dettagli`
  tipo: `radio_inline`
  funzione: mostra/nasconde label "Dettagli" nella scheda progetto.

### 4.14 Tab `Indirizzi di ricerca` (`ricerca`)
Funzione: testi introduttivi della pagina panoramica attivita'/indirizzi di ricerca.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo intro indirizzi di ricerca (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo intro indirizzi di ricerca (EN).

### 4.15 Tab `Luoghi` (`luoghi`)
Funzione: testi introduttivi e opzione mappa della pagina luoghi.

Campi principali:
- `Descrizione Sezione Luoghi`
  tipo: `wysiwyg`
  funzione: testo intro luoghi (IT).
- `Descrizione Sezione Luoghi ENG`
  tipo: `wysiwyg`
  funzione: testo intro luoghi (EN).
- `Visualizza mappa`
  tipo: `radio_inline`
  funzione: controlla la posizione/visibilita' della mappa nella pagina.

### 4.16 Tab `Risorse tecniche` (`risorse-tecniche`)
Funzione: testi introduttivi della pagina panoramica risorse tecniche.

Campi principali:
- `Descrizione Sezione`
  tipo: `wysiwyg`
  funzione: testo intro risorse tecniche (IT).
- `Descrizione Sezione ENG`
  tipo: `wysiwyg`
  funzione: testo intro risorse tecniche (EN).

### 4.17 Tab `Sponsors` (`sponsors`)
Funzione: layout della sezione sponsor in homepage.

Campi principali:
- `Numero di elementi`
  tipo: `select`
  funzione: numero di sponsor mostrati per riga.

### 4.18 Tab `Socialmedia` (`socials`)
Funzione: abilita icone social e definisce i link canale.

Campi principali:
- `Mostra le icone social`
  tipo: `radio_inline`
  funzione: abilita social in header/footer.
- `Facebook`, `Youtube`, `Instagram`, `Twitter`, `Linkedin`, `GitHub`, `Pinterest`, `Mastodon`, `Iris`, `Alumni`
  tipo: `text_url`
  funzione: URL dei profili/pagine social o istituzionali.

### 4.19 Tab `Indico` (`indico`) - solo admin
Funzione: configura integrazione e import automatico eventi da Indico.

Campi principali:
- `Attiva l'integrazione con Indico`
  tipo: `radio_inline`
  funzione: abilita/disabilita import Indico.
- `Abilita messaggi debug`
  tipo: `radio_inline`
  funzione: abilita log debug in `error.log`.
- `Url Indico`
  tipo: `text`
  obbligatorio: `Si`
  funzione: endpoint base Indico.
- `Token API`
  tipo: `text` (password)
  funzione: token API per autenticazione.
- `Categoria`
  tipo: `text_small` (numerico)
  obbligatorio: `Si`
  funzione: ID categoria eventi da importare.
- `Keywords`
  tipo: `text`
  obbligatorio: `Si`
  funzione: parole chiave di filtro eventi (separate da virgola).
- `Lingua default`
  tipo: `select`
  funzione: lingua di import preferita.
- `Schedulazione`
  tipo: `select`
  funzione: frequenza automatica import.
- `Tipo import`
  tipo: `select`
  funzione: import definitivo o dry-run.
- `Scelta eventi`
  tipo: `select`
  funzione: criterio temporale eventi da importare.
- `Stato dell'oggetto importato`
  tipo: `select`
  funzione: stato WP del contenuto importato (`publish/draft`).
- `Evento esistente`
  tipo: `select`
  funzione: comportamento su record gia' esistenti (`update/ignore`).

### 4.20 Tab `Iris` (`iris`) - solo admin
Funzione: configura integrazione IRIS AP Brevetti e parametri di import.

Campi principali:
- `Attiva l'importazione dei brevetti`
  tipo: `radio_inline`
  funzione: abilita integrazione IRIS brevetti.
- `Abilita messaggi debug`
  tipo: `radio_inline`
  funzione: abilita log debug in `error.log`.
- `Url endpoint brevetti`
  tipo: `text`
  obbligatorio: `Si`
  funzione: endpoint web-service IRIS AP Brevetti.
- `Username`
  tipo: `text`
  obbligatorio: `Si`
  funzione: utente per autenticazione al servizio.
- `Password`
  tipo: `text` (password)
  funzione: password per autenticazione.
- `Schedulazione`
  tipo: `select`
  funzione: frequenza import automatico.
- `Tipo import`
  tipo: `select`
  funzione: import definitivo o dry-run.
- `Brevetto esistente`
  tipo: `select`
  funzione: comportamento su record gia' importati (`update/ignore`).

### 4.21 Tab `Altro` (`setup`) - solo admin
Funzione: impostazioni tecniche trasversali (stile, newsletter, login, lingua, analytics, API, SEO).

Campi principali:
- `Stile del sito`
  tipo: `select`
  funzione: stile grafico (`standard` / `custom`).
- `Attiva la newsletter`
  tipo: `radio_inline`
  funzione: abilita modulo/newsletter di sito.
- `Gestore delle newsletter`
  tipo: `select`
  funzione: provider newsletter (es. `Brevo`).
- `Token API`
  tipo: `text` (password)
  funzione: credenziale API provider newsletter.
- `ID della lista`
  tipo: `text_small` (numerico)
  funzione: lista contatti usata dal provider.
- `ID del template`
  tipo: `text_small` (numerico)
  funzione: template double opt-in.
- `Pulsante per il login visibile`
  tipo: `radio_inline`
  funzione: mostra/nasconde bottone login.
- `Selettore lingua visibile`
  tipo: `radio_inline`
  funzione: mostra/nasconde switch lingua.
- `Codice analytics`
  tipo: `textarea_code`
  funzione: snippet Web Analytics Italia.
- `Abilita REST API`
  tipo: `radio_inline`
  funzione: abilita endpoint REST del tema.
- `Enable internal SEO management`
  tipo: `radio_inline`
  funzione: abilita gestione interna SEO/OG o delega a plugin esterno.

## 5. Funzioni operative per ciascun tab
Di seguito le operazioni pratiche supportate nei singoli tab.

### 5.1 `Opzioni di base`
- Modifica campi singoli: inserire/aggiornare valori testuali, logo e contatti.
- Reset pratico di un valore: svuotare il campo e salvare (se non obbligatorio).
- Upload logo: usare `Carica file` nel campo `file`, poi `Salva`.

### 5.2 `Avvisi in Home`
- Creare un avviso: nel gruppo `messages` cliccare `Aggiungi un messaggio`.
- Ordinare avvisi: trascinare i blocchi del gruppo nell'ordine desiderato.
- Rimuovere avvisi: aprire il blocco e cliccare `Rimuovi il messaggio`.
- Modificare un avviso: aggiornare colore, testo e link nel blocco esistente.

### 5.3 `Sezioni HP`
- Aggiungere una sezione home: nel gruppo `site_sections` cliccare `Aggiungi la sezione`.
- Ordinare le sezioni: trascinare i blocchi per impostare l'ordine di rendering.
- Disattivare una sezione senza cancellarla: impostare `Abilita la sezione` su `No`.
- Rimuovere una sezione: cliccare `Rimuovi la sezione` nel blocco.

### 5.4 `Layout HP`
- Hero: aggiornare titolo/testo/immagine e salvare.
- Carousel automatico: impostare `Selezione Automatica` su `Si`.
- Carousel manuale: impostare `Selezione Automatica` su `No` e compilare `Selezione articoli`.
- Box contenuti in evidenza: aprire `Box 1/2/3`, impostare tipo/template/label e salvare.

### 5.5 `Laboratorio`
- Aggiornare i testi di pagina: modificare `Etichetta` e `Descrizione` (IT/EN).
- Rimozione testo: cancellare contenuto del campo non obbligatorio e salvare.

### 5.6 `Blog`
- Aggiornare descrizione archivio blog: modificare campi `Descrizione Sezione` (IT/EN).

### 5.7 `Novita'`
- Aggiornare descrizioni sezione: modificare i campi IT/EN.
- Regolare il limite collegamenti: aggiornare `Numero pagine collegate`.

### 5.8 `Eventi`
- Aggiornare descrizioni sezione eventi: modificare i campi IT/EN.

### 5.9 `Persone`
- Configurare filtri: impostare modalita' filtro (`chip`, `combobox`, `disabled`).
- Attivare/disattivare filtro tag: usare `Filtra per TAG`.
- Personalizzare etichette filtro: aggiornare etichette IT/EN.
- Gestire visibilita' elementi UI: usare `Nascondi icona` e `Visualizza etichetta Dettagli`.

### 5.10 `Pubblicazioni`
- Aggiornare descrizione archivio pubblicazioni: modificare campi IT/EN.

### 5.11 `Brevetti`
- Aggiornare descrizione archivio brevetti: modificare campi IT/EN.

### 5.12 `Spin-off`
- Aggiornare descrizione archivio spin-off: modificare campi IT/EN.

### 5.13 `Progetti`
- Configurare paginazione: impostare `Modalita' di paginazione` e `Numero di elementi mostrati`.
- Configurare filtri tag: personalizzare etichette IT/EN.
- Gestire etichetta dettagli: usare `Visualizza etichetta Dettagli`.

### 5.14 `Indirizzi di ricerca`
- Aggiornare descrizione archivio: modificare campi IT/EN.

### 5.15 `Luoghi`
- Aggiornare descrizione archivio: modificare campi IT/EN.
- Gestire mappa: attivare/disattivare `Visualizza mappa`.

### 5.16 `Risorse tecniche`
- Aggiornare descrizione archivio: modificare campi IT/EN.

### 5.17 `Sponsors`
- Regolare layout sponsor: impostare `Numero di elementi` per riga.

### 5.18 `Socialmedia`
- Attivare social in frontend: impostare `Mostra le icone social` su `Si`.
- Aggiungere o aggiornare link social: compilare i campi URL.
- Rimuovere un social: svuotare il relativo campo URL e salvare.

### 5.19 `Indico` (solo admin)
- Abilitare integrazione: attivare `Attiva l'integrazione con Indico`.
- Configurare import: compilare endpoint/filtri/categoria e impostare schedulazione.
- Simulare import: usare `Tipo import = Dry Run`.
- Passare in produzione: usare `Tipo import = Finalizza import`.
- Gestire duplicati: scegliere `Evento esistente = Aggiorna` oppure `Ignora`.

### 5.20 `Iris` (solo admin)
- Abilitare integrazione: attivare `Attiva l'importazione dei brevetti`.
- Configurare credenziali: compilare endpoint, username e password.
- Test import: impostare `Tipo import = Dry Run`.
- Import definitivo: impostare `Tipo import = Finalizza import`.
- Gestire record esistenti: scegliere `Brevetto esistente = Aggiorna` oppure `Ignora`.

### 5.21 `Altro` (solo admin)
- Stile sito: selezionare `standard` o `custom`.
- Newsletter: attivare, impostare provider e credenziali.
- Login e lingua: attivare/disattivare pulsante login e selettore lingua.
- Analytics: aggiornare codice in `Codice analytics`.
- API/SEO: attivare o disattivare `REST API` e gestione SEO interna.

## 6. Campi obbligatori (riepilogo rapido)
Campi marcati obbligatori nel codice:
- Tab `Opzioni di base`: `Tipologia`, `Nome Laboratorio`, `Citta'`, `Indirizzo`, `Email`, `Telefono`, `Ente padre`, `Url ente padre`
- Tab `Indico`: `Url Indico`, `Categoria`, `Keywords`
- Tab `Iris`: `Url endpoint brevetti`, `Username`

## 7. Check-list rapida prima del salvataggio
- compilati tutti i campi obbligatori del tab corrente
- testi IT/EN coerenti dove previsti
- URL validi per link esterni/social/integrazioni
- schedulazioni import coerenti con il processo operativo
- credenziali (token/password) aggiornate e testate
- dopo ogni modifica, eseguito `Salva` e verificata la resa frontend
