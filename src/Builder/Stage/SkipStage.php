<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Skips the first n documents where n is the specified skip number and passes the remaining documents unmodified to the pipeline. For each input document, outputs either zero documents (for the first n documents) or one document (if after the first n documents).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/skip/
 */
class SkipStage implements StageInterface
{
    public const NAME = '$skip';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param int $skip */
    public int $skip;

    /**
     * @param int $skip
     */
    public function __construct(int $skip)
    {
        $this->skip = $skip;
    }
}
