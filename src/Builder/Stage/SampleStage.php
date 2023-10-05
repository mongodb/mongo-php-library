<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Randomly selects the specified number of documents from its input.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sample/
 */
class SampleStage implements StageInterface
{
    public const NAME = '$sample';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Int64|int $size The number of documents to randomly select. */
    public Int64|int $size;

    /**
     * @param Int64|int $size The number of documents to randomly select.
     */
    public function __construct(Int64|int $size)
    {
        $this->size = $size;
    }
}