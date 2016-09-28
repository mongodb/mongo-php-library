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
may be found [here](http://php.net/manual/en/mongodb.installation.php).

Installation directions for Composer may be found in its
[Getting Started](https://getcomposer.org/doc/00-intro.md) guide.

## Testing

The library's test suite uses [PHPUnit](https://phpunit.de/), which should be
installed as a development dependency by Composer.

The test suite may be executed with:

```
$ vendor/bin/phpunit
```

The `phpunit.xml.dist` file is used as the default configuration file for the
test suite. In addition to various PHPUnit options, it defines required
`MONGODB_URI` and `MONGODB_DATABASE` environment variables. You may customize
this configuration by creating your own `phpunit.xml` file based on the
`phpunit.xml.dist` file we provide.

### Testing on HHVM

By default, the PHPUnit script relies on the `php` interpreter for your shell
(i.e. `#!/usr/bin/env php`). You can run the test suite with HHVM like so:

```
$ hhvm vendor/bin/phpunit
```

## Releasing

The follow steps outline the release process for a maintenance branch (e.g.
releasing the `vX.Y` branch as X.Y.Z).

### Ensure PHP version compatibility

Ensure that the library test suite completes on supported versions of PHP and
HHVM.

### Transition JIRA issues and version

Update the fix version field for all resolved issues with the corresponding ".x"
fix version.

Update the version's release date and status from the
[Manage Versions](https://jira.mongodb.org/plugins/servlet/project-config/PHPLIB/versions)
page.

Transition all resolved issues for this version to the closed state. If changing
the issues in bulk, be sure to allow email notifications.

### Update version info

The PHP library uses [semantic versioning](http://semver.org/). Do not break
backwards compatibility in a non-major release or your users will kill you.

A version constant may be added at a later date (see:
[PHPLIB-131](https://jira.mongodb.org/browse/PHPLIB-131)). For now, there is
nothing to update.

### Tag release

The maintenance branch's HEAD will be the target for our release tag:

```
$ git tag -a -m "Release X.Y.Z" X.Y.Z
```

### Push tags

```
$ git push --tags
```

### Publish release notes

The following template should be used for creating GitHub release notes via
[this form](https://github.com/mongodb/mongo-php-library/releases/new).

```
The PHP team is happy to announce that version X.Y.Z of our MongoDB PHP library is now available. This library is a high-level abstraction for the PHP 5, PHP 7, and HHVM drivers (i.e. [`mongodb`](http://php.net/mongodb) extension).

**Release Highlights**

<one or more paragraphs describing important changes in this release>

A complete list of resolved issues in this release may be found at:
$JIRA_URL

**Documentation**

Documentation for this library may be found at:
https://docs.mongodb.com/php-library/

**Feedback**

If you encounter any bugs or issues with this library, please report them via this form:
https://jira.mongodb.org/secure/CreateIssue.jspa?pid=12483&issuetype=1

**Installation**

This library may be installed or upgraded with:

    composer require "mongodb/mongodb=^1.0.0"

Installation instructions for the PHP and HHVM driver may be found in the [PHP.net documentation](http://php.net/manual/en/mongodb.installation.php).
```

The URL for the list of resolved JIRA issues will need to be updated with each
release. You may obtain the list from
[this form](https://jira.mongodb.org/secure/ReleaseNote.jspa?projectId=12483).

If commits from community contributors were included in this release, append the
following section:

```
**Thanks**

Thanks for our community contributors for this release:

 * [$CONTRIBUTOR_NAME](https://github.com/$GITHUB_USERNAME)
```

Release announcements should also be sent to the `mongodb-user@googlegroups.com`
and `mongodb-announce@googlegroups.com` mailing lists.

Consider announcing each release on Twitter. Significant releases should also be
announced via [@MongoDB](http://twitter.com/mongodb) as well.
