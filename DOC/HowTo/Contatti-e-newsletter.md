# Contatti e newsletter

## Pagina Contatti

Per usare il modulo crea una pagina WordPress e assegna il template `Contatti`. I recapiti mostrati nella colonna laterale arrivano da `Configurazione > Opzioni di base`; verifica almeno email, telefono e indirizzo prima di pubblicare.

Il modulo invia il messaggio all'indirizzo email configurato per il laboratorio. Richiede il CAPTCHA: se il servizio non è disponibile, il pulsante di invio resta disabilitato. Dopo una modifica esegui sempre una prova con un indirizzo controllato e verifica sia la ricezione sia il mittente della mail.

## Pagina Newsletter

Per l'iscrizione crea una pagina con il template `Newsletter`. Un amministratore configura il servizio in `Configurazione > Altro`:

1. attiva la newsletter;
2. seleziona il gestore disponibile, per esempio Brevo;
3. inserisce token API, ID lista e ID del template di doppio consenso;
4. salva e prova l'iscrizione con un indirizzo di test.

Token e identificativi del provider sono credenziali riservate: non copiarli in pagine, screenshot, ticket o documentazione pubblica. Il form richiede CAPTCHA e invia una richiesta di conferma; l'iscrizione va considerata completata solo dopo il messaggio del provider.

## Problemi frequenti

- **Il pulsante è disabilitato:** CAPTCHA o plugin necessario non disponibile.
- **Non arriva la mail Contatti:** verifica indirizzo configurato e consegna email del server.
- **Newsletter non confermata:** controlla token, ID lista, template del provider e cartella spam dell'indirizzo di prova.
- **Recapiti errati nella pagina:** aggiorna `Configurazione > Opzioni di base` e riprova la pagina pubblica.
