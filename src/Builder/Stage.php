<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Regex;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

final class Stage
{
    use Stage\FactoryTrait {
        match as private generatedMatch;
    }

    /**
     * Filters the document stream to allow only matching documents to pass unmodified into the next pipeline stage. $match uses standard MongoDB queries. For each input document, outputs either one document (a match) or zero documents (no match).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/
     *
     * @param QueryInterface|FieldQueryInterface|Decimal128|Int64|Regex|stdClass|array<array-key,mixed>|bool|float|int|string|null ...$queries The query predicates to match
     */
    public static function match(QueryInterface|FieldQueryInterface|Decimal128|Int64|Regex|stdClass|array|bool|float|int|string|null ...$queries): MatchStage
    {
        // Override the generated method to allow variadic arguments
        return self::generatedMatch($queries);
    }

    private function __construct()
    {
        // This class cannot be instantiated
    }
}
