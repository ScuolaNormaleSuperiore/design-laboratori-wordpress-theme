# Manuale Gestione Contenuti Multilingua (ITA/ENG)

## 1. Scopo
Questo manuale descrive come gestire i contenuti in doppia lingua (`Italiano` e `English`) usando le funzioni base di Polylang nel tema.

## 2. Che cos'e' la gestione multilingua
Nel sito la gestione lingue e' basata su:
- plugin `Polylang`
- due lingue attive: `it` (default) e `en`
- traduzioni collegate tra contenuti equivalenti (post, pagine, CPT, tassonomie)

Obiettivo operativo:
- ogni contenuto pubblicato in italiano deve avere la sua versione inglese collegata
- tassonomie e pagine di archivio devono essere coerenti in entrambe le lingue

## 3. Prerequisiti e configurazione iniziale

### 3.1 Prerequisiti
- Plugin `Polylang` installato e attivo.
- Lingue configurate: `Italiano (it)` e `English (en)`.
- Lingua predefinita: `Italiano`.

### 3.2 Configurazione base in Polylang
Percorso: `Lingue > Lingue`
1. Verificare presenza di `Italiano` e `English`.
2. Verificare `Italiano` come lingua di default.
3. Salvare la configurazione.

Percorso: `Lingue > Impostazioni`
1. Verificare che i principali tipi di contenuto siano traducibili.
2. Verificare che le tassonomie principali siano traducibili.
3. Salvare.

Nota tecnica tema: il tema registra i CPT e le tassonomie traducibili via configurazione interna (integrazione Polylang).

## 4. Ambito contenuti traducibili
In generale, nel progetto sono gestiti in doppia lingua:
- pagine (`page`)
- articoli blog (`post`)
- content type custom (es. notizie, eventi, progetti, pubblicazioni, brevetti, spin-off, luoghi, risorse tecniche, banner, persone, indirizzi di ricerca)
- tassonomie (categorie, tag e tassonomie custom abilitate)

## 5. Flusso operativo standard (contenuti)

### 5.1 Creazione contenuto in italiano
1. Creare il contenuto in lingua `Italiano`.
2. Compilare titolo, corpo, campi ACF, tassonomie e immagine.
3. Pubblicare.

### 5.2 Creazione traduzione inglese
1. Aprire il contenuto italiano.
2. Nel box `Lingua` (Polylang), usare `+` su `English`.
3. Si apre una nuova scheda contenuto in inglese collegata all'originale.
4. Compilare titolo, corpo, campi ACF e tassonomie in inglese.
5. Pubblicare.

### 5.3 Verifica collegamento IT/EN
1. Tornare nella scheda italiana.
2. Verificare nel box `Lingua` che la traduzione EN sia associata.
3. Ripetere controllo nella scheda inglese (presenza collegamento verso IT).

## 6. Flusso operativo tassonomie (categorie, tag, tassonomie custom)

### 6.1 Creazione termini
1. Creare il termine in italiano.
2. Aprire il termine e aggiungere traduzione inglese dal box lingua.
3. Salvare.

### 6.2 Assegnazione termini ai contenuti
- Nella versione IT assegnare termini IT.
- Nella versione EN assegnare termini EN corrispondenti.

Nota: non riusare lo stesso termine italiano nei contenuti inglesi.

## 7. Gestione pagine e menu

### 7.1 Pagine statiche
- Ogni pagina istituzionale deve avere la relativa traduzione EN collegata.
- Verificare URL/slug e contenuti prima della pubblicazione.

### 7.2 Menu di navigazione
Percorso: `Aspetto > Menu`
1. Gestire un menu per IT e uno per EN (secondo configurazione Polylang).
2. Inserire nel menu EN solo pagine/contenuti EN.
3. Verificare che lo switch lingua porti alla pagina equivalente tradotta.

## 8. Gestione opzioni tema in doppia lingua
Nel pannello `Configurazione` molti campi hanno versione ENG dedicata (tipicamente nome campo con suffisso `ENG`).

Regola operativa:
- compilare il campo base per IT
- compilare il campo `ENG` per EN

Esempi tipici:
- `Nome Laboratorio` / `Nome Laboratorio EN`
- `Descrizione Sezione` / `Descrizione Sezione ENG`
- etichette filtro IT/ENG

## 9. Aggiornamento e manutenzione contenuti bilingue
Quando modifichi un contenuto IT gia' tradotto:
1. aggiornare prima IT
2. aprire la traduzione EN collegata
3. allineare i campi modificati
4. salvare entrambe le versioni

Consigli pratici:
- mantenere stessa struttura informativa tra IT e EN
- evitare traduzioni parziali su contenuti pubblici principali
- verificare periodicamente contenuti orfani (IT senza EN o EN senza IT)

## 10. Errori comuni da evitare
- creare contenuti EN non collegati all'originale IT
- duplicare manualmente senza usare il collegamento traduzione Polylang
- assegnare tassonomie IT dentro contenuti EN
- aggiornare solo una lingua su contenuti critici
- dimenticare i campi `ENG` nelle opzioni tema

## 11. Check-list rapida prima della pubblicazione
- contenuto IT pubblicato correttamente
- traduzione EN creata e collegata dal box lingua
- titolo, corpo e campi ACF coerenti in entrambe le lingue
- tassonomie corrette IT/EN
- immagine/allegati verificati
- eventuali campi configurazione `ENG` compilati
- controllo frontend con switch lingua su pagina IT e EN
