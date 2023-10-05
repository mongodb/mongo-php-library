<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDecimal;

/**
 * Access available per-document metadata related to the aggregation operation.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/
 */
class MetaAggregation implements ResolvesToDecimal
{
    public const NAME = '$meta';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

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
