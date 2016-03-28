# BSON Conversion

## Deserialization

By default, the library returns BSON documents and arrays as
MongoDB\Model\BSONDocument and MongoDB\Model\BSONArray objects, respectively.
Both of those classes extend PHP's [ArrayObject][arrayobject] class and
implement the driver's [MongoDB\BSON\Serializable][serializable] and
[MongoDB\BSON\Unserializable][unserializable] interfaces.

[arrayobject]: http://php.net/arrayobject
[serializable]: http://php.net/mongodb-bson-serializable
[unserializable]: http://php.net/mongodb-bson-unserializable

## Type Maps

Most methods that read data from MongoDB support a "typeMap" option, which
allows control over how BSON is converted to PHP. Additionally, the
[MongoDB\Client][client], [MongoDB\Database][database], and
[MongoDB\Collection][collection] classes accept a "typeMap" option, which will
apply to any supporting methods and selected classes by default.

[client]: ../classes/client.md
[database]: ../classes/database.md
[collection]: ../classes/collection.md

The [MongoDB\Client][client], [MongoDB\Database][database], and
[MongoDB\Collection][collection] classes use the following type map by default:

```php
[
    'array' => 'MongoDB\Model\BSONArray',
    'document' => 'MongoDB\Model\BSONDocument',
    'root' => 'MongoDB\Model\BSONDocument',
]
```

## Persistable Classes

Classes implementing [MongoDB\BSON\Persistable][persistable] will be serialized
and deserialized according to the [Persistence][persistence] specification. This
behavior occurs by default in the [driver][ext-mongodb] and does not require use
of the "typeMap" option.

[persistable]: http://php.net/mongodb-bson-persistable
[persistence]: http://php.net/manual/en/mongodb.persistence.php
[ext-mongodb]: https://php.net/mongodb

Given the following class definition:

```
<?php

class Person implements MongoDB\BSON\Persistable
{
    private $id;
    private $name;
    private $createdAt;

    public function __construct($name)
    {
        $this->id = new MongoDB\BSON\ObjectID;
        $this->name = (string) $name;

        // Get current time in milliseconds since the epoch
        $msec = floor(microtime(true) * 1000);
        $this->createdAt = new MongoDB\BSON\UTCDateTime($msec);
    }

    function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'name' => $this->name,
            'createdAt' => $this->createdAt,
        ];
    }

    function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->name = $data['name'];
        $this->createdAt = $data['createdAt'];
    }
}
```

The following example constructs a Person object, inserts it into the database,
and reads it back as an object of the same type (without the use of the
"typeMap" option):

```
<?php

$collection = (new MongoDB\Client)->demo->persons;

$result = $collection->insertOne(new Person('Bob'));

$person = $collection->findOne(['_id' => $result->getInsertedId()]);

var_dump($person);
```

The above example would output something similar to:

```
object(Person)#18 (3) {
  ["id":"Person":private]=>
  object(MongoDB\BSON\ObjectID)#15 (1) {
    ["oid"]=>
    string(24) "56fad2c36118fd2e9820cfc1"
  }
  ["name":"Person":private]=>
  string(3) "Bob"
  ["createdAt":"Person":private]=>
  object(MongoDB\BSON\UTCDateTime)#17 (1) {
    ["milliseconds"]=>
    int(1459278531218)
  }
}
```

The same document in the MongoDB shell might display as:

```
> db.persons.findOne()
{
  "_id" : ObjectId("56fad2c36118fd2e9820cfc1"),
  "__pclass" : BinData(128,"UGVyc29u"),
  "name" : "Bob",
  "createdAt" : ISODate("2016-03-29T19:08:51.218Z")
}
```

**Note:** [MongoDB\BSON\Persistable][persistable] may only be used for root and
embedded BSON documents; BSON arrays are not supported.

## Emulating the Legacy Driver

The legacy [mongo extension][ext-mongo] returned both BSON documents and
arrays as PHP arrays. While PHP arrays are convenient to work with, this
behavior was problematic for several reasons:

[ext-mongo]: http://php.net/mongo

 * Different BSON types could deserialize to the same PHP value (e.g.
   `{"0": "foo"}` and `["foo"]`), which made it impossible to infer the
   original BSON type.

 * Numerically indexed PHP arrays would be serialized as BSON documents if there
   was a gap in their key sequence. Such gaps were easily (and inadvertently)
   caused by unsetting a key to remove an element and forgetting to reindex the
   array.

The libary's MongoDB\Model\BSONDocument and MongoDB\Model\BSONArray classes
address these concerns by preserving the BSON type information during
serialization and deserialization; however, some users may still prefer the
legacy behavior. If desired, the following "typeMap" option can be used to have
the library return everything as a PHP array:

```
<?php

$client = new MongoDB\Client(
    null,
    [],
    ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
);

$document = $client->demo->zips->findOne(
    ['_id' => '94301'],
    ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
);

var_dump($document);
```

The above example would output something similar to:

```
array(5) {
  ["_id"]=>
  string(5) "94301"
  ["city"]=>
  string(9) "PALO ALTO"
  ["loc"]=>
  array(2) {
    [0]=>
    float(-122.149685)
    [1]=>
    float(37.444324)
  }
  ["pop"]=>
  int(15965)
  ["state"]=>
  string(2) "CA"
}
```
