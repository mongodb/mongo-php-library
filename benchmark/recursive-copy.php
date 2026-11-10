<?php

declare(strict_types=1);

/*
 * Micro-benchmark for MongoDB\recursive_copy().
 *
 * recursive_copy() is called by BSONDocument::__clone() and BSONArray::__clone(),
 * so it runs on every deep copy of a document returned by the library.
 *
 * The script is standalone and does not need a MongoDB server.
 *
 * Run from the repository root:
 *   php -d xdebug.mode=off benchmark/recursive-copy.php
 */

foreach ([__DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php'] as $autoload) {
    if (is_file($autoload)) {
        require $autoload;
        break;
    }
}

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

use function MongoDB\recursive_copy;

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

    printf("%-40s %12.1f ns/op\n", $label, $best);

    return $best;
}

/** Builds a document nested $depth levels deep. */
function makeNested(int $depth): BSONDocument
{
    $document = new BSONDocument(['leaf' => 1, 'tags' => new BSONArray(['a', 'b'])]);

    for ($i = 0; $i < $depth; $i++) {
        $document = new BSONDocument([
            'child' => $document,
            'name' => 'level ' . $i,
            'index' => $i,
        ]);
    }

    return $document;
}

echo "=== recursive_copy() on nested documents ===\n";

foreach ([1, 10, 100] as $depth) {
    $document = makeNested($depth);
    $objects = 2 * $depth + 1;

    printf("--- depth %d (%d objects) ---\n", $depth, $objects);
    $iterations = $depth > 50 ? 2000 : 20000;
    bench('recursive_copy($document)', fn () => recursive_copy($document), $iterations);
    bench('clone $document (BSONDocument::__clone)', fn () => clone $document, $iterations);
}

echo "\n=== Flat document with 20 fields ===\n";
$flat = new BSONDocument();
for ($i = 0; $i < 20; $i++) {
    $flat['field_' . $i] = 'value ' . $i;
}

bench('recursive_copy($flat)', fn () => recursive_copy($flat), 50000);

echo "\nPHP: " . PHP_VERSION . ", ext-mongodb: " . phpversion('mongodb') . "\n";
