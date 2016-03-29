# MongoDB\Collection

The MongoDB\Collection class provides methods for common operations on a
collection and its documents. This includes, but is not limited to, CRUD
operations (e.g. inserting, querying, counting) and managing indexes.

A Collection may be constructed directly (using the extension's Manager class),
selected from the library's [Client](client.md) or [Database](database.md)
classes, or cloned from an existing Collection via
[withOptions()](#withoptions). It supports the following options:

 * [readConcern](http://php.net/mongodb-driver-readconcern)
 * [readPreference](http://php.net/mongodb-driver-readpreference)
 * [typeMap](http://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps)
 * [writeConcern](http://php.net/mongodb-driver-writeconcern)

Operations within the Collection class (e.g. [find()](#find),
[insertOne()](#insertone)) will generally inherit the Collection's options. One
notable exception to this rule is that [aggregate()](#aggregate) (when not using
a cursor), [distinct()](#distinct), and the [findAndModify][findandmodify]
helpers do not yet support a "typeMap" option due to a driver limitation. This
means that they will always return BSON documents and arrays as stdClass objects
and arrays, respectively.

[findandmodify]: http://docs.mongodb.org/manual/reference/command/findAndModify/

---

## __construct()

```php
function __construct(MongoDB\Driver\Manager $manager, $databaseName, $collectionName, array $options = [])
```

If the Collection is constructed explicitly, any omitted options will be
inherited from the Manager object. If the Collection is selected from a
[Client](client.md) or [Database](database.md) object, options will be
inherited from that object.

### Supported Options

readConcern (MongoDB\Driver\ReadConcern)
:   The default read concern to use for collection operations. Defaults to the
    Manager's read concern.

readPreference (MongoDB\Driver\ReadPreference)
:   The default read preference to use for collection operations. Defaults to
    the Manager's read preference.

typeMap (array)
:   Default type map for cursors and BSON documents.

writeConcern (MongoDB\Driver\WriteConcern)
:   The default write concern to use for collection operations. Defaults to the
    Manager's write concern.

### See Also

 * [MongoDB\Collection::withOptions()](#withoptions)
 * [MongoDB\Database::selectCollection()](database.md#selectcollection)

---

## aggregate()

```php
function aggregate(array $pipeline, array $options = []): Traversable
```

Executes an aggregation framework pipeline on the collection.

This method's return value depends on the MongoDB server version and the
"useCursor" option. If "useCursor" is true, a MongoDB\Driver\Cursor will be
returned; otherwise, an ArrayIterator is returned, which wraps the "result"
array from the command response document.

**Note:** BSON deserialization of inline aggregation results (i.e. not using a
command cursor) does not yet support a "typeMap" options; however, classes
implementing [MongoDB\BSON\Persistable][persistable] will still be deserialized
according to the [Persistence][persistence] specification.

[persistable]: http://php.net/mongodb-bson-persistable
[persistence]: http://php.net/manual/en/mongodb.persistence.deserialization.php

### Supported Options

allowDiskUse (boolean)
:   Enables writing to temporary files. When set to true, aggregation stages can
    write data to the _tmp sub-directory in the dbPath directory. The default is
    false.

batchSize (integer)
:   The number of documents to return per batch.

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation. This only
    applies when the $out stage is specified.
    <br><br>
    For servers < 3.2, this option is ignored as document level validation is
    not available.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

readConcern (MongoDB\Driver\ReadConcern)
:   Read concern. Note that a "majority" read concern is not compatible with the
    $out stage.
    <br><br>
    For servers < 3.2, this option is ignored as read concern is not available.

readPreference (MongoDB\Driver\ReadPreference)
:   Read preference.

typeMap (array)
:   Type map for BSON deserialization. This will be applied to the returned
    Cursor (it is not sent to the server).
    <br><br>
    This is currently not supported for inline aggregation results (i.e.
    useCursor option is false or the server versions < 2.6).

useCursor (boolean)
:   Indicates whether the command will request that the server provide results
    using a cursor. The default is true.
    <br><br>
    For servers < 2.6, this option is ignored as aggregation cursors are not
    available.
    <br><br>
    For servers >= 2.6, this option allows users to turn off cursors if
    necessary to aid in mongod/mongos upgrades.

### See Also

 * [MongoDB Manual: aggregate command](http://docs.mongodb.org/manual/reference/command/aggregate/)
 * [MongoDB Manual: Aggregation Pipeline](https://docs.mongodb.org/manual/core/aggregation-pipeline/)

---

## bulkWrite()

```php
function bulkWrite(array $operations, array $options = []): MongoDB\BulkWriteResult
```

Executes multiple write operations.

### Operations Example

Example array structure for all supported operation types:

```php
[
    [ 'deleteMany' => [ $filter ] ],
    [ 'deleteOne'  => [ $filter ] ],
    [ 'insertOne'  => [ $document ] ],
    [ 'replaceOne' => [ $filter, $replacement, $options ] ],
    [ 'updateMany' => [ $filter, $update, $options ] ],
    [ 'updateOne'  => [ $filter, $update, $options ] ],
]
```
Arguments correspond to the respective operation methods; however, the
"writeConcern" option is specified for the top-level bulk write operation
instead of each individual operation.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

ordered (boolean)
:   If true, when an insert fails, return without performing the remaining
    writes. If false, when a write fails, continue with the remaining writes, if
    any. The default is true.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::deleteMany()](#deletemany)
 * [MongoDB\Collection::deleteOne()](#deleteone)
 * [MongoDB\Collection::insertOne()](#insertone)
 * [MongoDB\Collection::replaceOne()](#replaceone)
 * [MongoDB\Collection::updateMany()](#updatemany)
 * [MongoDB\Collection::updateOne()](#updateone)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)

---

## count()

```php
function count($filter = [], array $options = []): integer
```

Gets the number of documents matching the filter. Returns the number of matched
documents as an integer.

### Supported Options

hint (string|document)
:   The index to use. If a document, it will be interpretted as an index
    specification and a name will be generated.

limit (integer)
:   The maximum number of documents to count.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

readConcern (MongoDB\Driver\ReadConcern)
:   Read concern.
    <br><br>
    For servers < 3.2, this option is ignored as read concern is not available.

readPreference (MongoDB\Driver\ReadPreference)
:   Read preference.

skip (integer)
:   The number of documents to skip before returning the documents.

### See Also

 * [MongoDB Manual: count command](http://docs.mongodb.org/manual/reference/command/count/)

---

## createIndex()

```php
function createIndex($key, array $options = []): string
```

Create a single index for the collection. Returns the name of the created index
as a string.

### Key Example

The `$key` argument must be a document containing one or more fields mapped to
an order or type. For example:

```
// Ascending index on the "username" field
$key = [ 'username' => 1 ];

// 2dsphere index on the "loc" field with a secondary index on "created_at"
$key = [ 'loc' => '2dsphere', 'created_at' => 1 ];
```

### Supported Options

Index options are documented in the [MongoDB manual][createIndexes].

[createIndexes]: https://docs.mongodb.org/manual/reference/command/createIndexes/

### See Also

 * [MongoDB\Collection::createIndexes()](#createindexes)
 * [Tutorial: Indexes](../tutorial/indexes.md)
 * [MongoDB Manual: createIndexes command][createIndexes]
 * [MongoDB Manual: Indexes][indexes]

[indexes]: https://docs.mongodb.org/manual/indexes/

---

## createIndexes()

```
function createIndexes(array $indexes): string[]
```

Create one or more indexes for the collection. Returns the names of the created
indexes as an array of strings.

### Indexes Array

Each element in the `$indexes` array must have a "key" document, which contains
fields mapped to an order or type. Other options may follow. For example:

```php
[
    // Create a unique index on the "username" field
    [ 'key' => [ 'username' => 1 ], 'unique' => true ],
    // Create a 2dsphere index on the "loc" field with a custom name
    [ 'key' => [ 'loc' => '2dsphere' ], 'name' => 'geo' ],
]
```
If the "name" option is unspecified, a name will be generated from the "key"
document.

Index options are documented in the [MongoDB manual][createIndexes].

### See Also

 * [MongoDB\Collection::createIndex()](#createindex)
 * [Tutorial: Indexes](../tutorial/indexes.md)
 * [MongoDB Manual: createIndexes command][createIndexes]
 * [MongoDB Manual: Indexes][indexes]

---

## deleteMany()

```php
function deleteMany($filter, array $options = []): MongoDB\DeleteResult
```

Deletes all documents matching the filter.

### Supported Options

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::bulkWrite()](#bulkwrite)
 * [MongoDB\Collection::deleteOne()](#deleteone)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)
 * [MongoDB Manual: delete command](https://docs.mongodb.org/manual/reference/command/delete/)

---

## deleteOne()

```php
function deleteOne($filter, array $options = []): MongoDB\DeleteResult
```

Deletes at most one document matching the filter.

### Supported Options

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::bulkWrite()](#bulkwrite)
 * [MongoDB\Collection::deleteMany()](#deletemany)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)
 * [MongoDB Manual: delete command](https://docs.mongodb.org/manual/reference/command/delete/)

---

## distinct()

```php
function distinct($fieldName, $filter = [], array $options = []): mixed[]
```

Finds the distinct values for a specified field across the collection. Returns
an array of the distinct values.

### Supported Options

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

readConcern (MongoDB\Driver\ReadConcern)
:   Read concern.
    <br><br>
    For servers < 3.2, this option is ignored as read concern is not available.

readPreference (MongoDB\Driver\ReadPreference)
:   Read preference.

### See Also

 * [MongoDB Manual: distinct command](https://docs.mongodb.org/manual/reference/command/distinct/)

---

## drop()

```php
function drop(array $options = []): array|object
```

Drop this collection. Returns the command result document.

### Supported Options

typeMap (array)
:   Type map for BSON deserialization. This will be used for the returned
    command result document.

### Example

The following example drops the "demo.zips" collection:

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$result = $collection->drop();

var_dump($result);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#11 (1) {
  ["storage":"ArrayObject":private]=>
  array(3) {
    ["ns"]=>
    string(9) "demo.zips"
    ["nIndexesWas"]=>
    int(1)
    ["ok"]=>
    float(1)
  }
}
```

### See Also

 * [MongoDB\Database::dropCollection()](database.md#dropcollection)
 * [MongoDB Manual: drop command](https://docs.mongodb.org/manual/reference/command/drop/)

---

## dropIndex()

```php
function dropIndex($indexName, array $options = []): array|object
```

Drop a single index in the collection. Returns the command result document.

### Supported Options

typeMap (array)
:   Type map for BSON deserialization. This will be used for the returned
    command result document.

### See Also

 * [MongoDB\Collection::dropIndexes()](#dropindexes)
 * [Tutorial: Indexes](../tutorial/indexes.md)
 * [MongoDB Manual: dropIndexes command](http://docs.mongodb.org/manual/reference/command/dropIndexes/)
 * [MongoDB Manual: Indexes][indexes]

---

## dropIndexes()

```php
function dropIndexes(array $options = []): array|object
```

Drop all indexes in the collection. Returns the command result document.

### Supported Options

typeMap (array)
:   Type map for BSON deserialization. This will be used for the returned
    command result document.

### See Also

 * [MongoDB\Collection::dropIndex()](#dropindex)
 * [Tutorial: Indexes](../tutorial/indexes.md)
 * [MongoDB Manual: dropIndexes command](http://docs.mongodb.org/manual/reference/command/dropIndexes/)
 * [MongoDB Manual: Indexes][indexes]

---

## find()

```php
function find($filter = [], array $options = []): MongoDB\Driver\Cursor
```

Finds documents matching the query. Returns a MongoDB\Driver\Cursor.

### Supported Options

allowPartialResults (boolean)
:   Get partial results from a mongos if some shards are inaccessible (instead
    of throwing an error).

batchSize (integer)
:   The number of documents to return per batch.

comment (string)
:   Attaches a comment to the query. If "$comment" also exists in the modifiers
    document, this option will take precedence.

cursorType (enum)
:   Indicates the type of cursor to use. Must be either NON_TAILABLE, TAILABLE,
    or TAILABLE_AWAIT. The default is NON_TAILABLE.

limit (integer)
:   The maximum number of documents to return.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run. If "$maxTimeMS" also
    exists in the modifiers document, this option will take precedence.

modifiers (document)
:   Meta-operators modifying the output or behavior of a query.

noCursorTimeout (boolean)
:   The server normally times out idle cursors after an inactivity period (10
    minutes) to prevent excess memory use. Set this option to prevent that.

oplogReplay (boolean)
:   Internal replication use only. The driver should not set this.

projection (document)
:   Limits the fields to return for the matching document.

readConcern (MongoDB\Driver\ReadConcern)
:   Read concern.
    <br><br>
    For servers < 3.2, this option is ignored as read concern is not
    available.

readPreference (MongoDB\Driver\ReadPreference)
:   Read preference.

skip (integer)
:   The number of documents to skip before returning.

sort (document)
:   The order in which to return matching documents. If "$orderby" also exists
    in the modifiers document, this option will take precedence.

typeMap (array)
:   Type map for BSON deserialization. This will be applied to the returned
    Cursor (it is not sent to the server).

### See Also

 * [MongoDB\Collection::findOne()](#findOne)
 * [MongoDB Manual: find command](http://docs.mongodb.org/manual/reference/command/find/)

---

## findOne()

```php
function findOne($filter = [], array $options = []): array|object
```

Finds a single document matching the query. Returns the matching document or
null.

### Supported Options

comment (string)
:   Attaches a comment to the query. If "$comment" also exists in the modifiers
    document, this option will take precedence.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run. If "$maxTimeMS" also
    exists in the modifiers document, this option will take precedence.

modifiers (document)
:   Meta-operators modifying the output or behavior of a query.

projection (document)
:   Limits the fields to return for the matching document.

readConcern (MongoDB\Driver\ReadConcern)
:   Read concern.
    <br><br>
    For servers < 3.2, this option is ignored as read concern is not available.

readPreference (MongoDB\Driver\ReadPreference)
:   Read preference.

skip (integer)
:   The number of documents to skip before returning.

sort (document)
:   The order in which to return matching documents. If "$orderby" also exists
    in the modifiers document, this option will take precedence.

typeMap (array)
:   Type map for BSON deserialization.

### See Also

 * [MongoDB\Collection::find()](#find)
 * [MongoDB Manual: find command](http://docs.mongodb.org/manual/reference/command/find/)

---

## findOneAndDelete()

```php
function findOneAndDelete($filter, array $options = []): object|null
```

Finds a single document and deletes it, returning the original. The document to
return may be null if no document matched the filter.

**Note:** BSON deserialization of the returned document does not yet support a
"typeMap" option; however, classes implementing
[MongoDB\BSON\Persistable][persistable] will still be deserialized according to
the [Persistence][persistence] specification.

### Supported Options

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

projection (document)
:   Limits the fields to return for the matching document.

sort (document)
:   Determines which document the operation modifies if the query selects
    multiple documents.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern. This option is only supported for server versions >= 3.2.

### See Also

 * [MongoDB\Collection::findOneAndReplace()](#findoneandreplace)
 * [MongoDB\Collection::findOneAndUpdate()](#findoneandupdate)
 * [MongoDB Manual: findAndModify command][findandmodify]

---

## findOneAndReplace()

```php
function findOneAndReplace($filter, $replacement, array $options = []): object|null
```

Finds a single document and replaces it, returning either the original or the
replaced document.

The document to return may be null if no document matched the filter. By
default, the original document is returned. Specify
`MongoDB\Operation\FindOneAndReplace::RETURN_DOCUMENT_AFTER` for the
"returnDocument" option to return the updated document.

**Note:** BSON deserialization of the returned document does not yet support a
"typeMap" option; however, classes implementing
[MongoDB\BSON\Persistable][persistable] will still be deserialized according to
the [Persistence][persistence] specification.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

projection (document)
:   Limits the fields to return for the matching document.

returnDocument (enum)
:   Whether to return the document before or after the update is applied. Must
    be either `MongoDB\Operation\FindOneAndReplace::RETURN_DOCUMENT_BEFORE` or
    `MongoDB\Operation\FindOneAndReplace::RETURN_DOCUMENT_AFTER`. The default is
    `MongoDB\Operation\FindOneAndReplace::RETURN_DOCUMENT_BEFORE`.

sort (document)
:   Determines which document the operation modifies if the query selects
    multiple documents.

upsert (boolean)
:   When true, a new document is created if no document matches the query. The
    default is false.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern. This option is only supported for server versions >= 3.2.

### See Also

 * [MongoDB\Collection::findOneAndDelete()](#findoneanddelete)
 * [MongoDB\Collection::findOneAndUpdate()](#findoneandupdate)
 * [MongoDB Manual: findAndModify command][findandmodify]

---

## findOneAndUpdate()

```php
function findOneAndUpdate($filter, $update, array $options = []): object|null
```

Finds a single document and updates it, returning either the original or the
updated document.

The document to return may be null if no document matched the filter. By
default, the original document is returned. Specify
`MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER` for the
"returnDocument" option to return the updated document.

**Note:** BSON deserialization of the returned document does not yet support a
"typeMap" option; however, classes implementing
[MongoDB\BSON\Persistable][persistable] will still be deserialized according to
the [Persistence][persistence] specification.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

projection (document)
:   Limits the fields to return for the matching document.

returnDocument (enum)
:   Whether to return the document before or after the update is applied. Must
    be either `MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_BEFORE` or
    `MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER`. The default is
    `MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_BEFORE`.

sort (document)
:   Determines which document the operation modifies if the query selects
    multiple documents.

upsert (boolean)
:   When true, a new document is created if no document matches the query. The
    default is false.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern. This option is only supported for server versions >= 3.2.

### See Also

 * [MongoDB\Collection::findOneAndDelete()](#findoneanddelete)
 * [MongoDB\Collection::findOneAndReplace()](#findoneandreplace)
 * [MongoDB Manual: findAndModify command][findandmodify]

---

## getCollectionName()

```php
function getCollectionName(): string
```

Return the collection name.

---

## getDatabaseName()

```php
function getDatabaseName(): string
```

Return the database name.

---

## getNamespace()

```php
function getNamespace(): string
```

Return the collection namespace.

### See Also

 * [MongoDB Manual: namespace](https://docs.mongodb.org/manual/reference/glossary/#term-namespace)

---

## insertMany()

```php
function insertMany(array $documents, array $options = []): MongoDB\InsertManyResult
```

Inserts multiple documents.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

ordered (boolean)
:   If true, when an insert fails, return without performing the remaining
    writes. If false, when a write fails, continue with the remaining writes, if
    any. The default is true.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::bulkWrite()](#bulkwrite)
 * [MongoDB\Collection::insertOne()](#insertone)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)
 * [MongoDB Manual: insert command](http://docs.mongodb.org/manual/reference/command/insert/)

---

## insertOne()

```php
function insertOne($document, array $options = []): MongoDB\InsertOneResult
```

Inserts one document.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::bulkWrite()](#bulkwrite)
 * [MongoDB\Collection::insertMany()](#insertmany)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)
 * [MongoDB Manual: insert command](http://docs.mongodb.org/manual/reference/command/insert/)

---

## listIndexes()

```php
function listIndexes(array $options = []): MongoDB\Model\IndexInfoIterator
```

Returns information for all indexes for the collection. Elements in the returned
iterator will be MongoDB\Model\IndexInfo objects.

### Supported Options

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

### See Also

 * [Tutorial: Indexes](../tutorial/indexes.md)
 * [MongoDB Manual: listIndexes command](http://docs.mongodb.org/manual/reference/command/listIndexes/)
 * [MongoDB Manual: Indexes][indexes]
 * [MongoDB Specification: Enumerating Collections](https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst)

---

## replaceOne()

```php
function replaceOne($filter, $replacement, array $options = []): MongoDB\UpdateResult
```

Replaces at most one document matching the filter.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

upsert (boolean)
:   When true, a new document is created if no document matches the query. The
    default is false.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::bulkWrite()](#bulkwrite)
 * [MongoDB\Collection::updateMany()](#updatemany)
 * [MongoDB\Collection::updateOne()](#updateone)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)
 * [MongoDB Manual: update command](http://docs.mongodb.org/manual/reference/command/update/)

---

## updateMany()

```php
function updateMany($filter, $update, array $options = []): MongoDB\UpdateResult
```

Updates all documents matching the filter.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

upsert (boolean)
:   When true, a new document is created if no document matches the query. The
    default is false.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::bulkWrite()](#bulkwrite)
 * [MongoDB\Collection::replaceOne()](#replaceone)
 * [MongoDB\Collection::updateOne()](#updateone)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)
 * [MongoDB Manual: update command](http://docs.mongodb.org/manual/reference/command/update/)

---

## updateOne()

```php
function updateOne($filter, $update, array $options = []): MongoDB\UpdateResult
```

Updates at most one document matching the filter.

### Supported Options

bypassDocumentValidation (boolean)
:   If true, allows the write to opt out of document level validation.

upsert (boolean)
:   When true, a new document is created if no document matches the query. The
    default is false.

writeConcern (MongoDB\Driver\WriteConcern)
:   Write concern.

### See Also

 * [MongoDB\Collection::bulkWrite()](#bulkwrite)
 * [MongoDB\Collection::replaceOne()](#replaceone)
 * [MongoDB\Collection::updateMany()](#updatemany)
 * [Tutorial: CRUD Operations](../tutorial/crud.md)
 * [MongoDB Manual: update command](http://docs.mongodb.org/manual/reference/command/update/)

---

## withOptions()

```php
function withOptions(array $options = []): MongoDB\Collection
```

Returns a clone of this Collection with different options.

### Supported Options

readConcern (MongoDB\Driver\ReadConcern)
:   The default read concern to use for collection operations. Defaults to the
    Manager's read concern.

readPreference (MongoDB\Driver\ReadPreference)
:   The default read preference to use for collection operations. Defaults to
    the Manager's read preference.

typeMap (array)
:   Default type map for cursors and BSON documents.

writeConcern (MongoDB\Driver\WriteConcern)
:   The default write concern to use for collection operations. Defaults to the
    Manager's write concern.

### See Also

 * [MongoDB\Collection::__construct()](#__construct)
