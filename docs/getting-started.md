# Getting Started

### Requirements

Since this library is only a high-level abstraction for the driver, it requires
that the `mongodb` extension be installed:

```
$ pecl install mongodb
$ echo "extension=mongodb.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
```

Instructions for installing the `mongodb` extension on HHVM may be found in the
[Installation with HHVM](http://php.net/manual/en/mongodb.tutorial.install.hhvm.php)
article in the driver documentation.

### Installation

The preferred method of installing this library is with
[Composer](https://getcomposer.org/) by running the following from your project
root:

```
$ composer require "mongodb/mongodb=^1.0.0"
```

While not recommended, the package may also be installed manually via source
tarballs attached to
[GitHub releases](https://github.com/mongodb/mongo-php-library/releases).

### Configure Autoloading

Once the library is installed, ensure that your application includes Composer's
autoloader.

```
// This path should point to Composer's autoloader
require_once __DIR__ . "/vendor/autoload.php";
```

More information on this setup may be found in Composer's
[autoloading documentation](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

If you have installed the package manually (e.g. from a source tarball), you
will likely need configure autoloading manually:

 * Map the top-level `MongoDB\` namespace to the `src/` directory using your
   preferred autoloader implementation
 * Manually require the `src/functions.php` file, since PHP does not yet support
   function autoloading
