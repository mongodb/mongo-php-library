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
