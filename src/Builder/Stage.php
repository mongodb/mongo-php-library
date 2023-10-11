<?php

namespace MongoDB\Builder;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Type\QueryFilterInterface;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

enum Stage
{
    use Stage\FactoryTrait {
        match as private generatedMatch;
    }

    /**
     * Filters the document stream to allow only matching documents to pass unmodified into the next pipeline stage. $match uses standard MongoDB queries. For each input document, outputs either one document (a match) or zero documents (no match).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/
     * @param Document|QueryFilterInterface|QueryInterface|Serializable|array|stdClass $queries
     */
    public static function match(QueryFilterInterface|QueryInterface|Serializable|array|bool|float|int|null|stdClass|string ...$queries): MatchStage
    {
        // Override the generated method to allow variadic arguments
        return self::generatedMatch($queries);
    }
}
