# Welcome to PHongo CRUD

PHongo CRUD is an CRUD API ontop of [Phongo](https://github.com/bjori/phongo).
Its purpose is to provide standard MongoDB API and follows the MongoDB CRUD API Specification[1]
that all [MongoDB](http://mongodb.com) supported drivers follow.

PHongo CRUD provides several convenience methods that abstract the core PHongo extension.
The methods include functionality to insert a single document, counting all documents in
an collection, and delete documents from a collection.


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



## Generated API Docs

If you are just interested in looking at the API provided, checkout the apidoc generated
documentation on: [http://bjori.github.io/phongo-crud/api/class-MongoDB.Collection.html](http://bjori.github.io/phongo-crud/api/class-MongoDB.Collection.html)



## MongoDB Tutorial

MongoDB first-timer?
Checkout these links to get a quick understanding what MongoDB is, how it works, and
what the most common terms used with MongoDB mean.

 - [MongoDB CRUD Introduction](http://docs.mongodb.org/manual/core/crud-introduction/)
 - [What is a MongoDB Document](http://docs.mongodb.org/manual/core/document/)
 - [MongoDB `dot notation`](http://docs.mongodb.org/manual/core/document/#dot-notation)
 - [MongoDB ObjectId](http://docs.mongodb.org/manual/reference/object-id/)



[1] The specification has not been published yet - it is still a Work In Progress

