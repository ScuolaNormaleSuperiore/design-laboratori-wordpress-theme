# Demo locale con Docker

Questa configurazione avvia una demo completa del tema: WordPress, i plugin
necessari e il sito di esempio **Demo Lab** con contenuti dimostrativi. Usarla
per prove locali e dimostrazioni, mai come ambiente di produzione.

## Requisiti

- Docker Desktop o un'installazione Docker compatibile.
- Una porta HTTP locale libera.

## Avvio rapido

Dalla radice del repository, creare l'immagine:

```sh
docker build -t demolab-img -f SETUP/Docker/Dockerfile SETUP/Docker
```

Avviare quindi il container. La porta del database resta disponibile solo dal
computer locale:

```sh
docker run -p 80:80 -p 127.0.0.1:3306:3306 --name=demolab -d demolab-img
```

Aprire il sito in `http://localhost/` e il pannello WordPress in
`http://localhost/wp-admin/`. L'account amministratore della demo è
`manager` con password `password`; usarlo esclusivamente in questo ambiente
locale.

Adminer è disponibile in `http://localhost/adminer.php` per ispezionare il
database della demo. Usare i parametri `Mysql`, `127.0.0.1`, utente `admin`,
password `admin` e database `demolabdb`.

## Gestione del container

Per entrare nella shell del container:

```sh
docker exec -it demolab /bin/bash
```

Per fermarlo e rimuoverlo:

```sh
docker stop demolab
docker rm demolab
```

Ricostruire l'immagine dopo modifiche al Dockerfile o ai file sotto
`SETUP/Docker/`.

## Note

- Il Dockerfile clona il ramo `dev` del repository del tema durante la build.
  Per provare modifiche locali non ancora pubblicate, adattare consapevolmente
  il Dockerfile o usare un'altra procedura di montaggio locale.
- La guida tratta il container come un ambiente temporaneo: non inserire dati
  reali o segreti nelle immagini, nei dump o nei log.
