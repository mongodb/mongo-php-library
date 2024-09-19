<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Builder\Type\StageInterface;

use function is_array;

/**
 * Filters the document stream to allow only matching documents to pass unmodified into the next pipeline stage. $match uses standard MongoDB queries. For each input document, outputs either one document (a match) or zero documents (no match).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/
 */
class MatchStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var QueryInterface|array $query */
    public readonly QueryInterface|array $query;

    /**
     * @param QueryInterface|array $query
     */
    public function __construct(QueryInterface|array $query)
    {
        if (is_array($query)) {
            $query = QueryObject::create($query);
        }

        $this->query = $query;
    }

    public function getOperator(): string
    {
        return '$match';
    }
}
