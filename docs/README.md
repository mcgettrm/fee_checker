# Fee Checker

## Dependencies
- You must have docker desktop installed

## Quickstart
In the root of the project directory, run:
1. `docker-compose up -d` <-- note that the container will idle on boot so the `-d` is optional but gives you back your terminal window
2. `docker compose exec -it php-service sh`


## Development
`docker compose up -d --build` <-- Force rebuild