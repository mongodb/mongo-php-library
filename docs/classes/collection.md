# MongoDB\Collection

`MongoDB\Collection` is perhaps the most useful class in this library. It
provides methods for common operations on a collection, such as inserting
documents, querying, updating, counting, etc.

A Collection may be constructed directly (using the extension's Manager class)
or selected from the library's Client class. It supports the following options:

 * [readConcern](http://php.net/mongodb-driver-readconcern)
 * [readPreference](http://php.net/mongodb-driver-readpreference)
 * [typeMap](http://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps)
 * [writeConcern](http://php.net/mongodb-driver-writeconcern)

If any options are omitted, they will be inherited from the Manager constructor
argument or object from which the Collection was selected.

Operations within the Collection class (e.g. `find()`, `insertOne()`) will
generally inherit the Collection's options. One notable exception to this rule
is that `aggregate()` (when not using a cursor) and the `findAndModify` variants
do not yet support a type map option due to a driver limitation. This means that
they will return BSON documents and arrays as `stdClass` objects and PHP arrays,
respectively.

## Collection-level Operations

The Collection class has methods for collection-level operations, such as
dropping the collection, CRUD operations, or managing the collection's indexes.

### Dropping the Collection

```
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

## CRUD Operations

CRUD is an acronym for Create, Read, Update, and Delete. The Collection class
implements MongoDB's cross-driver
[CRUD specification](https://github.com/mongodb/specifications/blob/master/source/crud/crud.rst),
which defines a common API for collection-level read and write methods.

Each method on the Collection class corresponds to a particular Operation class
within the library. The Collection's method merely merges in relevant options
(e.g. read preferences, type maps). Documentation for each CRUD method and its
options may be found in either the CRUD specification or those Operation
classes.

### API Differences from the Legacy Driver

The CRUD API has some notable differences from the legacy driver's
[MongoCollection](http://php.net/mongocollection) class:

 * `insert()` and `batchInsert()` have been renamed to `insertOne()` and
   `insertMany()`, respectively.
 * `update()` has been split into `updateOne()`, `updateMany()`, and
   `replaceOne()`.
 * `remove()` has been split into `deleteOne()` and `deleteMany()`.
 * `findAndModify()` has been split into `findOneAndDelete()`,
   `findOneAndReplace()`, and `findOneAndUpdate()`.
 * `save()`, which was syntactic sugar for an insert or upsert operation, has
    been removed in favor of explicitly using `insertOne()` or `replaceOne()`
    (with the `upsert` option).
 * `aggregate()` and `aggregateCursor()` have been consolidated into a single
   `aggregate()` method.
 * A general-purpose `bulkWrite()` method replaces the legacy driver's
   [`MongoWriteBatch`](http://php.net/mongowritebatch) class.

The general rule in designing our new API was that explicit method names were
preferable to overloaded terms found in the old API. For instance, `save()` and
`findAndModify()` had two or three very different modes of operation, depending
on their arguments. These new methods names also distinguish between
[updating specific fields](https://docs.mongodb.org/manual/tutorial/modify-documents/#update-specific-fields-in-a-document)
and [full-document replacement](https://docs.mongodb.org/manual/tutorial/modify-documents/#replace-the-document).

### Finding One or More Document(s)

The `findOne()` and `find()` methods may be used to query for one or multiple
documents.

```
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

The `find()` method returns a
[`MongoDB\Driver\Cursor`](http://php.net/mongodb-driver-cursor) object, which
may be iterated upon to access all matched documents.

## Index Management

The Collection class implements MongoDB's cross-driver
[Index Management](https://github.com/mongodb/specifications/blob/master/source/index-management.rst)
and
[Enumerating Indexes](https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst)
specifications, which defines a common API for index-related methods.

### Creating Indexes

```
$collection = (new MongoDB\Client)->demo->zips;

$result = $collection->createIndex(['state' => 1]);
var_dump($result);
```

The above example would output something similar to:

```
string(7) "state_1"
```

### Dropping Indexes

```
$collection = (new MongoDB\Client)->demo->zips;

$result = $collection->dropIndex('state_1');
var_dump($result);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#11 (1) {
  ["storage":"ArrayObject":private]=>
  array(2) {
    ["nIndexesWas"]=>
    int(2)
    ["ok"]=>
    float(1)
  }
}
```

### Enumerating Indexes

```
/* listIndexes() returns an iterator of MongoDB\Model\IndexInfo objects */
$collection = (new MongoDB\Client)->demo->zips;

foreach ($collection->listIndexes() as $indexInfo) {
    var_dump($indexInfo);
}
```

The above example would output something similar to:

```
object(MongoDB\Model\IndexInfo)#4 (4) {
  ["v"]=>
  int(1)
  ["key"]=>
  array(1) {
    ["_id"]=>
    int(1)
  }
  ["name"]=>
  string(4) "_id_"
  ["ns"]=>
  string(9) "demo.zips"
}
```
