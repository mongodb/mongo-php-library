# Contributing to the PHP Library for MongoDB

## Initializing the Repository

Developers who would like to contribute to the library will need to clone it and
initialize the project dependencies with [Composer](https://getcomposer.org/):

```
$ git clone https://github.com/mongodb/mongo-php-library.git
$ cd mongo-php-library
$ composer update
```

In addition to installing project dependencies, Composer will check that the
required extension version is installed. Directions for installing the extension
may be found [here](https://php.net/manual/en/mongodb.installation.php).

Installation directions for Composer may be found in its
[Getting Started](https://getcomposer.org/doc/00-intro.md) guide.

## Testing

The library's test suite uses [PHPUnit](https://phpunit.de/), which is installed
through the [PHPUnit Bridge](https://symfony.com/phpunit-bridge) dependency by
Composer.

The test suite may be executed with:

```console
$ composer run test
```

The `phpunit.xml.dist` file is used as the default configuration file for the
test suite. In addition to various PHPUnit options, it defines environment
variables such as `MONGODB_URI` and `MONGODB_DATABASE`. You may customize
this configuration by creating your own `phpunit.xml` file based on the
`phpunit.xml.dist` file we provide. To run the tests in serverless mode, set the
`MONGODB_IS_SERVERLESS` environment variable to `on`.

To run tests against a cluster that requires authentication, either include the
credentials in the connection string (i.e. `MONGODB_URI`) or set the
`MONGODB_USERNAME` and `MONGODB_PASSWORD` environment variables accordingly.
Note that `MONGODB_USERNAME` and `MONGODB_PASSWORD` will override any
credentials present in the connection string.

By default, the `simple-phpunit` binary chooses the correct PHPUnit version for
the PHP version you are running. To run tests against a specific PHPUnit
version, use the `SYMFONY_PHPUNIT_VERSION` environment variable:

```console
$ SYMFONY_PHPUNIT_VERSION=8.5 vendor/bin/simple-phpunit
```

### Environment Variables

The test suite references the following environment variables:

 * `MONGODB_DATABASE`: Default database to use in tests. Defaults to
   `phplib_test`.
 * `MONGODB_PASSWORD`: If specified, this value will be appended as the
   `password` URI option for clients constructed by the test suite, which will
   override any credentials in the connection string itself.
 * `MONGODB_URI`: Connection string. Defaults to `mongodb://127.0.0.1/`, which
   assumes a MongoDB server is listening on localhost port 27017.
 * `MONGODB_USERNAME`: If specified, this value will be appended as the
   `username` URI option for clients constructed by the test suite, which will
   override any credentials in the connection string itself.

The following environment variable is used for [stable API testing](https://github.com/mongodb/specifications/blob/master/source/versioned-api/tests/README.rst):

 * `API_VERSION`: If defined, this value will be used to construct a
   [`MongoDB\Driver\ServerApi`](https://www.php.net/manual/en/mongodb-driver-serverapi.construct.php),
   which will then be specified as the `serverApi` driver option for clients
   created by the test suite.

The following environment variable is used for [serverless testing](https://github.com/mongodb/specifications/blob/master/source/serverless-testing/README.rst):

 * `MONGODB_IS_SERVERLESS`: Specify a true boolean string
   (see: [`FILTER_VALIDATE_BOOLEAN`](https://www.php.net/manual/en/filter.filters.validate.php))
   if `MONGODB_URI` points to a serverless instance. Defaults to false.

The following environment variables are used for [load balancer testing](https://github.com/mongodb/specifications/blob/master/source/load-balancers/tests/README.rst):

 * `MONGODB_SINGLE_MONGOS_LB_URI`: Connection string to a load balancer backed
   by a single mongos host.
 * `MONGODB_MULTI_MONGOS_LB_URI`: Connection string to a load balancer backed by
   multiple mongos hosts.

The following environment variables are used for [CSFLE testing](https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst):

 * `AWS_ACCESS_KEY_ID`
 * `AWS_SECRET_ACCESS_KEY`
 * `AWS_TEMP_ACCESS_KEY_ID`
 * `AWS_TEMP_SECRET_ACCESS_KEY`
 * `AWS_TEMP_SESSION_TOKEN`
 * `AZURE_TENANT_ID`
 * `AZURE_CLIENT_ID`
 * `AZURE_CLIENT_SECRET`
 * `CRYPT_SHARED_LIB_PATH`: If defined, this value will be used to set the
   `cryptSharedLibPath` autoEncryption driver option for clients created by the
   test suite.
 * `GCP_EMAIL`
 * `GCP_PRIVATE_KEY`
 * `KMIP_ENDPOINT`
 * `KMS_ENDPOINT_EXPIRED`
 * `KMS_ENDPOINT_WRONG_HOST`
 * `KMS_ENDPOINT_REQUIRE_CLIENT_CERT`
 * `KMS_TLS_CA_FILE`
 * `KMS_TLS_CERTIFICATE_KEY_FILE`

## Code quality

Before submitting a pull request, please ensure that your code adheres to the
coding standards and passes static analysis checks.

```console
$ composer run checks
```

### Coding standards

The library's code is checked using [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer),
which is installed as a development dependency by Composer. To check the code
for style errors, run the `phpcs` binary:

```console
$ vendor/bin/phpcs
```

To automatically fix all fixable errors, use the `phpcbf` binary:

```console
$ vendor/bin/phpcbf
```

### Static analysis

The library uses [psalm](https://psalm.dev) to run static analysis on the code
and ensure an additional level of type safety. New code is expected to adhere
to level 1, with a baseline covering existing issues. To run static analysis
checks, run the `psalm` binary:

```console
$ vendor/bin/psalm
```

To remove fixed errors from the baseline, you can use the `update-baseline`
command-line argument:

```console
$ vendor/bin/psalm --update-baseline
```

Note that this will not add new errors to the baseline. New errors should be
fixed instead of being added to the technical debt, but in case this isn't
possible it can be added to the baseline using `set-baseline`:

```console
$ vendor/bin/psalm --set-baseline=psalm-baseline.xml
```

### Refactoring

The library uses [rector](https://getrector.com/) to refactor the code for new features.
To run automatic refactoring, use the `rector` command:

```console
$ vendor/bin/rector
```

New rules can be added to the `rector.php` configuration file.

## Documentation

Documentation for the library lives in the `docs/` directory and is built with
tools in the related
[mongodb/docs-php-library](https://github.com/mongodb/docs-php-library)
repository. The tools repository is already configured to reference our sources.

That said, any changes to the documentation should be tested locally before
committing. Follow the following steps to build the docs locally with the tools
repository:

 * Clone the
   [mongodb/docs-php-library](https://github.com/mongodb/docs-php-library) tools
   repository.
 * Create and activate Python 2.7 virtual environment if necessary.

   ```console
   $ virtualenv -p python2.7 venv
   $ source venv/bin/activate
   ```
 * Install [giza](https://pypi.python.org/pypi/giza/) according to the instructions
   in the [mongodb/docs-tools](https://github.com/mongodb/docs-tools) README.
 * Sync your working copy of the documentation to the `source/` directory with
   `rsync -a --delete /path/to/mongo-php-library/docs/ source/`.
 * Build the documentation with `giza make publish`. You can suppress
   informational log messages with the `--level warning` option.
 * Generated documentation may be found in the `build/master/html` directory.

## Releasing

The releases are created by the maintainers of the library. The process is documented in
the [RELEASING.md](RELEASING.md) file.
