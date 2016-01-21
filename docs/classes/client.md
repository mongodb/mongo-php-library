# MongoDB\Client

`MongoDB\Client` serves as an entry point for the library and driver. It is
constructed with the same arguments as the driver's `MongoDB\Driver\Manager`
class, which it composes. Additional reference may be found in the
[`MongoDB\Driver\Manager::__construct`](http://php.net/manual/en/mongodb-driver-manager.construct.php])
and
[Connection String](https://docs.mongodb.org/manual/reference/connection-string/)
documentation.

```
/* By default, the driver connects to mongodb://localhost:27017 */
$client = new MongoDB\Client;

/* Any URI options will be merged into the URI string */
$client = new MongoDB\Client(
    'mongodb://rs1.example.com,rs2.example.com/?replicaSet=myReplicaSet',
    ['readPreference' => 'secondaryPreferred']
);
```

Driver options may be provided as the third argument. In addition to options
supported by the extension, the PHP library allows you to specify a default
type map to apply to the cursors it creates. A more thorough description of type
maps may be found in the driver's
[Persistence documentation](http://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps).

```
/* This example instructs the library to unserialize root and embedded BSON
 * documents as PHP arrays, like the legacy driver (i.e. ext-mongo). */
$client = new MongoDB\Client(null, [], [
    'typeMap' => ['root' => 'array', 'document' => 'array'],
);
```

By default, the library will unserialize BSON documents and arrays as
`MongoDB\Model\BSONDocument` and `MongoDB\Model\BSONArray` objects,
respectively. Each of these model classes extends PHP's
[`ArrayObject`](http://php.net/arrayobject) class and implements the driver's
[`MongoDB\BSON\Serializable`](http://php.net/mongodb-bson-serializable) and
[`MongoDB\BSON\Unserializable`](http://php.net/mongodb-bson-unserializable)
interfaces.

## Selecting Databases and Collections

The Client class provides methods for creating Database or Collection instances
(using its internal Manager instance). When selecting a Database or Collection,
the child will inherit options (e.g. read preference, type map) from the Client.
New options may also be provided to the `selectDatabase()` and
`selectCollection()` methods.

```
$client = new MongoDB\Client;

/* Select the "demo" database */
$db = $client->selectDatabase('demo');

/* Select the "demo.users" collection */
$collection = $client->selectCollection('demo', 'users');

/* selectDatabase() and selectCollection() also take an options array, which can
 * override any options inherited from the Client. */
$db = $client->selectDatabase('demo', [
    'readPreference' => new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_SECONDARY),
]);

/* The property accessor may also be used to select a database without
 * specifying additional options. PHP's complex syntax may be used for selecting
 * databases whose names contain special characters (e.g. "-"). */
$db = $client->demo;
$db = $client->{'another-app'};
```

## Database Management

The Client class has several methods for managing databases.

### Dropping Databases

```
$client = new MongoDB\Client;

$result = $client->dropDatabase('demo');
var_dump($result);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#8 (1) {
  ["storage":"ArrayObject":private]=>
  array(2) {
    ["dropped"]=>
    string(4) "demo"
    ["ok"]=>
    float(1)
  }
}
```

### Enumerating Databases

```
$client = new MongoDB\Client;

/* listDatabases() returns an iterator of MongoDB\Model\DatabaseInfo objects */
foreach ($client->listDatabases() as $databaseInfo) {
    var_dump($databaseInfo);
}
```

The above example would output something similar to:

```
object(MongoDB\Model\DatabaseInfo)#4 (3) {
  ["name"]=>
  string(5) "local"
  ["sizeOnDisk"]=>
  float(65536)
  ["empty"]=>
  bool(false)
}
object(MongoDB\Model\DatabaseInfo)#7 (3) {
  ["name"]=>
  string(4) "test"
  ["sizeOnDisk"]=>
  float(32768)
  ["empty"]=>
  bool(false)
}
```
