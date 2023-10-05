<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Skips the first n documents where n is the specified skip number and passes the remaining documents unmodified to the pipeline. For each input document, outputs either zero documents (for the first n documents) or one document (if after the first n documents).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/skip/
 */
class SkipStage implements StageInterface
{
    public const NAME = '$skip';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Int64|int $skip */
    public Int64|int $skip;

    /**
     * @param Int64|int $skip
     */
    public function __construct(Int64|int $skip)
    {
        $this->skip = $skip;
    }
}
