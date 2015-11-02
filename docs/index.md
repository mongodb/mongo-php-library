# MongoDB PHP Library

This library provides a high-level abstraction around the lower-level
[PHP driver](https://github.com/mongodb/mongo-php-driver) (i.e. the `mongodb`
extension).

While the extension provides a limited API for executing commands, queries, and
write operations, this library implements an API similar to that of the
[legacy PHP driver](http://php.net/manual/en/book.mongo.php). It contains
abstractions for client, database, and collection objects, and provides methods
for CRUD operations and common commands (e.g. index and collection management).

If you are developing an application with MongoDB, you should consider using
this library, or another high-level abstraction, instead of the extension alone.

For further information about the architecture of this library and the `mongodb`
extension, see:

 - http://www.mongodb.com/blog/post/call-feedback-new-php-and-hhvm-drivers

# Installation

Since this library is only a high-level abstraction for the driver, it requires
that the [`mongodb` extension be installed](http://mongodb.github.io/mongo-php-driver/#installation):

    $ pecl install mongodb
    $ echo "extension=mongodb.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

Instructions for installing the `mongodb` extension on HHVM may be found in the
[HHVM driver's README](https://github.com/mongodb/mongo-hhvm-driver/blob/master/README.rst).

The preferred method of installing this library is with
[Composer](https://getcomposer.org/) by running the following from your project
root:

    $ composer require "mongodb/mongodb=^1.0.0@beta"

## Generated API Docs

If you are just interested in referencing the API provided by this library, you
can view generated API documentation [here](./api).

## MongoDB Tutorial

If you are a new MongoDB user, these links should help you become more familiar
with MongoDB and introduce some of the concepts and terms you will encounter in
this documentation:

 - [Introduction to CRUD operations in MongoDB](http://docs.mongodb.org/manual/core/crud-introduction/)
 - [What is a MongoDB document?](http://docs.mongodb.org/manual/core/document/)
 - [MongoDB's *dot notation* for accessing document properties](http://docs.mongodb.org/manual/core/document/#dot-notation)
 - [ObjectId: MongoDB's document identifier](http://docs.mongodb.org/manual/reference/object-id/)
