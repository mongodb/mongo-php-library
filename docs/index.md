# MongoDB PHP Library

This library provides a high-level abstraction around the lower-level
[PHP driver](https://php.net/mongodb) (i.e. the `mongodb` extension).

While the extension provides a limited API for executing commands, queries, and
write operations, this library implements an API similar to that of the
[legacy PHP driver](http://php.net/manual/en/book.mongo.php). It contains
abstractions for client, database, and collection objects, and provides methods
for CRUD operations and common commands (e.g. index and collection management).
Support for GridFS is forthcoming.

If you are developing an application with MongoDB, you should consider using
this library, or another high-level abstraction, instead of the extension alone.

For additional information about this library and the ``mongodb`` extension,
see the [Architecture Overview](http://php.net/manual/en/mongodb.overview.php)
article in the driver documentation. [Derick Rethans](http://derickrethans.nl/)
has also written a series of blog posts entitled *New MongoDB Drivers for PHP
and HHVM*:

 * [Part One: History](http://derickrethans.nl/new-drivers.html)
 * [Part Two: Architecture](http://derickrethans.nl/new-drivers-part2.html)

## API Documentation

Generated API documentation for the library is available at:

 * [http://mongodb.github.io/mongo-php-library/api](http://mongodb.github.io/mongo-php-library/api)

## MongoDB Tutorial

If you are a new MongoDB user, these links should help you become more familiar
with MongoDB and introduce some of the concepts and terms you will encounter in
this documentation:

 * [Introduction to CRUD operations in MongoDB](http://docs.mongodb.org/manual/core/crud-introduction/)
 * [What is a MongoDB document?](http://docs.mongodb.org/manual/core/document/)
 * [MongoDB's *dot notation* for accessing document properties](http://docs.mongodb.org/manual/core/document/#dot-notation)
 * [ObjectId: MongoDB's document identifier](http://docs.mongodb.org/manual/reference/object-id/)
