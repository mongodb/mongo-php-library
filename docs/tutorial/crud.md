# CRUD Operations

CRUD is an acronym for Create, Read, Update, and Delete. These operations may be
performed via the [MongoDB\Collection][collection] class, which implements
MongoDB's cross-driver [CRUD specification][crud-spec]. This page will
demonstrate how to insert, query, update, and delete documents using the
library. A general introduction to CRUD operations in MongoDB may be found in
the [MongoDB Manual][crud].

[collection]: ../classes/collection.md
[crud-spec]: https://github.com/mongodb/specifications/blob/master/source/crud/crud.rst
[crud]: https://docs.mongodb.org/manual/crud/

This page covers the following common use cases:

 * Querying for [one](#finding-one-document) or [many](#finding-many-documents)
   documents at a time
 * [Projecting](#query-projection) fields in a query
 * Applying [limit, sort, and skip options](#limit-sort-and-skip-options) to a
   query
 * Inserting [one](#inserting-one-document) or [many](#inserting-many-documents)
   documents at a time
 * Updating [one](#updating-one-document) or [many](#updating-many-documents)
   documents at a time
 * [Replacing](#replacing-a-document) a document
 * [Upserting](#upserting-a-document) a document
 * Deleting [one](#deleting-one-document) or [many](#deleting-many-documents)
   documents at a time
 * [Aggregating](#aggregating-documents) documents

Note that the use of arrays to express documents in the following examples was
done for simplicity. The driver will also accept instances of stdClass or
[MongoDB\BSON\Serializable][serializable]) for these arguments (e.g. query
filters, inserted documents, update documents).

[serializable]: http://php.net/mongodb-bson-serializable

Documents destined for database storage (e.g. insert documents, replacement
documents, embedded documents included in an update operation) may also be
instances of [MongoDB\BSON\Persistable][persistable]. See
[Persistable Classes][persistable-classes] for more information.

[persistable]: http://php.net/mongodb-bson-persistable
[persistable-classes]: bson.md#persistable-classes

## Finding One Document

The [findOne()][findone] method returns the first matched document, or null if
no document was matched.

[findone]: ../classes/collection.md#findone

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$document = $collection->findOne(['_id' => '94301']);

var_dump($document);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#13 (1) {
  ["storage":"ArrayObject":private]=>
  array(5) {
    ["_id"]=>
    string(5) "94301"
    ["city"]=>
    string(9) "PALO ALTO"
    ["loc"]=>
    object(MongoDB\Model\BSONArray)#12 (1) {
      ["storage":"ArrayObject":private]=>
      array(2) {
        [0]=>
        float(-122.149685)
        [1]=>
        float(37.444324)
      }
    }
    ["pop"]=>
    int(15965)
    ["state"]=>
    string(2) "CA"
  }
}
```

## Finding Many Documents

The [find()][find] method returns a [MongoDB\Driver\Cursor][cursor] object,
which may be iterated upon to access all matched documents. The following
example queries for all zip codes in a given city:

[find]: ../classes/collection.md#find
[cursor]: http://php.net/mongodb-driver-cursor

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$cursor = $collection->find(['city' => 'JERSEY CITY', 'state' => 'NJ']);

foreach ($cursor as $document) {
    echo $document['_id'], "\n";
}
```

The above example would output something similar to:

```
07302
07304
07305
07306
07307
07310
```

## Query Projection

Queries may include a [projection][projection] to include or exclude specific
fields in the returned documents. The following example selects only the
population field for the zip code:

[projection]: https://docs.mongodb.org/manual/tutorial/project-fields-from-query-results/


```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$document = $collection->findOne(
    ['_id' => '10011'],
    ['projection' => ['pop' => 1]]
);

var_dump($document);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#12 (1) {
  ["storage":"ArrayObject":private]=>
  array(2) {
    ["_id"]=>
    string(5) "10011"
    ["pop"]=>
    int(46560)
  }
}
```

**Note:** the "_id" field is included by default unless explicitly excluded.

## Limit, Sort, and Skip Options

In addition to criteria, queries may take options to limit, sort, and skip
returned documents. The following example queries for the five most populous
zip codes in the United States:

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$cursor = $collection->find(
    [],
    [
        'limit' => 5,
        'sort' => ['pop' => -1],
    ]
);

foreach ($cursor as $document) {
    echo $document['_id'], "\n";
}
```

The above example would output something similar to:

```
60623: CHICAGO, IL
11226: BROOKLYN, NY
10021: NEW YORK, NY
10025: NEW YORK, NY
90201: BELL GARDENS, CA
```

## Inserting One Document

The [insertOne()][insertone] method may be used to insert a single document.
This method returns an instance of `MongoDB\InsertOneResult`, which may be used
to access the ID of the inserted document. Note that if a document does not
contain an `_id` field at the time of insertion, the driver will generate a
`MongoDB\BSON\ObjectID` to use as its ID.

[insertone]: ../classes/collection.md#insertone

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$insertOneResult = $collection->insertOne(['name' => 'Bob']);

printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
var_dump($insertOneResult->getInsertedId());
```

The above example would output something similar to:

```
Inserted 1 document(s)
object(MongoDB\BSON\ObjectID)#10 (1) {
  ["oid"]=>
  string(24) "5750905b6118fd170565aa81"
}
```

The following example inserts a document with an ID. Note that if an ID is not
unique for the collection, the insert will fail due to a duplicate key error.

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$insertOneResult = $collection->insertOne(['_id' => 1, 'name' => 'Alice']);

printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
var_dump($insertOneResult->getInsertedId());
```

The above example would output:

```
Inserted 1 document(s)
int(1)
```

## Inserting Many Documents

The [insertMany()][insertmany] method may be used to insert multiple documents
at a time. This method returns an instance of `MongoDB\InsertManyResult`, which
may be used to access the IDs of the inserted documents.

[insertmany]: ../classes/collection.md#insertmany

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$insertManyResult = $collection->insertMany([
    ['name' => 'Bob'],
    ['_id' => 1, 'name' => 'Alice'],
]);

printf("Inserted %d document(s)\n", $insertManyResult->getInsertedCount());
var_dump($insertManyResult->getInsertedIds());
```

The above example would output something similar to:

```
Inserted 2 document(s)
array(2) {
  [0]=>
  object(MongoDB\BSON\ObjectID)#10 (1) {
    ["oid"]=>
    string(24) "5750927b6118fd1ed64eb141"
  }
  [1]=>
  int(1)
}
```

## Updating One Document

The [updateOne()][updateone] method may be used to update a single document
matching a filter. This method returns an instance of `MongoDB\UpdateResult`,
which may be used to access statistics about the update operation.

[updateone]: ../classes/collection.md#updateone

This method has two required parameters: a query filter and an update document.
The query filter is similar to what might be provided to [find()][find]. The
update document consists of one or more [update operators][updateops].

[updateops]: https://docs.mongodb.com/manual/reference/operator/update/

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$collection->insertOne(['name' => 'Bob', 'state' => 'ny']);
$collection->insertOne(['name' => 'Alice', 'state' => 'ny']);
$updateResult = $collection->updateOne(
    ['state' => 'ny'],
    ['$set' => ['country' => 'us']]
);

printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
```

The above example would output something similar to:

```
Matched 1 document(s)
Modified 1 document(s)
```

Note that it is possible for a document to match the filter but not be modified
by an update:

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$collection->insertOne(['name' => 'Bob', 'state' => 'ny']);
$updateResult = $collection->updateOne(
    ['name' => 'Bob'],
    ['$set' => ['state' => 'ny']]
);

printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
```

The above example would output something similar to:

```
Matched 1 document(s)
Modified 0 document(s)
```

## Updating Many Documents

The [updateMany()][updatemany] method may be used to update multiple documents
at a time. This method returns an instance of `MongoDB\UpdateResult`, which may
be used to access statistics about the update operation.

[updatemany]: ../classes/collection.md#updatemany

This method has two required parameters: a query filter and an update document.
The query filter is similar to what might be provided to [find()][find]. The
update document consists of one or more [update operators][updateops].

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$collection->insertOne(['name' => 'Bob', 'state' => 'ny', 'country' => 'us']);
$collection->insertOne(['name' => 'Alice', 'state' => 'ny']);
$collection->insertOne(['name' => 'Sam', 'state' => 'ny']);
$updateResult = $collection->updateMany(
    ['state' => 'ny'],
    ['$set' => ['country' => 'us']]
);

printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
```

The above example would output something similar to:

```
Matched 3 document(s)
Modified 2 document(s)
```

## Replacing a Document

The [replaceOne()][replaceone] method may be used to replace a single document
matching a filter. This method returns an instance of `MongoDB\UpdateResult`,
which may be used to access statistics about the replacement operation.

[replaceone]: ../classes/collection.md#replaceone

This method has two required parameters: a query filter and a replacement
document. The query filter is similar to what might be provided to
[find()][find]. The replacement document will be used to overwrite the matched
document (excluding its ID, which is immutable) and must not contain
[update operators][updateops].

Note that the very nature of a replacement operation makes it easy to
inadvertently overwrite or delete fields in a document. When possible, users
should consider updating individual fields with [updateOne()][updateone] or
[updateMany()][updatemany].

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$collection->insertOne(['name' => 'Bob', 'state' => 'ny']);
$updateResult = $collection->replaceOne(
    ['name' => 'Bob'],
    ['name' => 'Robert', 'state' => 'ca']
);

printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
```

The above example would output something similar to:

```
Matched 1 document(s)
Modified 1 document(s)
```

Note that it is possible for a document to match the filter but not be modified
by a replacement (i.e. the matched document and replacement may be the same).

## Upserting a Document

An upsert is a variation of an update or replace operation, whereby a new
document is inserted if the query filter does not match an existing document.
An upsert may be specified via the `upsert` option for [updateOne()][updateone],
[updateMany()][updatemany], or [replaceOne()][replaceone]. The logic by which
the inserted document is created is discussed in the [MongoDB manual][upsert].

[upsert]: https://docs.mongodb.com/manual/reference/method/db.collection.update/#upsert-parameter

If a document has been upserted, its ID will be accessible via
`MongoDB\UpdateResult::getUpsertedId()`.

The following example demonstrates an upsert via [updateOne()][updateone]:

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$updateResult = $collection->updateOne(
    ['name' => 'Bob'],
    ['$set' => ['state' => 'ny']],
    ['upsert' => true]
);

printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
var_dump($collection->findOne(['_id' => $updateResult->getUpsertedId()]));
```

The above example would output something similar to:

```
Matched 0 document(s)
Modified 0 document(s)
object(MongoDB\Model\BSONDocument)#16 (1) {
  ["storage":"ArrayObject":private]=>
  array(3) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectID)#15 (1) {
      ["oid"]=>
      string(24) "57509c4406d7241dad86e7c3"
    }
    ["name"]=>
    string(3) "Bob"
    ["state"]=>
    string(2) "ny"
  }
}
```

The following example demonstrates an upsert via [replaceOne()][replaceone]:

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$updateResult = $collection->replaceOne(
    ['name' => 'Bob'],
    ['name' => 'Alice', 'state' => 'ny'],
    ['upsert' => true]
);

printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
var_dump($collection->findOne(['_id' => $updateResult->getUpsertedId()]));
```

The above example would output something similar to:

```
Matched 0 document(s)
Modified 0 document(s)
object(MongoDB\Model\BSONDocument)#16 (1) {
  ["storage":"ArrayObject":private]=>
  array(3) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectID)#15 (1) {
      ["oid"]=>
      string(24) "57509c6606d7241dad86e7c4"
    }
    ["name"]=>
    string(5) "Alice"
    ["state"]=>
    string(2) "ny"
  }
}
```

## Deleting One Document

The [deleteOne()][deleteone] method may be used to delete a single document
matching a filter. This method returns an instance of `MongoDB\DeleteResult`,
which may be used to access statistics about the delete operation.

[deleteone]: ../classes/collection.md#deleteone

This method has two required parameters: a query filter. The query filter is
similar to what might be provided to [find()][find].

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$collection->insertOne(['name' => 'Bob', 'state' => 'ny']);
$collection->insertOne(['name' => 'Alice', 'state' => 'ny']);
$deleteResult = $collection->deleteOne(['state' => 'ny']);

printf("Deleted %d document(s)\n", $deleteResult->getDeletedCount());
```

The above example would output something similar to:

```
Deleted 1 document(s)
```

## Deleting Many Documents

The [deleteMany()][deletemany] method may be used to delete multiple documents
at a time. This method returns an instance of `MongoDB\DeleteResult`, which may
be used to access statistics about the delete operation.

[deletemany]: ../classes/collection.md#deletemany

This method has two required parameters: a query filter. The query filter is
similar to what might be provided to [find()][find].

```
<?php

$collection = (new MongoDB\Client)->demo->users;
$collection->drop();

$collection->insertOne(['name' => 'Bob', 'state' => 'ny']);
$collection->insertOne(['name' => 'Alice', 'state' => 'ny']);
$deleteResult = $collection->deleteMany(['state' => 'ny']);

printf("Deleted %d document(s)\n", $deleteResult->getDeletedCount());
```

The above example would output something similar to:

```
Deleted 2 document(s)
```

## Aggregating Documents

The [Aggregation Framework][aggregation] may be used to issue complex queries
that filter, transform, and group collection data. The [aggregate()][aggregate]
method returns a [Traversable][traversable] object, which may be iterated
upon to access the results of an aggregation pipeline.

[aggregation]: https://docs.mongodb.org/manual/core/aggregation-pipeline/
[aggregate]: ../classes/collection.md#aggregate
[traversable]: http://php.net/traversable

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$cursor = $collection->aggregate([
    ['$group' => ['_id' => '$state', 'count' => ['$sum' => 1]]],
    ['$sort' => ['count' => -1]],
    ['$limit' => 5],
]);

foreach ($cursor as $state) {
    printf("%s has %d zip codes\n", $state['_id'], $state['count']);
}
```

The above example would output something similar to:

```
TX has 1671 zip codes
NY has 1595 zip codes
CA has 1516 zip codes
PA has 1458 zip codes
IL has 1237 zip codes
```

**Note:** [aggregate()][aggregate] is documented as returning a
[Traversable][traversable] object because the [aggregate][aggregate-cmd] command
may return its results inline (i.e. a single result document's array field,
which the library will package as a PHP iterator) or via a command cursor (i.e.
[MongoDB\Driver\Cursor][cursor]).

[aggregate-cmd]: (http://docs.mongodb.org/manual/reference/command/aggregate/)
