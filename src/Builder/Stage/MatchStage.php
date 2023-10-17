<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

use function is_array;
use function is_object;

/**
 * Filters the document stream to allow only matching documents to pass unmodified into the next pipeline stage. $match uses standard MongoDB queries. For each input document, outputs either one document (a match) or zero documents (no match).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/
 */
class MatchStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Document|QueryInterface|Serializable|array|stdClass $query */
    public readonly Document|Serializable|QueryInterface|stdClass|array $query;

    /**
     * @param Document|QueryInterface|Serializable|array|stdClass $query
     */
    public function __construct(Document|Serializable|QueryInterface|stdClass|array $query)
    {
        if (is_array($query) || is_object($query)) {
            $query = QueryObject::create(...$query);
        }

        $this->query = $query;
    }

    public function getOperator(): string
    {
        return '$match';
    }
}
