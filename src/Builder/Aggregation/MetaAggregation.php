<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDecimal;

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
