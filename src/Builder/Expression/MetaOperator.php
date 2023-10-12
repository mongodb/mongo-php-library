<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;

/**
 * Access available per-document metadata related to the aggregation operation.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/
 */
readonly class MetaOperator implements ResolvesToAny
{
    public const NAME = '$meta';
    public const ENCODE = Encode::Single;

    /** @param non-empty-string $keyword */
    public string $keyword;

    /**
     * @param non-empty-string $keyword
     */
    public function __construct(string $keyword)
    {
        $this->keyword = $keyword;
    }
}
