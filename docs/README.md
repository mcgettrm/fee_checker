# Fee Checker

## Intro

Hi Lendable!

I have thoroughly enjoyed completing this test for you. It has been a great mix of architectural challenges, some
algorithmic complexity and awareness of PHP quirks.

Throughout the test, I have attempted to slightly overengineer the solution in order to demonstrate knowledge of some
common design patterns, hexagonal architecture, SOLID principles, DDD and clean code. I wrote much of the code using TDD
due to the high amount of precision in the requirements document - which was great fun!

I started with an "integration" test around the `calculate-fee` endpoint but eventually moved the functionality into
classes and the application structure that you can see. Then, I added unit tests as I fleshed out certain classes.

I hope you enjoy

You will find a PNG of a conceptual overview of the solution here in the docs folder.

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
`composer qa` <-- from within the project root when SSHd into the container. Runs phpstan and phpunit tests.

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
- Dependencies can be injected in the `calculate-fee` binary, allowing, for example, different FeeStructureFactory
  implementations to be provided, allowing different strategies to be injected.

## Further Development

Given more time I would:

- Import a dependency injection container with autowiring etc etc
- I might consider triggering the factory build method inside the service rather than in a repository. This kindof sits
  outside the repository's area of concern.
- I tend to find the `Service` name a bit generic, my services tend to follow the `facade` pattern; providing abstracted
  access to a subsystem. I'd probably rename it.
- Review the decision to handle all currencies in pence.

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