<?php

// Returns a document using the default type map
$collection->findOne(['name' => 'Jane Doe'], [
    'codec' => null,
]);

// Disables codec usage as the aggregate result will have a different format
$collection->aggregate([['$group' => [
    '_id' => '$accountType',
    'count' => ['$sum' => 1],
]]], [
    'codec' => null,
]);
