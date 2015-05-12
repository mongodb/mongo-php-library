<?php

require_once __DIR__ . "/bootstrap.php";

$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$collection = new MongoDB\Collection($manager, "phplib_demo.bulkwrite");

function dumpWriteResults(MongoDB\BulkWriteResult $result)
{
    printf("Inserted %d documents, upserted %d, updated %d, and deleted %d\n",
        $result->getInsertedCount(),
        $result->getUpsertedCount(),
        $result->getModifiedCount(),
        $result->getDeletedCount()
    );

    if ($result->getUpsertedCount()) {
        foreach ($result->getUpsertedIds() as $index => $id) {
            printf("upsertedId[%d]: %s\n", $index, $id);
        }
    }
}

function dumpCollection($collection)
{
    printf("Dumping all documents in: %s.%s\n",
        $collection->getDatabaseName(),
        $collection->getCollectionName()
    );
    $n = 0;
    foreach($collection->find() as $document) {
        var_dump($document);
        $n++;
    }
    printf("Found %d documents\n", $n);
}

$result = $collection->bulkWrite([
    [
        "insertOne" => [
            [
                "name" => "Hannes Magnusson",
                "company" => "10gen",
            ]
        ],
    ],
    [
        "insertOne" => [
            [
                "name" => "Jeremy Mikola",
                "company" => "10gen",
            ]
        ],
    ],
    [
        "updateMany" => [
            ["company" => "10gen"],
            ['$set' => ["company" => "MongoDB"]],
        ],
    ],
    [
        "updateOne" => [
            ["name" => "Hannes Magnusson"],
            ['$set' => ["viking" => true]],
        ],
    ],
]);

dumpWriteResults($result);
echo "\n";
dumpCollection($collection);
echo "\n";

$result = $collection->bulkWrite([
    [
        "deleteOne" => [
            ["company" => "MongoDB"],
        ],
    ],
    [
        "updateOne" => [
            ["name" => "Hannes Magnusson"],
            ['$set' => ["nationality" => "Icelandic"]],
            ["upsert" => true],
        ],
    ],
    [
        "deleteMany" => [
            ["nationality" => [ '$ne' => "Icelandic"]],
        ],
    ],
]);

dumpWriteResults($result);
echo "\n";
dumpCollection($collection);
echo "\n";

$result = $collection->bulkWrite([
    [
        "deleteMany" => [
            [],
        ],
    ],
]);

dumpWriteResults($result);
echo "\n";
dumpCollection($collection);
