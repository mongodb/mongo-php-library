# Indexes

Indexes may be managed via the [MongoDB\Collection][collection] class, which
implements MongoDB's cross-driver [Index Management][index-spec] and
[Enumerating Indexes][enum-spec] specifications. This page will demonstrate how
to create, list, and drop indexes using the library. General information on how
indexes work in MongoDB may be found in the [MongoDB manual][indexes].

[collection]: ../classes/collection.md
[index-spec]: https://github.com/mongodb/specifications/blob/master/source/index-management.rst
[enum-spec]: https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst
[indexes]: https://docs.mongodb.org/manual/indexes/

## Creating Indexes

Indexes may be created via the [createIndex()][createindex] and
[createIndexes()][createindexes] methods. The following example creates an
ascending index on the "state" field:

[createindex]: ../classes/collection.md#createindex
[createindexes]: ../classes/collection.md#createindexes

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

$result = $collection->createIndex(['state' => 1]);

var_dump($result);
```

Creating an index will return its name, which is automatically generated from
its specification (i.e. fields and orderings). The above example would output
something similar to:

```
string(7) "state_1"
```

### Enumerating Indexes

Information about indexes in a collection may be obtained via the
[listIndexes()][listindexes] method, which returns an iterator of
MongoDB\Model\IndexInfo objects. The following example lists all indexes in the
"demo.zips" collection:

[listindexes]: ../classes/collection.md#listindexes

```
<?php

$collection = (new MongoDB\Client)->demo->zips;

foreach ($collection->listIndexes() as $indexInfo) {
    var_dump($indexInfo);
}
```

The above example would output something similar to:

```
object(MongoDB\Model\IndexInfo)#10 (4) {
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
object(MongoDB\Model\IndexInfo)#13 (4) {
  ["v"]=>
  int(1)
  ["key"]=>
  array(1) {
    ["state"]=>
    int(1)
  }
  ["name"]=>
  string(7) "state_1"
  ["ns"]=>
  string(9) "demo.zips"
}
```

### Dropping Indexes

Indexes may be dropped via the [dropIndex()][dropindex] and
[dropIndexes()][dropindexes] methods. The following example drops a single index
by its name:

[dropindex]: ../classes/collection.md#dropindex
[dropindexes]: ../classes/collection.md#dropindexes

```
<?php

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
