<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Randomly selects the specified number of documents from its input.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sample/
 */
readonly class SampleStage implements StageInterface
{
    public const NAME = '$sample';
    public const ENCODE = Encode::Object;

    /** @param int $size The number of documents to randomly select. */
    public int $size;

    /**
     * @param int $size The number of documents to randomly select.
     */
    public function __construct(int $size)
    {
        $this->size = $size;
    }
}
