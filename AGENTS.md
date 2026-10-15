# AGENTS.md - MongoDB PHP Library

This file provides guidance to AI coding agents when working with code in this repository.

## Overview

The official high-level MongoDB PHP library (`mongodb/mongodb`). It provides a
full-featured, typed API — clients, databases, collections, GridFS, change
streams, bulk writes, codecs — on top of the low-level `mongodb` extension.

The extension is a **separate repository**
([mongodb/mongo-php-driver](https://github.com/mongodb/mongo-php-driver)).
It owns the wire protocol, connection management, and BSON encoding. This library
builds the server commands and implements the operation-level behaviour on top.
A change to how a command is *executed* or how BSON is *encoded* belongs in the
extension; a change to how a command is *constructed* or how an operation
*behaves* belongs here.

Correctness, backwards compatibility, and conformance to the
[MongoDB Driver Specifications](https://github.com/mongodb/specifications) are
top priorities.

- Issue tracker: [JIRA PHPLIB project](https://jira.mongodb.org/browse/PHPLIB). GitHub Issues is not used for bugs or features.
- Branches: `v2.x` is the default, where new features and optimisations land. The latest `vX.Y` minor branch takes bug fixes and maintenance tasks. `v1.21` receives only critical bug fixes.
- Requires the `mongodb` extension — see `composer.json` for the PHP and `ext-mongodb` versions supported on this branch.

## Tech Stack

- PHP library distributed via Composer, with `declare(strict_types=1)` and full type declarations throughout.
- Testing: PHPUnit (`phpunit/phpunit`), installed as a direct dev dependency — there is no Symfony PHPUnit Bridge.
- Static analysis: Psalm, level 1, with `psalm-baseline.xml` covering pre-existing issues. Fix new errors rather than baselining them.
- Coding standards: PHP_CodeSniffer (`phpcs.xml.dist`).
- Refactoring: Rector (`rector.php`).

## Project Structure

- `src/` — the library. Key areas: `Builder/` (aggregation pipeline stages and query builders), `Codec/`, `Command/` (database commands), `Exception/`, `GridFS/`, `Model/` (BSON document models), `Operation/` (one class per server operation), `ValueObject/`.
- `tests/` — PHPUnit tests mirroring `src/`. `SpecTests/` and `UnifiedSpecTests/` are JSON/YAML-driven cross-driver spec tests with shared runners.
- `tests/specifications/` and `tests/drivers-evergreen-tools/` — git submodules, updated by Dependabot. Run `git submodule update --init` after cloning.
- `generator/` — code generator for `src/Builder/` (stages, query and update operators, accumulators, expressions) and the matching `tests/Builder/*/Pipelines.php`. A separate subproject, not part of the distributed package. It does not generate `src/Operation/`. Any file whose header reads `THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!` is generated — edit the generator or its config instead, then regenerate.
- `tools/`, `benchmark/`, `examples/` — helper scripts, PHPBench benchmarks, and runnable examples (the examples are also executed by the test suite).

## Commands

```bash
composer update              # install dependencies and initialize the submodules

vendor/bin/phpunit           # run the test suite
vendor/bin/phpcs             # coding standards
vendor/bin/phpcbf            # auto-fix coding-standard violations
vendor/bin/psalm             # static analysis
vendor/bin/rector --dry-run  # preview refactors
vendor/bin/rector            # apply refactors
```

Call the `vendor/bin/` binaries directly rather than the `composer run` aliases, so
you can pass each tool's own options. Run a single test file or filter with, e.g.
`vendor/bin/phpunit tests/Collection/CollectionFunctionalTest.php --filter testGetNamespace`.

## Testing

- Tests require a running MongoDB server. `phpunit.xml.dist` defaults to
  `MONGODB_URI=mongodb://127.0.0.1:27017/?serverSelectionTimeoutMS=100` and
  `MONGODB_DATABASE=phplib_test`; override them in a local `phpunit.xml` or via
  the environment.
- Authentication, stable-API, load-balancer, and CSFLE suites need additional
  environment variables. The complete list is in [CONTRIBUTING.md](CONTRIBUTING.md).
  If a suite you need requires a variable that is not set, stop and tell the user
  which one, rather than working around it.
- Atlas Search tests are tagged `#[Group('atlas')]` and are gated by
  `FunctionalTestCase::isAtlas()`, which only recognises a connection string whose
  host ends in `.mongodb.net` or `.mongodb-dev.net`. Against a local server they
  **skip silently** — a green run is not validation. CI provisions a real Atlas
  cluster; to run them locally, point `MONGODB_URI` at Atlas, or at Atlas Local in
  Docker behind a hostname matching that guard.
- Spec tests come from the `tests/specifications` submodule. When a submodule bump
  causes failures, follow "Handling test failures on updates" in CONTRIBUTING.md:
  skip a test only when it exercises a specification or runner feature that is not
  implemented yet, or when it exposes a spec bug too large to fix here. Fix small
  spec bugs in the same PR. Don't blanket-skip — a skip can hide a real library
  regression.
- `tests/DocumentationExamplesTest.php` runs the snippets published in the PHP
  library manual; keep them in sync when public behaviour changes.

## Code Conventions

- Minimum supported PHP version is in `composer.json`; code must work on that
  version and newer. Use `declare(strict_types=1)`, native type declarations on new
  code, and PHPDoc for generics and array shapes where native types cannot express them.
- Don't add native parameter or return types to **existing** public or protected
  methods in `src/` — those signatures are covered by the BC promise, and adding a
  type breaks downstream overrides that omit it (e.g. `Client::getManager()`).
  `phpcs.xml.dist` exempts `src/` from the mandatory-native-return-type rule for this
  reason; new code and `tests/` do require native types.
- Prefer `$array['key'] ?? $default` over `isset()` for default values.
- Run `vendor/bin/phpcbf` before committing, then verify with `vendor/bin/phpcs`.
- `.github/copilot-instructions.md` documents the review expectations for PHP
  version, typing, and BSON handling.

## Code Comments

Comments are the exception, not the default — well-named classes, methods, and
variables should carry the meaning. Add one only where the code cannot explain itself.

- Comment the *why*, never the *what*: a non-obvious constraint, a server quirk, a
  workaround for an upstream bug. Don't narrate the next line.
- Describe the code as it is, not how it came to be. No references to the change, the
  ticket, the PR, the review, or a previous version — that belongs in the commit
  message or PR description.
- Keep comments to a short line or two; a longer rationale belongs in the PR or docs.
- Don't add PHPDoc that only repeats the native signature; use it for what types
  can't express (array shapes, generics, `@throws`).

## Commit and PR Conventions

- Prefix commit messages and PR titles with the JIRA key:
  `PHPLIB-1234: Short description`.
- Target the **oldest maintained branch** the change applies to — the latest
  `vX.Y` minor branch for bug fixes and maintenance, `v2.x` for features and
  optimisations; the `merge-up` workflow propagates fixes to newer branches.
- Push the branch to your own fork and open the pull request from there.
- Open pull requests as **drafts**, and let a maintainer mark them ready once reviewed.
- Fill in `.github/PULL_REQUEST_TEMPLATE.md` and apply exactly one release label:
  `feature`, `bug`, `minor`, `security`, or `tidy`. `minor` and above appear in the
  release notes; `tidy` does not. Call out breaking changes explicitly. Don't add a
  "Test plan" section.
- The library follows semantic versioning — never break backwards compatibility in
  a non-major release. See [RELEASING.md](RELEASING.md).
- User documentation lives in
  [mongodb/docs-php-library](https://github.com/mongodb/docs-php-library), not in
  this repository.

### PR descriptions and comments

- Keep PR descriptions and review replies short — one to three sentences. Write in
  plain English.
- After a push, say what changed and why. Don't list the checks, tests, or PHP
  versions you ran; CI reports those.
- Reply to a review comment only to answer it or to say how it was resolved.
