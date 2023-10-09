<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Binary;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToBinData;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToNull;
use MongoDB\Builder\Expression\ResolvesToString;

/**
 * Returns the size of a given string or binary data value's content in bytes.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/binarySize/
 */
class BinarySizeAggregation implements ResolvesToInt
{
    public const NAME = '$binarySize';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|non-empty-string|null $expression */
    public Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression;

    /**
     * @param Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|non-empty-string|null $expression
     */
    public function __construct(Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression)
    {
        $this->expression = $expression;
    }
}
