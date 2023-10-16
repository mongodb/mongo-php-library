<?php

namespace MongoDB\Builder;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\OutputWindow;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Factories for Aggregation Pipeline Accumulator and Window Operators
 *
 * @see https://www.mongodb.com/docs/v3.4/reference/operator/aggregation-group/
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
 */
enum Accumulator
{
    use Accumulator\FactoryTrait;

    public static function outputWindow(
        Document|Serializable|WindowInterface|stdClass|array $operator,
        Optional|array $documents = Optional::Undefined,
        Optional|array $range = Optional::Undefined,
        Optional|string $unit = Optional::Undefined,
    ): OutputWindow {
        return new OutputWindow($operator, $documents, $range, $unit);
    }
}
