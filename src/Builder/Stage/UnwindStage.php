<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ArrayFieldPath;
use MongoDB\Builder\Type\StageInterface;

/**
 * Deconstructs an array field from the input documents to output a document for each element. Each output document replaces the array with an element value. For each input document, outputs n documents where n is the number of array elements and can be zero for an empty array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/
 */
class UnwindStage implements StageInterface
{
    public const NAME = '$unwind';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ArrayFieldPath|non-empty-string $field */
    public ArrayFieldPath|string $field;

    /**
     * @param ArrayFieldPath|non-empty-string $field
     */
    public function __construct(ArrayFieldPath|string $field)
    {
        $this->field = $field;
    }
}
