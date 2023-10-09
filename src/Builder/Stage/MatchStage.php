<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Filters the document stream to allow only matching documents to pass unmodified into the next pipeline stage. $match uses standard MongoDB queries. For each input document, outputs either one document (a match) or zero documents (no match).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/
 */
class MatchStage implements StageInterface
{
    public const NAME = '$match';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|QueryInterface|Serializable|array|stdClass $query */
    public Document|Serializable|QueryInterface|stdClass|array $query;

    /**
     * @param Document|QueryInterface|Serializable|array|stdClass $query
     */
    public function __construct(Document|Serializable|QueryInterface|stdClass|array $query)
    {
        $this->query = $query;
    }
}
