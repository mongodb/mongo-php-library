<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Reorders the document stream by a specified sort key. Only the order changes; the documents remain unmodified. For each input document, outputs one document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/
 */
class SortStage implements StageInterface
{
    public const NAME = '$sort';
    public const ENCODE = Encode::Single;

    /** @param Document|Serializable|array|stdClass $sort */
    public Document|Serializable|stdClass|array $sort;

    /**
     * @param Document|Serializable|array|stdClass $sort
     */
    public function __construct(Document|Serializable|stdClass|array $sort)
    {
        $this->sort = $sort;
    }
}
