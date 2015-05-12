# Usage

## Client class

`MongoDB\Client` serves as an entry point for the library and driver. It is
constructed with the same arguments as the driver's `MongoDB\Driver\Manager`
class, which it composes. The Client class provides methods for creating a
Database or Collection class (from the internal manager instance), as well as
top-level operations, such as enumerating and dropping databases.

## Collection class

`MongoDB\Collection` is perhaps the most useful class in this library. It
provides methods for common operations on a collection, such as inserting
documents, querying, updating, counting, etc.

Constructing a `MongoDB\Collection` requires a `MongoDB\Driver\Manager` and a
namespace for the collection. A [MongoDB namespace](http://docs.mongodb.org/manual/faq/developers/#faq-dev-namespace)
consists of a database name and collection name joined by a dot. `examples.zips`
is one example of a namespace. Through normal use of the library, a Collection
will typically be created via the `selectCollection()` method on the Manager or
Database classes.

A [write concern](http://docs.mongodb.org/manual/core/write-concern/)
and [read preference](http://docs.mongodb.org/manual/core/read-preference/) may
also be provided when constructing a Collection. If these options are omitted,
the Collection will inherit them from the parent through which it was selected,
or the Manager.

### Finding a specific document

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

## Database class

`MongoDB\Database` provides methods for common operations on a database, such
as creating, enumerating, and dropping collections.

A [write concern](http://docs.mongodb.org/manual/core/write-concern/)
and [read preference](http://docs.mongodb.org/manual/core/read-preference/) may
also be provided when constructing a Database. If these options are omitted,
the Database will inherit them from the Client through which it was selected,
or the Manager.
