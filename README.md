# Oauth application

## Installation

```shell
make install
make start
make migrate
```

## Importer les données de base

```shell
cat dump.sql | docker-compose run --rm db -uoauth -poauth
```

## Run

```shell
make start
```
