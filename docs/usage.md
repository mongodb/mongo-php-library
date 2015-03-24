# Usage

## Collection class

`MongoDB\Collection` is perhaps the most useful class in this library. It
provides methods for common operations on a collection, such as inserting
documents, querying, updating, counting, etc.

Constructing a `MongoDB\Collection` requires a `MongoDB\Manager` and a namespace
for the collection. A [MongoDB namespace](http://docs.mongodb.org/manual/faq/developers/#faq-dev-namespace)
consists of a database name and collection name joined by a dot. `examples.zips`
is on example of a namespace. A [write concern](http://docs.mongodb.org/manual/core/write-concern/)
and [read preference](http://docs.mongodb.org/manual/core/read-preference/) may
also be provided when constructing a Collection (if omitted, the Collection will
use the Manager's values as its defaults).

## Finding a specific document

```
<?php

// This path should point to Composer's autoloader
require_once __DIR__ . "/vendor/autoload.php";

$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$collection = new MongoDB\Collection($manager, "examples.zips");
$sunnyvale = $collection->findOne(array("_id" => "94086"));
var_dump($sunnyvale);

?>
```

Executing this script should yield the following output:

```
array(5) {
  ["_id"]=>
  string(5) "94086"
  ["city"]=>
  string(9) "SUNNYVALE"
  ["loc"]=>
  array(2) {
    [0]=>
    float(-122.023771)
    [1]=>
    float(37.376407)
  }
  ["pop"]=>
  int(56215)
  ["state"]=>
  string(2) "CA"
}
```
