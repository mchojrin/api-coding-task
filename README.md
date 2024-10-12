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

## Altering data

To alter data issue non-GET requests to the desired endpoint. 

Keep in mind that this will only work if you use a valid token, like the one you got from `make add-user`.

For instance, to add a character you can use a command such as:

`curl -X POST -H 'X-AUTH-TOKEN: 1ee143556a6f260858b3b90f8cc51f01a45e' -H "Content-Type: application/json" -d '{"name":"Mauro", "faction_id": "2", "equipment_id": "2", "kingdom": "My kingdom", "birth_date": "1977-12-22"}' http://localhost:8080/characters/`

Replace `1ee143556a6f260858b3b90f8cc51f01a45e` with your token.

To delete a faction you can use a command such as:

`curl -X DELETE -H 'X-AUTH-TOKEN: 1ee143556a6f260858b3b90f8cc51f01a45e' -H "Content-Type: application/json" http://localhost:8080/factions/`

To update an equipment you can use a command such as:

`curl -X PATCH -H 'X-AUTH-TOKEN: 1ee143556a6f260858b3b90f8cc51f01a45e' -H "Content-Type: application/json" -d '{"type":"weapons"}' http://localhost:8080/equipments/3`

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

## Deployment

To deploy the application in a production environment, follow the same initial steps and then alter the contents of the file `app/.env` from

```
APP_ENV=dev
```

To

```
APP_ENV=prod
```

After this change, in case of failure, the user will receive a generic error instead of details that might jeopardize the availability of the service. 

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

## Future improvements

* Add logging
* Improve error handling
* Use finer-grained authorization
* Implement authentication via JWT
* Prepare a specific docker image for production based on [https-portal](https://github.com/SteveLTN/https-portal)
