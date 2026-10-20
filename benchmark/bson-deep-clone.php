<?php

declare(strict_types=1);

/*
 * Micro-benchmark for the deep clone of BSONDocument and BSONArray. The previous
 * implementation is reproduced in full, so the comparison is end to end.
 */

/* Run from the repository root: php -d xdebug.mode=off benchmark/bson-deep-clone.php */

foreach ([__DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php'] as $autoload) {
    if (is_file($autoload)) {
        require $autoload;
        break;
    }
}

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

use function MongoDB\recursive_copy;

/* The copy helper as it was before the change: it wrote back into the array. */
function recursive_copy_before(mixed $element): mixed
{
    if (is_array($element)) {
        foreach ($element as $key => $value) {
            $element[$key] = recursive_copy_before($value);
        }

        return $element;
    }

    if (! is_object($element)) {
        return $element;
    }

    if (! (new ReflectionClass($element))->isCloneable()) {
        return $element;
    }

    return clone $element;
}

/* What BSONDocument::__clone() did before the change: one key at a time. */
$perFieldDocument = static fn (array $fields): BSONDocument => new class ($fields) extends BSONDocument {
    public function __clone()
    {
        foreach ($this as $key => $value) {
            $this[$key] = recursive_copy_before($value);
        }
    }
};

/* The same body, for the array model. */
$perFieldArray = static fn (array $values): BSONArray => new class ($values) extends BSONArray {
    public function __clone()
    {
        foreach ($this as $key => $value) {
            $this[$key] = recursive_copy_before($value);
        }
    }
};

/* The shipped models, so both sides are built the same way. */
$shippedDocument = static fn (array $fields): BSONDocument => new BSONDocument($fields);
$shippedArray = static fn (array $values): BSONArray => new BSONArray($values);

/* Best and median of a list of measurements. */
function summarize(array $times): array
{
    sort($times);
    $count = count($times);
    $middle = intdiv($count, 2);

    return [
        'best' => $times[0],
        'median' => $count % 2 === 1 ? $times[$middle] : ($times[$middle - 1] + $times[$middle]) / 2,
    ];
}

/*
 * Measures two variants in alternating rounds, then prints the best and the
 * median of each in nanoseconds per call.
 */
function bench(string $beforeLabel, string $afterLabel, callable $before, callable $after, int $iterations, int $rounds = 21): void
{
    for ($i = 0; $i < 500; $i++) {
        $before();
        $after();
    }

    $times = ['before' => [], 'after' => []];

    for ($round = 0; $round < $rounds; $round++) {
        /* Reverse the order each round, so neither variant always runs first. */
        $order = $round % 2 === 0 ? ['before', 'after'] : ['after', 'before'];

        foreach ($order as $variant) {
            $fn = $variant === 'before' ? $before : $after;

            $start = hrtime(true);
            for ($i = 0; $i < $iterations; $i++) {
                $fn();
            }

            $times[$variant][] = (hrtime(true) - $start) / $iterations;
        }
    }

    $beforeTimes = summarize($times['before']);
    $afterTimes = summarize($times['after']);

    printf("%-40s %11.1f ns/op  median %11.1f\n", $beforeLabel, $beforeTimes['best'], $beforeTimes['median']);
    printf(
        "%-40s %11.1f ns/op  median %11.1f  %+.1f%%\n",
        $afterLabel,
        $afterTimes['best'],
        $afterTimes['median'],
        ($afterTimes['best'] / $beforeTimes['best'] - 1) * 100,
    );
}

/* Builds the shape a query returns: 10 fields, a nested document, an array of
 * scalars and three BSON value objects. */
function makeFields(callable $documentFactory, callable $arrayFactory): array
{
    return [
        '_id' => new ObjectId(),
        'name' => 'value',
        'count' => 42,
        'created' => new UTCDateTime(),
        'tags' => $arrayFactory(['a', 'b', 'c']),
        'nested' => $documentFactory(['x' => 1, 'y' => new ObjectId()]),
        'active' => true,
        'score' => 1.5,
        'owner' => 'someone',
        'group' => 'g1',
    ];
}

/* Builds a tree nested $depth levels deep, using the factories at every level. */
function makeNested(int $depth, callable $documentFactory, callable $arrayFactory): BSONDocument
{
    $document = $documentFactory(['leaf' => 1, 'tags' => $arrayFactory(['a', 'b'])]);

    for ($i = 0; $i < $depth; $i++) {
        $document = $documentFactory([
            'child' => $document,
            'name' => 'level ' . $i,
            'index' => $i,
        ]);
    }

    return $document;
}

$fieldsBefore = makeFields($perFieldDocument, $perFieldArray);
$fieldsAfter = makeFields($shippedDocument, $shippedArray);

$documentBefore = $perFieldDocument($fieldsBefore);
$documentAfter = new BSONDocument($fieldsAfter);

echo "=== BSONDocument::__clone(), 10 fields with a nested document ===\n";
bench('per field write back', 'exchangeArray', static fn () => clone $documentBefore, static fn () => clone $documentAfter, 50000);

$arrayBefore = $perFieldArray(array_values($fieldsBefore));
$arrayAfter = new BSONArray(array_values($fieldsAfter));

echo "\n=== BSONArray::__clone(), same 10 values ===\n";
bench('per field write back', 'exchangeArray', static fn () => clone $arrayBefore, static fn () => clone $arrayAfter, 50000);

$flatFields = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
$flatBefore = $perFieldDocument($flatFields);
$flatAfter = new BSONDocument($flatFields);

echo "\n=== Flat document, 5 scalar fields ===\n";
bench('per field write back', 'exchangeArray', static fn () => clone $flatBefore, static fn () => clone $flatAfter, 100000);

echo "\n=== Nested documents, growing depth ===\n";

foreach ([1, 10, 100] as $depth) {
    $nestedBefore = makeNested($depth, $perFieldDocument, $perFieldArray);
    $nestedAfter = makeNested($depth, $shippedDocument, $shippedArray);

    printf("--- depth %d ---\n", $depth);
    bench('per field write back', 'exchangeArray', static fn () => clone $nestedBefore, static fn () => clone $nestedAfter, 20000);
}

echo "\n=== Both bodies produce the same document ===\n";

/*
 * Build the same document twice on the same class, because comparing an
 * anonymous subclass with a BSONDocument always returns false.
 */
$perField = new BSONDocument();
$perField->exchangeArray($fieldsAfter);
foreach ($perField as $key => $value) {
    $perField[$key] = recursive_copy_before($value);
}

$current = clone $documentAfter;

printf("same class:                  %s\n", $perField::class === $current::class ? 'yes' : 'no');
printf("same content:                %s\n", $perField == $current ? 'yes' : 'no');
printf("nested document is new:      %s\n", $current['nested'] !== $fieldsAfter['nested'] ? 'yes' : 'no');
printf("tags array is new:           %s\n", $current['tags'] !== $fieldsAfter['tags'] ? 'yes' : 'no');
printf("_id is new:                  %s\n", $current['_id'] !== $fieldsAfter['_id'] ? 'yes' : 'no');
printf("source document untouched:   %s\n", $fieldsAfter['nested']['x'] === 1 ? 'yes' : 'no');
printf("property access preserved:   %s\n", $current->name === 'value' ? 'yes' : 'no');

echo "\nPHP: " . PHP_VERSION . ', ext-mongodb: ' . phpversion('mongodb') . "\n";
