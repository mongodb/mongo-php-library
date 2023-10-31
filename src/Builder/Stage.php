<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use MongoDB\BSON\Serializable;
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
     * @param FieldQueryInterface|QueryInterface|Serializable|array<mixed>|bool|float|int|stdClass|string|null ...$queries The query predicates to match
     */
    public static function match(FieldQueryInterface|QueryInterface|Serializable|array|bool|float|int|stdClass|string|null ...$queries): MatchStage
    {
        // Override the generated method to allow variadic arguments
        return self::generatedMatch($queries);
    }

    private function __construct()
    {
        // This class cannot be instantiated
    }
}
