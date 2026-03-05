# Manuale Operatore

Versione: 1.0  
Data: 2026-02-17  
Tema: `design-laboratori-wordpress-theme`

## 1. Scopo del manuale
Questo documento guida gli operatori editoriali nell'uso quotidiano del sito realizzato con il tema Design Laboratori Italia.
L'obiettivo e' pubblicare e aggiornare contenuti senza intervenire sul codice.

## 2. A chi e' rivolto
- Redattori
- Editor
- Super Editor
- Amministratori

## 3. Ruoli e permessi
Nel progetto e' presente il ruolo `Super Editor`, equivalente a un Editor con permessi aggiuntivi per:
- configurazione del sito (`WP -> Configurazione`)
- gestione menu (`WP -> Aspetto -> Menu`)

Indicazione pratica:
- `Redattore/Editor`: lavora su contenuti.
- `Super Editor`: oltre ai contenuti, gestisce configurazione funzionale del tema.
- `Amministratore`: gestione completa del sito e plugin.

## 4. Tipi di contenuto principali
Nel menu di amministrazione puoi trovare (nomi variabili in base alla lingua):
- Persone
- Progetti
- News / Notizie
- Eventi
- Luoghi
- Pubblicazioni
- Brevetti
- Spin-off
- Risorse tecniche
- Attivita' di ricerca (indirizzi di ricerca)
- Blog (post WordPress standard)
- Banner

## 5. Flusso editoriale consigliato
Per ogni nuovo contenuto:
1. Crea il contenuto dal tipo corretto (es. Evento, Notizia, Persona).
2. Compila titolo, contenuto, campi aggiuntivi (ACF), immagine in evidenza.
3. Assegna categorie/tassonomie richieste.
4. Verifica lingua (IT/EN) e, se necessario, crea la traduzione.
5. Salva bozza.
6. Verifica anteprima desktop/mobile.
7. Pubblica.

Checklist minima prima della pubblicazione:
- Titolo chiaro e coerente
- Data corretta (se evento/notizia)
- Link funzionanti
- Immagine presente e pertinente
- Traduzione presente se il contenuto deve essere bilingue

## 6. Gestione pagine e archivi
Il tema include pagine/template gia' predisposti, ad esempio:
- `Il Laboratorio`
- `Persone`
- `Progetti`
- `Eventi`
- `Notizie`
- `Pubblicazioni`
- `Brevetti`
- `Spin-off`
- `Risorse Tecniche`
- `Mappa del sito`
- `Contatti`
- `Ricerca`

Nota operativa:
- Le pagine archivio mostrano automaticamente i contenuti dei relativi post type.
- In caso di risultati inattesi, controlla prima filtri, tassonomie e stato di pubblicazione dei contenuti.

## 7. Configurazione del tema (`WP -> Configurazione`)
Da questa sezione si gestiscono i parametri globali del sito (testi, sezioni home, contatti, ecc.).

Suggerimenti:
- Modifica una sezione alla volta.
- Salva e verifica il frontend dopo ogni modifica.
- Evita di incollare codice HTML/JS non necessario nei campi testuali.

## 8. Home page: cosa puo' gestire l'operatore
In base alla configurazione del sito, puoi intervenire su:
- hero principale
- blocchi in evidenza
- sezioni eventi/news/pubblicazioni
- carousel
- banner
- avvisi

Procedura consigliata:
1. Aggiorna contenuti sorgente (post/pagine/campi opzione).
2. Controlla l'ordine delle sezioni configurate.
3. Verifica la resa finale in home.

## 9. Menu e struttura di navigazione
Percorso: `WP -> Aspetto -> Menu`

Buone pratiche:
- Mantieni etichette brevi e chiare.
- Evita duplicati nello stesso livello.
- Dopo modifiche importanti, controlla header, footer e mappa del sito.

## 10. Lingue (IT/EN)
Il tema supporta italiano e inglese con Polylang.

Regole operative:
- Crea sempre la versione IT (default) e, quando richiesto, la EN.
- Collega correttamente le traduzioni tra loro.
- Verifica che menu e pagine principali siano presenti in entrambe le lingue.

## 11. Import dati (se abilitato)
Sono previsti import:
- Eventi da Indico
- Brevetti da IRIS

Uso consigliato:
1. Lancia import solo con account autorizzato.
2. Dopo import, verifica titoli, date, link e immagini su un campione di record.
3. In caso di anomalie, sospendi nuovi import e segnala al team tecnico.

Riferimenti utili:
- `DOC/Import brevettiI da RIS.md`
- `DOC/Schema-Import-Eventi.drawio.pdf`

## 12. Controlli periodici consigliati
Frequenza settimanale:
- Eventi scaduti/non aggiornati
- Link rotti nei contenuti recenti
- Contenuti senza immagine
- Traduzioni mancanti

Frequenza mensile:
- Revisione menu e pagine istituzionali
- Verifica modulo contatti/newsletter
- Verifica coerenza homepage

## 13. Troubleshooting rapido
Problema: un contenuto non appare in archivio  
Controlla: stato pubblicato, lingua, tassonomie, data, filtri attivi.

Problema: menu non aggiornato nel frontend  
Controlla: menu corretto assegnato alla posizione giusta e salvataggio effettuato.

Problema: traduzione non visibile  
Controlla: associazione IT/EN in Polylang e presenza del contenuto in entrambe le lingue.

Problema: homepage non coerente  
Controlla: ordine sezioni in configurazione e presenza dei contenuti sorgente.

## 14. Buone pratiche operative
- Lavora in bozza quando possibile.
- Evita copia/incolla da Word/Google Docs senza pulizia.
- Usa titoli coerenti con lo stile editoriale del sito.
- Mantieni immagini leggere e con nome file descrittivo.
- Registra le modifiche rilevanti (chi, cosa, quando).

## 15. Contatti e documenti correlati
Documentazione tecnica/progetto:
- `README.md`
- `DOC/HowTo Aggiornare Bootstrap Italia.md`
- `DOC/Migrazione da 14x a 15x.md`
- `DOC/Migrazione da 16x a 17x.md`

Per segnalare bug o anomalie funzionali: usare il canale interno del team tecnico o la issue tracker di progetto.
