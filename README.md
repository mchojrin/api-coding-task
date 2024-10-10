# API Backend Coding Task

This is the technical test project for API oriented backends.

## Technical requirements

- [Docker](https://www.docker.com/)

## Build

```bash
make build
```

This command executes the Docker image building process and performs the [Composer](https://getcomposer.org) dependencies installation.

## Test

```bash
make test
```

This command executes the test suite inside a docker container.

## Run

```bash
make start
```

This commands starts the docker containers necessary to run the application

## Stop

```bash
make stop
```

This commands stops the docker containers necessary to run the application

## Using the API

After starting the services, open a browser window at `http://localhost:8080` to get a general idea of what's available. You can use the command `make open` for this purpose.

At `http://localhost:8080/docs` you'll see the Open API specification.

## Rebuilding the documentation

```bash
make build-docs
```

This command generates the OpenAPI specification file (`var/openapi.yaml`).
Run this command to update the documentation available for clients.

---

## Technologies used

* [Symfony](https://symfony.com/)
* [Doctrine](https://www.doctrine-project.org/projects/orm.html)
* [Swagger php](https://zircote.github.io/swagger-php/)
* [PhpUnit](https://phpunit.de/index.html)