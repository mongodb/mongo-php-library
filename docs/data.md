# Example data

For the purpose of this documentation we will be using the
[zips.json](http://media.mongodb.org/zips.json) in our examples.

Importing the dataset into the database can be done in several ways,
for example using the following script:

```php
<?php

$file = "http://media.mongodb.org/zips.json";

$zips = file($file, FILE_IGNORE_NEW_LINES);


$batch = new MongoDB\WriteBatch(true);
foreach($zips as $string) {
    $document = json_decode($string);
    $batch->insert($document);
}

$manager = new MongoDB\Manager("mongodb://localhost");

$result = $manager->executeWriteBatch("examples.zips", $batch);
printf("Inserted %d documents\n", $result->getInsertedCount());

?>
```

Outputs

```
Inserted 29353 documents
```
