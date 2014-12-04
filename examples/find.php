<?php

require __DIR__ . "/../src/Collection.php";


$manager = new MongoDB\Manager("mongodb://localhost:27017");


var_dump($manager);
$collection = new MongoDB\Collection($manager, "phongo_test.functional_cursor_001");
$result = $collection->find(array("username" => "pacocha.quentin"), array("projection" => array("firstName" =>1)));


foreach($result as $document) {
    var_dump($document);
}



