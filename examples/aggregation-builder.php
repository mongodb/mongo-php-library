<?php
declare(strict_types=1);

/**
 * This example demonstrates how you can use the builder provided by this library to build an aggregation pipeline.
 */

namespace MongoDB\Examples\AggregationBuilder;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Client;

use function array_is_list;
use function assert;
use function getenv;
use function is_array;
use function is_object;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toRelaxedExtendedJSON;
use function printf;
use function random_int;

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
    $documents[] = ['randomValue' => random_int(0, 1000)];
}

$collection->insertMany($documents);

$pipeline = new Pipeline(
    Stage::group(
        _id: null,
        totalCount: Accumulator::sum(1),
        evenCount: Accumulator::sum(
            Expression::mod(Expression::numberFieldPath('randomValue'), 2),
        ),
        oddCount: Accumulator::sum(
            Expression::subtract(
                1,
                Expression::mod(Expression::numberFieldPath('randomValue'), 2),
            ),
        ),
        maxValue: Accumulator::max(Expression::numberFieldPath('randomValue')),
        minValue: Accumulator::min(Expression::numberfieldPath('randomValue')),
    ),
);

// @todo Accept a Pipeline instance in Collection::aggregate() and automatically encode it
$encoder = new BuilderEncoder();
$pipeline = $encoder->encode($pipeline);

assert(is_array($pipeline) && array_is_list($pipeline));

$cursor = $collection->aggregate($pipeline);

foreach ($cursor as $document) {
    assert(is_object($document));
    printf("%s\n", toJSON($document));
}
