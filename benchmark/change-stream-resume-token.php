<?php

declare(strict_types=1);

/*
 * Micro-benchmark for MongoDB\Model\ChangeStreamIterator::extractResumeToken().
 *
 * This method runs for every document a change stream returns. A change stream
 * needs a replica set, so this script does not open one: extractResumeToken()
 * only reads its argument on the success path, so the iterator is built without
 * running its constructor and the method is called through a bound closure.
 * That isolates the token extraction from cursor and network time.
 *
 * The script is standalone and does not need a MongoDB server.
 *
 * Run from the repository root:
 *   php -d xdebug.mode=off benchmark/change-stream-resume-token.php
 */

foreach ([__DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php'] as $autoload) {
    if (is_file($autoload)) {
        require $autoload;
        break;
    }
}

use MongoDB\Model\BSONDocument;
use MongoDB\Model\ChangeStreamIterator;

$iterator = (new ReflectionClass(ChangeStreamIterator::class))->newInstanceWithoutConstructor();

$extractResumeToken = Closure::bind(
    function (array|object $document): array|object {
        return $this->extractResumeToken($document);
    },
    $iterator,
    ChangeStreamIterator::class,
);

/**
 * Returns the best of $rounds runs, each one averaging $iterations calls.
 *
 * The best run is reported because it is the one least disturbed by the
 * operating system scheduler.
 */
function bench(string $label, callable $fn, int $iterations, int $rounds = 7): float
{
    for ($i = 0; $i < 200; $i++) {
        $fn();
    }

    $best = PHP_FLOAT_MAX;
    for ($round = 0; $round < $rounds; $round++) {
        $start = hrtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $fn();
        }

        $best = min($best, (hrtime(true) - $start) / $iterations);
    }

    printf("%-46s %12.1f ns/op\n", $label, $best);

    return $best;
}

/**
 * Builds a change event shaped like the ones a change stream returns: a fixed
 * top-level document, with the payload nested in "fullDocument".
 */
function makeEvent(int $fields, bool $asBsonDocument): array|object
{
    $fullDocument = [];
    for ($i = 0; $i < $fields; $i++) {
        $fullDocument['field_' . $i] = 'value ' . $i;
    }

    if ($asBsonDocument) {
        $fullDocument = new BSONDocument($fullDocument);
    }

    $event = [
        '_id' => ['_data' => str_repeat('A', 32)],
        'operationType' => 'insert',
        'clusterTime' => '1234567890',
        'ns' => ['db' => 'test', 'coll' => 'bench'],
        'documentKey' => ['_id' => 'abc'],
        'fullDocument' => $fullDocument,
    ];

    if ($asBsonDocument) {
        return new BSONDocument($event);
    }

    return (object) $event;
}

echo "=== extractResumeToken(), default type map (BSONDocument) ===\n";

foreach ([5, 50, 500] as $fields) {
    $event = makeEvent($fields, true);
    printf("--- change event with %d payload fields ---\n", $fields);
    bench('extractResumeToken(BSONDocument)', fn () => $extractResumeToken($event), 20000);
}

echo "\n=== extractResumeToken(), stdClass root type map ===\n";

foreach ([5, 50, 500] as $fields) {
    $event = makeEvent($fields, false);
    printf("--- change event with %d payload fields ---\n", $fields);
    bench('extractResumeToken(stdClass)', fn () => $extractResumeToken($event), 20000);
}

echo "\nPHP: " . PHP_VERSION . ", ext-mongodb: " . phpversion('mongodb') . "\n";
