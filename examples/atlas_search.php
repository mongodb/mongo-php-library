<?php

/**
 * This example demonstrates how to create an Atlas Search index and perform a search query.
 * It requires a MongoDB Atlas M10+ cluster with Sample Dataset loaded.
 *
 * Use the MONGODB_URI environment variable to specify the connection string from the Atlas UI.
 */

declare(strict_types=1);

namespace MongoDB\Examples;

use Closure;
use MongoDB\Client;
use RuntimeException;

use function define;
use function getenv;
use function hrtime;
use function iterator_to_array;
use function sleep;
use function str_contains;

require __DIR__ . '/../vendor/autoload.php';

$uri = getenv('MONGODB_URI');
if (! $uri || ! str_contains($uri, '.mongodb.net')) {
    echo 'This example requires a MongoDB Atlas cluster.', "\n";
    echo 'Make sure you set the MONGODB_URI environment variable.', "\n";
    exit(1);
}

// Atlas Search index management operations are asynchronous.
// They usually take less than 5 minutes to complete.
define('WAIT_TIMEOUT_SEC', 300);

// The sample dataset is loaded into the "sample_airbnb.listingsAndReviews" collection.
$databaseName = getenv('MONGODB_DATABASE') ?: 'sample_airbnb';
$collectionName = getenv('MONGODB_COLLECTION') ?: 'listingsAndReviews';

$client = new Client($uri);
$collection = $client->selectCollection($databaseName, $collectionName);

$count = $collection->estimatedDocumentCount();
if ($count === 0) {
    echo 'This example requires the "', $databaseName, '" database with the "', $collectionName, '" collection.', "\n";
    echo 'Load the sample dataset in your MongoDB Atlas cluster before running this example:', "\n";
    echo '    https://www.mongodb.com/docs/atlas/sample-data/', "\n";
    exit(1);
}

// Delete the index if it already exists
$indexes = iterator_to_array($collection->listSearchIndexes());
foreach ($indexes as $index) {
    if ($index->name === 'default') {
        echo "The index already exists. Dropping it.\n";
        $collection->dropSearchIndex($index->name);

        // Wait for the index to be deleted.
        wait(function () use ($collection) {
            echo '.';
            foreach ($collection->listSearchIndexes() as $index) {
                if ($index->name === 'default') {
                    return false;
                }
            }

            return true;
        });
    }
}

// Create the search index
echo "\nCreating the index.\n";
$collection->createSearchIndex(
    /* The index definition requires a mapping
     * See: https://www.mongodb.com/docs/atlas/atlas-search/define-field-mappings/ */
    ['mappings' => ['dynamic' => true]],
    // "default" is the default index name, this config can be omitted.
    ['name' => 'default'],
);

// Wait for the index to be queryable.
wait(function () use ($collection) {
    echo '.';
    foreach ($collection->listSearchIndexes() as $index) {
        if ($index->name === 'default') {
            return $index->queryable;
        }
    }

    return false;
});

// Perform a text search
echo "\n", 'Performing a text search...', "\n";
$results = $collection->aggregate([
    [
        '$search' => [
            'index' => 'default',
            'text' => [
                'query' => 'view beach ocean',
                'path' => ['name'],
            ],
        ],
    ],
    ['$project' => ['name' => 1, 'description' => 1]],
    ['$limit' => 10],
])->toArray();

foreach ($results as $document) {
    echo ' - ', $document['name'], "\n";
}

echo "\n", 'Enjoy MongoDB Atlas Search!', "\n\n";

/**
 * This function waits until the callback returns true or the timeout is reached.
 */
function wait(Closure $callback): void
{
    $timeout = hrtime()[0] + WAIT_TIMEOUT_SEC;
    while (hrtime()[0] < $timeout) {
        if ($callback()) {
            return;
        }

        sleep(5);
    }

    throw new RuntimeException('Time out');
}
