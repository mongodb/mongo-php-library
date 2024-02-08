<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\OutputWindow;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Factories for Aggregation Pipeline Accumulator and Window Operators
 *
 * @see https://www.mongodb.com/docs/v3.4/reference/operator/aggregation-group/
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
 */
final class Accumulator
{
    use Accumulator\FactoryTrait;

    /**
     * @param Document|Serializable|WindowInterface|array<string, mixed>|stdClass $operator  Window operator to use in the $setWindowFields stage.
     * @param Optional|array{string|int,string|int}                               $documents A window where the lower and upper boundaries are specified relative to the position of the current document read from the collection.
     * @param Optional|array{string|numeric,string|numeric}                       $range     Arguments passed to the init function.
     * @param Optional|non-empty-string                                           $unit      Specifies the units for time range window boundaries. If omitted, default numeric range window boundaries are used.
     */
    public static function outputWindow(
        Document|Serializable|WindowInterface|stdClass|array $operator,
        Optional|array $documents = Optional::Undefined,
        Optional|array $range = Optional::Undefined,
        Optional|TimeUnit|string $unit = Optional::Undefined,
    ): OutputWindow {
        return new OutputWindow($operator, $documents, $range, $unit);
    }

    private function __construct()
    {
        // This class cannot be instantiated
    }
}
