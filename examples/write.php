<?php

require __DIR__ . "/../src/MongoDB/QueryFlags.php";
require __DIR__ . "/../src/MongoDB/CursorType.php";
require __DIR__ . "/../src/MongoDB/Collection.php";


$manager = new MongoDB\Manager("mongodb://localhost:27017");


$collection = new MongoDB\Collection($manager, "crud.examples");
$hannes = array(
	"name"    => "Hannes", 
	"nick"    => "bjori",
	"citizen" => "Iceland",
);
$hayley = array(
	"name"    => "Hayley",
	"nick"    => "Ninja",
	"citizen" => "USA",
);
$bobby = array(
    "name" => "Robert Fischer",
    "nick" => "Bobby Fischer",
    "citizen" => "USA",
);

try {
    $result = $collection->insertOne($hannes);
    printf("Inserted: %s (out of expected 1)\n", $result->getNumInserted());
    $result = $collection->insertOne($hayley);
    printf("Inserted: %s (out of expected 1)\n", $result->getNumInserted());
    $result = $collection->insertOne($bobby);
    printf("Inserted: %s (out of expected 1)\n", $result->getNumInserted());

    $result = $collection->find(array("nick" => "bjori"), array("projection" => array("name" => 1)));
    echo "Searching for nick => bjori, should have only one result:\n";
    foreach($result as $document) {
        var_dump($document);
    }

    $result = $collection->deleteOne($document);
    printf("Deleted: %s (out of expected 1)\n", $result->getNumRemoved());
    $result = $collection->updateOne(
        array("citizen" => "USA"),
        array('$set' => array("citizen" => "Iceland"))
    );
    printf("Updated: %s (out of expected 1)\n", $result->getNumModified());

    $result = $collection->find(array("citizen" => "Iceland"), array("comment" => "Excellent query"));
    echo "Searching for citizen => Iceland, verify Hayley is now Icelandic\n";
    foreach($result as $document) {
        var_dump($document);
    }
    $result = $collection->deleteOne($document);
    printf("Deleted: %d (out of expected 1)\n", $result->getNumRemoved());

} catch(Exception $e) {
    echo $e->getMessage(), "\n";
    exit;
}

try {
    /* These two were removed earlier */
    $result = $collection->insertOne($hannes);
    printf("Inserted: %s (out of expected 1)\n", $result->getNumInserted());
    $result = $collection->insertOne($hayley);
    printf("Inserted: %s (out of expected 1)\n", $result->getNumInserted());

    $result = $collection->find();
    echo "Find all docs, should be 3, verify 2x USA citizen, 1 Icelandic\n";
    foreach($result as $document) {
        var_dump($document);
    }

    $result = $collection->updateMany(
        array("citizen" => "USA"),
        array('$set' => array("citizen" => "Iceland"))
    );

    printf("Updated: %d (out of expected 2), verify everyone is Icelandic\n", $result->getNumModified());
    $result = $collection->find();
    foreach($result as $document) {
        var_dump($document);
    }
    $result = $collection->replaceOne(
        array("nick" => "Bobby Fischer"),
        array("name" => "Magnus Carlsen", "nick" => "unknown", "citizen" => "Norway")
    );
    printf("Replaced: %d (out of expected 1), verify Bobby has been replaced with Magnus\n", $result->getNumModified());
    $result = $collection->find();
    foreach($result as $document) {
        var_dump($document);
    }

    $result = $collection->deleteMany(array("citizen" => "Iceland"));
    printf("Deleted: %d (out of expected 3)\n", $result->getNumRemoved());
} catch(Exception $e) {
    echo $e->getMessage(), "\n";
    exit;

}

