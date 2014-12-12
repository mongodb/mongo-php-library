phongo-crud
===========

MongoDB CRUD interface for [PHongo](https://github.com/bjori/phongo).


This interface is meant for the general public to use with PHongo,
and will serve as the default reference interface when creating other bindings.


## Documentation
- http://bjori.github.io/phongo/

# Installation

As PHongo CRUD is an abstraction layer for PHongo, it naturally requires [PHongo to be
installed](http://bjori.github.io/phongo/#installation):

	$ wget https://github.com/bjori/phongo/releases/download/0.1.2/phongo-0.1.2.tgz
	$ pecl install phongo-0.1.2.tgz
	$ echo "extension=phongo.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

The best way to then install PHongo CRUD is via [composer](https://getcomposer.org/)
by adding the following to
[composer.json](https://getcomposer.org/doc/01-basic-usage.md#composer-json-project-setup):

```json
    "repositories": [
        {
	    "type": "vcs",
	    "url": "https://github.com/bjori/phongo-crud"
        }
    ],
    "require": {
        "ext-phongo": ">=0.1.2",
        "bjori/phongo-crud": "dev-master"
    }
```

and then running

```shell
$ composer install
```

## Reporting tickets
