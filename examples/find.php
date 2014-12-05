<?php

require __DIR__ . "/../src/MongoDB/QueryFlags.php";
require __DIR__ . "/../src/MongoDB/CursorType.php";
require __DIR__ . "/../src/MongoDB/Collection.php";


$manager = new MongoDB\Manager("mongodb://localhost:27017");


$collection = new MongoDB\Collection($manager, "crud.examples");
$result = $collection->find(array("nick" => "bjori"), array("projection" => array("name" =>1)));


foreach($result as $document) {
    var_dump($document);
}



