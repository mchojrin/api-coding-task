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

After starting the services, open a browser window at `http://localhost:8080` to get a general idea of what's available.

At `http://localhost:8080/docs` you'll see the Open API specification.

---

Type `make help` for more tasks present in `Makefile`.

## Functional requirements

**Implement a CRUD (Create-Read-Update-Delete) API.**

The following add-ons will be positively evaluated:

- Authentication
- Authorization
- Cache
- Documentation

---

A light infrastructure is provided with a populated MySQL database with example data and a web server using PHP built-in development server.

## Non functional requirements

- The presence of unit, integration and acceptance tests will positively appreciated.
- Use whatever you want to achieve this: MVC, hexagonal arquitecture, DDD, etc.
- A deep knowledge about SOLID, YAGNI or KISS would be positively evaluated.
- DevOps knowledge (GitHub Actions, Jenkins, etc.) would be appreciated too.
- It's important to find a balance between code quality and deadline; releasing a non functional application in time or a perfect application out of time may be negatively evaluated.
- Good and well-documented commits will be appreciated.
- Efficient and smart use of third party libraries will be positively appreciated.

---

Beyond the requirements of this test we want to see what you can do, feel free to show us your real potential and, the
most important part, have fun!

