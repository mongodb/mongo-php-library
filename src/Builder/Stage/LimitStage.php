<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Passes the first n documents unmodified to the pipeline where n is the specified limit. For each input document, outputs either one document (for the first n documents) or zero documents (after the first n documents).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/limit/
 */
class LimitStage implements StageInterface
{
    public const NAME = '$limit';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param int $limit */
    public int $limit;

    /**
     * @param int $limit
     */
    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }
}
