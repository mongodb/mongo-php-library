# MongoDB\Database

`MongoDB\Database` provides methods for common operations on a database, such
as creating, enumerating, and dropping collections.

A Database may be constructed directly (using the extension's Manager class) or
selected from the library's Client class. It supports the following options:

 * [readConcern](http://php.net/mongodb-driver-readconcern)
 * [readPreference](http://php.net/mongodb-driver-readpreference)
 * [typeMap](http://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps)
 * [writeConcern](http://php.net/mongodb-driver-writeconcern)

If any options are omitted, they will be inherited from the Manager constructor
argument or object from which the Database was selected.

Operations within the Database class (e.g. `command()`) will generally inherit
the Database's options.

### Selecting Collections

The Database class provides methods for creating Collection instances (using its
internal Manager instance). When selecting a Collection, the child will inherit
options (e.g. read preference, type map) from the Database. New options may also
be provided to the `selectCollection()` method.

```
$db = (new MongoDB\Client)->demo;

/* Select the "users" collection */
$collection = $db->selectCollection('users');

/* selectCollection() also takes an options array, which can override any
 * options inherited from the Database. */
$collection = $client->selectCollection('users', [
    'readPreference' => new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_SECONDARY),
]);

/* The property accessor may also be used to select a collection without
 * specifying additional options. PHP's complex syntax may be used for selecting
 * collection whose names contain special characters (e.g. "."). */
$collection = $db->users;
$collection = $db->{'system.profile'};
```

## Database-level Operations

The Database class has methods for database-level operations, such as dropping
the database, executing a command, or managing the database's collections.

### Dropping the Database

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

### Executing a Command

While the library provides helpers for some common database commands, it is far
from an [exhaustive list](https://docs.mongodb.org/manual/reference/command/).
The following example demonstrates how the
[createUser](https://docs.mongodb.org/manual/reference/command/createUser/)
command might be invoked:

```
$db = (new MongoDB\Client)->demo;

/* Define a command document for creating a new database user */
$createUserCmd = [
    'createUser' => 'username',
    'pwd' => 'password',
    'roles' => [ 'readWrite' ],
];

/* It doesn't hurt to specify an explicit read preference for the command, in
 * case the Database was created with a different read preference. This isn't
 * required for other command helpers, as the library knows which commands might
 * require a primary; however, the Database::command() method is generic. */
$cursor = $db->command($createUserCmd, [
    'readPreference' => new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY),
]);

/* The command result will be the first and only document in the cursor */
var_dump($cursor->toArray()[0]);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#8 (1) {
  ["storage":"ArrayObject":private]=>
  array(1) {
    ["ok"]=>
    float(1)
  }
}
```

## Collection Management

The Database class has several methods for managing collections.

### Creating Collections

MongoDB already creates collections implicitly when they are first referenced in
commands (e.g. inserting a document into a new collection); however, collections
may also be explicitly created with specific options. This is useful for
creating
[capped collections](https://docs.mongodb.org/manual/core/capped-collections/),
enabling
[document validation](https://docs.mongodb.org/manual/core/document-validation/),
or supplying storage engine options.

```
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

### Dropping Collections

```
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

### Enumerating Collections

The Database class implements MongoDB's
[Enumerating Collections specification](https://github.com/mongodb/specifications/blob/master/source/enumerate-collections.rst).

```
$db = (new MongoDB\Client)->demo;

/* listCollections() returns an iterator of MongoDB\Model\CollectionInfo objects */
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
