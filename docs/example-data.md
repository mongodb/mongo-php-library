# Example Data

Some examples in this documentation use example data fixtures from
[zips.json](http://media.mongodb.org/zips.json). This is a dataset comprised of
United States postal codes, populations, and geographic locations.

Importing the dataset into MongoDB can be done in several ways. The following
example uses [PHP driver](http://php.net/mongodb) (i.e. `mongodb` extension).

```
$file = 'http://media.mongodb.org/zips.json';
$zips = file($file, FILE_IGNORE_NEW_LINES);

$bulk = new MongoDB\Driver\BulkWrite;

foreach ($zips as $string) {
    $document = json_decode($string);
    $bulk->insert($document);
}

$manager = new MongoDB\Driver\Manager('mongodb://localhost');

$result = $manager->executeBulkWrite('demo.zips', $bulk);
printf("Inserted %d documents\n", $result->getInsertedCount());
```

Executing this script should yield the following output:

```
Inserted 29353 documents
```

You may also import the dataset using the
[`mongoimport`](http://docs.mongodb.org/manual/reference/program/mongoimport/)
command, which is included with MongoDB:

```
$ mongoimport --db demo --collection zips --file zips.json --drop
```
