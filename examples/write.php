<?php

require __DIR__ . "/../src/Collection.php";


$manager = new MongoDB\Manager("mongodb://localhost:27017");


$collection = new MongoDB\Collection($manager, "crud.examples");
$hannes = array(
	"name"    => "Hannes", 
	"nick"    => "bjori",
	"citizen" => "Iceland",
);
$hayley = array(
	"name"    => "Hayley",
	"nick"    => "Alien Ninja",
	"citizen" => "USA",
);
$jonpall = array(
	"name"    => "Jon Pall",
	"nick"    => "unknown",
	"citizen" => "Iceland",
);

try {
    $hannes_id = $collection->insertOne($hannes);
} catch(Exception $e) {
    echo $e->getMessage(), "\n";
    exit;
}

try {
    $results = $collection->insertMany(array($hayley, $jonpall));
} catch(Exception $e) {
    echo $e->getMessage(), "\n";
    exit;
}



