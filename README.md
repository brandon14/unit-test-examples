<p align="center">
  <a href="https://github.com/brandon14/unit-test-examples/actions/workflows/run-tests.yml"><img src="https://img.shields.io/github/workflow/status/brandon14/unit-test-examples/run-tests?style=flat-square&maxAge=36000" alt="Build Status"></a>
  <a href="https://codeclimate.com/github/brandon14/unit-test-examples/maintainability"><img src="https://img.shields.io/codeclimate/maintainability/brandon14/unit-test-examples.svg?style=flat-square" alt="Code Climate Maintainability"></a>
  <a href="https://codecov.io/gh/brandon14/unit-test-examples"><img src="https://img.shields.io/codecov/c/github/brandon14/unit-test-examples.svg?style=flat-square" alt="CodeCov"></a>
  <a href="https://github.com/brandon14/unit-test-examples/blob/master/LICENSE"><img src="https://img.shields.io/github/license/brandon14/unit-test-examples.svg?style=flat-square" alt="License"></a>
</p>
<p align="center">
  <a href="https://github.com/brandon14/unit-test-examples/issues"><img src="https://img.shields.io/github/issues/brandon14/unit-test-examples.svg?style=flat-square" alt="Issues"></a>
  <a href="https://github.com/brandon14/unit-test-examples/issues?q=is%3Aissue+is%3Aclosed"><img src="https://img.shields.io/github/issues-closed/brandon14/unit-test-examples.svg?style=flat-square" alt="Issues Closed"></a>
  <a href="https://github.com/brandon14/unit-test-examples/pulls"><img src="https://img.shields.io/github/issues-pr/brandon14/unit-test-examples.svg?style=flat-square" alt="Pull Requests"></a>
  <a href="https://github.com/brandon14/unit-test-examples/pulls?q=is%3Apr+is%3Aclosed"><img src="https://img.shields.io/github/issues-pr-closed/brandon14/unit-test-examples.svg?style=flat-square" alt="Pull Requests Closed"></a>
</p>

# Unit Test Examples

## Table Of Contents

1. [Requirements](https://github.com/brandon14/unit-test-examples#requirements)
2. [Purpose](https://github.com/brandon14/unit-test-examples#purpose)
3. [Standards](https://github.com/brandon14/unit-test-examples#standards)
4. [Coverage](https://github.com/brandon14/unit-test-examples#coverage)
5. [Contributing](https://github.com/brandon14/unit-test-examples#contributing)
6. [Documentation](https://github.com/brandon14/unit-test-examples#documentation)

## Requirements

| Tech                                 | Version  |
| ------------------------------------ | -------- |
| [PHP](https://secure.php.net/)       | >= 7.4.0 |
| [Composer](https://getcomposer.org/) | *        |

| PHP Extension    | Version  |
| ---------------- | -------- |
| ext-pdo          | *        |
| ext-redis        | >= 5.0.0 |
| ext-zend-opcache | *        |


## Purpose

This repository serves to demonstrate writing unit test for a PHP library using PHP Unit.
These tests are in no way perfect, but I tried to get good coverage while still
providing meaningful test. After all you can have 100% coverage and still not really
test the full functionality of your application.

The project is composed of two *"service"* classes that make up the functionality
of this mock library. One is the `LastModified` service and the other is the `StatusService`.

The `LastModified` service provides a way to register `LastModifiedTimeProvider` classes
that can return an `int` time value that represents the time that whatever the provider is
representing (i.e. Users, files, Products, etc.) was last modified. The service can take
1 to many providers, and return the most recent modified time. If you could imagine, this
could be used as part of a blog website were the providers could be one to iterate over
the webserver files to get the last modified time, and another provider to check for the
most recent blog post in the database. The service also facilitates getting the last modified
time for a single provider or an array of registered providers.

The `StatusService` functions in a similar way with `StatusServiceProviders` that can
return an array containing the "status" of whatever the provider is representing (i.e.
web service, database, cache service, etc). The service facilitates getting the status of
a single provider, many, or all providers much like the `LastModified` service. As you
could imagine, this could be useful in many ways. If you had a website that hit a database,
you could use this service to provide an API to get the status of the database.

With both services, I provided a couple simple providers to provide an example on how
to implement the providers. Also as stated above, the test serve to show how to write
PHP Unit tests and have examples of mocking the filesystem, and through clean architecture
you can have classes that need external service such as databases and cache services and
be able to write unit tests without breaking outside the boundary of the application
using mock objects.

When running the tests via `phpunit`, the order of the tests are randomized. The reason for this
is so that you can identify tests that depend upon other tests due to shared state (i.e. database state).
In these examples, there is no shared state amongst tests, but it is still
good to randomize unit test order so that none of these issues will creep into the project.

## Standards

This project adheres to PSR standards where applicable. Also I tried to design this example
project using a clean architecture that promotes SOLID principles and seeks to help
others in writing cleaner code. I am no expert by any means, but I feel although the
example services may be a bit contrived, the implementation of them follow at least good
practices for the most part.

I also wanted to use at least PHP 7.1 to show off easier input type validation for method
parameters. By making PHP more strict, not only do we help the compiler out, it also makes
our code less error prone. Imagine writing a function the takes an array as a parameter with
no type hint, and 4 years down the road some new developer doesn't read the docs (or they weren't
there) and passes a string into it, and :boom:, some code just blew up.

## Coverage

The latest code coverage information can be found via [Codecov](https://codecov.io/gh/brandon14/unit-test-examples). We
strive to maintain 100% coverage as this is an example repo highlighting writing unit tests in PHP.

## Contributing

Got something you wanna add? Found a bug or otherwise bad code? Feel free to submit pull
requests to add in new features, fix bugs, or clean things up. Just be sure to follow the
[Code of Conduct](https://github.com/brandon14/unit-test-examples/blob/master/.github/CODE_OF_CONDUCT.md)
and [Contributing Guide](https://github.com/brandon14/brandonclothier.me/blob/master/.github/CONTRIBUTING.md),
and I encourage creating clean and well described pull requests if possible.

## Documentation

Documentation to this project can be found [here](https://brandon14.github.io/unit-test-examples/). While this isn't a library
and does not really need documentation per-se, I wanted to add auto-building the documentation as a Github Actions in order
to explore the possibilities with Github Actions.

This code is released under the MIT license.

Copyright &copy; 2018-2021 Brandon Clothier

<p align="center">
  <a href="https://forthebadge.com"><img src="https://forthebadge.com/images/badges/60-percent-of-the-time-works-every-time.svg" alt="Works 60% Of The Time"></a>
  <a href="https://forthebadge.com"><img src="https://forthebadge.com/images/badges/certified-steve-bruhle.svg" alt="Dr. Steve Brule"></a>
  <a href="https://forthebadge.com"><img src="https://forthebadge.com/images/badges/contains-technical-debt.svg" alt="Contains Technical Debt"></a>
  <a href="https://forthebadge.com"><img src="https://forthebadge.com/images/badges/built-by-neckbeards.svg" alt="Built By Neckbeards"></a>
</p>

---

## Contributors

![GitHub Contributors Image](https://contrib.rocks/image?repo=brandon14/unit-test-examples)

---

## 😂 Here is a random joke that'll make you laugh!

![Jokes Card](https://readme-jokes.vercel.app/api)
