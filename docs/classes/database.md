# MongoDB\Database

The MongoDB\Database class provides methods for common operations on a database,
such as executing commands and managing collections.

A Database may be constructed directly (using the extension's Manager class) or
selected from the library's [Client](client.md) class. It supports the following
options:

 * [readConcern](http://php.net/mongodb-driver-readconcern)
 * [readPreference](http://php.net/mongodb-driver-readpreference)
 * [typeMap](http://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps)
 * [writeConcern](http://php.net/mongodb-driver-writeconcern)

If any options are omitted, they will be inherited from the Manager constructor
argument or object from which the Database was selected.

Operations within the Database class (e.g. [command()](#command)) will generally
inherit the Database's options.

---

## __construct()

```php
function __construct(MongoDB\Driver\Manager $manager, $databaseName, array $options = [])
```

If the Database is constructed explicitly, any omitted options will be inherited
from the Manager object. If the Database is selected from a [Client](client.md)
object, options will be inherited from that object.

### Supported Options

readConcern (MongoDB\Driver\ReadConcern)
:   The default read concern to use for database operations and selected
    collections. Defaults to the Manager's read concern.

readPreference (MongoDB\Driver\ReadPreference)
:   The default read preference to use for database operations and selected
    collections. Defaults to the Manager's read preference.

typeMap (array)
:   Default type map for cursors and BSON documents.

writeConcern (MongoDB\Driver\WriteConcern)
:   The default write concern to use for database operations and selected
    collections. Defaults to the Manager's write concern.

### See Also

 * [MongoDB\Database::withOptions()](#withoptions)

---

## __get()

```php
function __get($collectionName): MongoDB\Collection
```

Select a collection within this database.

The Collection will inherit options (e.g. read preference, type map) from the
Database object. Use [selectCollection()](#selectcollection) to override any
options.

**Note:** collections whose names contain special characters (e.g. ".") may be
selected with complex syntax (e.g. `$database->{"system.profile"}`) or
[selectCollection()](#selectcollection).

### Example

The following example selects the "demo.users" and "demo.system.profile"
collections:

```
<?php

$db = (new MongoDB\Client)->demo;

$users = $db->users;
$systemProfile = $db->{'system.profile'};
```

### See Also

 * [MongoDB\Database::selectCollection()](#selectcollection)
 * [PHP Manual: Property Overloading](http://php.net/oop5.overloading#object.get)

---

## command()

```php
function function command($command, array $options = []): MongoDB\Driver\Cursor
```

Execute a command on this database.

### Supported Options

readPreference (MongoDB\Driver\ReadPreference)
:   The read preference to use when executing the command. This may be used when
    issuing the command to a replica set or mongos node to ensure that the
    driver sets the wire protocol accordingly or adds the read preference to the
    command document, respectively.

typeMap (array)
:   Type map for BSON deserialization. This will be applied to the returned
    Cursor (it is not sent to the server).

### See Also

 * [Tutorial: Database Commands](../tutorial/commands.md)
 * [MongoDB Manual: Database Commands](https://docs.mongodb.org/manual/reference/command/)

## createCollection

```php
function createCollection($collectionName, array $options = []): array|object
```

Create a new collection explicitly. Returns the command result document.

MongoDB already creates collections implicitly when they are first referenced in
commands (e.g. inserting a document into a new collection); however, collections
may also be explicitly created with specific options. This is useful for
creating [capped collections][capped], enabling
[document validation][validation], or supplying storage engine options.

[capped]: https://docs.mongodb.org/manual/core/capped-collections/
[validation]: https://docs.mongodb.org/manual/core/document-validation/

### Supported Options

autoIndexId (boolean)
:   Specify false to disable the automatic creation of an index on the _id
    field. For replica sets, this option cannot be false. The default is true.

capped (boolean)
:   Specify true to create a capped collection. If set, the size option must
    also be specified. The default is false.

flags (integer)
:   Options for the MMAPv1 storage engine only. Must be a bitwise combination
    `MongoDB\Operation\CreateCollection::USE_POWER_OF_2_SIZES` and
    `MongoDB\Operation\CreateCollection::NO_PADDING`. The default is
    `MongoDB\Operation\CreateCollection::USE_POWER_OF_2_SIZES`.

indexOptionDefaults (document)
:   Default configuration for indexes when creating the collection.

max (integer)
:   The maximum number of documents allowed in the capped collection. The size
    option takes precedence over this limit.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

size (integer)
:   The maximum number of bytes for a capped collection.

storageEngine (document)
:   Storage engine options.

typeMap (array)
:   Type map for BSON deserialization. This will only be used for the returned
    command result document.

validationAction (string)
:   Validation action.

validationLevel (string)
:   Validation level.

validator (document)
:   Validation rules or expressions.

### Example

The following example creates the "demo.users" collection with a custom
[document validator][validation] (available in MongoDB 3.2+):

```
<?php

$db = (new MongoDB\Client)->demo;

$result = $db->createCollection('users', [
    'validator' => [
        'username' => ['$type' => 'string'],
        'email' => ['$regex' => '@mongodb\.com$'],
    ],
]);

var_dump($result);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#11 (1) {
  ["storage":"ArrayObject":private]=>
  array(1) {
    ["ok"]=>
    float(1)
  }
}
```

### See Also

 * [MongoDB Manual: create command](http://docs.mongodb.org/manual/reference/command/create/)

---

## drop

```php
function drop(array $options = []): array|object
```

Drop this database. Returns the command result document.

### Supported Options

typeMap (array)
:   Type map for BSON deserialization. This will only be used for the returned
    command result document.

### Example

The following example drops the "demo" database:

```
$db = (new MongoDB\Client)->demo;

$result = $db->drop();

var_dump($result);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#11 (1) {
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

 * [MongoDB\Client::dropDatabase()](client.md#dropdatabase)
 * [MongoDB Manual: dropDatabase command](http://docs.mongodb.org/manual/reference/command/dropDatabase/)

---

## dropCollection

```php
function dropCollection($collectionName, array $options = []): array|object
```

Drop a collection within this database. Returns the command result document.

### Supported Options

typeMap (array)
:   Type map for BSON deserialization. This will only be used for the returned
    command result document.

### Example

The following example drops the "demo.users" collection:

```
<?php

$db = (new MongoDB\Client)->demo;

$result = $db->dropCollection('users');

var_dump($result);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#11 (1) {
  ["storage":"ArrayObject":private]=>
  array(3) {
    ["ns"]=>
    string(10) "demo.users"
    ["nIndexesWas"]=>
    int(1)
    ["ok"]=>
    float(1)
  }
}
```

### See Also

 * [MongoDB\Collection::drop()](collection.md#drop)
 * [MongoDB Manual: drop command](http://docs.mongodb.org/manual/reference/command/drop/)

---

## getDatabaseName()

```php
function getDatabaseName(): string
```

Return the database name.

---

## listCollections()

```php
function listCollections(array $options = []): MongoDB\Model\CollectionInfoIterator
```

Returns information for all collections in this database. Elements in the
returned iterator will be MongoDB\Model\CollectionInfo objects.

### Supported Options

filter (document)
:   Query by which to filter collections.

maxTimeMS (integer)
:   The maximum amount of time to allow the query to run.

### Example

The following example lists all collections in the "demo" database:

```
<?php

$db = (new MongoDB\Client)->demo;

foreach ($db->listCollections() as $collectionInfo) {
    var_dump($collectionInfo);
}
```

The above example would output something similar to:

```
object(MongoDB\Model\CollectionInfo)#8 (2) {
  ["name"]=>
  string(5) "users"
  ["options"]=>
  array(0) {
  }
}
object(MongoDB\Model\CollectionInfo)#13 (2) {
  ["name"]=>
  string(14) "system.profile"
  ["options"]=>
  array(2) {
    ["capped"]=>
    bool(true)
    ["size"]=>
    int(1048576)
  }
}
```

### See Also

 * [MongoDB Manual: listCollections command](http://docs.mongodb.org/manual/reference/command/listCollections/)
 * [MongoDB Specification: Enumerating Collections](https://github.com/mongodb/specifications/blob/master/source/enumerate-collections.rst)

---

## selectCollection()

```php
function selectCollection($collectionName, array $options = []): MongoDB\Collection
```

Select a collection within this database.

The Collection will inherit options (e.g. read preference, type map) from the
Database object unless otherwise specified.

### Supported Options

readConcern (MongoDB\Driver\ReadConcern)
:   The default read concern to use for collection operations. Defaults to the
    Database's read concern.

readPreference (MongoDB\Driver\ReadPreference)
:   The default read preference to use for collection operations. Defaults to
    the Database's read preference.

typeMap (array)
:   Default type map for cursors and BSON documents. Defaults to the Database's
    type map.

writeConcern (MongoDB\Driver\WriteConcern)
:   The default write concern to use for collection operations. Defaults to the
    Database's write concern.

### Example

The following example selects the "demo.users" collection:

```
<?php

$db = (new MongoDB\Client)->demo;

$collection = $db->selectCollection('users');
```

The following examples selects the "demo.users" collection with a custom read
preference:

```
<?php

$db = (new MongoDB\Client)->demo;

$collection = $db->selectCollection(
    'users',
    [
        'readPreference' => new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_SECONDARY),
    ]
);
```

### See Also

 * [MongoDB\Collection::__construct()](collection.md#__construct)
 * [MongoDB\Database::__get()](#__get)

---

## withOptions()

```php
function withOptions(array $options = []): MongoDB\Database
```

Returns a clone of this database with different options.

### Supported Options

readConcern (MongoDB\Driver\ReadConcern)
:   The default read concern to use for database operations and selected
    collections. Defaults to the Manager's read concern.

readPreference (MongoDB\Driver\ReadPreference)
:   The default read preference to use for database operations and selected
    collections. Defaults to the Manager's read preference.

typeMap (array)
:   Default type map for cursors and BSON documents.

writeConcern (MongoDB\Driver\WriteConcern)
:   The default write concern to use for database operations and selected
    collections. Defaults to the Manager's write concern.

### See Also

 * [MongoDB\Database::__construct()](#__construct)
