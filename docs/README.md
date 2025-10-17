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

- A bit uneasy about the use of concrete classes for storing the breakpoint sets

## Extensibility

- Interfaces provide a high level of abstraction
- Strategy pattern allows novel algorithm implementations with minor blast radius
    - E.g: round up to nearest 10
    - E.g: implement something non linear
- `FeeStructure` domain object is a confluence of a breakpoint collection and a strategy, allowing a mix-and match of
  strategies and term lengths with a minor blast radius
- Built with hexagonal architecture in mind, the public interface of the `FeeCalculatorService` is protected from
  knowledge of the "request vector" by the `FeeCalculatorController`. A web controller could be built to process and
  validate web requests into a format that the domain can understand
- The `FeeStructureRepositoryInterface` protects the domain layer from knowledge of its persistence. This could be
  reimplemented to load data from a database instead of from the filesystem (which is effectively what I have done with
  the two BreakPoint classes).

## Further Development

Given more time I would

- Import a dependency injection container with autowiring etc etc

## TODO

- Special exception types?
- Some way to stop the stdErr from outputting when runing phpunit?
- Introduce a dependency injection container?
- What if the mappings aren't ordered?

## Requirements

[x] Values in between the breakpoints should be interpolated linearly between the lower bound and upper bound that they
fall between.

[x] The number of breakpoints, their values, or storage might change.

[x] The term can be either 12 or 24 (the number of months). You can also assume values will always be within this set.

[x] The fee should be rounded up such that the sum of the fee and the loan amount is exactly divisible by £5.

[x] The minimum amount for a loan is £1,000, and the maximum is £20,000.

[x] You can assume values will always be within this range but there may be any values up to 2 decimal places.