<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;
use MongoDB\Driver\Session;

use function assert;
use function getenv;
use function is_object;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toRelaxedExtendedJSON;
use function MongoDB\with_transaction;
use function printf;

require __DIR__ . '/../vendor/autoload.php';

function toJSON(object $document): string
{
    return toRelaxedExtendedJSON(fromPHP($document));
}

// Transactions require a replica set (MongoDB >= 4.0) or sharded cluster (MongoDB >= 4.2)
$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$collection = $client->test->with_transaction;
$collection->drop();

// Create collection outside of transaction; this is required when using MongoDB < 4.4
$client->test->createCollection('with_transaction');

$insertData = function (Session $session) use ($collection): void {
    $collection->insertMany(
        [
            ['x' => 1],
            ['x' => 2],
            ['x' => 3],
        ],
        ['session' => $session]
    );

    $collection->updateMany(
        ['x' => ['$gt' => 1]],
        ['$set' => ['y' => 1]],
        ['session' => $session]
    );
};

$session = $client->startSession();

with_transaction($session, $insertData);

$cursor = $collection->find([]);

foreach ($cursor as $document) {
    assert(is_object($document));
    printf("%s\n", toJSON($document));
}
