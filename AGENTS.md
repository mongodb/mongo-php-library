# AGENTS.md - MongoDB PHP Library

This file provides guidance to AI coding agents when working with code in this repository.

## Overview

The official high-level MongoDB PHP library (`mongodb/mongodb`). It provides a
full-featured, typed API — clients, databases, collections, GridFS, change
streams, bulk writes, codecs — on top of the low-level `mongodb` extension.

The extension is a **separate repository**
([mongodb/mongo-php-driver](https://github.com/mongodb/mongo-php-driver)).
Connection handling, BSON encoding, and server command behaviour belong there,
not here; the library only wraps what the extension exposes.

Correctness, backwards compatibility, and conformance to the
[MongoDB Driver Specifications](https://github.com/mongodb/specifications) are
top priorities.

- Issue tracker: [JIRA PHPLIB project](https://jira.mongodb.org/browse/PHPLIB). GitHub Issues is not used for bugs or features.
- Default branch: `v2.x`. Older maintenance lines: `v2.5`, `v2.4`, `v1.21`.
- Requires the `mongodb` extension — see `composer.json` for the PHP and `ext-mongodb` versions supported on this branch.

## Tech Stack

- PHP library distributed via Composer, with `declare(strict_types=1)` and full type declarations throughout.
- Testing: PHPUnit, installed through the Symfony PHPUnit Bridge.
- Static analysis: Psalm, level 1, with `psalm-baseline.xml` covering pre-existing issues. Fix new errors rather than baselining them.
- Coding standards: PHP_CodeSniffer (`phpcs.xml.dist`).
- Refactoring: Rector (`rector.php`).

## Project Structure

- `src/` — the library. Key areas: `Builder/` (aggregation pipeline stages and query builders), `Codec/`, `Command/` (database commands), `Exception/`, `GridFS/`, `Model/` (BSON document models), `Operation/` (one class per server operation), `ValueObject/`.
- `tests/` — PHPUnit tests mirroring `src/`. `SpecTests/` and `UnifiedSpecTests/` are JSON/YAML-driven cross-driver spec tests with shared runners.
- `tests/specifications/` and `tests/drivers-evergreen-tools/` — git submodules, updated by Dependabot. Run `git submodule update --init` after cloning.
- `generator/` — code generator for `src/Builder/` and `src/Operation/`; a separate subproject, not part of the distributed package. Files it emits start with `THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!` — edit the generator or its config instead, then regenerate.
- `tools/`, `benchmark/`, `examples/` — helper scripts, PHPBench benchmarks, and runnable examples (the examples are also executed by the test suite).

## Commands

```bash
composer update          # install dependencies and initialize the submodules
composer run test        # run the PHPUnit suite
composer run checks      # all checks below (coding standards + Psalm + Rector dry-run)
composer run check:cs    # phpcs
composer run fix:cs      # phpcbf — auto-fix coding-standard violations
composer run check:psalm # psalm
composer run check:rector
composer run fix:rector  # apply Rector refactors
```

Run a single test file or filter with PHPUnit directly, e.g.
`vendor/bin/phpunit tests/Collection/CollectionTest.php --filter testMethod`.

## Testing

- Tests require a running MongoDB server. `phpunit.xml.dist` defaults to
  `MONGODB_URI=mongodb://127.0.0.1:27017/?serverSelectionTimeoutMS=100` and
  `MONGODB_DATABASE=phplib_test`; override them in a local `phpunit.xml` or via
  the environment.
- Authentication, stable-API, load-balancer, and CSFLE suites need additional
  environment variables. The complete list is in [CONTRIBUTING.md](CONTRIBUTING.md).
  If a suite you need requires a variable that is not set, stop and tell the user
  which one, rather than working around it.
- Spec tests come from the `tests/specifications` submodule. When a submodule bump
  causes failures, the convention is to skip the affected test with a reference to
  a tracking ticket — not to change library behaviour. See "Handling test failures
  on updates" in CONTRIBUTING.md.
- `tests/DocumentationExamplesTest.php` runs the snippets published in the PHP
  library manual; keep them in sync when public behaviour changes.

## Code Conventions

- Minimum supported PHP version is in `composer.json`; code must work on that
  version and newer. Use full native type declarations, `declare(strict_types=1)`,
  and PHPDoc for generics and array shapes where native types cannot express them.
- `.github/copilot-instructions.md` documents the review expectations for PHP
  version, typing, and BSON handling.

## Commit and PR Conventions

- Prefix commit messages and PR titles with the JIRA key:
  `PHPLIB-1234: Short description`.
- Target the **oldest maintained branch** the change applies to (a bug fix goes to
  `v2.5`, not `v2.x`); the `merge-up` workflow propagates it to newer branches.
- Apply exactly one release label, per `.github/PULL_REQUEST_TEMPLATE.md`:
  `feature`, `bug`, `minor`, `security`, or `tidy`. `minor` and above appear in the
  release notes; `tidy` does not. Call out breaking changes explicitly.
- The library follows semantic versioning — never break backwards compatibility in
  a non-major release. See [RELEASING.md](RELEASING.md).
- User documentation lives in
  [mongodb/docs-php-library](https://github.com/mongodb/docs-php-library), not in
  this repository.
