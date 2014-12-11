# Usage


# MongoDB\Collection

MongoDB\Collection is the main object of this component.
It has convenience methods for most of the _usual suspects_ tasks, such as inserting
documents, querying, updating, counting, and so on.

Constructing a MongoDB\Collection requires a MongoDB\Manager and then namespace to operate
on. A [MongoDB namespace](http://docs.mongodb.org/manual/faq/developers/#faq-dev-namespace)
is in the form of "databaseName.collectionName", for example: `examples.zips`.
A [WriteConcern](http://docs.mongodb.org/manual/core/write-concern/) and
[ReadPreference](http://docs.mongodb.org/manual/core/read-preference/) can also optionally
be provided, if omitted the default values from the MongoDB\Manager will be used.


## finding a specific document
```
<?php
require __DIR__ . "/../". "vendor/autoload.php";

$manager = new MongoDB\Manager("mongodb://localhost:27017");
$collection = new MongoDB\Collection($manager, "examples.zips");
$sunnyvale = $collection->findOne(array("_id" => "94086"));
var_dump($sunnyvale);

?>
```
Outputs
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
