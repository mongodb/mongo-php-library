<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;

use function assert;
use function getenv;
use function is_object;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toRelaxedExtendedJSON;
use function printf;
use function rand;

require __DIR__ . '/../vendor/autoload.php';

function toJSON(object $document): string
{
    return toRelaxedExtendedJSON(fromPHP($document));
}

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$collection = $client->test->aggregate;
$collection->drop();

$documents = [];

for ($i = 0; $i < 100; $i++) {
    $documents[] = ['randomValue' => rand(0, 1000)];
}

$collection->insertMany($documents);

$pipeline = [
    [
        '$group' => [
            '_id' => null,
            'totalCount' => ['$sum' => 1],
            'evenCount' => [
                '$sum' => ['$mod' => ['$randomValue', 2]],
            ],
            'oddCount' => [
                '$sum' => ['$subtract' => [1, ['$mod' => ['$randomValue', 2]]]],
            ],
            'maxValue' => ['$max' => '$randomValue'],
            'minValue' => ['$min' => '$randomValue'],
        ],
    ],
];

$cursor = $collection->aggregate($pipeline);

foreach ($cursor as $document) {
    assert(is_object($document));
    printf("%s\n", toJSON($document));
}
