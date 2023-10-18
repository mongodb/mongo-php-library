<?php

namespace MongoDB\Benchmark\BSON;

use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\BSON\PackedArray;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;

use function array_values;
use function iterator_to_array;

#[BeforeMethods('prepareData')]
#[Revs(10)]
#[Warmup(1)]
final class PackedArrayBench
{
    private static PackedArray $array;

    public function prepareData(): void
    {
        $array = array_values(Data::readJsonFile(Data::LARGE_FILE_PATH));

        self::$array = PackedArray::fromPHP($array);
    }

    public function benchCheckFirst(): void
    {
        self::$array->has(0);
    }

    public function benchCheckLast(): void
    {
        self::$array->has(94354);
    }

    public function benchAccessFirst(): void
    {
        self::$array->get(0);
    }

    public function benchAccessLast(): void
    {
        self::$array->get(94354);
    }

    public function benchIteratorToArray(): void
    {
        iterator_to_array(self::$array);
    }

    public function benchToPHPArray(): void
    {
        self::$array->toPHP();
    }

    public function benchToPHPArrayViaIteration(): void
    {
        $array = [];

        foreach (self::$array as $key => $value) {
            $array[$key] = $value;
        }
    }

    public function benchIteration(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        // phpcs:ignore Generic.ControlStructures.InlineControlStructure.NotAllowed
        foreach (self::$array as $key => $value);
    }

    public function benchIterationAfterIteratorToArray(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        // phpcs:ignore Generic.ControlStructures.InlineControlStructure.NotAllowed
        foreach (iterator_to_array(self::$array) as $key => $value);
    }

    public function benchIterationAsArray(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        // phpcs:ignore Generic.ControlStructures.InlineControlStructure.NotAllowed
        foreach (self::$array->toPHP() as $key => $value);
    }
}
