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
use function time;

require __DIR__ . '/../vendor/autoload.php';

function toJSON(object $document): string
{
    return toRelaxedExtendedJSON(fromPHP($document));
}

// Change streams require a replica set or sharded cluster
$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$collection = $client->test->changestream;
$collection->drop();

// Create collection before starting change stream; this is required on MongoDB 3.6
$client->test->createCollection('changestream');

$changeStream = $collection->watch();

$documents = [];

for ($i = 0; $i < 10; $i++) {
    $documents[] = ['x' => $i];
}

$collection->insertMany($documents);

$changeStream->rewind();

$startTime = time();

while (true) {
    if ($changeStream->valid()) {
        $event = $changeStream->current();
        assert(is_object($event));
        printf("%s\n", toJSON($event));
    }

    $changeStream->next();

    if (time() - $startTime > 3) {
        printf("Aborting after 3 seconds...\n");
        break;
    }
}
