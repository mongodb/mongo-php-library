<?php
require __DIR__ . "/" . "../vendor/autoload.php";

function dumpWriteResults(MongoDB\WriteResult $result) {
    printf("Inserted %d documents, upserted %d, updated %d and deleted %d\n",
        $result->getInsertedCount(), $result->getUpsertedCount(),
        $result->getModifiedCount(), $result->getDeletedCount()
    );

    if ($result->getUpsertedCount()) {
        foreach ($result->getUpsertedIds() as $index => $id) {
            printf("upsertedId[%d]: %s", $index, $id);
        }
    }
}
function dumpCollection($collection) {
    printf("\n---\nDumping all documents in: %s.%s\n",
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


$manager = new MongoDB\Manager("mongodb://localhost:27017");
$collection = new MongoDB\Collection($manager, "crud.bulkWrite");
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
dumpCollection($collection);


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

echo "\n\n";
dumpWriteResults($result);
dumpCollection($collection);

$result = $collection->bulkWrite([
    [
        "deleteMany" => [
            [],
        ],
    ],
]);

echo "\n\n";
dumpWriteResults($result);
dumpCollection($collection);

