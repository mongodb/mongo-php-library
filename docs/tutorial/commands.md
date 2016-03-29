# Database Commands

While the library provides helpers for some common database commands, it is far
from an [exhaustive list][command-list]. This page will demonstrate how to
execute arbitrary commands on the MongoDB server via the
[Database::command()][command] method and access their results.

[command-list]: https://docs.mongodb.org/manual/reference/command/
[command]: ../classes/database.md#command

## Single Result Documents

The [command()][command] method always returns a
[MongoDB\Driver\Cursor][cursor]. Unless otherwise stated in the MongoDB
documentation, command responses are returned as a single document. Reading such
a result will require iterating on the cursor and accessing the first (and only)
document, like so:

[cursor]: http://php.net/mongodb-driver-cursor

```
<?php

$database = (new MongoDB\Client)->demo;

$cursor = $database->command(['ping' => 1]);

var_dump($cursor->toArray()[0]);
```

The above example would output something similar to:

```
object(MongoDB\Model\BSONDocument)#2 (1) {
  ["storage":"ArrayObject":private]=>
  array(1) {
    ["ok"]=>
    float(1)
  }
}
```

## Iterable Results as a Command Cursor

Some commands, such as [listCollections][listcollections], return their results
via an iterable command cursor. In this case, the returned
[MongoDB\Driver\Cursor][cursor] may be iterated in the same manner as one might
do with a [Collection::find()][find] query, like so:

[listcollections]: http://docs.mongodb.org/manual/reference/command/listCollections/
[find]: ../classes/collection.md#find

```
<?php

$database = (new MongoDB\Client)->demo;

$cursor = $database->command(['listCollections' => 1]);

foreach ($cursor as $collection) {
    echo $collection['name'], "\n";
}
```

The above example would output something similar to:

```
persons
posts
zips
```

**Note:** at the protocol level, commands that support a cursor actually return
a single result document with the essential ingredients for constructing the
command cursor (i.e. the cursor's ID, namespace, and first batch of results);
however, the driver's [executeCommand()][executecommand] method already detects
such a result and constructs the iterable command cursor for us.

[executecommand]: http://php.net/manual/en/mongodb-driver-manager.executecommand.php

## Specifying a Read Preference

Some commands, such as [createUser][createUser], can only be executed on a
primary server. Command helpers in the library, such as
[Database::drop()][drop], know to apply their own read preference if necessary;
however, [command()][command] is a generic method and has no special knowledge.
It defaults to the read preference of the Database object on which it is
invoked. In such cases, it can be helpful to explicitly specify the correct read
preference, like so:

[createUser]: https://docs.mongodb.org/manual/reference/command/createUser/
[drop]: ../classes/database.md#drop

```
<?php

$db = (new MongoDB\Client)->demo;

$cursor = $db->command(
    [
        'createUser' => 'username',
        'pwd' => 'password',
        'roles' => ['readWrite'],
    ],
    [
        'readPreference' => new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY),
    ]
);

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
