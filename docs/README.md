# Fee Checker

## Project Dependencies

- You must have docker desktop installed

## Quickstart

In the root of the project directory, run:

1. `docker-compose up -d` <-- note that the container will idle on boot so the `-d` is optional but gives you back your
   terminal window.
2. Either run `docker-compose exec -it php-service sh` from the root of the project, or enter the container via the Exec
   tab in the Docker Desktop GUI.

## Development

`docker compose up -d --build` <-- Force rebuild

## Some Decisions Tradeoffs

Decided not to provide helpful validation output to `STDOUT`. The spirit of the brief implied to me that it should
return a well-formatted fee or nothing and that the error code would cover the rest but this is something I would look
at further.

## TODO

- Special exception types?
- Some way to stop the stdErr from outputting when runing phpunit?
- Introduce a dependency injection container?