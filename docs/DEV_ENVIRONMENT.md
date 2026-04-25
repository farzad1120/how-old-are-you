# Development environment

This repo ships a Docker-based dev environment so you can build and exercise
the plugin without installing PHP or WordPress on your host.

## Prerequisites

- Docker 24+ with Compose V2

## Spin up WordPress + MariaDB

```sh
docker compose -f docker-compose.dev.yml up -d
```

Open <http://localhost:8080> and complete the WordPress install. The plugin is
auto-mounted at `wp-content/plugins/how-old-are-you/`; activate it under
**Plugins → Installed Plugins**.

Tear down (including the DB volume):

```sh
docker compose -f docker-compose.dev.yml down -v
```

## Run WP-CLI

```sh
docker compose -f docker-compose.dev.yml run --rm wpcli wp plugin list
docker compose -f docker-compose.dev.yml run --rm wpcli wp plugin activate how-old-are-you
```

## Run Composer / PHPCS / PHPUnit ad hoc

PHP/Composer are not required on the host — use the official images:

```sh
# Install dev dependencies
docker run --rm -v "$PWD":/app -w /app composer:2 install

# Lint
docker run --rm -v "$PWD":/app -w /app composer:2 run lint

# Test
docker run --rm -v "$PWD":/app -w /app composer:2 run test
```

Or, with the dev container running, drop into a PHP shell:

```sh
docker compose -f docker-compose.dev.yml exec wordpress bash
```
