<?php

require __DIR__ . "/../src/Collection.php";


$manager = new MongoDB\Manager("mongodb://localhost:27017");


var_dump($manager);
$collection = new MongoDB\Collection($manager, "crud.examples");
$result = $collection->find(array("nick" => "bjori"), array("projection" => array("name" =>1)));


foreach($result as $document) {
    var_dump($document);
}



