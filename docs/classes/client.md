# MongoDB\Client

The MongoDB\Client class serves as an entry point for the library. It is the
preferred class for connecting to a MongoDB server or cluster of servers and
serves as a gateway for accessing individual databases and collections. It is
analogous to the driver's [MongoDB\Driver\Manager][manager] class, which it
composes.

[manager]: http://php.net/mongodb-driver-manager

---

## __construct()

```php
function __construct($uri = 'mongodb://localhost:27017', array $uriOptions = [], array $driverOptions = [])
```

Constructs a new Client instance.

Additional URI options may be provided as the second argument and will take
precedence over any like options present in the URI string (e.g. authentication
credentials, query string parameters).

Driver options may be provided as the third argument. In addition to any options
supported by the extension, this library allows you to specify a default
type map to apply to the cursors it creates. A more thorough description of type
maps may be found in the driver's [Persistence documentation][typemap].

[typemap]: http://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps

### Supported URI Options

See [MongoDB\Driver\Manager::__construct()][manager-construct] and the
[MongoDB manual][connection-string].

[manager-construct]: http://php.net/manual/en/mongodb-driver-manager.construct.php
[connection-string]: https://docs.mongodb.org/manual/reference/connection-string/

### Supported Driver Options

typeMap (array)
:   Default type map for cursors and BSON documents.

### Example

By default, the driver connects to a standalone server on localhost via port
27017. The following example demonstrates how to connect to a replica set.
Additionally, it demonstrates a replica set with a custom read preference:

```
<?php

$client = new MongoDB\Client(
    'mongodb://rs1.example.com,rs2.example.com/?replicaSet=myReplicaSet',
    [
        'readPreference' => 'secondaryPreferred'
    ]
);
```

By default, the library will unserialize BSON documents and arrays as
MongoDB\Model\BSONDocument and MongoDB\Model\BSONArray objects, respectively.
The following example demonstrates how to have the library unserialize
everything as a PHP array, as was done in the legacy
[mongo extension][ext-mongo]:

[ext-mongo]: http://php.net/mongo

```
<?php

$client = new MongoDB\Client(
    null,
    [],
    ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
);
```

### See Also

 * [MongoDB\Driver\Manager::__construct()][manager-construct]
 * [MongoDB Manual: Connection String][connection-string]

---

## __get()

```php
function __get($databaseName): MongoDB\Database
```

Select a database.

The Database will inherit options (e.g. read preference, type map) from the
Client object. Use [selectDatabase()](#selectdatabase) to override any options.

**Note:** databases whose names contain special characters (e.g. "-") may be
selected with complex syntax (e.g. `$client->{"that-database"}`) or
[selectDatabase()](#selectdatabase).

### Example

The following example selects the "demo" and "another-app" databases:

```
<?php

$client = new MongoDB\Client;

$demo = $client->demo;
$anotherApp = $client->{'another-app'};
```

### See Also

 * [MongoDB\Client::selectDatabase()](#selectdatabase)
 * [PHP Manual: Property Overloading](http://php.net/oop5.overloading#object.get)

---

## dropDatabase

```php
function dropDatabase($databaseName, array $options = []): array|object
```

Drop a database. Returns the command result document.

### Supported Options

typeMap (array)
:   Type map for BSON deserialization. This will only be used for the returned
    command result document.

### Example

The following example drops the "demo" database:

```
<?php

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

### See Also

 * [MongoDB\Database::drop()](database.md#drop)
 * [MongoDB Manual: dropDatabase command](http://docs.mongodb.org/manual/reference/command/dropDatabase/)

---

## listDatabases()

```php
function listDatabases(array $options = []): MongoDB\Model\DatabaseInfoIterator
```

Returns information for all database on the server. Elements in the returned
iterator will be MongoDB\Model\DatabaseInfo objects.

### Supported Options

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

### Example

The following example lists all databases on the server:

```
<?php

$client = new MongoDB\Client;

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

### See Also

 * [MongoDB Manual: listDatabases command](http://docs.mongodb.org/manual/reference/command/listDatabases/)

---

## selectCollection()

```php
function selectCollection($databaseName, $collectionName, array $options = []): MongoDB\Collection
```

Select a collection on the server.

The Collection will inherit options (e.g. read preference, type map) from the
Client object unless otherwise specified.

### Supported Options

readConcern (MongoDB\Driver\ReadConcern)
:   The default read concern to use for collection operations. Defaults to the
    Client's read concern.

readPreference (MongoDB\Driver\ReadPreference)
:   The default read preference to use for collection operations. Defaults to
    the Client's read preference.

typeMap (array)
:   Default type map for cursors and BSON documents. Defaults to the Client's
    type map.

writeConcern (MongoDB\Driver\WriteConcern)
:   The default write concern to use for collection operations. Defaults to the
    Client's write concern.

### Example

The following example selects the "demo.users" collection:

```
<?php

$client = new MongoDB\Client;

$collection = $client->selectCollection('demo', 'users');
```

The following examples selects the "demo.users" collection with a custom read
preference:

```
<?php

$client = new MongoDB\Client;

$collection = $client->selectCollection(
    'demo',
    'users',
    [
        'readPreference' => new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_SECONDARY),
    ]
);
```

### See Also

 * [MongoDB\Collection::__construct()](collection.md#__construct)
 * [MongoDB\Client::__get()](#__get)


---

## selectDatabase()

```php
function selectDatabase($databaseName array $options = []): MongoDB\Collection
```

Select a database on the server.

The Database will inherit options (e.g. read preference, type map) from the
Client object unless otherwise specified.

### Supported Options

readConcern (MongoDB\Driver\ReadConcern)
:   The default read concern to use for database operations. Defaults to the
    Client's read concern.

readPreference (MongoDB\Driver\ReadPreference)
:   The default read preference to use for database operations. Defaults to the
    Client's read preference.

typeMap (array)
:   Default type map for cursors and BSON documents. Defaults to the Client's
    type map.

writeConcern (MongoDB\Driver\WriteConcern)
:   The default write concern to use for database operations. Defaults to the
    Client's write concern.

### Example

The following example selects the "demo" database:

```
<?php

$client = new MongoDB\Client;

$db = $client->selectDatabase('demo');
```

The following examples selects the "demo" database with a custom read
preference:

```
<?php

$client = new MongoDB\Client;

$db = $client->selectDatabase(
    'demo',
    [
        'readPreference' => new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_SECONDARY),
    ]
);
```

### See Also

 * [MongoDB\Database::__construct()](database.md#__construct)
 * [MongoDB\Client::__get()](#__get)

---
