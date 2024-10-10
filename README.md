# API Backend Coding Task

This is the technical test project for API oriented backends.

## Technical requirements

- [Docker](https://www.docker.com/)

## Installation

To use the application you'll need:

* The webserver container
* The database container
* An authorized user

Run the following commands to get everything in place:

1. `make build`
2. `make add-user`
3. Take a note of the token returned by the last command, you'll need it to issue requests.

## Test

```bash
make test
```

This command executes the test suite inside a docker container.

## Running the application

Use the command:

```bash
make start
```

To get the containers up and running.

Use the command:

```bash
make open
```

To open a browser at the root URL.
At `http://localhost:8080/docs` you'll see the Open API specification.

## Stop

```bash
make stop
```

This commands stops the docker containers necessary to run the application

## Rebuilding the documentation

```bash
make build-docs
```

This command generates the OpenAPI specification file (`var/openapi.yaml`).
Run this command to update the documentation available for clients.

## More commands

Use the command

```bash
make help
```

To get a full list of available commands

---

## Technologies used

* [Symfony](https://symfony.com/)
* [Doctrine](https://www.doctrine-project.org/projects/orm.html)
* [Swagger php](https://zircote.github.io/swagger-php/)
* [PhpUnit](https://phpunit.de/index.html)