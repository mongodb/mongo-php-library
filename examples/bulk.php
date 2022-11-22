<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;
use MongoDB\Driver\WriteConcern;

use function assert;
use function getenv;
use function is_object;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toRelaxedExtendedJSON;
use function printf;

require __DIR__ . '/../vendor/autoload.php';

function toJSON(object $document): string
{
    return toRelaxedExtendedJSON(fromPHP($document));
}

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$collection = $client->test->bulk;
$collection->drop();

$documents = [];

for ($i = 0; $i < 10; $i++) {
    $documents[] = ['x' => $i];
}

$collection->insertMany($documents, ['writeConcern' => new WriteConcern('majority')]);

$collection->bulkWrite(
    [
        [
            'deleteMany' => [
                ['x' => ['$gt' => 7]], // Filter
            ],
        ],
        [
            'deleteOne' => [
                ['x' => 4], // Filter
            ],
        ],
        [
            'replaceOne' => [
                ['x' => 1], // Filter
                ['y' => 1], // Replacement
            ],
        ],
        [
            'updateMany' => [
                ['x' => ['$gt' => 5]], // Filter
                ['$set' => ['updateMany' => true]], // Update
            ],
        ],
        [
            'updateOne' => [
                ['x' => 2], // Filter
                ['$set' => ['y' => 2]], // Update
            ],
        ],
        [
            'insertOne' => [
                ['x' => 10], // Document
            ],
        ],
    ]
);

$cursor = $collection->find([]);

foreach ($cursor as $document) {
    assert(is_object($document));
    printf("%s\n", toJSON($document));
}
