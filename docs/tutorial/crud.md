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

## Querying

### Finding One Document

The [findOne()][findone] method returns the first matched document, or null if
no document was matched. By default, the library returns BSON documents and
arrays as MongoDB\Model\BSONDocument and MongoDB\Model\BSONArray objects,
respectively. Both of those classes extend PHP's [ArrayObject][arrayobject]
class and implement the driver's [MongoDB\BSON\Serializable][serializable] and
[MongoDB\BSON\Unserializable][unserializable] interfaces.

[findone]: ../classes/collection.md#findone
[arrayobject]: http://php.net/arrayobject
[serializable]: http://php.net/mongodb-bson-serializable
[unserializable]: http://php.net/mongodb-bson-unserializable

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

Most methods that read data from MongoDB support a "typeMap" option, which
allows control over how BSON is converted to PHP. If desired, this option can be
used to return everything as a PHP array, as was done in the legacy
[mongo extension][ext-mongo]:

[ext-mongo]: http://php.net/mongo

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$document = $collection->findOne(
    ['_id' => '94301'],
    ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
);

var_dump($document);
```

The above example would output something similar to:

```
array(5) {
  ["_id"]=>
  string(5) "94301"
  ["city"]=>
  string(9) "PALO ALTO"
  ["loc"]=>
  array(2) {
    [0]=>
    float(-122.149685)
    [1]=>
    float(37.444324)
  }
  ["pop"]=>
  int(15965)
  ["state"]=>
  string(2) "CA"
}
```

### Finding Many Documents

The [find()][find] method returns a [MongoDB\Driver\Cursor][cursor] object,
which may be iterated upon to access all matched documents.

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
