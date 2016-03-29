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
    ['projection' => ['pop => 1']]
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

## Aggregation

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
